<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $kiadasideje = $eszkoztipus = $vegsoverzio = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/eszkozok/db/firmwaredb.php");

        redirectToGyujto("firmwarelista");
    }
    
    $button = "Új firmware felvitele";
    $irhat = true;
    $form = "modules/eszkozok/forms/firmwareszerkesztform";
    $oldalcim = "Új firmware hozzáadása";

    $eszkoztipuslista = mySQLConnect("SELECT modellek.id AS id, modell, gyartok.nev AS gyarto FROM modellek LEFT JOIN gyartok ON modellek.gyarto = gyartok.id ORDER BY gyarto ASC, modell ASC");

    if(isset($_GET['id']))
    {
        $firmwareid = $_GET['id'];
        $firmwareszerk = mySQLConnect("SELECT * FROM firmwarelist WHERE id = $firmwareid;");
        $firmwareszerkeszt = mysqli_fetch_assoc($firmwareszerk);

        $id = $firmwareszerkeszt['id'];
        $nev = $firmwareszerkeszt['nev'];
        $kiadasideje = $firmwareszerkeszt['kiadasideje'];
        $eszkoztipus = $firmwareszerkeszt['eszkoztipus'];
        $vegsoverzio = $firmwareszerkeszt['vegsoverzio'];

        $button = "Firmware szerkesztése";
        $oldalcim = "Firmware szerkesztése";
    }

    include('././templates/edit.tpl.php');

}