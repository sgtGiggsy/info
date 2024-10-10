<?php
$start_time = microtime(true);
//? Alap includolások
include('./includes/config.inc.php');
include('./includes/functions.php');
include('./Classes/Ertesites.class.php');
include('./Classes/MySQLhandler.class.php');
include('./Classes/MailHandler.class.php');

define('ROOT_PATH', getenv('APP_ROOT_PATH'));
define('DEBUG_MODE', false);

$RootPath = ROOT_PATH;
$dbcallcount = 0;
$loginsuccess = $sajatolvas = $csoportolvas = $mindolvas = $sajatir = $csoportir = $mindir = false;
$szulonyit = $id = $userid = $felhasznaloid = $loginid = $activitylogid = $gyujtooldal = $selectedurl = null;
$pagetofind = "fooldal";
$params = array();
//$querylist = array();

//? Session indítása, vagy folytatása
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params('604800');
	session_start();
}

//? Cache beállítása
//header('Cache-Control: no-cache');
//header('Pragma: no-cache');
header("Content-Encoding: compress");
header("Cache-Control: must-revalidate, private, max-age=31536000");
//header("Content-Security-Policy-Report-Only: script-src 'nonce-{RANDOM}' 'strict-dynamic';");

//? Primitív "bruteforce" elleni védelem
if(!isset($_SESSION['badpasscount']) || ($_SESSION['badpasscount'] > 4 && time() - $_SESSION['lastbadpasstime'] > 600))
{
    $_SESSION['badpasscount'] = 0;
    $_SESSION['lastbadpasstime'] = null;
}

if($_SESSION['badpasscount'] > 4 && time() - $_SESSION['lastbadpasstime'] < 300)
{
    echo "<h1>Öt alkalommal is hibás jelszót adtál meg!</h1>";
    echo "<h2>Ne próbálkozz újra " . timeStampForSQL(time() + 300) . " előtt!</h2>";
    die;
}

//? Címsorból vett GET értékek tisztítása nemkívánt karakterektől
foreach($_GET as $key => $value)
{
    if($key != "page" && $key != "subpage" && $key != "id")
        $params[$key] = $value;
    
    $value = trim($value);
    $value = strip_tags($value);
    $value = str_replace(array("\r\n", "\r", "\n", "'", "\"", "<", ">", ";", ":", "(", ")"), "", $value);
    $_GET[$key] = $value;
}

//? Alapvető $_GET és $_SESSION műveletek lebonyolítása, és kilépés
//? Ha nincs betölteni kívánt oldal, a főoldal marad betöltésre kiválasztva
if(isset($_GET['page']))
{
    $pagetofind = $_GET['page'];

	if($pagetofind == "kilep" && $_SESSION['id'] && $_SESSION['felhasznalonev'])
	{
        session_destroy();
		header("Location: $RootPath/index.php");
		die();
	}
}

//? Az lekérdezendő elem ID-jének begyüjtése
if(isset($_GET['id']))
    $id = $_GET['id'];

if(isset($_GET['loginid']))
    $loginid = $_GET['loginid'];

//? Az előző oldal helyének kiderítése
if(!isset($_SESSION['elozmenyek']))
{
    $backtosender = @$_SERVER['HTTP_REFERER'];
    $_SESSION['elozmenyek'] = array(@$_SERVER['HTTP_REFERER']);
}
else
{
    if(@$_SERVER['HTTP_REFERER'] && !str_contains(@$_SERVER['HTTP_REFERER'], @$_SERVER['REDIRECT_URL']) && !str_contains(@$_SERVER['HTTP_REFERER'], "belepes"))
    {
        array_push($_SESSION['elozmenyek'], $_SERVER['HTTP_REFERER']);
        $backtosender = $_SERVER['HTTP_REFERER'];
    }
    else
        $backtosender = end($_SESSION['elozmenyek']);

    if(count($_SESSION['elozmenyek']) > 5)
        array_shift($_SESSION['elozmenyek']);
}

//? Felhasználó beléptetése
if((!isset($_SESSION['id']) || !$_SESSION['id']) && isset($_POST['felhasznalonev']))
{
    $samaccountname = $_POST['felhasznalonev'];
    $plainpassword = $_POST['jelszo'];
    $hashedpassword = password_hash($plainpassword, PASSWORD_DEFAULT); // A jelszó hash-e az adatbázisban tároláshoz

    // LDAP-on keresztüli autentikációt elvégző rész
    if(count($LDAP_SERVERS) >= 0) // Ha van megadott LDAP szerver a beállításokban, akkor megpróbáljuk azt használni a belépéshez
    {
        $ldapusername = $samaccountname . "@" . $LDAP_DOMAIN; // Felhasználónév LDAP formátumra konvertálása

        foreach($LDAP_SERVERS as $x)
        {
            if(checkLDAPConnection($x))
            {
                $ldapconnection = ldap_connect($x, $LDAP_PORT); // LDAP kapcsolat inicializálása

                ldap_set_option($ldapconnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldapconnection, LDAP_OPT_REFERRALS, 0);

                @$ldapbind = ldap_bind($ldapconnection, $ldapusername, $plainpassword); // LDAP bejelentkezés, mivel nem errort, csak warningot dobhat hiba esetén, el kell nyomni a hibaüzenetet
                if($ldapbind)
                {
                    $filter = "(&(objectClass=user)(sAMAccountName=$samaccountname))";
                    $ldapsearch = ldap_search($ldapconnection, $LDAP_DIR, $filter); // A felhasználó adatainak lekérése a DC-től
                    if($ldapsearch)
                    {
                        $ldapresults = ldap_get_entries($ldapconnection, $ldapsearch);
                        // Ha nincs email, vagy megjelenő név valakinél megadva, warningot dobna a lekérés, így el kell nyomnunk az esetleges hibaüzenetet
                        @$email = $ldapresults[0]['mail'][0];
                        @$nev = $ldapresults[0]['displayname'][0];
                        @$osztaly = $ldapresults[0]['department'][0];
                        @$szervezet = szervezetValaszto($ldapresults[0]['company'][0]);
                        @$telefon = $ldapresults[0]['telephonenumber'][0];
                        @$beosztas = $ldapresults[0]['title'][0];
                        @$thumb = $ldapresults[0]['thumbnailphoto'][0];
                        /*foreach($ldapresults[0] as $x)
                        {
                            print_r($x);
                            echo "<br>";
                        }*/
                    }
                    break;
                }
            }
        }
    }

    // MySQL-en keresztüli autentikációt elvégző rész
    $login = new MySQLHandler('SELECT id, jelszo FROM felhasznalok WHERE felhasznalonev = ?', $samaccountname);
    $login->Bind($userid, $jelszo);

    // A lényegi bejelentkeztetést végző elágazás, csak akkor lépünk be ide, ha legalább az egyik módon van érvényes eredmény a felhasználónév-jelszó párosra
    if((isset($ldapbind) && $ldapbind) || (isset($jelszo) && (password_verify($_POST['jelszo'], $jelszo))))
    {
        if(isset($ldapbind) && $ldapbind) // Az LDAP bejelentkezést elvégző modul.
        {
            if(isset($jelszo)) // Ha létezett már a felhasználó a MySQL adatbázisban, frissítjük az adatait a DC-től kapottakkal
            {
                $update = new MySQLHandler('UPDATE felhasznalok SET jelszo=?, nev=?, email=?, osztaly=?, szervezet=?, telefon=?, beosztas=?, profilkep=? WHERE felhasznalonev=?',
                    $hashedpassword, $nev, $email, $osztaly, $szervezet, $telefon, $beosztas, $thumb, $samaccountname);
            }
            else // Ha nem létezett a felhasználó a MySQL adatbázisban, létrehozzuk (a jelen táblabeállítás szerint a MySQL-ben automatikusan 1-es, azaz legalacsonyabb belépett joggal jön létre minden felhasználó)
            {
                $insert = new MySQLHandler('INSERT INTO felhasznalok (felhasznalonev, jelszo, nev, email, osztaly, szervezet, telefon, beosztas, profilkep) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    $samaccountname, $hashedpassword, $nev, $email, $osztaly, $szervezet, $telefon, $beosztas, $thumb);
            }
        }

        if(isset($ldapbind) && !$ldapbind) // Ez az elágazás kényszeríti ki, hogy ha van elérhető DC kiszolgáló, akkor annak a hitelesítését használja a modul
        {
            $hiba = "Windows kiszolgáló elérhető, a megadott felhasználónév vagy jelszó helytelen!";
        }
        else
        {
            session_regenerate_id();
            if($login->sorokszama == 1) // Ez az egyedüli "Sikeres bejelentkezés" ág. Bármely más ágra fut ki a modul, a bejelentkezés sikertelen
            {
                $_SESSION['id'] = true;
                $loginid = logLogin($userid);
                $loginsuccess = true;
                $_SESSION['badpasscount'] = 0;
            }
        }
    }
    else // Nem létező felhasználó, vagy hibás jelszó esetén lefutó ág. Csak akkor kerülünk ide, ha egyik metódussal sem érkezett érvényes válasz a felhasználónév-jelszó párosra
    {
        $hiba = "Felhasználónév vagy jelszó nem megfelelő!";
    }

    if(isset($hiba) && $hiba)
    {
		$failed = new MySQLHandler('INSERT INTO failedlogins (felhasznalonev, ipcim) VALUES (?, ?)',
            $_POST['felhasznalonev'], $_SERVER['REMOTE_ADDR']);
        echo "<h2>$hiba</h2>";
        ?><script type='text/javascript'>alert('<?=$hiba?>')</script>
        <head><meta http-equiv="refresh" content="0; URL='./belepes'" /></head><?php
        $_SESSION['badpasscount']++;
        $_SESSION['lastbadpasstime'] = time();
        die;
    }
}

//? A felhasználó jelen státuszának ellenőrzése (jogos, vagy sem bejelentkezve maradni)
if(isset($_SESSION['id']) && $_SESSION['id'])
{
	if(!isset($samaccountname))
    {
        $samaccountname = $_SESSION['felhasznalonev'];
    }
    // Mivel van aktív sessionje, ellenőrizzük, hogy továbbra is jogosult-e bejelentkezve lenni
    $result = new MySQLHandler("SELECT id, felhasznalonev, nev, profilkep, szervezet FROM felhasznalok WHERE felhasznalonev = ?", $samaccountname);

    if($result->sorokszama == 1)
    {
        $result->Bind($_SESSION['id'], $_SESSION['felhasznalonev'], $_SESSION['nev'], $_SESSION['profilkep'], $szervezet);
        // Ez a rész gondoskodik róla, hogy ha egy adott oldalt próbált a felhasználó felkeresni,
        // a sikeres bejelentkezés után vissza legyen oda irányítva
        if($loginsuccess)
        {
            header("Location: " . $backtosender . "?sikeres=bejelentkezes&loginid=" . $loginid);
            die;
        }
    }
    else
    {
        session_destroy();
		$_SESSION['id'] = false; 
    }
}
else
    $_SESSION['id'] = false;

//? Oldal működéséhez használt alapbeállítások betöltése
$beallitas = new MySQLHandler("SELECT * FROM beallitasok");
$beallitas = $beallitas->Result();
foreach($beallitas as $x)
{
    $nev = $x['nev'];
    $ertek = trim($x['ertek']);
    $_SESSION["$nev"] = $ertek;
}

//? A menüpontok, valamint a felhasználó jogosultságainak, csoporttagságainak és személyes beálltásainak lekérése.
//? Innentől kezdve a $felhasznaloid változónak bejelentkezett felhasználó esetén léteznie KELL
//? A menüpontok lekérése is itt történik meg
if($_SESSION['id'])
{
    $felhasznaloid = $_SESSION['id'];
    $szemelyesbeallitasok = new MySQLHandler("SELECT * FROM szemelyesbeallitasok WHERE felhid = ?", $felhasznaloid);
    $szemelyes = $szemelyesbeallitasok->Fetch();

    // Csoporttagságok begyüjtése
    $csoporttagsagok = new MySQLHandler("SELECT csoportok.nev AS csoportnev, szervezet, telephely
        FROM csoportok
            INNER JOIN csoporttagsagok ON csoportok.id = csoporttagsagok.csoport
            LEFT JOIN csoportjogok ON csoportjogok.csoport = csoporttagsagok.csoport
        WHERE felhasznalo = ?", $felhasznaloid);
    $csoporttagsagok = $csoporttagsagok->Result();

    $menusql = new MySQLHandler("SELECT id, menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, iras, olvasas, lathat
        FROM
        (
            SELECT menupontok.id AS id, menupontok.menupont AS menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, iras, olvasas, 1 AS lathat
            FROM menupontok
                INNER JOIN jogosultsagok ON menupontok.id = jogosultsagok.menupont
            WHERE jogosultsagok.felhasznalo = ? AND menupontok.aktiv > 0 AND menupontok.aktiv < 4
        UNION ALL
            SELECT menupontok.id AS id, menupontok.menupont AS menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, NULL, NULL, 1 AS lathat
            FROM menupontok
            WHERE menupontok.aktiv > 1 AND menupontok.aktiv < 4
        UNION ALL
            SELECT menupontok.id AS id, menupontok.menupont AS menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, iras, olvasas, NULL AS lathat
            FROM menupontok
                INNER JOIN jogosultsagok ON menupontok.id = jogosultsagok.menupont
            WHERE jogosultsagok.felhasznalo = ? AND menupontok.aktiv IS NULL OR menupontok.aktiv = 0
        ) AS egyesitett
    GROUP BY id
    ORDER BY menuterulet ASC, sorrend ASC;", $felhasznaloid, $felhasznaloid);
}
else
{
    $felhasznaloid = false;
    $szemelyes = false;
    $menusql = new MySQLHandler("SELECT id, menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, NULL AS iras, NULL olvasas, lathat
        FROM
        (
            SELECT id, menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, NULL AS iras, NULL olvasas, 1 AS lathat
            FROM menupontok
            WHERE aktiv > 2
        UNION ALL
            SELECT id, menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl, NULL AS iras, NULL olvasas, NULL ASlathat
            FROM menupontok
            WHERE aktiv < 3 OR aktiv IS NULL
        ) AS egyesitett
        GROUP BY id
        ORDER BY menuterulet ASC, sorrend ASC");
}
$menu = $menusql->Result();

//? A jelen oldalhoz tartozó jogosultságok megállapítása,
//? valamint, amennyiben van olyan, a jelen oldal szülőjének megjelölése
$menuterulet = array(array(), array(), array());
foreach($menu as $oldal)
{
    switch($pagetofind)
    {
        case $oldal['szerkoldal'] :
        case $oldal['oldal'] :
            $selectedurl = $oldal['url'];
        case $oldal['gyujtooldal'] : 
            ($selectedurl) ? : $selectedurl = $oldal['gyujtourl'];
        case $oldal['dboldal'] :
            ($selectedurl) ? : $selectedurl = $oldal['dburl'];
            switch($oldal['olvasas'])
            {
                case 3: $mindolvas = true;
                case 2: $csoportolvas = true;
                case 1: $sajatolvas = true;
            }

            switch($oldal['iras'])
            {
                case 3: $mindir = true;
                case 2: $csoportir = true;
                case 1: $sajatir = true;
            }

            $currentpage = $oldal;
            $szulonyit = $oldal['szulo'];
        break;
    }
    $menuterulet[$oldal['menuterulet']][] = $oldal;
}

//? Fallback megoldás arra az esetre, ha a lekérni próbált oldalhoz nincs adatbázis bejegyzés
if(!isset($currentpage))
{
    $selectedurl = $pagetofind;
    $currentpage['oldal'] = $pagetofind;
    $currentpage['cimszoveg'] = "Oldal";
    $currentpage['gyujtocimszoveg'] = "Oldal";
    $currentpage['aktiv'] = 3;
}

//? Szükség esetén 404-es hibaoldal generálása
try
{
    $page = @fopen("./{$selectedurl}.php", "r");
    if(!$page)
        throw new Exception();
}
catch(Exception $e)
{
    http_response_code(404);
    $selectedurl = "404";
    $currentpage['gyujtocimszoveg'] = "Oldal nem található!";
}

//? Folyamatértesítés
if(@$_GET['sikeres'] == "uj")
    $succesmessage = "Új " . $currentpage['cimszoveg'] . " hozzáadása sikeres";
elseif(@$_GET['sikeres'] == "szerkesztes")
    $succesmessage = "A(z) " . $currentpage['cimszoveg'] . " szerkesztése sikerült";
elseif(@$_GET['sikeres'] == "bejelentkezes")
    $succesmessage = "Sikeres bejelentkezés";

//? JavaScript fájlok listája
$javascriptfiles = [
    "includes/js/functions.js",
    "includes/js/tableActions.js",
    "includes/js/ertesites.js",
    "includes/js/pageload.js"
];

if($pagetofind != "aktiveszkoz" && $pagetofind != "sohoeszkoz" && $pagetofind != "mediakonverter" || ($pagetofind == "aktiveszkoz" && isset($_GET['action'])))
    $javascriptfiles[] = "includes/js/progressOverlay.js";

//? PHP változók átadni a JavaScriptnek
$PHPvarsToJS = [
    array('name' => 'RootPath', 'val' => $RootPath)];

if($loginid)
    $PHPvarsToJS[] = array('name' => 'loginid', 'val' => $loginid);

if($felhasznaloid && @$szemelyes['switchstateshow'])
{
    $PHPvarsToJS[] = array('name' => 'Felhasznaloid', 'val' => $felhasznaloid);
    $javascriptfiles[] = "modules/eszkozok/includes/eszkozonlinecheck.js";
}

if($felhasznaloid != 1)
    $activitylogid = logActivity($felhasznaloid, $params);

//? Oldal megjelenítése
include('./templates/index.tpl.php');

//? Oldal legenerálásának sebessége, kizárólag a root felhasználó részére
$end_time = microtime(true);
$pagegentime = round($end_time - $start_time, 2);
$ftevekenyseg = new MySQLHandler("UPDATE felhasznalotevekenysegek SET dbcallcount=?, pagegentime=? WHERE id=?", $dbcallcount + 1, $pagegentime, $activitylogid);

if($felhasznaloid == 1)
{
    echo "<div id='pageloadinfo'>Oldal generálás ideje: " . $pagegentime . " mp<br />" . "Adatbázis hívások száma: " . $dbcallcount . "</div>";
    
    if(@$querylist)
    {
        echo "<div class='contentcenter olvashato' style='margin: 0 auto'>";
        foreach($querylist as $query)
        {   
            echo "<p style='padding-bottom: 1em'>" . FormatSQL($query) . "</p>";
        }
        echo "</div>";
    }
}
?>