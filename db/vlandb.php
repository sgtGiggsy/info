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
        $stmt = $con->prepare('INSERT INTO vlanok (id, nev, leiras kceh) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['id'], $_POST['nev'], $_POST['leiras'], $_POST['kceh']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Vlan hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE vlanok SET id=?, nev=?, leiras=?, kceh=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['id'], $_POST['nev'], $_POST['leiras'], $_POST['kceh'], $_POST['origid']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Vlan szerkesztése sikertelen!<br></h2>";
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