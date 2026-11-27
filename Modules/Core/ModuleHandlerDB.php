<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\MySQLHandler;

trait ModuleHandlerDB
{
    protected static function DB_GetInstalledModules() : ?array {
        $sql = new MySQLHandler("SELECT * FROM core_modulok WHERE telepitve = 1 ORDER BY slug;");
        return $sql->AsArray("slug");
    }

    protected static function DB_GetModul(string $slug, bool $onlyinstalled = false) : ?array {
        $installed = "";
        if($onlyinstalled) {
            $installed = " AND telepitve = 1";
        }
        $sql = new MySQLHandler("SELECT * FROM core_modulok WHERE slug = ? $installed;", $slug);
        $modul = $sql->RecordAsArray();
        $modul_id = $modul['modul_id'] ?? null;
        if($modul_id === null)
            return null;
        $felhasznalok = new MySQLHandler("SELECT felhasznalo_id, usernev, nev, olvasas, iras
                FROM core_modulok_jogosultsagok
                    INNER JOIN core_felhasznalok USING (felhasznalo_id)
                WHERE modul_id = ?;", $modul_id);
        return ['moduladatok' => $modul, 'felhasznalok' => $felhasznalok->AsArray("usernev")];
    }
}