<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
//if(!$mindir)
if(1 > 2)
{
    getPermissionError();
}
else
{
    $alapform = "modules/beallitasok/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
        {
            $irhat = true;
        }
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        getPermissionError();
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/beallitasok/db/menudb.php");

        header("Location: ./menu?action=edit");
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $magyarazat = null;
        $form = $alapform . "menuszerkesztesform";
        $button = "Menük szerkesztése";
        $oldalcim = "Menük szerkesztése";

        $szulo = array();
        foreach($menu as $x)
        {
            $szulo[] = array("id" => $x['id'], "menupont" => $x['menupont']);
        }
        $sortcriteria = 'menupont';
        usort($szulo, function($a, $b)
        {
            return $a['menupont'] > $b['menupont'];
        });

        if($_GET['action'] == "addnew")
        {
            $form = $alapform . "menuujform";
            $button = "Menü hozzáadása";
            $oldalcim = "Új menü hozzáadása";
        }

        include('./templates/edit.tpl.php');
    }

    
}