<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect();

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO hkrgepek (gepnev, felhasznalo) VALUES (?, ?)');
        $stmt->bind_param('ss', $_POST['gepnev'], $_POST['felhasznalo']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>HKR gép hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE hkrgepek SET gepnev=?, felhasznalo=? WHERE id=?');
        $stmt->bind_param('ssi', $_POST['gepnev'], $_POST['felhasznalo'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>HKR gép szerkesztése sikertelen!<br></h2>";
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