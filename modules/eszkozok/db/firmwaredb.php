<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO firmwarelist (nev, kiadasideje, eszkoztipus, vegsoverzio) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['nev'], $_POST['kiadasideje'], $_POST['eszkoztipus'], $_POST['vegsoverzio']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Firmware hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE firmwarelist SET nev=?, kiadasideje=?, eszkoztipus=?, vegsoverzio=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['nev'], $_POST['kiadasideje'], $_POST['eszkoztipus'], $_POST['vegsoverzio'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Firmware szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}