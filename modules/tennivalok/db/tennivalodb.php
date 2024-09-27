<?php

if(!isset($_SESSION['id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>";
}
else
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO tennivalok (cim, leiras, felhasznalo, prioritas, bovitett) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $_POST['cim'], $_POST['leiras'], $_SESSION['id'], $_POST['prioritas'], $_POST['bovitett']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A tennivaló hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: ./tennivalok");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE tennivalok SET befejezve=? WHERE id=?');
        $stmt->bind_param('si', $_POST['befejezve'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A tennivaló szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: ./tennivalok");
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}