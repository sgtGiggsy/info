<?php

if(isset($irhat) && $irhat)
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
?>