<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    mySQLConnect("DELETE FROM vizsgak_engedelyezettek WHERE vizsga = $vizsgaid;");

    $darab = count($_POST['engedelyezett']);

    for($i = 0; $i < $darab; $i++)
    {
        $stmt = $con->prepare('INSERT INTO vizsgak_engedelyezettek (felhasznalo, vizsga) VALUES (?, ?)');
        $stmt->bind_param('ss', $_POST['engedelyezett'][$i], $vizsgaid);
        $stmt->execute();
    }
}