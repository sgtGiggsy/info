<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "addnew")
    {
        $key = bin2hex(random_bytes(32));
        
        $stmt = $con->prepare('INSERT INTO api (menupont, apikey, jogosultsagszint, aktiv) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $_POST['menupont'], $key, $_POST['jogosultsagszint'], $_POST['aktiv']);
        $stmt->execute();

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Az API kulcs hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "updateapi")
    {
        $stmt = $con->prepare('UPDATE api SET menupont=?, jogosultsagszint=?, aktiv=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['menupont'], $_POST['jogosultsagszint'], $_POST['aktiv'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Az API kulcs szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}