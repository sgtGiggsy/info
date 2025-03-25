<?php

if(!$csoportolvas)
{
    getPermissionError();
}
else
{
    $paramarr = array();
    $javascriptfiles[] = "modules/feladattervezo/includes/feladatterv.js";
    
    // Mivel ehhez a menüponthoz mindenki hozzáfér legalább saját jogosultsággal a legegegyszerűbb
    // itt jogosultságot adni nekik. Olyanokra, akik magasabb jogosultsággal rendelkeznek
    // ez nincs kihatással
    $egyenioldal = false;

    // Először kiválasztjuk a megjelenítendő feladatok listáját.
    $where = "WHERE feladatterv_feladatok.szulo IS NULL AND feladatterv_feladatok.aktiv = 1";

    include("./modules/feladattervezo/includes/lekerdezes.php");

    // Ha a $feladatterv változó false állapotó, hiba adása, és kilépés
    if(!$feladatterv)
    {
        echo "<br><h2>Nincs olyan létező feladat, aminek a megtekintéséhez rendelkezik jogosultsággal!</h2>";
        ?><div class="feladatelem" id="ujfeladat-<?=$newelemid?>"><?php
            $irhat = true;
            $szulo = null;
            include("./modules/feladattervezo/forms/feladattervform.php");
        ?></div><?php
    }

    // Ha ide futunk ki, az adott feladatterv megjelenítése következik
    else
    {
        // Megállapítjuk, hogy a felhasználó írhatja-e a feladatot

        include("./modules/feladattervezo/includes/lista.php");
    }
}