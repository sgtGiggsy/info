<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    $ldapstring = explode(";", $_POST['ldapstring']);

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO szervezetek (nev, rovid, statusz) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $_POST['nev'], $_POST['rovid'], $_POST['statusz']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>szervezet hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE szervezetek SET nev=?, rovid=?, statusz=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['nev'], $_POST['rovid'], $_POST['statusz'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>szervezet szerkesztése sikertelen!<br></h2>";
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

    if(mysqli_errno($con) == 0)
    {
        if(isset($_POST['id']))
        {
            $pid = $_POST['id'];
            mySQLConnect("DELETE FROM szervezetldap WHERE szervezet = $pid;");
        }

        foreach($ldapstring as $needle)
        {
            if($needle != "")
            {
                $stmt = $con->prepare('INSERT INTO szervezetldap (szervezet, needle) VALUES (?, ?)');
                $stmt->bind_param('ss', $_POST['id'], trim($needle));
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>LDAP hozzáadása sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
        }
    }
}