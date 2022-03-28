<?php
$con = mySQLConnect(false);
if(isset($mindir) && $mindir)
{
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if(isset($_POST['mode']))
    {
        $nev = $_POST['mode'];
    }
    else
    {
        $nev = "név";
    }

    $stmt = $con->prepare('INSERT INTO teszt (nev) VALUES (?)');
    $stmt->bind_param('s', $nev);
    $stmt->execute();
    if(mysqli_errno($con) != 0)
    {
        echo "<h2>Gyártó hozzáadása sikertelen!<br></h2>";
        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
    }

    if($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE gyartok SET nev=? WHERE id=?');
        $stmt->bind_param('si', $_POST['nev'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Gyártó szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $RootPath/gyartoklistaja");
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}
else
{
    $j = "jogosultsaghiany";
    $stmt = $con->prepare('INSERT INTO teszt (nev) VALUES (?)');
    $stmt->bind_param('s', $j);
    $stmt->execute();
    if(mysqli_errno($con) != 0)
    {
        echo "<h2>Gyártó hozzáadása sikertelen!<br></h2>";
        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
    }
}