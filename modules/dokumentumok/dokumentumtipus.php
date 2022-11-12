<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    $nev = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/dokumentumok/db/dokumentumtipusdb.php");

        redirectToGyujto("dokumentumtipusok");
    }
    
    $button = "Új dokumentumtípus";
    $irhat = true;
    $form = "modules/dokumentumok/forms/dokumentumtipusszerkesztform";
    $oldalcim = "Új dokumentumtípus hozzáadása";

    if(isset($_GET['id']))
    {
        $dokumentumtipus = mySQLConnect("SELECT * FROM dokumentumtipusok WHERE id = $id;");
        $dokumentumtipus = mysqli_fetch_assoc($dokumentumtipus);

        $id = $dokumentumtipus['id'];
        $nev = $dokumentumtipus['nev'];

        $button = "Dokumentumtípus szerkesztése";
        $oldalcim = "Dokumentumtípus szerkesztése";
    }

    include('././templates/edit.tpl.php');

}