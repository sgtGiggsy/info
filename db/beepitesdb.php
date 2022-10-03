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
        $stmt = $con->prepare('INSERT INTO beepitesek (nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, admin, pass, megjegyzes, vlan, switchport, letrehozo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssssssssss', $_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['admin'], $_POST['pass'], $_POST['megjegyzes'], $_POST['vlan'], $_POST['switchport'], $_SESSION[getenv('SESSION_NAME').'id']);
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
        $timestamp = date('Y-m-d H:i:s');
        $beepid = $_POST['id'];

        mySQLConnect("INSERT INTO beepitesek_history (beepitesid, nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, megjegyzes, admin, pass, vlan, switchport, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje)
            SELECT id, nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, megjegyzes, admin, pass, vlan, switchport, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje
            FROM beepitesek
            WHERE id = $beepid");

        $stmt = $con->prepare('UPDATE beepitesek SET nev=?, eszkoz=?, ipcim=?, rack=?, helyiseg=?, pozicio=?, beepitesideje=?, kiepitesideje=?, admin=?, pass=?, megjegyzes=?, vlan=?, switchport=?, utolsomodosito=?, utolsomodositasideje=? WHERE id=?');
        $stmt->bind_param('sssssssssssssssi', $_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['admin'], $_POST['pass'], $_POST['megjegyzes'], $_POST['vlan'], $_POST['switchport'], $_SESSION[getenv('SESSION_NAME').'id'], $timestamp, $beepid);
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