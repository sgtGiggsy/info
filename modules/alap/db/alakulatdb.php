<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO alakulatok (nev, rovid) VALUES (?, ?)');
        $stmt->bind_param('ss', $_POST['nev'], $_POST['rovid']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Alakulat hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE alakulatok SET nev=?, rovid=? WHERE id=?');
        $stmt->bind_param('ssi', $_POST['nev'], $_POST['rovid'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Alakulat szerkesztése sikertelen!<br></h2>";
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