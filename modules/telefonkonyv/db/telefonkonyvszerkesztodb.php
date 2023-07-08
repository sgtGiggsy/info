<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_POST['id'])
    {
        $id = $_POST['id'];

        mySQLConnect("DELETE FROM telefonkonyvadminok WHERE felhasznalo = $id;");

        if(isset($_POST['csoport']))
        {
            foreach($_POST['csoport'] as $csoportid)
            {
                $stmt = $con->prepare('INSERT INTO telefonkonyvadminok (felhasznalo, csoport) VALUES (?, ?)');
                $stmt->bind_param('ss', $id, $csoportid);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A változás beküldése sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
        }

        $menusorszamok = array("87", "88", "89");
        $iras = 2;
        $olvasas = 2;

        foreach($menusorszamok as $menusorszam)
        {        
            mySQLConnect("DELETE FROM jogosultsagok WHERE felhasznalo = $id AND menupont = $menusorszam;");

            if(isset($_POST['csoport']))
            {
                $stmt = $con->prepare('INSERT INTO jogosultsagok (felhasznalo, menupont, iras, olvasas) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $id, $menusorszam, $iras, $olvasas);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A változás beküldése sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
            }
        }
    }
}