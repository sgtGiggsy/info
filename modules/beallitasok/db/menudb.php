<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO menupontok (menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssss', $_POST['menupont'], $_POST['szulo'], $_POST['url'], $_POST['oldal'], $_POST['cimszoveg'], $_POST['szerkoldal'], $_POST['aktiv'], $_POST['menuterulet'], $_POST['sorrend']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Menüpont hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $RootPath/beallitasok?menupontok");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE menupontok SET menupont=?, szulo=?, url=?, oldal=?, cimszoveg=?, szerkoldal=?, aktiv=?, menuterulet=?, sorrend=? WHERE id=?');
        $stmt->bind_param('sssssssssi', $_POST['menupont'], $_POST['szulo'], $_POST['url'], $_POST['oldal'], $_POST['cimszoveg'], $_POST['szerkoldal'], $_POST['aktiv'], $_POST['menuterulet'], $_POST['sorrend'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Menüpont szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $RootPath/beallitasok?menupontok");
        }
    }
    elseif($_GET["action"] == "delete")
    {
        $record = null;
        //mySQLConnect("DELETE FROM menupontok WHERE id = $record;");
        //mySQLConnect("DELETE FROM jogosultsagok WHERE menupont = $record;");
    }
}