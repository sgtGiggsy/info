<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    purifyPost();

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO eszkozok (modell, sorozatszam, tulajdonos, varians, megjegyzes, raktar) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $_POST['modell'], $_POST['sorozatszam'], $_POST['tulajdonos'], $_POST['varians'], $_POST['megjegyzes'], $_POST['raktar']);
        try{
            $stmt->execute();
        }
        catch(Exception $e)
        {
            echo $e;
            die;
        }
        

        $last_id = mysqli_insert_id($con);
        $modif_id = modId("1", "eszkoz", $last_id);
        mySQLConnect("UPDATE eszkozok SET modid = $modif_id WHERE id = $last_id");

        if($eszkoztipus)
        {
            if($eszkoztipus == "aktiveszkoz")
            {
                $stmt = $con->prepare('INSERT INTO aktiveszkozok (eszkoz, mac, poe, ssh, web, portszam, uplinkportok, szoftver, modid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssssssss', $last_id, $_POST['mac'], $_POST['poe'], $_POST['ssh'], $_POST['web'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $modif_id);
                $stmt->execute();
            }

            if($eszkoztipus == "sohoeszkoz")
            {
                $stmt = $con->prepare('INSERT INTO sohoeszkozok (eszkoz, mac, lanportok, wanportok, szoftver, modid) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssss', $last_id, $_POST['mac'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $modif_id);
                $stmt->execute();
            }

            elseif($eszkoztipus == "telefonkozpont")
            {
                $stmt = $con->prepare('INSERT INTO telefonkozpontok (eszkoz, nev) VALUES (?, ?)');
                $stmt->bind_param('ss', $last_id, $_POST['nev']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "mediakonverter")
            {
                $eszkoz = $last_id;
                include("././modules/alap/db/portdb.php");
            }

            elseif($eszkoztipus == "simkartya")
            {
                $stmt = $con->prepare('INSERT INTO simkartyak (eszkoz, telefonszam, pinkod, pukkod, tipus, felhasznaloszam, modid) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssssss', $last_id, $_POST['telefonszam'], $_POST['pinkod'], $_POST['pukkod'], $_POST['tipus'], $_POST['felhasznaloszam'], $modif_id);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "update")
    {
        $timestamp = date('Y-m-d H:i:s');
        $eszkoz = $_POST['id'];

        $modif_id = modId("2", "eszkoz", $eszkoz);

        mySQLConnect("INSERT INTO eszkozok_history (eszkozid, modell, sorozatszam, tulajdonos, varians, megjegyzes, leadva, hibas, raktar, modid)
            SELECT id, modell, sorozatszam, tulajdonos, varians, megjegyzes, leadva, hibas, raktar, modid
            FROM eszkozok
            WHERE id = $eszkoz");
        
        $stmt = $con->prepare('UPDATE eszkozok SET modell=?, sorozatszam=?, tulajdonos=?, varians=?, megjegyzes=?, leadva=?, hibas=?, raktar=?, modid=? WHERE id=?');
        $stmt->bind_param('sssssssssi', $_POST['modell'], $_POST['sorozatszam'], $_POST['tulajdonos'], $_POST['varians'], $_POST['megjegyzes'], $_POST['leadva'], $_POST['hibas'], $_POST['raktar'], $modif_id, $eszkoz);
        $stmt->execute();

        if($eszkoztipus)
        {
            if($eszkoztipus == "aktiveszkoz")
            {
                mySQLConnect("INSERT INTO aktiveszkozok_history (akteszkid, eszkoz, mac, poe, ssh, web, portszam, uplinkportok, szoftver, modid)
                    SELECT id, eszkoz, mac, poe, ssh, web, portszam, uplinkportok, szoftver, modid
                    FROM aktiveszkozok
                    WHERE eszkoz = $eszkoz");

                $stmt = $con->prepare('UPDATE aktiveszkozok SET mac=?, poe=?, ssh=?, web=?, portszam=?, uplinkportok=?, szoftver=?, modid=? WHERE eszkoz=?');
                $stmt->bind_param('ssssssssi', $_POST['mac'], $_POST['poe'], $_POST['ssh'], $_POST['web'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $modif_id, $_POST['id']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "sohoeszkoz")
            {
                mySQLConnect("INSERT INTO sohoeszkozok_history (sohoeszkozid, eszkoz, wanportok, lanportok, mac, szoftver, modid)
                    SELECT id, eszkoz, wanportok, lanportok, mac, szoftver, modid
                    FROM sohoeszkozok
                    WHERE eszkoz = $eszkoz");
                
                $stmt = $con->prepare('UPDATE sohoeszkozok SET mac=?, lanportok=?, wanportok=?, szoftver=?, modid=? WHERE eszkoz=?');
                $stmt->bind_param('sssssi', $_POST['mac'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $modif_id, $_POST['id']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "telefonkozpont")
            {
                $stmt = $con->prepare('UPDATE telefonkozpontok SET nev=? WHERE eszkoz=?');
                $stmt->bind_param('ss', $_POST['nev'], $_POST['id']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "simkartya")
            {
                mySQLConnect("INSERT INTO simkartyak_history (simid, eszkoz, telefonszam, pinkod, pukkod, tipus, felhasznaloszam, modid)
                    SELECt id, eszkoz, telefonszam, pinkod, pukkod, tipus, felhasznaloszam, modid
                    FROM simkartyak
                    WHERE eszkoz = $eszkoz");

                $stmt = $con->prepare('UPDATE simkartyak SET telefonszam=?, pinkod=?, pukkod=?, tipus=?, felhasznaloszam=?, modid=? WHERE eszkoz=?');
                $stmt->bind_param('ssssssi', $_POST['telefonszam'], $_POST['pinkod'], $_POST['pukkod'], $_POST['tipus'], $_POST['felhasznaloszam'], $modif_id, $_POST['id']);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}