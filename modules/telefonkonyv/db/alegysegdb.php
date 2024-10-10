<?php

if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();
    $mysql->KeepAlive();

    purifyPost();

    $sorrend = $_POST['sorrend'];

    if($_GET["action"] == "new")
    {
        $mysql->Query("UPDATE telefonkonyvcsoportok SET sorrend = sorrend + 1 WHERE sorrend > ?;", $sorrend);

        if(!verifyWholeNum($sorrend))
        {
            $sorrend = $sorrend + 0.5;
        }
        $mysql->Query('INSERT INTO telefonkonyvcsoportok (nev, sorrend) VALUES (?, ?)', $_POST['nev'], $sorrend);
        if(!$mysql->siker)
        {
            echo "<h2>Alegység hozzáadása sikertelen!<br></h2>";
        }
    }
    elseif($_GET["action"] == "update")
    {
        $alegysid = $_POST['id'];

        if(!verifyWholeNum($sorrend))
        {
            $mysql->Query("SELECT sorrend FROM telefonkonyvcsoportok WHERE id = ?;", $alegysid);
            $mysql->Bind($origsorrend);
            $mysql->Query("UPDATE telefonkonyvcsoportok SET sorrend = sorrend + 1 WHERE sorrend > ? AND torolve IS NULL;", $sorrend);
        }
        
        $mysql->Query('UPDATE telefonkonyvcsoportok SET nev=?, sorrend=? WHERE id=?', $_POST['nev'], $sorrend, $_POST['id']);
        if(!$mysql->siker)
        {
            echo "<h2>Alegység szerkesztése sikertelen!<br></h2>";
        }
        elseif(isset($origsorrend) && $origsorrend)
        {
            // Mivel egy elemet áthelyeztünk, ahonnan kivettük, képződne egy lyuk, ha nem csökkentenénk a felette lévős sorszámokat
            $mysql->Query("UPDATE telefonkonyvcsoportok SET sorrend = sorrend - 1 WHERE sorrend > ? AND torolve IS NULL;", $origsorrend);
        }
    }
    elseif($_GET["action"] == "delete")
    {
        $sorrend = $_POST['sorrend'];
        $torolve = 1;
        $nulled = null;
        if($mindir)
        {
            $mysql->Query('UPDATE telefonkonyvcsoportok SET torolve=?, sorrend=? WHERE id=?', $torolve, $nulled, $_POST['id']);
            $mysql->Query("UPDATE telefonkonyvcsoportok SET sorrend = sorrend - 1 WHERE sorrend > ? AND torolve IS NULL;", $sorrend);
        }
        else
        {
            echo "<h2>Nincs jogosultsága az alegység törlésére!</h2>";
        }
    }
    $mysql->Close();
}