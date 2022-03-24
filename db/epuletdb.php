<?php

if(isset($irhat) && $irhat)
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
        $stmt = $con->prepare('INSERT INTO epuletek (szam, telephely, nev, tipus) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['szam'], $_POST['telephely'], $_POST['nev'], $_POST['tipus']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Épület hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE epuletek SET szam=?, telephely=?, nev=?, tipus=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['szam'], $_POST['telephely'], $_POST['nev'], $_POST['tipus'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Épület szerkesztése sikertelen!<br></h2>";
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