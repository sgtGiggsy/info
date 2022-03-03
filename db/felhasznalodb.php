<?php

if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>'" /></head><?php
}
elseif(!isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) || $_SESSION[getenv('SESSION_NAME').'jogosultsag'] < 50)
{
    echo "<h2>Nincs jogosultsagod a műveletre!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>'" /></head><?php
}
else
{
    $con = mySQLConnect(false);

    if($_GET["action"] == "new")
    {
    }
    elseif($_GET["action"] == "update")
    {
        if ($stmt = $con->prepare('UPDATE felhasznalok SET jogosultsag=? WHERE id=?'))
        {
            $stmt->bind_param('si', $_POST['jogosultsag'], $_POST['id']);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A felhasználó szerkesztése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
            else
            {
                echo "<h2>A felhasználó szerkesztése sikeres</h2>";
                ?><head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>/felhasznalok'" /></head><?php
            }
        }
        else
        {
            echo "<h2>A felhasználó szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}