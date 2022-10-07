<?php

if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>'" /></head><?php
}
else
{
    if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
    {
        echo "<h2>Nincs jogosultságod a beállítások szerkesztésére!</h2>" ?>
        <head><meta http-equiv="refresh" content="1; URL='<?=$RootPath?>'" /></head><?php
    }
    else
    {
        $con = mySQLConnect(false);
        foreach($_POST as $key => $value)
        {
            if ($value == "NULL" || $value == "")
            {
                $value = NULL;
            }

            $stmt = $con->prepare('UPDATE beallitasok SET ertek=? WHERE nev=?');
            $stmt->bind_param('ss', $value, $key);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>Az érték megváltoztatása sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
        }
        //header("Location: $backtosender");
    }
}
?>