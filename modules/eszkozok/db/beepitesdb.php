<?php

if(isset($irhat) && $irhat)
{
    $sql = new MySQLHandler();
    $sql->KeepAlive();

    purifyPost();

    $beepido = dateTimeLocalToTimeStamp($_POST['beepitesideje']);
    $kiepido = dateTimeLocalToTimeStamp($_POST['kiepitesideje']);

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO beepitesek (nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, admin, pass, megjegyzes, vlan, switchport) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $sql->Run($_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['admin'], $_POST['pass'], $_POST['megjegyzes'], $_POST['vlan'], $_POST['switchport']);

        $last_id = $sql->last_insert_id;
        $modif_id = modId("1", "beepites", $last_id);
        $sql->Query("UPDATE beepitesek SET modid = ? WHERE id = ?", $modif_id, $last_id);

        if(!$sql->siker)
        {
            echo "<h2>Eszköz beépítése sikertelen!<br></h2>";
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

        $modif_id = modId("2", "beepites", $beepid);
        
        $sql->Query("INSERT INTO beepitesek_history (beepitesid, nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, megjegyzes, admin, pass, vlan, switchport, modid)
            SELECT id, nev, eszkoz, ipcim, rack, helyiseg, pozicio, beepitesideje, kiepitesideje, megjegyzes, admin, pass, vlan, switchport, modid
            FROM beepitesek
            WHERE id = ?;", $beepid);

        $sql->Prepare('UPDATE beepitesek SET nev=?, eszkoz=?, ipcim=?, rack=?, helyiseg=?, pozicio=?, beepitesideje=?, kiepitesideje=?, admin=?, pass=?, megjegyzes=?, vlan=?, switchport=?, modid=? WHERE id=?');
        $sql->Run($_POST['nev'], $_POST['eszkoz'], $_POST['ipcim'], $_POST['rack'], $_POST['helyiseg'], $_POST['pozicio'], $beepido, $kiepido, $_POST['admin'], $_POST['pass'], $_POST['megjegyzes'], $_POST['vlan'], $_POST['switchport'], $modif_id, $beepid);
        
        if(!$sql->siker)
        {
            echo "<h2>Eszköz beépítésének szerkesztése sikertelen!<br></h2>";
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