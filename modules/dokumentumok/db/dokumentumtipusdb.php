<?php

if(isset($irhat) && $irhat)
{
    $sql = new MySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO dokumentumtipusok (nev) VALUES (?)');
        $sql->Run($_POST['nev']);
        if(!$sql->siker)
        {
            echo "<h2>Dokumentumtipus hozzáadása sikertelen!</h2>";
        }
    }
    elseif($_GET["action"] == "update")
    {
        $sql->Prepare('UPDATE dokumentumtipusok SET nev=? WHERE id=?');
        $sql->Run($_POST['nev'], $_POST['id']);
        if(!$sql->siker)
        {
            echo "<h2>Dokumentumtipus szerkesztése sikertelen!</h2>";
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}