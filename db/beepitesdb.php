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
        $stmt = $con->prepare('INSERT INTO beepitesek (nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssss', $_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Vizsga elindítása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE beepitesek SET nev=?, eszkoz=?, ipcim=?, rack=?, helyiseg=?, pozicio=?, beepitesideje=?, kiepitesideje=? WHERE id=?');
        $stmt->bind_param('ssssssssi', $_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Vizsga elindítása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
    //header("Location: $RootPath/mindeneszkoz");
}