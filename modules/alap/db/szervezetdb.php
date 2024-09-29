<?php

if(isset($irhat) && $irhat)
{
    $szervezetdb = new mySQLHandler();

    purifyPost();

    $ldapstring = explode(";", $_POST['ldapstring']);

    if($_GET["action"] == "new")
    {
        $szervezetdb->Query('INSERT INTO szervezetek (nev, rovid, statusz) VALUES (?, ?, ?)',
            array($_POST['nev'], $_POST['rovid'], $_POST['statusz']), true);
    }
    elseif($_GET["action"] == "update")
    {
        $szervezetdb->Query('UPDATE szervezetek SET nev=?, rovid=?, statusz=? WHERE id=?',
            array($_POST['nev'], $_POST['rovid'], $_POST['statusz'], $_POST['id']), true);
    }
    elseif($_GET["action"] == "delete")
    {
    }

    if($szervezetdb->siker)
    {
        if(isset($_POST['id']))
        {
            $szervezetdb->Query("DELETE FROM szervezetldap WHERE szervezet = ?", $_POST['id'], true);
        }

        $szervezetdb->Prepare('INSERT INTO szervezetldap (szervezet, needle) VALUES (?, ?)');
        foreach($ldapstring as $needle)
        {
            if($needle != "")
            {
                $savendl = trim($needle);
                $szervezetdb->Run(array($_POST['id'], $savendl));
            }
        }
    }

    $szervezetdb->Close($backtosender);
}