<?php

if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>";
}
else
{
    $con = mySQLConnect(false);

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO bugok (cim, leiras, felhasznalo, oldal, tipus, prioritas) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $_POST['cim'], $_POST['leiras'], $_SESSION[getenv('SESSION_NAME').'id'], $_POST['oldal'], $_POST['tipus'],  $_POST['prioritas']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
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
        $stmt = $con->prepare('UPDATE rackszekrenyek SET nev=?, gyarto=?, unitszam=?, helyiseg=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['nev'], $_POST['gyarto'], $_POST['unitszam'], $_POST['helyiseg'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
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