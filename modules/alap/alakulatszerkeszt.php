<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $rovid = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/alakulatdb.php");

        redirectToGyujto("alakulatok");
    }
    
    $button = "Új alakulat";
    $irhat = true;
    $form = "modules/alap/forms/alakulatszerkesztform";
    $oldalcim = "Új alakulat hozzáadása";

    if(isset($_GET['id']))
    {
        $alakulatid = $_GET['id'];
        $alakulatszerk = mySQLConnect("SELECT * FROM alakulatok WHERE id = $alakulatid;");
        $alakulatszerk = mysqli_fetch_assoc($alakulatszerk);

        $id = $alakulatszerk['id'];
        $nev = $alakulatszerk['nev'];
        $rovid = $alakulatszerk['rovid'];

        $button = "Alakulat szerkesztése";
        $oldalcim = "Alakulat szerkesztése";
    }

    include('././templates/edit.tpl.php');

}