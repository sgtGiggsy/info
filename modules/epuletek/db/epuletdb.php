<?php

if(isset($irhat) && $irhat)
{
    $sql = new MySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO epuletek (szam, telephely, nev, tipus, megjegyzes) VALUES (?, ?, ?, ?, ?)');
        $sql->Run($_POST['szam'], $_POST['telephely'], $_POST['nev'], $_POST['tipus'], $_POST['megjegyzes']);

        if(!$sql->siker)
        {
            echo "<h2>Épület hozzáadása sikertelen!</h2>";
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $sql->Prepare('UPDATE epuletek SET szam=?, telephely=?, nev=?, tipus=?, megjegyzes=?, naprakesz=? WHERE id=?');
        $sql->Run($_POST['szam'], $_POST['telephely'], $_POST['nev'], $_POST['tipus'], $_POST['megjegyzes'], $_POST['naprakesz'], $_POST['id']);

        if(!$sql->siker)
        {
            echo "<h2>Épület szerkesztése sikertelen!</h2>";
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