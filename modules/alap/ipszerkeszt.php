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

    $eszkozok = mySQLConnect("SELECT * FROM eszkozok");

    if(isset($_GET['id']))
    {
        $ipid = $_GET['id'];
        $ipszerk = mySQLConnect("SELECT * FROM ipcimek WHERE id = $ipid;");
        $ipszerk = mysqli_fetch_assoc($ipszerk);

        $id = $ipszerk['id'];
        $ipcim = $ipszerk['ipcim'];
        $vlan = $ipszerk['vlan'];
        $eszkoz = $ipszerk['eszkoz'];
        $megjegyzes = $ipszerk['megjegyzes'];

        $button = "IP cím szerkesztése";
        $oldalcim = "IP cím szerkesztése";
    }

    include('././templates/edit.tpl.php');
}