
<?php

var_dump(OPENSSL_VERSION_TEXT);
var_dump(defined('LDAP_OPT_X_TLS_CACERTFILE'));
var_dump(defined('LDAP_OPT_X_TLS_REQUIRE_CERT'));
var_dump(defined('LDAP_OPT_X_TLS_DEMAND'));

foreach(get_defined_constants() As $name => $value) {
	if(str_starts_with($name, 'LDAP_OPT_X_TLS')) {
		echo $name . ' = ' . var_export($value, true) . "<br>";
	}
}

// LDAP modul ellenőrzése
if (!extension_loaded('ldap')) {
    die('HIBA: A PHP LDAP modul nincs betöltve.');
}

// Tanúsítvány elérési útja
$caFile = 'C:/http/Apache24/conf/ssl/company-root-ca.cer';

// Ellenőrizzük a szükséges TLS opciókat
if (!defined('LDAP_OPT_X_TLS_CACERTFILE')) {
    die('HIBA: A PHP LDAP build nem támogatja a CA fájl beállítását.');
}

// CA tanúsítvány és kötelező ellenőrzés beállítása
if (!ldap_set_option(null, LDAP_OPT_X_TLS_CACERTFILE, $caFile)) {
    die('HIBA: A CA tanúsítvány beállítása sikertelen.');
}

if (!ldap_set_option(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_DEMAND)) {
    die('HIBA: A tanúsítvány-ellenőrzés beállítása sikertelen.');
}

// Domain Controller
$server = 'company-dc.company.domain.local';
$cafile = 'C:\http\Apache24\conf\ssl\root-cert.crt';
ldap_set_option(null, LDAP_OPT_X_TLS_CACERTFILE, $cafile);
ldap_set_option(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_DEMAND);

// Kapcsolat inicializálása
$ldap = ldap_connect("ldaps://{$server}:636");

if (!$ldap) {
    die('HIBA: Az LDAP kapcsolat inicializálása sikertelen.');
}

// LDAP v3
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

// Tesztelés hitelesítéssel
$username = 'samaccountname@company.domain.local';
$password = 'LDAPPassword';

if (ldap_bind($ldap, $username, $password)) {
    echo 'SIKER: LDAPS kapcsolat és hitelesítés rendben.';
} else {
    echo 'LDAP hiba: ' . ldap_error($ldap);
    echo '<br>LDAP hibakód: ' . ldap_errno($ldap);
}

ldap_unbind($ldap);
