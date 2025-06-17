<?php
if(isset($irhat) && $irhat)
{
    $mysql = new MySQLHandler();
    $mysql->KeepAlive();


    purifyPost();

    if($_GET["action"] == "addnew")
    {
        $mysql->Prepare('INSERT INTO munkalaptemplateek (szoveg) VALUES (?)');
        $mysql->Run($_POST['szoveg']);
        if(!$mysql->siker)
        {
            echo "<h2>A template hozzáadása sikertelen!</h2>";
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "update")
    {
        $mysql->Prepare('UPDATE munkalaptemplateek SET szoveg=? WHERE id=?');
        $mysql->Run($_POST['szoveg'], $_POST['id']);
        if(!$mysql->siker)
        {
            echo "<h2>A template szerkesztése sikertelen!</h2>";
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
elseif($csoportir)
{
    if($_GET["action"] == "hasznalt")
    {
        if($_GET['tempid'] && is_numeric($_GET['tempid']))
        {
            $tempid = $_GET['tempid'];
            $mysql->Query("UPDATE munkalaptemplateek SET hasznalva = hasznalva + 1 WHERE id = ?", $tempid);
        }
        else
        {
            echo "Mivel próbálkozol?";
        }
    }
}
