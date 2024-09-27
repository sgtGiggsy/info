<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $ipcim = $vlan = $eszkoz = $megjegyzes = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/ipcimdb.php");

        redirectToGyujto("ipcimek");
    }
    
    $irhat = true;
    $form = "modules/alap/forms/ipszerkesztform";
    $oldalcim = "Új IP cím hozzáadása";
    $button = "Új IP cím";

    $eszkozok = new MySQLHandler("SELECT * FROM eszkozok;");
    $eszkozok = $eszkozok->Result();

    if(isset($_GET['id']))
    {
        $ipszerk = new MySQLHandler("SELECT id, ipcim, vlan, eszkoz, megjegyzes FROM ipcimek WHERE id = ?;", $_GET['id']);
        $ipszerk->Bind($id, $ipcim, $vlan, $eszkoz, $megjegyzes);

        $button = "IP cím szerkesztése";
        $oldalcim = "IP cím szerkesztése";
    }

    include('././templates/edit.tpl.php');
}