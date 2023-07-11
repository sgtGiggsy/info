<?php

if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>'" /></head><?php
}
else
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        if ($stmt = $con->prepare('INSERT INTO kitoltesek (felhasznalo) VALUES (?)'))
        {
            $stmt->bind_param('s', $_SESSION[getenv('SESSION_NAME').'id']);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>Vizsga elindítása sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
            else
            {
                $con = mySQLConnect("SELECT * FROM kitoltesek ORDER BY id DESC LIMIT 1");
                $vizsga = mysqli_fetch_assoc($con);
                $_SESSION[getenv('SESSION_NAME').'vizsga'] = $vizsga['id'];
                echo "<h2>Vizsga sikeresen elindítva</h2>";
                ?><head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>/vizsga'" /></head><?php
            }
        }
        else
        {
            echo "<h2>Vizsga elindítása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        if ($stmt = $con->prepare('UPDATE kitoltesek SET felhasznalo=? WHERE id=?'))
        {
            $stmt->bind_param('si', $_SESSION[getenv('SESSION_NAME').'id'], $_POST['id']);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A vizsga szerkesztése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
            else
            {
                echo "<h2>A vizsga szerkesztése sikeres</h2>";
                ?><head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>/vizsga'" /></head><?php
            }
        }
        else
        {
            echo "<h2>A vizsga szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}