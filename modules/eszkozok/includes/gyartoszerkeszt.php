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
        include("./modules/eszkozok/db/gyartodb.php");
        
        redirectToGyujto("gyartoklistaja");
    }

    $nev = $magyarazat = null;
    $button = "Új gyártó";
    $irhat = true;
    $form = "modules/eszkozok/forms/gyartoszerkesztform";
    $oldalcim = "Új gyártó hozzáadása"; 

    if($elemid)
    {
        $gyarto = new MySQLHandler("SELECT id, nev FROM gyartok WHERE id = ?", $elemid);
        $gyarto = $gyarto->Bind($id, $nev);

        $button = "Szerkesztés";
        $oldalcim = "Gyártó szerkesztése";
    }

    include('././templates/edit.tpl.php');
}