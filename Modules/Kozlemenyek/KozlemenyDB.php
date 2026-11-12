<?php

namespace Infoinventory\Modules\Kozlemenyek;

use Infoinventory\Classes\MySQLHandler;

class KozlemenyDB
{
    public static function GetKozlemenyLista(int $startindex = 0) : ?array {
        $kozlemenyek = new MySQLHandler("SELECT kozlemeny_id, slug, core_felhasznalok.felhasznalo_id AS felhasznalo_id, nev, cim, bevezetes, szovegtorzs, ido, cimke
            FROM kozlemenyek_kozlemenyek
                LEFT JOIN core_felhasznalok ON kozlemenyek_kozlemenyek.felhasznalo_id = core_felhasznalok.felhasznalo_id
            WHERE publikalt = 1
            ORDER BY kozlemeny_id DESC
            LIMIT ?, 4;", $startindex);
        if(!$kozlemenyek->siker || $kozlemenyek->sorokszama == 0)
            return null;
        else
            return $kozlemenyek->AsArray();
    }

    public static function GetKozlemeny(int|string $azonosito) : ?array {
        $where = "slug";
        if(is_int($azonosito))
            $where = "kozlemeny_id";
        $kozlemeny = new MySQLHandler("SELECT kozlemeny_id, slug, core_felhasznalok.felhasznalo_id AS felhasznalo_id, nev, cim, bevezetes, szovegtorzs, ido, cimke
            FROM kozlemenyek_kozlemenyek
                LEFT JOIN core_felhasznalok ON kozlemenyek_kozlemenyek.felhasznalo_id = core_felhasznalok.felhasznalo_id
            WHERE $where = ?;", $azonosito);
        if(!$kozlemeny->siker || $kozlemeny->sorokszama == 0)
            return null;
        else
            return $kozlemeny->RecordAsArray();
    }
}