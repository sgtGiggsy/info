<?php
// Alap includolások
include('./includes/config.inc.php');
include('./includes/functions.php');
$RootPath = getenv('APP_ROOT_PATH');
$dbcallcount = 0;

// Session indítása, vagy folytatása
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params('604800');
	session_start();
}

// Cache beállítása
header('Cache-Control: no-cache');
header('Pragma: no-cache');
//header("Cache-Control: must-revalidate, private, max-age=31536000");
//header("Content-Security-Policy-Report-Only: script-src 'nonce-{RANDOM}' 'strict-dynamic';");

// Alapvető $_GET és $_SESSION műveletek lebonyolítása, és kilépés
$page = $id = $current = $felhasznaloid = null; $loginsuccess = false;

// Címsorból vett GET értékek tisztítása nemkívánt karakterektől
foreach($_GET as $key => $value)
{
    $value = trim($value);
    $value = strip_tags($value);
    $value = str_replace(array("\r\n", "\r", "\n", "'", "\"", "<", ">", ";", ":", "(", ")"), "", $value);
    $_GET[$key] = $value;
}

if(isset($_GET['page']))
{
    $page = $_GET['page'];
    $current = $page;

	if($page == "kilep")
	{
        session_destroy();
        session_start();
		header("Location: $RootPath/index.php");
		die();
	}
}

// Az előző oldal helyének kiderítése
if(!isset($_SESSION[getenv('SESSION_NAME').'elozo']))
{
    $backtosender = @$_SERVER['HTTP_REFERER'];
    $_SESSION[getenv('SESSION_NAME').'elozo'] = @$_SERVER['HTTP_REFERER'];
}
else
{
    if(str_contains(@$_SERVER['HTTP_REFERER'], @$_SERVER['REDIRECT_URL']) || isset($_GET['action']))
    {
        $backtosender = $_SESSION[getenv('SESSION_NAME').'elozo'];
    }
    else
    {
        $backtosender = @$_SERVER['HTTP_REFERER'];
        $_SESSION[getenv('SESSION_NAME').'elozo'] = @$_SERVER['HTTP_REFERER'];
    } 
}

// Az lekérdezendő elem ID-jének begyüjtése
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}

// Felhasználó beléptetése
if((!isset($_SESSION[getenv('SESSION_NAME').'id']) || !$_SESSION[getenv('SESSION_NAME').'id']) && isset($_POST['felhasznalonev']) && !(isset($_GET['page']) && $_GET['page'] == "kilep"))
{
	$con = mySQLConnect();

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
                        @$alakulat = alakulatValaszto($ldapresults[0]['company'][0]);
                        @$telefon = $ldapresults[0]['telephonenumber'][0];
                        @$beosztas = $ldapresults[0]['title'][0];
                        @$thumb = $ldapresults[0]['thumbnailphoto'][0];
                        /*foreach($ldapresults[0] as $x)
                        {
                            print_r($x);
                            echo "<br>";
                        }*/

                        //echo $alakulat . " és " . $telefon;
                    }
                    break;
                }
            }
        }
    }

    // MySQL-en keresztüli autentikációt elvégző rész
    if ($stmt = $con->prepare('SELECT id, jelszo FROM felhasznalok WHERE felhasznalonev = ?'))
    {
        $stmt->bind_param('s', $samaccountname);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0)
        {
            $stmt->bind_result($id, $jelszo);
            $stmt->fetch();
            $stmt->close();
        }
    }

    // A lényegi bejelentkeztetést végző elágazás, csak akkor lépünk be ide, ha legalább az egyik módon van érvényes eredmény a felhasználónév-jelszó párosra
    if((isset($ldapbind) && $ldapbind) || (isset($jelszo) && (password_verify($_POST['jelszo'], $jelszo))))
    {
        if(isset($ldapbind) && $ldapbind) // Az LDAP bejelentkezést elvégző modul.
        {
            if(isset($jelszo)) // Ha létezett már a felhasználó a MySQL adatbázisban, frissítjük az adatait a DC-től kapottakkal
            {
                if ($stmt = $con->prepare('UPDATE felhasznalok SET jelszo=?, nev=?, email=?, osztaly=?, alakulat=?, telefon=?, beosztas=?, profilkep=? WHERE felhasznalonev=?'))
                {
                    $stmt->bind_param('sssssssss', $hashedpassword, $nev, $email, $osztaly, $alakulat, $telefon, $beosztas, $thumb, $samaccountname);
                    $stmt->execute();
                }
            }
            else // Ha nem létezett a felhasználó a MySQL adatbázisban, létrehozzuk (a jelen táblabeállítás szerint a MySQL-ben automatikusan 1-es, azaz legalacsonyabb belépett joggal jön létre minden felhasználó)
            {
                if ($stmt = $con->prepare('INSERT INTO felhasznalok (felhasznalonev, jelszo, nev, email, osztaly, alakulat, telefon, beosztas, profilkep) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    $stmt->bind_param('sssssssss', $samaccountname, $hashedpassword, $nev, $email, $osztaly, $alakulat, $telefon, $beosztas, $thumb);
                    $stmt->execute();
                }
            }
        }

        if(isset($ldapbind) && !$ldapbind) // Ez az elágazás kényszeríti ki, hogy ha van elérhető DC kiszolgáló, akkor annak a hitelesítését használja a modul
        {
            $hiba = "Windows kiszolgáló elérhető, a megadott felhasználónév vagy jelszó helytelen!";
        }
        else
        {
            $result = mySQLConnect("SELECT * FROM felhasznalok WHERE felhasznalonev = '$samaccountname'");

            session_regenerate_id();
            if(mysqli_num_rows($result) == 1) // Ez az egyedüli "Sikeres bejelentkezés" ág. Bármely más ágra fut ki a modul, a bejelentkezés sikertelen
            {
                $userid = mysqli_fetch_assoc($result)['id'];
                $_SESSION[getenv('SESSION_NAME').'id'] = true;
                logLogin($userid);
                $loginsuccess = true;
            }
            else
            {
                ?><script type='text/javascript'>alert('Adatbázis elérés hiba!')</script>
                <head><meta http-equiv="refresh" content="0; URL='./belepes'" /></head><?php
            }
        }
    }
    else // Nem létező felhasználó, vagy hibás jelszó esetén lefutó ág. Csak akkor kerülünk ide, ha egyik metódussal sem érkezett érvényes válasz a felhasználónév-jelszó párosra
    {
        $hiba = "Felhasználónév vagy jelszó nem megfelelő!";
    }

    if(isset($hiba) && $hiba)
    {
        $con = mySQLConnect();
		if ($stmt = $con->prepare('INSERT INTO failedlogins (felhasznalonev, ipcim) VALUES (?, ?)'))
		{
			$stmt->bind_param('ss', $_POST['felhasznalonev'], $_SERVER['REMOTE_ADDR']);
			$stmt->execute();
		}
        echo "<h2>$hiba</h2>";
        ?><script type='text/javascript'>alert('<?=$hiba?>')</script>
        <head><meta http-equiv="refresh" content="0; URL='./belepes'" /></head><?php
        die;
    }
}

if(isset($_SESSION[getenv('SESSION_NAME').'id']) && $_SESSION[getenv('SESSION_NAME').'id'])
{
	if(!isset($samaccountname))
    {
        $samaccountname = $_SESSION[getenv('SESSION_NAME').'felhasznalonev'];
    }
    // Mivel van aktív sessionje, ellenőrizzük, hogy továbbra is jogosult-e bejelentkezve lenni
    $result = mySQLConnect("SELECT * FROM felhasznalok WHERE felhasznalonev = '$samaccountname'");
    $row = $result->fetch_assoc();

    if(mysqli_num_rows($result) == 1)
    {
        $_SESSION[getenv('SESSION_NAME').'id'] = $row['id'];
        $_SESSION[getenv('SESSION_NAME').'felhasznalonev'] = $row['felhasznalonev'];
        $_SESSION[getenv('SESSION_NAME').'nev'] =  $row['nev'];
        $_SESSION['profilkep'] =  $row['profilkep'];
        $alakulat = $row['alakulat'];

        // Ez a rész gondoskodik róla, hogy ha egy adott oldalt próbált a felhasználó felkeresni,
        // a sikeres bejelentkezés után vissza legyen oda irányítva
        if($loginsuccess && isset($_GET['kuldooldal']) && $_GET['kuldooldal'] != "belepes")
        {
            if(isset($_GET['kuldooldalid']))
            {
                header("Location: $RootPath/" . $_GET['kuldooldal'] . "/" . $_GET['kuldooldalid'] . "?sikeres=bejelentkezes");
                die;
            }
            else
            {
                header("Location: $RootPath/" . $_GET['kuldooldal'] . "?sikeres=bejelentkezes");
                die;
            }
        }
    }
    else
    {
        session_destroy();
		$_SESSION[getenv('SESSION_NAME').'id'] = false; 
    }
}
else
{
	parseUserAgent();
    $_SESSION[getenv('SESSION_NAME').'id'] = false;
}

// Oldal működéséhez használt alapbeállítások betöltése
$beallitas = mySQLConnect("SELECT * FROM beallitasok");
foreach($beallitas as $x)
{
    $nev = $x['nev'];
    $ertek = trim($x['ertek']);
    $_SESSION[getenv('SESSION_NAME')."$nev"] = $ertek;
}
if($_SESSION[getenv('SESSION_NAME').'ismetelheto'] == 0)
{
    $_SESSION[getenv('SESSION_NAME').'ismetelheto'] = false;
}
else
{
    $_SESSION[getenv('SESSION_NAME').'ismetelheto'] = true;
}

// Betöldendő oldal kiválasztása, menüterületek feltöltése, és felhasználói jogosultságok megállapítása
$menu = mySQLConnect("SELECT * FROM menupontok ORDER BY menuterulet ASC, sorrend ASC, aktiv DESC, id ASC");
$sajatolvas = $csoportolvas = $mindolvas = $sajatir = $csoportir = $mindir = false;

// Ha nincs betölteni kívánt oldal, a főoldal kiválasztása betöltésre
if(!(isset($_GET['page'])))
{
    $pagetofind = "fooldal";
}
else
{
    $pagetofind = $_GET['page'];
}

// Felhasználó jogosultságainak lekérése, a menüpontok is ezalapján jelennek meg,
// innentől kezdve a $felhasznaloid változónak bejelentkezett felhasználó esetén léteznie KELL
if($_SESSION[getenv('SESSION_NAME').'id'])
{
    $felhasznaloid = $_SESSION[getenv('SESSION_NAME').'id'];
    $jogosultsagok = mySQLConnect("SELECT * FROM jogosultsagok WHERE felhasznalo = $felhasznaloid;");
}

// Felhasználó személyes beállításainak lekérése
if($_SESSION[getenv('SESSION_NAME').'id'])
{
    $szemelyesbeallitasok = mySQLConnect("SELECT * FROM szemelyesbeallitasok WHERE felhid = $felhasznaloid");
    $szemelyes = mysqli_fetch_assoc($szemelyesbeallitasok);

    // Csoporttagságok begyüjtése
    $csoporttagsagok = mySQLConnect("SELECT csoportok.nev AS csoportnev, alakulat, telephely
        FROM csoportok
            INNER JOIN csoporttagsagok ON csoportok.id = csoporttagsagok.csoport
            LEFT JOIN csoportjogok ON csoportjogok.csoport = csoporttagsagok.csoport
        WHERE felhasznalo = $felhasznaloid;");
}

// Menüterületeket tároló tömb elkészítése
$menuk = array();

foreach($menu as $menupont)
{
    if(!isset($menuk[$menupont['menuterulet']]))
    {
        $menuk[$menupont['menuterulet']] = array();
    }

    // Ha megvan a jelenlegi oldal, a hozzá tartozó jogosultságok beállítása
    $gyujtotemp = $menupont['gyujtooldal'];
    if($gyujtotemp)
    {
        $gyujtotemp = explode('?', $gyujtotemp)[0];
    }
    if($menupont['oldal'] == $pagetofind || $gyujtotemp == $pagetofind || $menupont['dboldal'] == $pagetofind || $menupont['szerkoldal'] == $pagetofind)
    {
        if($_SESSION[getenv('SESSION_NAME').'id'])
		{
			foreach($jogosultsagok as $jogosultsag)
			{
				if($menupont['id'] == $jogosultsag['menupont'])
				{
                    switch($jogosultsag['olvasas'])
                    {
                        case 3: $mindolvas = true;
                        case 2: $csoportolvas = true;
                        case 1: $sajatolvas = true;
                    }

                    switch($jogosultsag['iras'])
                    {
                        case 3: $mindir = true;
                        case 2: $csoportir = true;
                        case 1: $sajatir = true;
                    }
					break;
				}
			}
		}
        $currentpage = $menupont;
        $gyujtooldal = $gyujtotemp;
    }

    // Ha egy menüpont megjelenése nincs kifejezett joghoz kötve, menüterülethez adása
    // ha joghoz kötött, a felhasználó jogosultsága szerinti eljárás
    // Megjelenési jogok:
    // aktiv 0 = senkinek
    // aktiv 1 = jogosultaknak
    // aktiv 2 = bejelentkezetteknek
    // aktiv 3 = mindenkinek
    // aktiv 4 = kijelentkezetteknek
    if($menupont['aktiv'] == 0 || $menupont['aktiv'] == 3 || (($menupont['aktiv'] == 2 && $_SESSION[getenv('SESSION_NAME').'id'])) || ($menupont['aktiv'] == 4 && !$_SESSION[getenv('SESSION_NAME').'id']))
    {
        array_push($menuk[$menupont['menuterulet']], $menupont);
    }
    elseif($menupont['aktiv'] == 1 && $_SESSION[getenv('SESSION_NAME').'id'])
    {
        foreach($jogosultsagok as $jogosultsag)
        {
            if($menupont['id'] == $jogosultsag['menupont'])
            {
                if($jogosultsag['olvasas'] > 0)
                {
                    array_push($menuk[$menupont['menuterulet']], $menupont);
                    break;
                }
            }
        }
    }
}

// Fallback megoldás arra az esetre, ha a lekérni próbált oldalhoz nincs adatbázis bejegyzés
if(!isset($currentpage))
{
    $currentpage['url'] = $_GET['page'];
    $currentpage['oldal'] = $_GET['page'];
    $currentpage['cimszoveg'] = "Oldal";
    $currentpage['gyujtocimszoveg'] = "Oldal";
    $currentpage['aktiv'] = 3;
}

// Szükség esetén 404-es hibaoldal generálása
try
{
    $page = @fopen("./{$currentpage['url']}.php", "r");
    if (!$page)
    {
        $page = @fopen("./{$currentpage['gyujtourl']}.php", "r");
        if(!$page)
        {
            $page = @fopen("./{$currentpage['dburl']}.php", "r");
            if(!$page)
            {
                throw new Exception();
            }
        }
    }
}
catch(Exception $e)
{
    http_response_code(404);
    $currentpage['url'] = "404";
    $currentpage['gyujtocimszoveg'] = "Oldal nem található!";
}

// Folyamatértesítés
if(@$_GET['sikeres'] == "uj")
{
    $succesmessage = "Új " . $currentpage['cimszoveg'] . " hozzáadása sikeres";
}
elseif(@$_GET['sikeres'] == "szerkesztes")
{
    $succesmessage = "A(z) " . $currentpage['cimszoveg'] . " szerkesztése sikerült";
}
elseif(@$_GET['sikeres'] == "bejelentkezes")
{
    $succesmessage = "Sikeres bejelentkezés";
}

// A jelenlegi oldal adatainak $_GET-hez történő előkészítése
if(isset($_GET['page']) && isset($_GET['id']))
{
    $kuldooldal = "&kuldooldal=" . $_GET['page'] . "&kuldooldalid=" . $_GET['id'];
}
elseif(isset($_GET['page']))
{
    $kuldooldal = "&kuldooldal=" . $_GET['page'];
}
else
{
    $kuldooldal = null;
}

// JavaScript fájlok listája
$javascriptfiles = [
    "includes/js/pageload.js",
    "includes/js/functions.js",
    "includes/js/tableActions.js",
    "includes/js/ertesites.js"
];

if(isset($_GET['page']) && $_GET['page'] != "aktiveszkoz" && $_GET['page'] != "sohoeszkoz" && $_GET['page'] != "mediakonverter" || ($_GET['page'] == "aktiveszkoz" && isset($_GET['action'])))
{
    $javascriptfiles[] = "includes/js/progressOverlay.js";
}

if(isset($cselect) && $cselect)
{
    $javascriptfiles[] = "includes/js/customSelect.js";
}


// PHP változók átadni a JavaScriptnek
$PHPvarsToJS = [
    array(
        'name' => 'RootPath',
        'val' => $RootPath
    )
];

if($felhasznaloid && $szemelyes['switchstateshow'])
{
    $PHPvarsToJS[] = array('name' => 'Felhasznaloid', 'val' => $felhasznaloid);
    $javascriptfiles[] = "modules/eszkozok/includes/eszkozonlinecheck.js";
}

// Oldal megjelenítése
include('./templates/index.tpl.php');

?>