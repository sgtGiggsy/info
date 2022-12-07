<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO csoportok (nev, szak, leiras) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_POST['nev'], $_POST['szak'], $_POST['leiras']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Csoport hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE csoportok SET nev=?, szak=?, leiras=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['nev'], $_POST['szak'], $_POST['leiras'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Csoport szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "removemember")
    {
        if(isset($_GET['memberid']))
        {
            $felhasznaloid = $_GET['memberid'];
            mySQLConnect("DELETE FROM csoporttagsagok WHERE felhasznalo = $felhasznaloid AND csoport = $id");
        }
    }

    elseif($_GET["action"] == "addmember")
    {
        $stmt = $con->prepare('INSERT INTO csoporttagsagok (felhasznalo, csoport) VALUES (?, ?)');
        $stmt->bind_param('ss', $_POST['felhasznalo'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Felhasználó csoporthoz adása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "removeresponsibility")
    {
        if(isset($_GET['csopjogid']))
        {
            $csopjogid = $_GET['csopjogid'];
            mySQLConnect("DELETE FROM csoportjogok WHERE id = $csopjogid");
        }
    }

    elseif($_GET["action"] == "addresponsibility")
    {
        $stmt = $con->prepare('INSERT INTO csoportjogok (csoport, alakulat, telephely) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_POST['id'], $_POST['alakulat'], $_POST['telephely']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A felelősségi kör csoporthoz adása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
}