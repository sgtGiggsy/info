<?php
/*
* Alapvető működés megvalósítva
TODO Megoldani, hogy kész lekérdezés után le lehessen kérni további mezőket
*/
include("../../../includes/config.inc.php");
include("../../../includes/functions.php");

$szuresek = getWhere("(modellek.tipus = 1 OR modellek.tipus = 2) AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid) OR aktiveszkoz_allapot.id IS NULL)");
$where = $szuresek['where'];

$query = "SELECT
            eszkozok.id AS id,
            sorozatszam,
            beepitesek.id AS beepid,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            online
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN aktiveszkozok ON aktiveszkozok.eszkoz = eszkozok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid
        WHERE $where AND aktiveszkoz_allapot.online = 0
        ORDER BY ipcimek.ipcim;";

$offline = mySQLConnect($query);
$offlinelist = json_encode(mysqliToArray($offline));

echo $offlinelist;