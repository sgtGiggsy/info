<?php

if(isset($irhat) && $irhat)
{
    $szervezetdb = new mySQLHandler();
    $szervezetdb->KeepAlive();

    purifyPost();

    $ldapstring = explode(";", $_POST['ldapstring']);

    if($_GET["action"] == "new")
    {
        $szervezetdb->Query('INSERT INTO szervezetek (nev, rovid, statusz) VALUES (?, ?, ?)',
            $_POST['nev'], $_POST['rovid'], $_POST['statusz']);
    }
    elseif($_GET["action"] == "update")
    {
        $szervezetdb->Query('UPDATE szervezetek SET nev=?, rovid=?, statusz=? WHERE id=?',
            $_POST['nev'], $_POST['rovid'], $_POST['statusz'], $_POST['id']);
    }
    elseif($_GET["action"] == "delete")
    {
    }

    if($szervezetdb->siker)
    {
        if(isset($_POST['id']))
        {
            $szervezetdb->Query("DELETE FROM szervezetldap WHERE szervezet = ?", $_POST['id']);
        }

        $szervezetdb->Prepare('INSERT INTO szervezetldap (szervezet, needle) VALUES (?, ?)');
        foreach($ldapstring as $needle)
        {
            if($needle != "")
            {
                $savendl = trim($needle);
                $szervezetdb->Run($_POST['id'], $savendl);
            }
        }
    }

    $szervezetdb->Close($backtosender);
}