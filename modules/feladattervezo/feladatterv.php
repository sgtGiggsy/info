<?php

if(!$_SESSION['id'] || !$csoportolvas)
{
    getPermissionError();
}
else
{
    $feladatterv = false;
    $where = null;
    $paramarr = array();
    $javascriptfiles[] = "modules/feladattervezo/includes/feladatterv.js";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        
        if($csoportir && in_array($_GET['action'], array("new", "update", "delete", "stateupdate", "komment")))
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($csoportir && ($_GET['action'] == "addnew" || $_GET['action'] == "edit"))
        {
            $irhat = true;
        }
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    if($irhat && $dbir)
    {
        include("./modules/feladattervezo/db/feladattervdb.php");
    }
    
    // Mivel ehhez a menüponthoz mindenki hozzáfér legalább saját jogosultsággal a legegegyszerűbb
    // itt jogosultságot adni nekik. Olyanokra, akik magasabb jogosultsággal rendelkeznek
    // ez nincs kihatással
    $egyenioldal = true;

    $oldalcim = "Feladat tervezése";
    $form = "modules/feladattervezo/forms/feladattervform";

    if(isset($_GET['id']))
    {
        // Először kiválasztjuk a megjelenítendő hibajegyek listáját.
        // Plusz jogosultság nélkül mindenki csak a sajátját látja.

        $where = "WHERE ((feladatterv_feladatok.feladat_id = ? OR feladatterv_feladatok.szulo = ?) AND feladatterv_feladatok.aktiv = 1)";
        $paramarr[] = $id;
        $paramarr[] = $id;

    }
    include("./modules/feladattervezo/includes/lekerdezes.php");

    // Ha a $feladatterv változó false állapotó, hiba adása, és kilépés
    if(!$feladatterv)
    {
        echo "<br><h2>Nincs ilyen sorszámú feladat, vagy nincs jogosultsága a megtekintéséhez!</h2>";
    }

    elseif(isset($_GET['action']) && $_GET['action'] == 'addnew')
    {
        ?><div class="feladatelem" id="ujfeladat-<?=$newelemid?>"><?php
            $szulo = null;
            include("./modules/feladattervezo/forms/feladattervform.php");
        ?></div><?php
    }

    if(isset($_GET['action']) && $id && $_GET['action'] == 'edit')
    {
        ?><div class="feladatelem" id="ujfeladat-<?=$newelemid?>"><?php
            $szulo = null;
            include("./modules/feladattervezo/forms/feladattervform.php");
        ?></div><?php
    }

    // Ha ide futunk ki, az adott feladatterv megjelenítése következik
    elseif($id)
    {
        include("./modules/feladattervezo/includes/lista.php");
    }
}