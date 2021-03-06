<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    $beepido = dateTimeLocalToTimeStamp($_POST['beepitesideje']);
    $kiepido = dateTimeLocalToTimeStamp($_POST['kiepitesideje']);

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO beepitesek (nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, admin, pass, megjegyzes, raktar, vlan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssssss', $_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['admin'], $_POST['pass'], $_POST['megjegyzes'], $_POST['raktar'], $_POST['vlan']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz beépítése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE beepitesek SET nev=?, eszkoz=?, ipcim=?, rack=?, helyiseg=?, pozicio=?, beepitesideje=?, kiepitesideje=?, admin=?, pass=?, megjegyzes=?, raktar=?, vlan=? WHERE id=?');
        $stmt->bind_param('sssssssssssssi', $_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['admin'], $_POST['pass'], $_POST['megjegyzes'], $_POST['raktar'], $_POST['vlan'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz beépítésének szerkesztése sikertelen!<br></h2>";
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