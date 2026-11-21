<?php

namespace Infoinventory\Modules\Kozlemenyek;

use Infoinventory\Classes\MySQLHandler;

trait KozlemenyDB
{
    public static function DB_GetKozlemenyLista(int $startindex = 0, int $limit = 4, int $moduljog = 0, ?array $csoporttagsagok = null, ?int $publikalt = 1) : ?array {
        $querystring = "SELECT kozlemeny_id, slug, core_felhasznalok.felhasznalo_id AS felhasznalo_id, nev, cim, bevezetes, szovegtorzs, ido, cimke, IF(csoport_id, csoport_id, 0) AS csoport_id
            FROM kozlemenyek_kozlemenyek
                INNER JOIN core_felhasznalok USING (felhasznalo_id)";
        $queryparams = [];

        if($moduljog == 3) {
            $querystring .= " WHERE publikalt = ?";
            $queryparams = [$publikalt];
        }

        elseif($moduljog == 2 || $moduljog == 1) {
            $csoportplaceholders = implode(', ', array_fill(0, count($csoporttagsagok), '?'));
            $querystring .= " WHERE publikalt = ? AND (csoport_id IS NULL OR csoport_id IN ($csoportplaceholders))";
            $queryparams = array_merge([$publikalt], $csoporttagsagok);
        }

        $querystring .= " ORDER BY kozlemeny_id DESC";
        $querystring .= " LIMIT ?, ?";
        $queryparams = array_merge($queryparams, [$startindex, $limit]);

        $kozlemenyek = new MySQLHandler($querystring, ...$queryparams);

        if(!$kozlemenyek->siker || $kozlemenyek->sorokszama == 0)
            return null;
        else
            return $kozlemenyek->AsArray();
    }

    public static function DB_GetKozlemeny(int|string $azonosito, ?int $publikalt = 1) : ?array {
        $where = "slug";
        if(is_int($azonosito))
            $where = "kozlemeny_id";

        $querystring = "SELECT kozlemeny_id, slug, core_felhasznalok.felhasznalo_id AS felhasznalo_id, nev, cim, bevezetes, szovegtorzs, ido, cimke, IF(csoport_id, csoport_id, 0) AS csoport_id, publikalt
            FROM kozlemenyek_kozlemenyek
                INNER JOIN core_felhasznalok USING (felhasznalo_id)
            WHERE $where = ? AND publikalt = ?;";

        $kozlemeny = new MySQLHandler($querystring, $azonosito, $publikalt);

        if(!$kozlemeny->siker || $kozlemeny->sorokszama == 0)
            return null;
        else
            return $kozlemeny->RecordAsArray();
    }
}