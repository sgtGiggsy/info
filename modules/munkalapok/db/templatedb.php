<?php
if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "addnew")
    {
        $stmt = $con->prepare('INSERT INTO munkalaptemplateek (szoveg) VALUES (?)');
        $stmt->bind_param('s', $_POST['szoveg']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A template hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE munkalaptemplateek SET szoveg=? WHERE id=?');
        $stmt->bind_param('si', $_POST['szoveg'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A template szerkesztése sikertelen!<br></h2>";
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
elseif($csoportir)
{
    if($_GET["action"] == "hasznalt")
    {
        if($_GET['tempid'] && is_numeric($_GET['tempid']))
        {
            $tempid = $_GET['tempid'];
            mySQLConnect("UPDATE munkalaptemplateek SET hasznalva = hasznalva + 1 WHERE id = $tempid;");
        }
        else
        {
            echo "Mivel próbálkozol?";
        }
    }
}
