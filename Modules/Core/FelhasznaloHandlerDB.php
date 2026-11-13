<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\MySQLHandler;
use Infoinventory\Classes\Felhasznalo;
use Infoinventory\Classes\Modulkezelo;

class FelhasznaloHandlerDB
{
    public static function CreateUserFromLDAP(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        $createuser = new MySQLHandler("INSERT INTO core_felhasznalok (usernev, nev, email, jelszo, osztaly, telefon, beosztas, profilkep, descript) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);",
            $felhasznalo->usernev, $felhasznalo->nev, $felhasznalo->email, $hashedpassword, $felhasznalo->osztaly, $felhasznalo->telefon, $felhasznalo->beosztas, $felhasznalo->profilkep, $felhasznalo->leiras);
        return $createuser->siker;
    }

    public static function UpdateUserFromLDAP(Felhasznalo $felhasznalo, string $hashedpassword) : bool {
        $updateuser = new MySQLHandler("UPDATE core_felhasznalok SET jelszo = ?, nev = ?, email = ?, osztaly = ?, telefon = ?, beosztas = ?, profilkep = ?, descript = ? WHERE usernev = ?;",
            $hashedpassword, $felhasznalo->nev, $felhasznalo->email, $felhasznalo->osztaly, $felhasznalo->telefon, $felhasznalo->beosztas, $felhasznalo->profilkep, $felhasznalo->leiras, $felhasznalo->usernev);
        return $updateuser->siker;
    }

    public static function SzervezetValaszto(string $needle) : ?int {
        if($needle) {
            $szervezet = new MySQLHandler("SELECT szervezet FROM `szervezetldap` WHERE ? LIKE CONCAT('%', szervezetldap.needle, '%');", $needle);
            @$szervezet = $szervezet->Fetch()['szervezet'];
        } else {
            $szervezet = null;
        }

        return $szervezet;
    }

    public static function GetUser(int|string $azonosito) : ?array {
        $where = "usernev";
        if(is_int($azonosito)) {
            $where = "felhasznalo_id";
        }

        $getuser = new MySQLHandler("SELECT core_felhasznalok.felhasznalo_id AS uid, usernev, nev, email,
                elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras,
                aktiv, godadmin, sajat.csoport_id AS sajatszervezet,
                sajatcsoport.csoport_nev AS sajatszervezetnev, aktiv,
                GROUP_CONCAT(core_csoportok.csoport_id ORDER BY core_csoportok.csoport_id SEPARATOR '; ')
                    AS csoporttagsagok,
                GROUP_CONCAT(core_csoportok.csoport_nev ORDER BY core_csoportok.csoport_id SEPARATOR '; ')
                    AS csoportnevek,
                GROUP_CONCAT(core_csoportok_tagsagok.iras ORDER BY core_csoportok_tagsagok.csoport_id SEPARATOR '; ')
                    AS irasjogok
	        FROM core_felhasznalok
    	        LEFT JOIN core_csoportok_tagsagok USING (felhasznalo_id)
                LEFT JOIN core_csoportok USING (csoport_id)
                LEFT JOIN core_csoportok_tagsagok AS sajat ON sajat.felhasznalo_id = core_felhasznalok.felhasznalo_id AND sajat.sajatszervezet = 1
                LEFT JOIN core_csoportok AS sajatcsoport ON sajatcsoport.csoport_id = sajat.csoport_id
            WHERE core_felhasznalok.$where = ?;", $azonosito);

        if($getuser->sorokszama == 0)
            return null;

        return $getuser->RecordAsArray();
    }

    public static function GetUserList(int $startindex = 0, int $dbszam = 20, ?int ...$olvasjogok) : ?array {
        if(Modulkezelo::ModulJogosultsag('felhasznalok')['olvasas'] == 3) {
            $getuser = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras, aktiv, sajatszervezet, csoport_nev AS sajatszervezetnev, aktiv
                FROM core_felhasznalok
                    LEFT JOIN core_csoportok_tagsagok USING (felhasznalo_id)
                    LEFT JOIN core_csoportok USING (csoport_id)
                WHERE sajatszervezet = 1
                LIMIT ?, ?;", $startindex, $dbszam);
        }
        if(Modulkezelo::ModulJogosultsag('felhasznalok')['olvasas'] == 2) {
            $paramplacehold = implode(', ', array_fill(0, count(FELHASZNALO->GetCsoportIDs()), '?'));

            $getuser = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras, aktiv, sajatszervezet, csoport_nev AS sajatszervezetnev, aktiv
                FROM core_felhasznalok
                    LEFT JOIN core_csoportok_tagsagok USING (felhasznalo_id)
                    LEFT JOIN core_csoportok USING (csoport_id)
                WHERE sajatszervezet = 1 AND csoport_id IN ($paramplacehold)
                LIMIT $startindex, $dbszam;", ...FELHASZNALO->GetCsoportIDs());
        }
        if(Modulkezelo::ModulJogosultsag('felhasznalok')['olvasas'] == 1) {
            $getuser = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, descript AS leiras, aktiv, sajatszervezet, csoport_nev AS sajatszervezetnev, aktiv
                FROM core_felhasznalok
                    LEFT JOIN core_csoportok_tagsagok USING (felhasznalo_id)
                    LEFT JOIN core_csoportok USING (csoport_id)
                WHERE sajatszervezet = 1 AND csoport_id = ?
                LIMIT ?, ?;", FELHASZNALO->szervezet, $startindex, $dbszam);
        }

        if(!isset($getuser) || $getuser->sorokszama == 0)
            return null;

        return $getuser->AsArray();
    }

    public static function GetCsoportList() : array {
        $csoportok = new MySQLHandler("SELECT csoport_id, csoport_nev FROM core_csoportok WHERE statusz = 1 ORDER BY csoport_nev ASC;");
        if($csoportok->sorokszama == 0)
            return [];
        return $csoportok->ArrayForSelect();
    }

    public static function FelhasznaloSzerkeszt(?array $post, ?int $profilkep = null) : bool {
        if(!$post)
            return false;
        $valarr = [];
        $sql = new MySQLHandler();
        $qstring = "UPDATE core_felhasznalok SET ";
        if(isset($post['descript'])) {
            $qstring .= "descript = ?, ";
            $valarr[] = $post['descript'];
        }
        if(isset($post['telefon'])) {
            $qstring .= "telefon = ?, ";
            $valarr[] = $post['telefon'];
        }
        if(isset($post['email'])) {
            $qstring .= "email = ?, ";
            $valarr[] = $post['email'];
        }
        if(isset($post['nev'])) {
            $qstring .= "nev = ?, ";
            $valarr[] = $post['nev'];
        }
        if(isset($profilkep)) {
            $qstring .= "profilkep = ?, ";
            $valarr[] = $profilkep;
        }
        if(isset($post['usernev'])) {
            $qstring .= "usernev = ?, ";
            $valarr[] = $post['usernev'];
        }
        if(isset($post['osztaly'])) {
            $qstring .= "osztaly = ?, ";
            $valarr[] = $post['osztaly'];
        }
        if(isset($post['beosztas'])) {
            $qstring .= "beosztas = ?, ";
            $valarr[] = $post['beosztas'];
        }
        if(isset($post['aktiv'])) {
            $qstring .= "aktiv = ?, ";
            $valarr[] = $post['aktiv'];
        }
        if(isset($profilkep)) {
            $qstring .= "profilkep = ?, ";
            $valarr[] = $profilkep;
        }
        $qstring = rtrim($qstring, ', ');
        $qstring .= " WHERE felhasznalo_id = ?;";
        $valarr[] = $post['felhasznalo_id'];

        $sql->Query($qstring, ...$valarr);

        return $sql->siker;
    }

    public static function UjFelhasznalo(?array $post, ?string $hashedpassword, ?int $profilkep = null) : bool {
        if(!$post)
            return false;
        $valarr = [];
        $sql = new MySQLHandler();
        $qstring = "INSERT INTO core_felhasznalok (usernev, email, nev, descript, telefon, osztaly, beosztas";
        if(isset($profilkep)) {
            $qstring .= ", profilkep";
        }
        if(isset($hashedpassword)) {
            $qstring .= ", jelszo";
        }
        $qstring .= ") VALUES (?, ?, ?, ?, ?, ?, ?";
        if(isset($post['aktiv'])) {
            $qstring .= ", ?";
        }
        if(isset($profilkep)) {
            $qstring .= ", ?";
        }
        if(isset($hashedpassword)) {
            $qstring .= ", ?";
        }
        $qstring .= ");";

        $valarr[] = $post['usernev'];
        $valarr[] = $post['email'];
        $valarr[] = $post['nev'];
        $valarr[] = $post['descript'];
        $valarr[] = $post['telefon'];
        $valarr[] = $post['osztaly'];
        $valarr[] = $post['beosztas'];
        if(isset($post['aktiv'])) {
            $valarr[] = $post['aktiv'];
        }
        if(isset($profilkep)) {
            $valarr[] = $profilkep;
        }
        if(isset($hashedpassword)) {
            $valarr[] = $hashedpassword;
        }

        $sql->Query($qstring, ...$valarr);

        return $sql->siker;
    }

    public static function CsoporthozAd(int $felhasznaloid, int $csoportid) : bool {
        $sql = new MySQLHandler("INSERT INTO core_csoportok_tagsagok (felhasznalo_id, csoport_id, sajatszervezet) VALUES (?, ?, ?);", $felhasznaloid, $csoportid, 1);
        return $sql->siker;
    }

    public static function CsoportbolTorol(int $felhasznaloid) : bool {
        $sql = new MySQLHandler("DELETE FROM core_csoportok_tagsagok WHERE felhasznalo_id = ? AND sajatszervezet = ?;", $felhasznaloid, 1);
        return $sql->siker;
    }
}