<?php

if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();
    $mysql->KeepAlive();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $mysql->Prepare('INSERT INTO menupontok (menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $mysql->Run($_POST['menupont'], $_POST['szulo'], $_POST['url'], $_POST['oldal'], $_POST['cimszoveg'], $_POST['szerkoldal'], $_POST['aktiv'], $_POST['menuterulet'], $_POST['sorrend'], $_POST['gyujtourl'], $_POST['gyujtocimszoveg'], $_POST['gyujtooldal'], $_POST['dburl'], $_POST['dboldal'], $_POST['apiurl']);

        if(!$mysql->siker)
        {
            echo "<h2>A menüpont hozzáadása sikertelen!<br></h2>";
        }
    }

    elseif($_GET["action"] == "update")
    {        
        $mysql->Prepare('UPDATE menupontok SET menupont=?, szulo=?, url=?, oldal=?, cimszoveg=?, szerkoldal=?, aktiv=?, menuterulet=?, sorrend=?, gyujtourl=?, gyujtocimszoveg=?, gyujtooldal=?, dburl=?, dboldal=?, apiurl=? WHERE id=?');
        foreach($_POST['menupont'] as $key)
        {
            if(count($key) > 1)
            {
                $mysql->Run($key['menupont'], $key['szulo'], $key['url'], $key['oldal'], $key['cimszoveg'], $key['szerkoldal'], $key['aktiv'], $key['menuterulet'], $key['sorrend'], $key['gyujtourl'], $key['gyujtocimszoveg'], $key['gyujtooldal'], $key['dburl'], $key['dboldal'], $key['apiurl'], $key['id']);
                if(!$mysql->siker)
                {
                    echo "<h2>A menüpont szerkesztése sikertelen!<br></h2>";
                }
            }
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}