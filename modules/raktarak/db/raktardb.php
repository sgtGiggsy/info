<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO raktarak (nev, szervezet, helyiseg) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_POST['nev'], $_POST['szervezet'], $_POST['helyiseg']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Rack hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE raktarak SET nev=?, szervezet=?, helyiseg=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['nev'], $_POST['szervezet'], $_POST['helyiseg'], $_POST['id']);
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