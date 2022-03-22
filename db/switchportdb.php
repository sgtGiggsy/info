<?php

if(!(isset($mindir) && $mindir))
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    $backtosender = $_SERVER['HTTP_REFERER'];
    if($_GET["action"] == "new")
    {
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE switchportok SET port=?, mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=? WHERE id=?');
        $stmt->bind_param('sssssssi', $_POST['port'], $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A port szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "generate")
    {
        for($i = $_POST['kezdoacc']; $i <= $_POST['zaroacc']; $i++)
        {
            //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
            $port = $_POST['accportpre'] . $i; //$portsorszam;

            $stmt = $con->prepare('INSERT INTO switchportok (eszkoz, port, sebesseg) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $_POST['eszkoz'], $port, $_POST['accportsebesseg']);
            $stmt->execute();
        }

        for($i = $_POST['kezdoupl']; $i <= $_POST['zaroupl']; $i++)
        {
            //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
            $port = $_POST['uplportpre'] . $i; //$portsorszam;

            $tipus = "1"; // Tipus 1 = uplink, Tipus 2 = access

            $stmt = $con->prepare('INSERT INTO switchportok (eszkoz, port, sebesseg, tipus) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $_POST['eszkoz'], $port, $_POST['uplportsebesseg'], $tipus);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
    //header("Location: $RootPath/menuszerkeszt");
}