<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO menupontok (menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend, gyujtourl, gyujtocimszoveg, gyujtooldal, dburl, dboldal, apiurl) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssssssss', $_POST['menupont'], $_POST['szulo'], $_POST['url'], $_POST['oldal'], $_POST['cimszoveg'], $_POST['szerkoldal'], $_POST['aktiv'], $_POST['menuterulet'], $_POST['sorrend'], $_POST['gyujtourl'], $_POST['gyujtocimszoveg'], $_POST['gyujtooldal'], $_POST['dburl'], $_POST['dboldal'], $_POST['apiurl']);
        $stmt->execute();

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A menüpont hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "update")
    {        
        foreach($_POST['menupont'] as $key)
        {
            if(count($key) > 1)
            {
                $stmt = $con->prepare('UPDATE menupontok SET menupont=?, szulo=?, url=?, oldal=?, cimszoveg=?, szerkoldal=?, aktiv=?, menuterulet=?, sorrend=?, gyujtourl=?, gyujtocimszoveg=?, gyujtooldal=?, dburl=?, dboldal=?, apiurl=? WHERE id=?');
                $stmt->bind_param('sssssssssssssssi', $key['menupont'], $key['szulo'], $key['url'], $key['oldal'], $key['cimszoveg'], $key['szerkoldal'], $key['aktiv'], $key['menuterulet'], $key['sorrend'], $key['gyujtourl'], $key['gyujtocimszoveg'], $key['gyujtooldal'], $key['dburl'], $key['dboldal'], $key['apiurl'], $key['id']);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A menüpont szerkesztése sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}