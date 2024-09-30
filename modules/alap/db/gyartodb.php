<?php

if(isset($irhat) && $irhat)
{
    $gyartosql = new mySQLHandler();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $gyartosql->Query('INSERT INTO gyartok (nev) VALUES (?)', $_POST['nev']);
    }
    elseif($_GET["action"] == "update")
    {
        $gyartosql->Query('UPDATE gyartok SET nev=? WHERE id=?', $_POST['nev'], $_POST['id']);
    }
    elseif($_GET["action"] == "delete")
    {
    }
    $gyartosql->Close($backtosender);
}