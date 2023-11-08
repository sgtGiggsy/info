<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
//if(!$mindir)
if(@!$mindir)
{
    getPermissionError();
}
else
{
    $alapform = "modules/beallitasok/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = true;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "addnew" || $_GET['action'] == "updateapi")
        {
            $dbir = true;
        }
    }
    
    $menupontok = mySQLConnect("SELECT * FROM menupontok ORDER BY menupont ASC");

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        getPermissionError();
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/beallitasok/db/apidb.php");
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    if($irhat)
    {
        $apilista = mySQLConnect("SELECT * FROM api ORDER BY menupont ASC");
        $button = "API szerkesztése";
        $oldalcim = "API kulcsok";
        $magyarazat = "<h2>API kulcsok</h2><p></p>";
        include('./modules/beallitasok/forms/apiform.php');
    }
}