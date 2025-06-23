<?php

if(@!$sajatir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(isset($_GET['print']))
    {
        include("./modules/munkalapok/includes/munkaprint.php");
        die();
    }
    
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/munkalapok/db/munkadb.php");
        redirectToGyujto(null);
    }

    $igenylo = $igenylesideje = $vegrehajtasideje = $munkavegzo2 = $leiras = $eszkoz = $magyarazat = null;
    $modenged = true;
    $munkaprint = "?print";
    $javascriptfiles[] = "modules/munkalapok/includes/templatebeszur.js";
    $form = "modules/munkalapok/forms/munkaszerkesztform";
    $hely = $_SESSION["defaultmunkahely"];
    $ugyintezo = $_SESSION["defaultugyintezo"];
    $munkavegzo1 = $_SESSION['id'];
    $templateek = new MySQLHandler("SELECT id, szoveg FROM munkalaptemplateek ORDER BY hasznalva DESC, szoveg ASC;");
    $templateek = $templateek->Result();

    $oldalcim = "Munkalap adatai";
    $button = "Munka rögzítése";
    $button2 = "Munka rögzítése és nyomtatása";

    if($elemid)
    {
        $munka = new MySQLHandler("SELECT *, IF(vegrehajtasideje > date_sub(now(), INTERVAL 31 DAY), 1, 0) AS modenged FROM munkalapok WHERE id = ?", $elemid);
        $munka = $munka->Fetch();

        $hely = $munka['hely'];
        $igenylo = $munka['igenylo'];
        $igenylesideje = $munka['igenylesideje'];
        $vegrehajtasideje = $munka['vegrehajtasideje'];
        $munkavegzo1 = $munka['munkavegzo1'];
        $munkavegzo2 = $munka['munkavegzo2'];
        $leiras = $munka['leiras'];
        $eszkoz = $munka['eszkoz'];
        $ugyintezo = $munka['ugyintezo'];
        $modenged = $munka['modenged'];
        $munkaprint = "/" . $elemid . "?print";

        $button = "Munka szerkesztése";
        $button2 = "Munka szerkesztése és nyomtatása";
    }

    if(!$modenged)
        echo "<h2>A kiválasztott munkalap egy hónapnál régebbi, így az adatai már nem módosíthatóak!</h2>";
    else
        include('./templates/edit.tpl.php');
}