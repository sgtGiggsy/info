<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $leiras = $kceh = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/vlandb.php");
        redirectToGyujto("vlanok");
    }
    
    $button = "Új VLAN";
    $irhat = true;
    $form = "modules/alap/forms/vlanszerkesztform";
    $oldalcim = "Új VLAN hozzáadása";

    if(isset($_GET['id']))
    {
        $vlanid = $_GET['id'];
        $vlanszerk = mySQLConnect("SELECT * FROM vlanok WHERE id = $vlanid;");
        $vlanszerk = mysqli_fetch_assoc($vlanszerk);

        $id = $vlanszerk['id'];
        $nev = $vlanszerk['nev'];
        $leiras = $vlanszerk['leiras'];
        $kceh = $vlanszerk['kceh'];

        $button = "VLAN szerkesztése";
        $oldalcim = "VLAN szerkesztése";
    }

    include('././templates/edit.tpl.php');

}