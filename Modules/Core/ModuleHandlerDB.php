<?php

namespace Infoinventory\Modules\Core;

use Infoinventory\Classes\MySQLHandler;

trait ModuleHandlerDB
{
    protected static function DB_GetInstalledModules() : ?array {
        $sql = new MySQLHandler("SELECT * FROM core_modulok WHERE telepitve = 1 ORDER BY slug;");
        return $sql->AsArray("slug");
    }
}