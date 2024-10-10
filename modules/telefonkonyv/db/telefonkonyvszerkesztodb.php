<?php

if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();
    $mysql->KeepAlive();

    purifyPost();

    if($_POST['id'])
    {
        $id = $_POST['id'];

        $mysql->Query("DELETE FROM telefonkonyvadminok WHERE felhasznalo = ?;", $id);

        if(isset($_POST['csoport']))
        {
            $mysql->Prepare('INSERT INTO telefonkonyvadminok (felhasznalo, csoport) VALUES (?, ?)');
            foreach($_POST['csoport'] as $csoportid)
            {
                $mysql->Run($id, $csoportid);
            }
            if(!$mysql->siker)
            {
                echo "<h2>A változás beküldése sikertelen!<br></h2>";
            }
        }
    }
}