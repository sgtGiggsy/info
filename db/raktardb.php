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

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO raktarak (nev, alakulat, helyiseg) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_POST['nev'], $_POST['alakulat'], $_POST['helyiseg']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Rack hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE raktarak SET nev=?, alakulat=?, helyiseg=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['nev'], $_POST['alakulat'], $_POST['helyiseg'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Rack szerkesztése sikertelen!<br></h2>";
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