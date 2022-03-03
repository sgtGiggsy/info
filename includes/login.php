<?php
###############################################################
#
# Név:			LDAP - MySQL hibrid bejelentkeztető modul
#
# Működés:      Amennyiben van elérhető DC kiszolgáló, a formból vett adatokkal azon próbálja meg a hitelesítést.
#				Elérhetetlen kiszolgáló esetén az adatbázisban tárolt bejelentkezési adatokat próbálja használni.
#				A működéshez ELENGEDHETETLEN a MySQL elérés, még abban az esetben is, ha van elérhető DC kiszolgáló.
#
# Megjegyzések:	A felhasználói bemenet "fertőtlenítése" miatt a modul védett az egyszerűbb MySQLinjection támadások ellen
#
# Bemenet:      * $_POST['felhasznalonev']
#				* $_POST['jelszo']
#
# Függőségek:   * MySQL adatbázis ('felhasznalok' tábla; 'id', 'felhasznalonev', 'jelszo', 'jogosultsag', 'nev', 'email' attribútumokkal)
#				* mySQLConnect függvény (és annak függőségei: $DATABASE_ bejegyzések a settings.php-ban)
#				* $LDAP_ bejegyzések a settings.php-ban
#               * engedélyezett ldap modul a PHP.ini-ben
#
###############################################################

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

$con = mySQLConnect(false);

$samaccountname = $_POST['felhasznalonev'];
$plainpassword = $_POST['jelszo'];
$hashedpassword = password_hash($_POST['jelszo'], PASSWORD_DEFAULT); // A jelszó hash-e az adatbázisban tároláshoz

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
			if ($stmt = $con->prepare('UPDATE felhasznalok SET jelszo=?, nev=?, email=? WHERE felhasznalonev=?'))
			{
				$stmt->bind_param('ssss', $hashedpassword, $nev, $email, $samaccountname);
				$stmt->execute();
			}
		}
		else // Ha nem létezett a felhasználó a MySQL adatbázisban, létrehozzuk (a jelen táblabeállítás szerint a MySQL-ben automatikusan 1-es, azaz legalacsonyabb belépett joggal jön létre minden felhasználó)
		{
			if ($stmt = $con->prepare('INSERT INTO felhasznalok (felhasznalonev, jelszo, nev, email) VALUES (?, ?, ?, ?)'))
			{
				$stmt->bind_param('ssss', $samaccountname, $hashedpassword, $nev, $email);
				$stmt->execute();
			}
		}
	}

	if(isset($ldapbind) && !$ldapbind) // Ez az elágazás kényszeríti ki, hogy ha van elérhető DC kiszolgáló, akkor annak a hitelesítését használja a modul
	{
		$hiba = true;
	}
	else
	{
		$result = mySQLConnect("SELECT * FROM felhasznalok WHERE felhasznalonev = '$samaccountname'");
		$row = $result->fetch_assoc();

		session_regenerate_id();
		if(mysqli_num_rows($result) == 1) // Ez az egyedüli "Sikeres bejelentkezés" ág. Bármely más ágra fut ki a modul, a bejelentkezés sikertelen
		{
			$_SESSION[getenv('SESSION_NAME').'id'] = $row['id'];
			$_SESSION[getenv('SESSION_NAME').'felhasznalonev'] = $row['felhasznalonev'];
			$_SESSION[getenv('SESSION_NAME').'nev'] =  $row['nev'];
			$_SESSION[getenv('SESSION_NAME').'jogosultsag'] = $row['jogosultsag'];
			$_SESSION[getenv('SESSION_NAME').'email'] = $row['email'];
			echo "<h2>Sikeres bejelentkezés!</h2>";
			?><head><meta http-equiv="refresh" content="0; URL='./'" /></head><?php
		}
		else
		{
			?><script type='text/javascript'>alert('Adatbázis elérés hiba!')</script>
			<head><meta http-equiv="refresh" content="0; URL='./?page=belepes'" /></head><?php
		}
	}
}
else // Nem létező felhasználó, vagy hibás jelszó esetén lefutó ág. Csak akkor kerülünk ide, ha egyik metódussal sem érkezett érvényes válasz a felhasználónév-jelszó párosra
{
	$hiba = true;
}

if(isset($hiba) && $hiba)
{
	echo "<h2>Hibás felhasználónév, vagy jelszó!</h2>";
	?><script type='text/javascript'>alert('Hibás felhasználónév, vagy jelszó!')</script>
	<head><meta http-equiv="refresh" content="0; URL='./?page=belepes'" /></head><?php
}
?>