<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO rackszekrenyek (nev, gyarto, unitszam, helyiseg) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['nev'], $_POST['gyarto'], $_POST['unitszam'], $_POST['helyiseg']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Rack hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
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
    }
    elseif($_GET["action"] == "delete")
    {
    }
}