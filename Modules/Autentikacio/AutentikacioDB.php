<?php

namespace Infoinventory\Modules\Autentikacio;

use ArrayObject;
use Infoinventory\Classes\MySQLHandler;
use Infoinventory\Classes\Felhasznalo;

class AutentikacioDB
{
    public static function FailedLoginNum(string $usernev) : int {
        $failed = new MySQLHandler("SELECT COUNT(*) AS kiserletszam
            FROM bejelentkezesi_hibak
            WHERE probalkozas_ideje > (NOW() - INTERVAL 10 MINUTE)
                AND (ip_cim = ? OR felhasznalo_nev = ?);", $_SERVER['REMOTE_ADDR'], $usernev);
        return $failed->Fetch()['kiserletszam'];
    }

    public static function LoginHandler(string $usernev) : ?array {
        $lhandler = new MySQLHandler('SELECT felhasznalo_id, jelszo FROM felhasznalok WHERE usernev = ?', $usernev);
        if($lhandler->sorokszama == 0)
            return null;
        else {
            $lhandler = $lhandler->Fetch();
            return ['felhasznalo_id' => $lhandler['felhasznalo_id'], 'jelszo' => $lhandler['jelszo']];
        }
    }

    public static function UsernevFromEmail(string $email) : ?string {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $user = new MySQLHandler("SELECT usernev FROM felhasznalok WHERE email = ?;", $email);
        if($user->sorokszama == 1)
            return htmlspecialchars($user->Fetch()['usernev'], ENT_QUOTES, 'UTF-8');
        else
            return null;
    }

    public static function UsernameCheck(string $usernev) : bool {
        $usernev = htmlspecialchars($usernev, ENT_QUOTES, 'UTF-8');
        $check = new MySQLHandler("SELECT usernev FROM felhasznalok WHERE usernev = ?", $usernev);
        if($check->sorokszama == 0)
            return false;
        else
            return true;
    }

    public static function EmailCheck(string $email) : bool {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        $emailcheck = new MySQLHandler("SELECT email FROM felhasznalok WHERE email = ?", $email);
        if($emailcheck->sorokszama == 0)
            return false;
        else
            return true;
    }

    public static function GetJelszo(int $uid) : ?string {
        $regijelszo = new MySQLHandler('SELECT jelszo FROM felhasznalok WHERE felhasznalo_id = ?', $uid);
        return $regijelszo->Fetch()['jelszo'] ?? null;
    }

    public static function UpdateUser(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        $updateuser = new MySQLHandler("UPDATE felhasznalok SET jelszo = ?, nev = ?, email = ?, osztaly = ?, telefon = ?, beosztas = ?, profilkep = ?, descript = ? WHERE usernev = ?;",
            $hashedpassword, $felhasznalo->nev, $felhasznalo->email, $felhasznalo->osztaly, $felhasznalo->telefon, $felhasznalo->beosztas, $felhasznalo->profilkep, $felhasznalo->leiras, $felhasznalo->usernev);
        return $updateuser->siker;
    }

    public static function GetUser(int|string $azonosito) : ?array {
        if(is_int($azonosito)) {
            $getuser = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras, aktiv
                FROM felhasznalok
                WHERE felhasznalo_id = ?;", $azonosito);
        }
        else {
            $getuser = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras, aktiv
                FROM felhasznalok
                WHERE usernev = ?;", $azonosito);
        }

        if($getuser->sorokszama == 0)
            return null;

        return $getuser->RecordAsArray();
    }

    public static function UserRegisztracio(string $usernev, string $email, string $hashedpassword, string $verifstring) : int {
        $usernev = htmlspecialchars($usernev, ENT_QUOTES, 'UTF-8');
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $newuser = new MySQLHandler("INSERT INTO felhasznalok (usernev, email, jelszo, allapot, verifstring, verifstring_hatarido)
            VALUES (?, ?, ?, ?, ?, NOW() + INTERVAL 1 HOUR)",
            $usernev, $email, $hashedpassword, 1, $verifstring);
        return $newuser->last_insert_id;
    }

    public static function ElfelejtettJelszo(string $verifstring, string $email) : bool {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $ujjelszo = new MySQLHandler("UPDATE felhasznalok SET verifstring = ?, verifstring_hatarido = NOW() + INTERVAL 1 HOUR WHERE email = ?;", $verifstring, $email);
        return $ujjelszo->siker;
    }

    public static function ElfelejtettUj(string $hashedpassword, string $verifstring) : bool {
        $verifstring = htmlspecialchars($verifstring, ENT_QUOTES, 'UTF-8');
        $ujjelszo = new MySQLHandler("UPDATE felhasznalok SET jelszo = ?, verifstring = ?, verifstring_hatarido = ? WHERE verifstring = ?;", $hashedpassword, null, null, $verifstring);
        return $ujjelszo->siker;
    }

    public static function JelszoCsere(int $uid, string $hashedpassword) : bool {
        $jelszovaltas = new MySQLHandler('UPDATE felhasznalok SET jelszo = ?, jelszo_ido = current_timestamp() WHERE felhasznalo_id = ?;', $hashedpassword, $uid);
        return $jelszovaltas->siker;
    }

    public static function MegerositoKodCheck(string $megerositokod) : ?int {
        $megerositokod = htmlspecialchars($megerositokod, ENT_QUOTES, 'UTF-8');
        $osszevet = new MySQLHandler("SELECT felhasznalo_id FROM felhasznalok WHERE verifstring = ?;", $megerositokod);
        if($osszevet->sorokszama == 1)
            return $osszevet->Fetch()['felhasznalo_id'];
        else
            return null;
    }

    public static function UserMegerosit(int $felhasznalo_id) : bool {
        $meger = new MySQLHandler("UPDATE felhasznalok SET allapot = 2, verifstring = ? WHERE felhasznalo_id = ?;", null, $felhasznalo_id);
        return $meger->siker;
    }

    public static function ElfelejtettJelszoMegerosit(string $megerositokod) : bool {
        $megerositokod = htmlspecialchars($megerositokod, ENT_QUOTES, 'UTF-8');
        $osszevet = new MySQLHandler("SELECT felhasznalo_id FROM felhasznalok WHERE verifstring = ? AND verifstring_hatarido > NOW();", $megerositokod);
        if($osszevet->sorokszama == 1)
            return true;
        else
            return false;
    }
}