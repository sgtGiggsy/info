<?php

if(isset($irhat) && $irhat)
{
    purifyPost();

    $adminfelhid = $_POST['felhasznalo'];

    $vizsgaadmindel = new MySQLHandler("DELETE FROM vizsgak_adminok WHERE felhasznalo = ? AND vizsga = ?", $adminfelhid, $vizsgaid);

    if($_POST['alapadmin'])
    {
        $vizsgaadmin = new MySQLHandler("INSERT INTO vizsgak_adminok (felhasznalo, vizsga, beallitasok, kerdesek, adminkijeloles, ujkornyitas) VALUES (?, ?, ?, ?, ?, ?)",
            $adminfelhid, $vizsgaid, $_POST['beallitasok'], $_POST['kerdesek'], $_POST['adminkijeloles'], $_POST['ujkornyitas']);

        if(!$vizsgaadmin->siker)
        {
            echo "<h2>A változás beküldése sikertelen!<br></h2>";
        }
    }
}