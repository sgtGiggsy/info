<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO ipcimek (ipcim, vlan, eszkoz, megjegyzes) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['ipcim'], $_POST['vlan'], $_POST['eszkoz'], $_POST['megjegyzes']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>IP cím hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE ipcimek SET ipcim=?, vlan=?, eszkoz=?, megjegyzes=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['ipcim'], $_POST['vlan'], $_POST['eszkoz'], $_POST['megjegyzes'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>IP cím szerkesztése sikertelen!<br></h2>";
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