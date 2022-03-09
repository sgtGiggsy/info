<?php
// Alap includolások
include('./includes/config.inc.php');
include('./includes/functions.php');
include('./includes/pages.inc.php');
include('./includes/menu.inc.php');
$RootPath = getenv('APP_ROOT_PATH');

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
$page = null; $id = null;
if(isset($_GET['page']))
{
	$page = $_GET['page'];

	if($page == "kilep")
	{
        $kerdesid = $_SESSION[getenv('SESSION_NAME').'kerdesid'];
        session_destroy();
        session_start();
        $_SESSION[getenv('SESSION_NAME')."id"] = false;
        $_SESSION[getenv('SESSION_NAME').'kerdesid'] = $kerdesid;
		header("Location: $RootPath/index.php");
		die();
	}
}

if(isset($_GET['id']))
{
	$id = $_GET['id'];
}

// Felhasználó beléptetése/jogosultsági szintjének ellenőrzése
$admin = false;
if((!isset($_SESSION[getenv('SESSION_NAME').'id']) || !$_SESSION[getenv('SESSION_NAME').'id']) && isset($_POST['felhasznalonev']) && !(isset($_GET['page']) && $_GET['page'] == "kilep"))
{
	$con = mySQLConnect(false);

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
                if ($stmt = $con->prepare('UPDATE felhasznalok SET jelszo=?, nev=?, email=?, osztaly=? WHERE felhasznalonev=?'))
                {
                    $stmt->bind_param('sssss', $hashedpassword, $nev, $email, $osztaly, $samaccountname);
                    $stmt->execute();
                }
            }
            else // Ha nem létezett a felhasználó a MySQL adatbázisban, létrehozzuk (a jelen táblabeállítás szerint a MySQL-ben automatikusan 1-es, azaz legalacsonyabb belépett joggal jön létre minden felhasználó)
            {
                if ($stmt = $con->prepare('INSERT INTO felhasznalok (felhasznalonev, jelszo, nev, email, osztaly) VALUES (?, ?, ?, ?, ?)'))
                {
                    $stmt->bind_param('sssss', $samaccountname, $hashedpassword, $nev, $email, $osztaly);
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
                echo "<h2>Sikeres bejelentkezés!</h2>";
                $userdbid = mysqli_fetch_assoc($result)['id'];
                $_SESSION[getenv('SESSION_NAME').'id'] = true;
                logLogin($userdbid)
                ?><head><meta http-equiv="refresh" content="0; URL='./'" /></head><?php
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
        $con = mySQLConnect(false);
		if ($stmt = $con->prepare('INSERT INTO failedlogins (felhasznalonev, ipcim) VALUES (?, ?)'))
		{
			$stmt->bind_param('ss', $_POST['felhasznalonev'], $_SERVER['REMOTE_ADDR']);
			$stmt->execute();
		}
        echo "<h2>$hiba</h2>";
        ?><script type='text/javascript'>alert('<?=$hiba?>')</script>
        <head><meta http-equiv="refresh" content="0; URL='./belepes'" /></head><?php
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
        $_SESSION[getenv('SESSION_NAME').'jogosultsag'] = $row['jogosultsag'];
        $_SESSION[getenv('SESSION_NAME').'email'] = $row['email'];
    }
    else
    {
        session_destroy();
		$_SESSION[getenv('SESSION_NAME').'id'] = false; 
    }
}
else
{
	$_SESSION[getenv('SESSION_NAME').'id'] = false;
}

if(!isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']))
{
    $_SESSION[getenv('SESSION_NAME').'jogosultsag'] = 0;
}

// Megjelenítendő oldal kiválasztása
getSettings();
include('./includes/prepageload.inc.php');
$pagename = currentPage($pages);

// Oldal megjelenítése
include('./templates/index.tpl.php');
?>