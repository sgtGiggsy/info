<?php

if(isset($mindir) && $mindir)
{
    $sql = new MySQLHandler();
    $sql->KeepAlive();

    purifyPost();

    if($_GET["action"] == "new")
    {
        $sql->Prepare('INSERT INTO helyisegek (epulet, emelet, helyisegszam, helyisegnev) VALUES (?, ?, ?, ?)');
        $sql->Run($_POST['epulet'], $_POST['emelet'], $_POST['helyisegszam'], $_POST['helyisegnev']);

        if(!$sql->siker)
        {
            echo "<h2>Helyiség hozzáadása sikertelen!</h2>";
        }
        else
        {
            redirectToKuldo();
        }
    }
    elseif($_GET["action"] == "update")
    {
        $sql->Prepare('UPDATE helyisegek SET epulet=?, emelet=?, helyisegszam=?, helyisegnev=? WHERE id=?');
        $sql->Run($_POST['epulet'], $_POST['emelet'], $_POST['helyisegszam'], $_POST['helyisegnev'], $_POST['id']);

        if(!$sql->siker)
        {
            echo "<h2>A port szerkesztése sikertelen!</h2>";
        }
        else
        {
            redirectToKuldo();
        }
    }
    elseif($_GET["action"] == "generate")
    {
        //! TESZTELNI !//
        $helyisegek = (new MySQLHandler("SELECT helyisegszam, helyisegnev FROM helyisegek WHERE epulet = ?;", $_POST['epulet']))->AsArray('helyisegszam');
        $sql->Prepare('INSERT INTO helyisegek (epulet, emelet, helyisegszam) VALUES (?, ?, ?)');
        for($i = $_POST['kezdohelyisegszam']; $i <= $_POST['zarohelyisegszam']; $i++)
        {
            $helyisegszam = str_pad($i, $_POST['szamjegyszam'], "0", STR_PAD_LEFT);
            if($helyisegek[$helyisegszam])
                $sql->Run($_POST['epulet'], $_POST['emelet'], $helyisegszam);
        }

        if($sql->siker)
        {
            echo "<h2>Helyiségek hozzáadása sikertelen!</h2>";
        }
        else
        {
            redirectToKuldo();
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}