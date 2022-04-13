<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL")
        {
            $_POST[$key] = NULL;
        }
    }

    if(isset($_GET['csvimport']))
    {
        $csv = csvToArray($finalfile);
        $errorcount = 0;

        if(isset($csv[0]['kozpont']) && isset($csv[0]['szam']) && (isset($csv[0]['cimke']) || isset($csv[0]['címke'])) && isset($csv[0]['tipus']) && isset($csv[0]['port']) && isset($csv[0]['jog']))
        {
            $kozpont = $csv[0]['kozpont'];
            $telefontipusok = mySQLConnect("SELECT id, nev FROM telefonkeszulektipusok;");
            $kozpontportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port
                FROM portok
                    INNER JOIN tkozpontportok ON portok.id = tkozpontportok.port
                    INNER JOIN eszkozok ON tkozpontportok.eszkoz = eszkozok.id
                    INNER JOIN telefonkozpontok ON telefonkozpontok.eszkoz = eszkozok.id
                WHERE telefonkozpontok.nev = '$kozpont';");

            $con = mySQLConnect(false);
            

            foreach($csv as $x)
            {
                $szam = $x['szam'];
                $port = str_replace(" ", "", $x['port']);
                $megjegyzes = $tkozpontport = $tipus = null;
                foreach($telefontipusok as $teltip)
                {
                    if($teltip['nev'] == $x['tipus'])
                    {
                        $tipus = $teltip['id'];
                        break;
                    }
                }

                foreach($kozpontportok as $kozpontport)
                {
                    if($kozpontport['port'] == $port)
                    {
                        $tkozpontport = $kozpontport['id'];
                        break;
                    }
                }

                if(!$tkozpontport)
                {
                    $megjegyzes = $port;
                }

                if(isset($x['cimke']))
                {
                    $cimke = trimCimke($x['cimke']);
                }
                else
                {
                    $cimke = trimCimke($x['címke']);
                }

                $telefonszam = mySQLconnect("SELECT szam FROM telefonszamok WHERE szam = $szam;");
                if(mysqli_num_rows($telefonszam) == 0)
                {
                    $stmt = $con->prepare('INSERT INTO telefonszamok (szam, cimke, tipus, tkozpontport, jog, megjegyzes) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->bind_param('ssssss', $szam, $cimke, $tipus, $tkozpontport, $x['jog'], $megjegyzes);
                    $stmt->execute();
                    if(mysqli_errno($con) != 0)
                    {
                        $errorcount++;
                        echo "<h2>Telefonszám hozzáadása sikertelen!<br></h2>";
                        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                    }
                }
                else
                {
                    $stmt = $con->prepare('UPDATE telefonszamok SET cimke=?, tipus=?, tkozpontport=?, jog=?, megjegyzes=? WHERE szam=?');
                    $stmt->bind_param('ssssss', $cimke, $tipus, $tkozpontport, $x['jog'], $megjegyzes, $szam);
                    $stmt->execute();
                    if(mysqli_errno($con) != 0)
                    {
                        $errorcount++;
                        echo "<h2>Telefonszám szerkesztése sikertelen!<br></h2>";
                        echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                    }
                }
            }
        }
        else
        {
            $errorcount++;
            echo "<h2 style='color:red'>HIBA!<br>A megadott CSV fájl nem tartalmazza valamelyik szükséges oszlopot.</h2>";
        }

        if($errorcount == 0)
        {
            echo "<h2>A CSV fájl tartalmának importálása hibák nélkül zajlott le.</h2>";
        }
    }

    elseif($_GET["action"] == "new")
    {
        $stmt = $con->prepare('INSERT INTO menupontok (menupont, szulo, url, oldal, cimszoveg, szerkoldal, aktiv, menuterulet, sorrend) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssss', $_POST['menupont'], $_POST['szulo'], $_POST['url'], $_POST['oldal'], $_POST['cimszoveg'], $_POST['szerkoldal'], $_POST['aktiv'], $_POST['menuterulet'], $_POST['sorrend']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Menüpont hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $RootPath/beallitasok?menupontok");
        }
    }

    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE menupontok SET menupont=?, szulo=?, url=?, oldal=?, cimszoveg=?, szerkoldal=?, aktiv=?, menuterulet=?, sorrend=? WHERE id=?');
        $stmt->bind_param('sssssssssi', $_POST['menupont'], $_POST['szulo'], $_POST['url'], $_POST['oldal'], $_POST['cimszoveg'], $_POST['szerkoldal'], $_POST['aktiv'], $_POST['menuterulet'], $_POST['sorrend'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Menüpont szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $RootPath/beallitasok?menupontok");
        }
    }
    elseif($_GET["action"] == "delete")
    {
    }
}