<?php

if(@!$contextmenujogok['jogosultsagok'])
{
    //echo $sajatolvas . $csoportolvas . $mindolvas . $sajatir . $csoportir . $mindir;
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonszamok/db/telefonjogosultsagdb.php");

        redirectToGyujto("jogosultsagok");
    }
    
    $button = "Új telefonjog";
    $irhat = true;
    $form = "modules/telefonszamok/forms/telefonjogszerkform";
    $oldalcim = "Új telefon jogosultság rögzítése";

    if($elemid)
    {
        $telefonjogszerk = mySQLConnect("SELECT * FROM telefonjogosultsagok WHERE id = $elemid;");
        $telefonjogszerk = mysqli_fetch_assoc($telefonjogszerk);

        $nev = $telefonjogszerk['nev'];

        $button = "Telefonjog szerkesztése";
        $oldalcim = "Telefonjog szerkesztése";

    }

    include('././templates/edit.tpl.php');

}