<?php

if(isset($irhat) && $irhat)
{
    $sql = new MySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO raktarak (nev, szervezet, helyiseg) VALUES (?, ?, ?)');
        $sql->Run($_POST['nev'], $_POST['szervezet'], $_POST['helyiseg']);

        if(!$sql->siker)
        {
            echo "<h2>Rack hozzáadása sikertelen!<br></h2>";
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $sql->Prepare('UPDATE raktarak SET nev=?, szervezet=?, helyiseg=? WHERE id=?');
        $sql->Run($_POST['nev'], $_POST['szervezet'], $_POST['helyiseg'], $_POST['id']);

        if(!$sql->siker)
        {
            echo "<h2>Rack szerkesztése sikertelen!<br></h2>";
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}