<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO dokumentumtipusok (nev) VALUES (?)');
        $stmt->bind_param('s', $_POST['nev']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Dokumentumtipus hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE dokumentumtipusok SET nev=? WHERE id=?');
        $stmt->bind_param('si', $_POST['nev'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Dokumentumtipus szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}