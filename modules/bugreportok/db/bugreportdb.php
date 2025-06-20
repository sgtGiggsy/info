<?php

if(!isset($_SESSION['id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>";
}
else
{
    $sql = new MySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO bugok (cim, leiras, felhasznalo, oldal, tipus, prioritas) VALUES (?, ?, ?, ?, ?, ?)');
        $sql->Run($_POST['cim'], $_POST['leiras'], $felhasznaloid, $_POST['oldal'], $_POST['tipus'],  $_POST['prioritas']);
        if(!$sql->siker)
        {
            echo "<h2>A hiba jelentése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $sql->Prepare('UPDATE rackszekrenyek SET nev=?, gyarto=?, unitszam=?, helyiseg=? WHERE id=?');
        $sql->Run($_POST['nev'], $_POST['gyarto'], $_POST['unitszam'], $_POST['helyiseg'], $_POST['id']);
        if(!$sql->siker)
        {
            echo "<h2>Rack szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
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