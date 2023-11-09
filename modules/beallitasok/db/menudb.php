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
        $postid = 1;
        $ujsor = true;
        do
        {
            if(isset($_POST['id-'.$postid]))
            {
                $stmt = $con->prepare('UPDATE menupontok SET menupont=?, szulo=?, url=?, oldal=?, cimszoveg=?, szerkoldal=?, aktiv=?, menuterulet=?, sorrend=?, gyujtourl=?, gyujtocimszoveg=?, gyujtooldal=?, dburl=?, dboldal=?, apiurl=? WHERE id=?');
                $stmt->bind_param('sssssssssssssssi', $_POST['menupont-'.$postid], $_POST['szulo-'.$postid], $_POST['url-'.$postid], $_POST['oldal-'.$postid], $_POST['cimszoveg-'.$postid], $_POST['szerkoldal-'.$postid], $_POST['aktiv-'.$postid], $_POST['menuterulet-'.$postid], $_POST['sorrend-'.$postid], $_POST['gyujtourl-'.$postid], $_POST['gyujtocimszoveg-'.$postid], $_POST['gyujtooldal-'.$postid], $_POST['dburl-'.$postid], $_POST['dboldal-'.$postid], $_POST['apiurl-'.$postid], $_POST['id-'.$postid]);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A menüpont szerkesztése sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
            else
            {
                $ujsor = false;
            }
            $postid++;
        } while ($ujsor);
    }

    elseif($_GET["action"] == "delete")
    {
    }
}