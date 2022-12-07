<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO epuletek (szam, telephely, nev, tipus, megjegyzes) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $_POST['szam'], $_POST['telephely'], $_POST['nev'], $_POST['tipus'], $_POST['megjegyzes']);
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
        $stmt = $con->prepare('UPDATE epuletek SET szam=?, telephely=?, nev=?, tipus=?, megjegyzes=?, naprakesz=? WHERE id=?');
        $stmt->bind_param('ssssssi', $_POST['szam'], $_POST['telephely'], $_POST['nev'], $_POST['tipus'], $_POST['megjegyzes'], $_POST['naprakesz'], $_POST['id']);
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