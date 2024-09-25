<?php

if(isset($irhat) && $irhat)
{
    $vlandb = new mySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $vlandb->Query('INSERT INTO vlanok (id, nev, leiras, kceh) VALUES (?, ?, ?, ?)',
            array($_POST['id'], $_POST['nev'], $_POST['leiras'], $_POST['kceh']));
    }
    elseif($_GET["action"] == "update")
    {
        $vlandb->Query('UPDATE vlanok SET nev=?, leiras=?, kceh=? WHERE id=?',
            array($_POST['nev'], $_POST['leiras'], $_POST['kceh'], $_POST['id']));
    }
    elseif($_GET["action"] == "delete")
    {
    }

    $vlandb->Close($backtosender);
}