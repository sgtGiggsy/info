<?php

if(isset($mindir) && $mindir)
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
        for($i = $_POST['kezdohelyisegszam']; $i <= $_POST['zarohelyisegszam']; $i++)
        {
            $helyisegszam = str_pad($i, $_POST['szamjegyszam'], "0", STR_PAD_LEFT);
            $epulet = $_POST['epulet'];
            $helyiseg = mySQLConnect("SELECT * FROM helyisegek WHERE epulet = $epulet AND helyisegszam = $helyisegszam");
            if(mysqli_num_rows($helyiseg) == 0)
            {
                $stmt = $con->prepare('INSERT INTO helyisegek (epulet, emelet, helyisegszam) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $epulet, $_POST['emelet'], $helyisegszam);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Helyiségek hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
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