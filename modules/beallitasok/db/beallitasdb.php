<?php

if(isset($irhat) && $irhat)
{
    $beallsql = new MySQLHandler();
    $beallsql->KeepAlive();

    $beallsql->Prepare('UPDATE beallitasok SET ertek=? WHERE nev=?');
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $value = NULL;
        }

        $beallsql->Run($value, $key);
        if(!$beallsql->siker)
        {
            echo "<h2>Az érték megváltoztatása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    //header("Location: $backtosender");
}