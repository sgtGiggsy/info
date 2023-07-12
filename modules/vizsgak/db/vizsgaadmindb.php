<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    $adminfelhid = $_POST['felhasznalo'];

    mySQLConnect("DELETE FROM vizsgak_adminok WHERE felhasznalo = $adminfelhid AND vizsga = $vizsgaid;");

    if($_POST['alapadmin'])
    {
        $stmt = $con->prepare('INSERT INTO vizsgak_adminok (felhasznalo, vizsga, beallitasok, kerdesek, adminkijeloles, ujkornyitas) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $adminfelhid, $vizsgaid, $_POST['beallitasok'], $_POST['kerdesek'], $_POST['adminkijeloles'], $_POST['ujkornyitas']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A változás beküldése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
}