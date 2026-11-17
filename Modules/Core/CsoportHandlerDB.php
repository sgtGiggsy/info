<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\MySQLHandler;

trait CsoportHandlerDB
{
    private static function DB_GetCsoportList(int $jogszint, ?string $kereses, mixed ...$queryparams) : ?array {
        $osszrekord = 0;
        $phdb = count($queryparams) - 2; // A limit paraméterek nem számítanak a csoport ID-k számába
        if(is_null($kereses)) {
            $keresesstring = '';
        }
        else {
            $keresesstring = "(csoport_nev LIKE ? OR rovid_nev LIKE ? OR leiras LIKE ?)";
            $kereses = '%' . $kereses. '%';
            $limit = array_pop($queryparams);
            $startindex = array_pop($queryparams);
            $queryparams = array_merge($queryparams, [$kereses, $kereses, $kereses], [$startindex, $limit]);
        }

        if($jogszint == 3) {
            if($keresesstring) {
                $keresesstring = "WHERE $keresesstring";
            }
            $getcsoport = new MySQLHandler("SELECT csoport_id, csoport_nev, slug, rovid_nev, statusz, leiras, count(felhasznalo_id) AS tagok_szama
                FROM core_csoportok
                    LEFT JOIN core_csoportok_tagsagok USING (csoport_id)
                $keresesstring
                GROUP BY csoport_id
                ORDER BY csoport_nev
                LIMIT ?, ?;", ...$queryparams);
            $osszrekord = $getcsoport->GetLimitNelkuliDB();
        }
        elseif($jogszint == 2) {
            if($keresesstring) {
                $keresesstring = "AND $keresesstring";
            }
            $paramplacehold = implode(', ', array_fill(0, $phdb, '?'));

            $getcsoport = new MySQLHandler("SELECT slug, csoport_id, csoport_nev, rovid_nev, statusz, leiras, count(felhasznalo_id) AS tagok_szama
                FROM core_csoportok
                    LEFT JOIN core_csoportok_tagsagok USING (csoport_id)
                WHERE csoport_id IN ($paramplacehold) $keresesstring
                GROUP BY csoport_id
                ORDER BY csoport_nev
                LIMIT ?, ?;", ...$queryparams);
            $osszrekord = $getcsoport->GetLimitNelkuliDB();
        }

        if(!isset($getcsoport) || $getcsoport->sorokszama == 0)
            return null;

        return ['dataset' => $getcsoport->EscapedArray(), 'osszrekord' => $osszrekord];
    }

    private static function DB_GetCsoport(int|string $azonosito) : ?array {
        $where = "slug";
        if(is_int($azonosito))
            $where = "csoport_id";

        $getcsoport = new MySQLHandler("SELECT csoport_id, csoport_nev, slug, rovid_nev, statusz, leiras
                FROM core_csoportok
                WHERE $where = ?;", $azonosito);
        if($getcsoport->sorokszama == 0)
            return null;

        return $getcsoport->RecordAsArray();
    }

    private static function DB_GetCsoportTagok(int $csoport_id) : array {
        $getusers = new MySQLHandler("SELECT felhasznalo_id AS uid, usernev, nev, aktiv, sajatszervezet, aktiv
                FROM core_felhasznalok
                    LEFT JOIN core_csoportok_tagsagok USING (felhasznalo_id)
                WHERE csoport_id = ?
                ORDER BY nev;", $csoport_id);
        return $getusers->EscapedArray();
    }
}