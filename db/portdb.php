<?php

if(isset($mindir) && $mindir)
{
    $con = mySQLConnect(false);
    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "update" && $_GET["tipus"] == "switch")
    {
        $tulportid = $_POST['csatlakozas'];
        $helyiportid = $_POST['portid'];
        if($tulportid)
        {
            $iftulportswitch = mySQLConnect("SELECT portok.id AS id FROM portok INNER JOIN switchportok ON switchportok.port = portok.id WHERE portok.id = $tulportid;");
        }
        $iftulportchange = mySQLConnect("SELECT id FROM portok WHERE csatlakozas = $helyiportid;");

        $stmt = $con->prepare('UPDATE switchportok SET mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=? WHERE id=?');
        $stmt->bind_param('ssssssi', $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $_POST['id']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=?, csatlakozas=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['port'], $_POST['csatlakozo'], $_POST['csatlakozas'], $_POST['portid']);
        $stmt->execute();

        if($tulportid && mysqli_num_rows($iftulportswitch) == 1)
        {
            $stmt = $con->prepare('UPDATE portok SET csatlakozo=?, csatlakozas=? WHERE id=?');
            $stmt->bind_param('ssi', $_POST['csatlakozo'], $_POST['portid'], $_POST['csatlakozas']);
            $stmt->execute();

            $stmt = $con->prepare('UPDATE switchportok SET mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=? WHERE port=?');
            $stmt->bind_param('ssssssi', $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $_POST['csatlakozas']);
            $stmt->execute();
        }

        if(!$tulportid && mysqli_num_rows($iftulportchange) == 1)
        {
            $oldportid = mysqli_fetch_assoc($iftulportchange)['id'];
            $null = null;
            $stmt = $con->prepare('UPDATE portok SET csatlakozas=? WHERE id=?');
            $stmt->bind_param('si', $null, $oldportid);
            $stmt->execute();
        }

        /*
        $eszkozport = $_POST['portid'];
        $vegpontport = $_POST['epuletport'];

        $kozoskeres = mySQLConnect("SELECT kapcsolat
            FROM kapcsolatportok 
            WHERE port = $eszkozport AND kapcsolat IN (SELECT kapcsolat
                FROM kapcsolatportok 
                WHERE port = $vegpontport);");
        
        if(mysqli_num_rows($kozoskeres) == 0)
        {
            $fizikaireteg = 1;
            $nev = "$eszkozport - $vegpontport";
            $stmt = $con->prepare('INSERT INTO kapcsolatok (fizikaireteg, nev) VALUES (?, ?)');
            $stmt->bind_param('ss', $fizikaireteg, $nev);
            $stmt->execute();

            $last_id = mysqli_insert_id($con);

            $stmt = $con->prepare('INSERT INTO kapcsolatportok (kapcsolat, port) VALUES (?, ?)');
            $stmt->bind_param('ss', $last_id, $_POST['portid']);
            $stmt->execute();

            $stmt = $con->prepare('INSERT INTO kapcsolatportok (kapcsolat, port) VALUES (?, ?)');
            $stmt->bind_param('ss', $last_id, $_POST['epuletport']);
            $stmt->execute();
        }
        else
        {

        } */

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A port szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    if($_GET["action"] == "update" && $_GET["tipus"] == "soho")
    {
        $stmt = $con->prepare('UPDATE sohoportok SET sebesseg=? WHERE id=?');
        $stmt->bind_param('si', $_POST['sebesseg'], $_POST['id']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=?, csatlakozas=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['port'], $_POST['csatlakozo'], $_POST['csatlakozas'], $_POST['portid']);
        $stmt->execute();

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A port szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "switch")
    {
        $tipus = "1"; // Tipus 1 = uplink, Tipus 2 = access
        $csatlakozo = "1";
        if($_POST['accportpre'])
        {
            for($i = $_POST['kezdoacc']; $i <= $_POST['zaroacc']; $i++)
            {
                //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
                $port = $_POST['accportpre'] . $i; //$portsorszam;

                $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
                $stmt->bind_param('ss', $port, $csatlakozo);
                $stmt->execute();

                $last_id = mysqli_insert_id($con);

                $stmt = $con->prepare('INSERT INTO switchportok (eszkoz, port, sebesseg) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $_POST['eszkoz'], $last_id, $_POST['accportsebesseg']);
                $stmt->execute();
            }
        }

        if($_POST['uplportpre'])
        {
            for($i = $_POST['kezdoupl']; $i <= $_POST['zaroupl']; $i++)
            {
                //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
                $port = $_POST['uplportpre'] . $i; //$portsorszam;

                $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
                $stmt->bind_param('ss', $port, $csatlakozo);
                $stmt->execute();

                $last_id = mysqli_insert_id($con);

                $stmt = $con->prepare('INSERT INTO switchportok (eszkoz, port, sebesseg, tipus) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $_POST['eszkoz'], $last_id, $_POST['uplportsebesseg'], $tipus);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "soho")
    {
        $tipus = "1"; // Tipus 1 = uplink, Tipus 2 = access
        $csatlakozo = "1";
        if($_POST['accportpre'])
        {
            for($i = $_POST['kezdoacc']; $i <= $_POST['zaroacc']; $i++)
            {
                //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
                $port = $_POST['accportpre'] . $i; //$portsorszam;

                $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
                $stmt->bind_param('ss', $port, $csatlakozo);
                $stmt->execute();

                $last_id = mysqli_insert_id($con);

                $stmt = $con->prepare('INSERT INTO sohoportok (eszkoz, port, sebesseg) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $_POST['eszkoz'], $last_id, $_POST['accportsebesseg']);
                $stmt->execute();
            }
        }

        if($_POST['uplportpre'])
        {
            for($i = $_POST['kezdoupl']; $i <= $_POST['zaroupl']; $i++)
            {
                
                //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
                $port = $_POST['uplportpre'] . $i; //$portsorszam;

                $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
                $stmt->bind_param('ss', $port, $csatlakozo);
                $stmt->execute();

                $last_id = mysqli_insert_id($con);

                $stmt = $con->prepare('INSERT INTO sohoportok (eszkoz, port, sebesseg) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $_POST['eszkoz'], $last_id, $_POST['uplportsebesseg']);
                $stmt->execute();
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "vegpont")
    {
        for($i = $_POST['kezdoport']; $i <= $_POST['zaroport']; $i++)
        {
            $portsorszam = str_pad($i, $_POST['nullara'], "0", STR_PAD_LEFT);
            $port = $_POST['portelotag'] . $portsorszam;

            $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
            $stmt->bind_param('ss', $port, $_POST['csatlakozo']);
            $stmt->execute();

            $last_id = mysqli_insert_id($con);

            $stmt = $con->prepare('INSERT INTO vegpontiportok (epulet, port) VALUES (?, ?)');
            $stmt->bind_param('ss', $_POST['epulet'], $last_id);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "rack")
    {
        for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
        {
            $stmt = $con->prepare('INSERT INTO rackportok (rack, port) VALUES (?, ?)');
            $stmt->bind_param('ss', $_POST['rack'], $i);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "helyiseg")
    {
        for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
        {
            $stmt = $con->prepare('UPDATE vegpontiportok SET helyiseg=? WHERE port=?');
            $stmt->bind_param('si', $_POST['helyiseg'], $i);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "telefonkozpont")
    {
        if($_POST['portpre'])
        {
            for($i = $_POST['kezdoharmadik']; $i <= $_POST['zaroharmadik']; $i++)
            {
                for($j = $_POST['kezdonegyedik']; $j <= $_POST['zaronegyedik']; $j++)
                {
                    $port = $_POST['portpre'] . $i . "-" . $j; //$portsorszam;
                    //echo "$port <br>";
                    $stmt = $con->prepare('INSERT INTO portok (port) VALUES (?)');
                    $stmt->bind_param('s', $port);
                    $stmt->execute();

                    $last_id = mysqli_insert_id($con);

                    $stmt = $con->prepare('INSERT INTO tkozpontportok (eszkoz, port) VALUES (?, ?)');
                    $stmt->bind_param('ss', $_POST['eszkoz'], $last_id);
                    $stmt->execute();
                }
            }
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "new" && $_GET["tipus"] == "mediakonverter")
    {
        $eszkozmodell = mySQLConnect("SELECT modell FROM eszkozok WHERE id = $eszkoz");
        $modell = mysqli_fetch_assoc($eszkozmodell)['modell'];
        $konvertermodell = mySQLConnect("SELECT * FROM mediakonvertermodellek WHERE modell = $modell");
        $konvertermodell = mysqli_fetch_assoc($konvertermodell);

        // TRANSZPORT PORT
        $transzpszabvany = $konvertermodell['transzpszabvany'];
        $transzpszabvany = mySQLConnect("SELECT * FROM atviteliszabvanyok WHERE id = $transzpszabvany;");
        $transzpszabvany = mysqli_fetch_assoc($transzpszabvany)['nev'];

        $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
        $stmt->bind_param('ss', $transzpszabvany, $konvertermodell['transzpcsatlakozo']);
        $stmt->execute();

        $last_id = mysqli_insert_id($con);

        $stmt = $con->prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $eszkoz, $last_id, $konvertermodell['transzpsebesseg'], $konvertermodell['transzpszabvany']);
        $stmt->execute();

        // LAN OLDALI PORT
        $lanszabvany = $konvertermodell['lanszabvany'];
        $lanszabvany = mySQLConnect("SELECT * FROM atviteliszabvanyok WHERE id = $lanszabvany;");
        $lanszabvany = mysqli_fetch_assoc($lanszabvany)['nev'];

        $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
        $stmt->bind_param('ss', $lanszabvany, $konvertermodell['lancsatlakozo']);
        $stmt->execute();

        $last_id = mysqli_insert_id($con);

        $stmt = $con->prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $eszkoz, $last_id, $konvertermodell['lansebesseg'], $konvertermodell['lanszabvany']);
        $stmt->execute();
    }

    elseif($_GET["action"] == "update" && $_GET["tipus"] == "mediakonverter")
    {
        /* Erre valószínű nem lesz szükség, majd később eldöntöm
        
        // TRANSZPORT PORT
        $szabvany = $_POST['szabvany'];
        $atviteliszabvany = mySQLConnect("SELECT * FROM atviteliszabvanyok WHERE id = $szabvany;");
        $szabvany = mysqli_fetch_assoc($atviteliszabvany)['nev'];

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=? WHERE id=?');
        $stmt->bind_param('sss', $szabvany, $_POST['transzpcsatlakozo'], $_POST['transzportid']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE mediakonverterportok SET sebesseg=?, szabvany=? WHERE id=?');
        $stmt->bind_param('sss', $_POST['transzpsebesseg'], $_POST['szabvany'], $_POST['transzportkonvid']);
        $stmt->execute();

        // LAN OLDALI PORT
        $lanszabvany = $_POST['lanszabvany'];
        $lanszabvany = mySQLConnect("SELECT * FROM atviteliszabvanyok WHERE id = $lanszabvany;");
        $lanszabvany = mysqli_fetch_assoc($lanszabvany)['nev'];

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=? WHERE id=?');
        $stmt->bind_param('sss', $lanszabvany, $_POST['lancsatlakozo'], $_POST['lanportid']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE mediakonverterportok SET sebesseg=?, szabvany=? WHERE id=?');
        $stmt->bind_param('sss', $_POST['lansebesseg'], $_POST['lanszabvany'], $_POST['lanportkonvid']);
        $stmt->execute();*/
    }

    elseif($_GET["action"] == "new")
    {
    }

    elseif($_GET["action"] == "update")
    {
        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=? WHERE id=?');
        $stmt->bind_param('ssi', $_POST['port'], $_POST['csatlakozo'], $_POST['portid']);
        $stmt->execute();

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A port szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "clearportassign")
    {
        if(!isset($eszkoz))
        {
            $eszkoz = $_POST['eszkoz'];
            $reload = true;
        }
        mySQLConnect("UPDATE portok SET csatlakozas = null WHERE csatlakozas IN (
            SELECT csatlakozas
            FROM portok
                LEFT JOIN switchportok ON portok.id = switchportok.port
                LEFT JOIN sohoportok ON portok.id = sohoportok.port
            WHERE switchportok.eszkoz = $eszkoz OR sohoportok.eszkoz = $eszkoz)");

        if(@$reload)
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}