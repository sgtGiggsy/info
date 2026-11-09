<?php

namespace Infoinventory\Modules;

use Infoinventory\Classes\MySQLHandler;

class FelhasznaloDB
{
    public static function GetFelhasznalo(?int $felhasznalo_id) : ?array
    {
        if(!$felhasznalo_id)
            return null;

        $felhasznalo = new MySQLHandler("SELECT usernev, email, teljesnev, fajl AS profilkep, felhasznalok.felhasznalo_id AS felhasznalo_id, szint, regisztracio, allapot
            FROM felhasznalok
                LEFT JOIN feltoltesek ON felhasznalok.profilkep = feltoltesek.feltoltes_id
            WHERE felhasznalok.felhasznalo_id = ?;", $felhasznalo_id);
        return $felhasznalo->EscapedSingleElem();
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