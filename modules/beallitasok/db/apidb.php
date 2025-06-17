<?php

if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();

    purifyPost();

    if($_GET["action"] == "addnew")
    {
        $key = bin2hex(random_bytes(32));
        
        $mysql->Prepare('INSERT INTO api (menupont, apikey, jogosultsagszint, aktiv) VALUES (?, ?, ?, ?)');
        $mysql->Run($_POST['menupont'], $key, $_POST['jogosultsagszint'], $_POST['aktiv']);

        if(!$mysql->siker)
        {
            echo "<h2>Az API kulcs hozzáadása sikertelen!<br></h2>";
        }
    }

    elseif($_GET["action"] == "updateapi")
    {
        $mysql->Prepare('UPDATE api SET menupont=?, jogosultsagszint=?, aktiv=? WHERE id=?');
        $mysql->Run($_POST['menupont'], $_POST['jogosultsagszint'], $_POST['aktiv'], $_POST['id']);

        if(!$mysql->siker)
        {
            echo "<h2>Az API kulcs szerkesztése sikertelen!<br></h2>";
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}