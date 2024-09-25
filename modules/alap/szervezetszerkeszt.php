<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $rovid = $magyarazat = $statusz = $ldapstring = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/szervezetdb.php");

        redirectToGyujto("szervezetek");
        die;
    }
    
    $button = "Új szervezet";
    $irhat = true;
    $form = "modules/alap/forms/szervezetszerkesztform";
    $oldalcim = "Új szervezet hozzáadása";

    if(isset($_GET['id']))
    {
        $szervezetid = $_GET['id'];
        $szervezetszerk = mySQLConnect("SELECT * FROM szervezetek WHERE id = $szervezetid;");
        $szervezetszerk = mysqli_fetch_assoc($szervezetszerk);

        $ldapstringSQL = mySQLConnect("SELECT needle FROM szervezetldap WHERE szervezet = $szervezetid;");
        foreach($ldapstringSQL as $x)
        {
            $ldapstring .= $x['needle'] . "; ";
        }
        $ldapstring = rtrim($ldapstring, "; ");

        $id = $szervezetszerk['id'];
        $nev = $szervezetszerk['nev'];
        $rovid = $szervezetszerk['rovid'];
        $statusz = $szervezetszerk['statusz'];

        $button = "Szervezet szerkesztése";
        $oldalcim = "Szervezet szerkesztése";
    }

    include('././templates/edit.tpl.php');

}