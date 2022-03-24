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
        $stmt = $con->prepare('INSERT INTO helyisegek (epulet, emelet, helyisegszam, helyisegnev) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['epulet'], $_POST['emelet'], $_POST['helyisegszam'], $_POST['helyisegnev']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Helyiség hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE helyisegek SET epulet=?, emelet=?, helyisegszam=?, helyisegnev=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['epulet'], $_POST['emelet'], $_POST['helyisegszam'], $_POST['helyisegnev'], $_POST['id']);
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