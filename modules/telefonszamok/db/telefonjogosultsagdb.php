<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO telefonjogosultsagok (id, nev) VALUES (?, ?)');
        $stmt->bind_param('ss', $_POST['id'], $_POST['nev']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Telefonjogosultság hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE telefonjogosultsagok SET nev=? WHERE id=?');
        $stmt->bind_param('si', $_POST['nev'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Telefonjogosultság szerkesztése sikertelen!<br></h2>";
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