<?php

if(!$csoportolvas)
{
    getPermissionError();
}
else
{
    $adattabla = "feladatterv_feladatok";
    $oldalnev = "feladattervek";
    $oldalcim = "Feladatok listája";
    $paramarr = array();
    $javascriptfiles[] = "modules/feladattervezo/includes/feladatterv.js";
    $table = "modules/feladattervezo/includes/lista";
    $countquery = 'SELECT count(*) AS db FROM feladatterv_feladatok LEFT JOIN felhasznalok felvivo ON feladatterv_feladatok.felvitte = felvivo.id WHERE feladatterv_feladatok.szulo IS NULL AND feladatterv_feladatok.aktiv = 1 AND felvivo.szervezet = ?';
    $cqueryparams = array($szervezet);
    $egyenioldal = $keres = false;
    //$lapozas = "LIMIT $start, $megjelenit";
    $lapozas = true;

    // Először kiválasztjuk a megjelenítendő feladatok listáját.
    $where = "WHERE feladatterv_feladatok.szulo IS NULL AND feladatterv_feladatok.aktiv = 1";

    $lekerdezes = "./modules/feladattervezo/includes/lekerdezes.php";
    

    // Ha a $feladatterv változó false állapotó, hiba adása, és kilépés
    

    // Ha ide futunk ki, az adott feladatterv megjelenítése következik
    // Megállapítjuk, hogy a felhasználó írhatja-e a feladatot

    //include("./modules/feladattervezo/includes/lista.php");
    if($csoportir)
    {
        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/feladatterv?action=addnew'">Új feladat létrehozása</button>
        </div><?php
    }

    include('././templates/lapozas.tpl.php');

    if(!$feladatterv)
    {
        echo "<br><h2>Nincs olyan létező feladat, aminek a megtekintéséhez rendelkezik jogosultsággal!</h2>";
        ?><div class="feladatelem" id="ujfeladat-<?=$newelemid?>"><?php
            $irhat = true;
            $szulo = null;
            include("./modules/feladattervezo/forms/feladattervform.php");
        ?></div><?php
    }
}