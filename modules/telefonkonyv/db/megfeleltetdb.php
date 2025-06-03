<?php

if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();
    $mysql->KeepAlive();

    purifyPost();

    $mysql->Prepare("UPDATE telefonkonyvfelhasznalok SET felhasznalo = ? WHERE id = ?;");

    if($_GET["action"] == "megfeleltet")
    {
        $pdarab = count($_POST['tkonyvfelhid']);
        for($ind = 0; $ind < $pdarab; $ind++)
        {
            if(!is_null($_POST['adfelhid'][$ind]))
                $mysql->Run($_POST['adfelhid'][$ind], $_POST['tkonyvfelhid'][$ind]);
        }
    }
    $mysql->Close();
}