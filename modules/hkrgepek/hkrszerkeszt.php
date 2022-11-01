<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $gepnev = $felhasznalo = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/hkrgepek/db/hkrdb.php");
        redirectToGyujto("hkrgepek");
    }
    
    $button = "Új HKR gép";
    $irhat = true;
    $form = "modules/hkrgepek/forms/hkrszerkesztform";
    $oldalcim = "Új HKR gép hozzáadása";

    if(isset($_GET['id']))
    {
        $hkrszerk = mySQLConnect("SELECT * FROM hkrgepek WHERE id = $id;");
        $hkrszerk = mysqli_fetch_assoc($hkrszerk);

        $gepnev = $hkrszerk['gepnev'];
        $felhasznalo = $hkrszerk['felhasznalo'];

        $button = "HKR gép szerkesztése";
        $oldalcim = "HKR gép szerkesztése";

    }

    include('././templates/edit.tpl.php');

}