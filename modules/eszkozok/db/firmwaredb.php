<?php

if(isset($irhat) && $irhat)
{
    $sql = new MySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO firmwarelist (nev, kiadasideje, eszkoztipus, vegsoverzio) VALUES (?, ?, ?, ?)');
        $sql->Run($_POST['nev'], $_POST['kiadasideje'], $_POST['eszkoztipus'], $_POST['vegsoverzio']);

        if(!$sql->siker)
        {
            echo "<h2>Firmware hozzáadása sikertelen!<br></h2>";
        }
    }
    elseif($_GET["action"] == "update")
    {
        $sql->Prepare('UPDATE firmwarelist SET nev=?, kiadasideje=?, eszkoztipus=?, vegsoverzio=? WHERE id=?');
        $sql->Run($_POST['nev'], $_POST['kiadasideje'], $_POST['eszkoztipus'], $_POST['vegsoverzio'], $_POST['id']);

        if(!$sql->siker)
        {
            echo "<h2>Firmware szerkesztése sikertelen!<br></h2>";
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}