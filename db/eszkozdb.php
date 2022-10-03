<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO eszkozok (modell, sorozatszam, tulajdonos, varians, megjegyzes, raktar, letrehozo) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssss', $_POST['modell'], $_POST['sorozatszam'], $_POST['tulajdonos'], $_POST['varians'], $_POST['megjegyzes'], $_POST['raktar'], $_SESSION[getenv('SESSION_NAME').'id']);
        $stmt->execute();

        $last_id = mysqli_insert_id($con);


        if($eszkoztipus)
        {
            if($eszkoztipus == "aktiv")
            {
                $stmt = $con->prepare('INSERT INTO aktiveszkozok (eszkoz, mac, poe, ssh, web, portszam, uplinkportok, szoftver, letrehozo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssssssss', $last_id, $_POST['mac'], $_POST['poe'], $_POST['ssh'], $_POST['web'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $_SESSION[getenv('SESSION_NAME').'id']);
                $stmt->execute();
            }

            if($eszkoztipus == "soho")
            {
                $stmt = $con->prepare('INSERT INTO sohoeszkozok (eszkoz, mac, lanportok, wanportok, szoftver, letrehozo) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssss', $last_id, $_POST['mac'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $_SESSION[getenv('SESSION_NAME').'id']);
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
                include("./db/portdb.php");
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "update")
    {
        $timestamp = date('Y-m-d H:i:s');
        $eszkoz = $_POST['id'];

        mySQLConnect("INSERT INTO eszkozok_history (eszkozid, modell, sorozatszam, tulajdonos, varians, megjegyzes, leadva, hibas, raktar, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje)
            SELECT id, modell, sorozatszam, tulajdonos, varians, megjegyzes, leadva, hibas, raktar, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje
            FROM eszkozok
            WHERE id = $eszkoz");
        
        $stmt = $con->prepare('UPDATE eszkozok SET modell=?, sorozatszam=?, tulajdonos=?, varians=?, megjegyzes=?, leadva=?, hibas=?, raktar=?, utolsomodosito=?, utolsomodositasideje=? WHERE id=?');
        $stmt->bind_param('ssssssssssi', $_POST['modell'], $_POST['sorozatszam'], $_POST['tulajdonos'], $_POST['varians'], $_POST['megjegyzes'], $_POST['leadva'], $_POST['hibas'], $_POST['raktar'], $_SESSION[getenv('SESSION_NAME').'id'], $timestamp, $eszkoz);
        $stmt->execute();

        if($eszkoztipus)
        {
            if($eszkoztipus == "aktiv")
            {
                mySQLConnect("INSERT INTO aktiveszkozok_history (akteszkid, eszkoz, mac, poe, ssh, web, portszam, uplinkportok, szoftver, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje)
                    SELECT id, eszkoz, mac, poe, ssh, web, portszam, uplinkportok, szoftver, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje
                    FROM aktiveszkozok
                    WHERE eszkoz = $eszkoz");

                $stmt = $con->prepare('UPDATE aktiveszkozok SET mac=?, poe=?, ssh=?, web=?, portszam=?, uplinkportok=?, szoftver=?, utolsomodosito=?, utolsomodositasideje=? WHERE eszkoz=?');
                $stmt->bind_param('sssssssssi', $_POST['mac'], $_POST['poe'], $_POST['ssh'], $_POST['web'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $_SESSION[getenv('SESSION_NAME').'id'], $timestamp, $_POST['id']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "soho")
            {
                mySQLConnect("INSERT INTO sohoeszkozok_history (sohoeszkozid, eszkoz, wanportok, lanportok, mac, szoftver, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje)
                    SELECT id, eszkoz, wanportok, lanportok, mac, szoftver, letrehozo, utolsomodosito, letrehozasideje, utolsomodositasideje
                    FROM sohoeszkozok
                    WHERE eszkoz = $eszkoz");
                
                $stmt = $con->prepare('UPDATE sohoeszkozok SET mac=?, lanportok=?, wanportok=?, szoftver=?, utolsomodosito=?, utolsomodositasideje=? WHERE eszkoz=?');
                $stmt->bind_param('ssssssi', $_POST['mac'], $_POST['portszam'], $_POST['uplinkportok'], $_POST['szoftver'], $_SESSION[getenv('SESSION_NAME').'id'], $timestamp, $_POST['id']);
                $stmt->execute();
            }

            elseif($eszkoztipus == "telefonkozpont")
            {
                $stmt = $con->prepare('UPDATE telefonkozpontok SET nev=? WHERE eszkoz=?');
                $stmt->bind_param('ss', $_POST['nev'], $_POST['id']);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Eszköz szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}