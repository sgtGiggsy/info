<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\MySQLHandler;
use Infoinventory\Classes\Felhasznalo;

class FelhasznaloHandlerDB
{
    public static function UpdateUser(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        $updateuser = new MySQLHandler("UPDATE core_felhasznalok SET jelszo = ?, nev = ?, email = ?, osztaly = ?, telefon = ?, beosztas = ?, profilkep = ?, descript = ? WHERE usernev = ?;",
            $hashedpassword, $felhasznalo->nev, $felhasznalo->email, $felhasznalo->osztaly, $felhasznalo->telefon, $felhasznalo->beosztas, $felhasznalo->profilkep, $felhasznalo->leiras, $felhasznalo->usernev);
        return $updateuser->siker;
    }

    public static function GetUser(int|string $azonosito) : ?array {
        $where = "usernev";
        if(is_int($azonosito)) {
            $where = "felhasznalo_id";
        }

        $getuser = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras, aktiv, godadmin,
                    (SELECT csoport_id FROM core_csoportok_tagsagok WHERE felhasznalo_id = uid AND sajatszervezet = 1) AS sajatszervezet,
                    GROUP_CONCAT(csoport_id ORDER BY csoport_id SEPARATOR '; ') AS csoporttagsagok,
                    GROUP_CONCAT(csoport_nev ORDER BY csoport_id SEPARATOR '; ') AS csoportnevek,
                    GROUP_CONCAT(iras ORDER BY csoport_id SEPARATOR '; ') AS irasjogok
                FROM core_felhasznalok
                    LEFT JOIN core_csoportok_tagsagok USING (felhasznalo_id)
                    LEFT JOIN core_csoportok USING (csoport_id)
                WHERE $where = ?;", $azonosito);

        if($getuser->sorokszama == 0)
            return null;

        return $getuser->RecordAsArray();
    }

    public static function GetFelhasznalok(int $startindex = 0, int $dbszam = 20) : ?array {
        $felhasznalo = new MySQLHandler("SELECT usernev, email, teljesnev, fajl AS profilkep, felhasznalok.felhasznalo_id AS felhasznalo_id, szint, regisztracio, allapot
            FROM felhasznalok
                LEFT JOIN feltoltesek ON felhasznalok.profilkep = feltoltesek.feltoltes_id
            LIMIT ?, ?;", $startindex, $dbszam);
        return $felhasznalo->EscapedArray();
    }

    public static function FelhasznaloSzerkeszt(?array $post, ?int $profilkep = null) : bool {
        if(!$post)
            return false;
        $valarr = [];
        $sql = new MySQLHandler();
        $qstring = "UPDATE felhasznalok SET ";
        if(isset($post['szint'])) {
            $qstring .= "szint = ?, ";
            $valarr[] = $post['szint'];
        }
        if(isset($post['allapot'])) {
            $qstring .= "allapot = ?, ";
            $valarr[] = $post['allapot'];
        }
        if(isset($post['email'])) {
            $qstring .= "email = ?, ";
            $valarr[] = $post['email'];
        }
        if(isset($post['teljesnev'])) {
            $qstring .= "teljesnev = ?, ";
            $valarr[] = $post['teljesnev'];
        }
        if(isset($profilkep)) {
            $qstring .= "profilkep = ?, ";
            $valarr[] = $profilkep;
        }
        if(isset($post['usernev'])) {
            $qstring .= "usernev = ?, ";
            $valarr[] = $post['usernev'];
        }
        $qstring = rtrim($qstring, ', ');
        $qstring .= " WHERE felhasznalo_id = ?;";
        $valarr[] = $post['felhasznalo_id'];

        $sql->Query($qstring, ...$valarr);

        return $sql->siker;
    }
}