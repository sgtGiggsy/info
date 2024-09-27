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
        $szervezetszerk = new MySQLHandler("SELECT id, nev, rovid, statusz FROM szervezetek WHERE id = ?;", $szervezetid);
        $szervezetszerk->Bind($id, $nev, $rovid, $statusz);

        $ldapstringSQL = new MySQLHandler("SELECT needle FROM szervezetldap WHERE szervezet = ?;", $szervezetid);
        $ldapstringSQL = $ldapstringSQL->result;

        foreach($ldapstringSQL as $x)
        {
            $ldapstring .= $x['needle'] . "; ";
        }
        $ldapstring = rtrim($ldapstring, "; ");

        $button = "Szervezet szerkesztése";
        $oldalcim = "Szervezet szerkesztése";
    }

    include('././templates/edit.tpl.php');

}