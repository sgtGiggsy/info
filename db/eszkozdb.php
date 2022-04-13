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

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO eszkozok (modell, sorozatszam, tulajdonos, varians, megjegyzes) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $_POST['modell'], $_POST['sorozatszam'], $_POST['tulajdonos'], $_POST['varians'], $_POST['megjegyzes']);
        $stmt->execute();

        $last_id = mysqli_insert_id($con);

        if($eszkoztipus)
        {
            if($eszkoztipus == "aktiv")
            {
                $stmt = $con->prepare('INSERT INTO aktiveszkozok (eszkoz, mac, portszam, uplinkportok, szoftver) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssss', $last_id, $_POST['mac'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "telefonkozpont")
            {
                $stmt = $con->prepare('INSERT INTO telefonkozpontok (eszkoz, nev) VALUES (?, ?)');
                $stmt->bind_param('ss', $last_id, $_POST['nev']);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE eszkozok SET modell=?, sorozatszam=?, tulajdonos=?, varians=?, megjegyzes=? WHERE id=?');
        $stmt->bind_param('sssssi', $_POST['modell'], $_POST['sorozatszam'], $_POST['tulajdonos'], $_POST['varians'], $_POST['megjegyzes'], $_POST['id']);
        $stmt->execute();

        if($eszkoztipus)
        {
            if($eszkoztipus == "aktiv")
            {
                $stmt = $con->prepare('UPDATE aktiveszkozok SET mac=?, portszam=?, uplinkportok=?, szoftver=? WHERE eszkoz=?');
                $stmt->bind_param('ssssi', $_POST['mac'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $_POST['id']);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz szerkesztése sikertelen!<br></h2>";
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