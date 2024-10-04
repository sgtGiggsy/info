<?php

if(!$globaltelefonkonyvadmin)
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvszerkesztodb.php");
        
        redirectToGyujto("telefonkonyvszerkesztok");
    }

    $magyarazat = $felhasznalolista = null;
    $irhat = true;
    $felhasznaloszur = "NULL AS felhid,";
    $action = "update";
    $oldalcim = "Új szerkesztő felvétele a telefonkönyvhöz";
    $button = "Jogosultságok módosítása";
    $form = "modules/telefonkonyv/forms/telefonkonyvadminform";

    if(isset($_GET['action']) && !isset($_GET['id']))
    {
        if($_GET['action'] == "addnew")
        {
            $action = "new";
        }
    }

    if(isset($_GET['id']))
    {
        $felhasznalo = mySQLConnect("SELECT nev, felhasznalonev FROM felhasznalok WHERE id = $id");
        $felhasznalo = mysqli_fetch_assoc($felhasznalo);
        $felhasznalo = $felhasznalo['nev'] . " (" . $felhasznalo['felhasznalonev'] . ")";
        $oldalcim = "$felhasznalo felelősségi körének módosítása";
        $felhasznaloszur = "(SELECT felhasznalo FROM telefonkonyvadminok WHERE csoport = csopid AND felhasznalo = $id) AS felhid,";
    }
    else {
        $felhasznalolista = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok");
    }

    $alegysegek = mySQLConnect("SELECT telefonkonyvcsoportok.id AS csopid,
            $felhasznaloszur
            telefonkonyvcsoportok.nev AS nev
        FROM telefonkonyvcsoportok
        ORDER BY sorrend ASC;");

    include('././templates/edit.tpl.php');
}