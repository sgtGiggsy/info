<?php

if(isset($irhat) && $irhat)
{
    $ipcimdb = new mySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $ipcimdb->Query('INSERT INTO ipcimek (ipcim, vlan, eszkoz, megjegyzes) VALUES (?, ?, ?, ?)', $_POST['ipcim'], $_POST['vlan'], $_POST['eszkoz'], $_POST['megjegyzes']);
    }
    elseif($_GET["action"] == "update")
    {
        $ipcimdb->Query('UPDATE ipcimek SET ipcim=?, vlan=?, eszkoz=?, megjegyzes=? WHERE id=?', $_POST['ipcim'], $_POST['vlan'], $_POST['eszkoz'], $_POST['megjegyzes'], $_POST['id']);
    }
    elseif($_GET["action"] == "delete")
    {
    }

    $ipcimdb->Close($backtosender);
}