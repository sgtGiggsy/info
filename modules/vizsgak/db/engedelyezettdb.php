<?php

if(isset($irhat) && $irhat)
{
    purifyPost();

    $torol = new MySQLHandler("DELETE FROM vizsgak_engedelyezettek WHERE vizsga = ?;", $vizsgaid);

    $darab = null;
    try
    {
        if($_POST['engedelyezett'])
            $darab = count($_POST['engedelyezett']);
    }
    catch(Exception $e)
    {
    }

    if($darab > 0)
    {
        $engedelyezettek = new MySQLHandler();
        $engedelyezettek->Prepare('INSERT INTO vizsgak_engedelyezettek (felhasznalo, vizsga) VALUES (?, ?)');
        for($i = 0; $i < $darab; $i++)
        {
            $engedelyezettek->Run(array($_POST['engedelyezett'][$i], $vizsgaid));
        }
    }
}