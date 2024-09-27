<?php

if(@!$mindir)
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/gyartodb.php");
        
        redirectToGyujto("gyartoklistaja");
    }

    $nev = $magyarazat = null;
    $button = "Új gyártó";
    $irhat = true;
    $form = "modules/alap/forms/gyartoszerkesztform";
    $oldalcim = "Új gyártó hozzáadása";

    if(isset($_GET['id']))
    {
        $gyartoid = $_GET['id'];
        $gyarto = new MySQLHandler("SELECT id, nev FROM gyartok WHERE id = ?", $_GET['id']);
        $gyarto = $gyarto->Bind($id, $nev);

        $button = "Szerkesztés";
        $oldalcim = "Gyártó szerkesztése";
    }

    include('././templates/edit.tpl.php');
}