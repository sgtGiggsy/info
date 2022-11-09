<?php

if(isset($mindir) && $mindir)
{
    $con = mySQLConnect(false);
    $user = $_SESSION[getenv('SESSION_NAME').'id'];
    $timestamp = date('Y-m-d H:i:s');

    foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
    }

    if($_GET["action"] == "update" && $_GET["tipus"] == "switch") // Verziókövetés kész
    {
        $tulportid = $_POST['csatlakozas'];
        $helyiportid = $_POST['portid'];
        $switchportid = $_POST['id'];

        $modif_id = modId("2", "port", $helyiportid);

        if($tulportid)
        {
            $iftulportswitch = mySQLConnect("SELECT portok.id AS id FROM portok INNER JOIN switchportok ON switchportok.port = portok.id WHERE portok.id = $tulportid;");
        }
        $iftulportchange = mySQLConnect("SELECT id FROM portok WHERE csatlakozas = $helyiportid;");

        mySQLConnect("INSERT INTO switchportok_history (switchportid, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid)
            SELECT id, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid
            FROM switchportok
            WHERE id = $switchportid");

        mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, modid)
            SELECT id, port, csatlakozo, csatlakozas, modid
            FROM portok
            WHERE id = $helyiportid");
        
        $stmt = $con->prepare('UPDATE switchportok SET mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=?, modid=? WHERE id=?');
        $stmt->bind_param('sssssssi', $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $modif_id, $_POST['id']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=?, csatlakozas=?, modid=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['port'], $_POST['csatlakozo'], $_POST['csatlakozas'], $modif_id, $_POST['portid']);
        $stmt->execute();

        if($tulportid && mysqli_num_rows($iftulportswitch) == 1)
        {
            $modif_id = modId("2", "port", $tulportid);

            mySQLConnect("INSERT INTO switchportok_history (switchportid, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid)
                SELECT id, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid
                FROM switchportok
                WHERE port = $tulportid");

            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, modid)
                SELECT id, port, csatlakozo, csatlakozas, modid
                FROM portok
                WHERE id = $tulportid");
            
            $stmt = $con->prepare('UPDATE portok SET csatlakozo=?, csatlakozas=?, utolsomodosito=?, utolsomodositasideje=? WHERE id=?');
            $stmt->bind_param('ssssi', $_POST['csatlakozo'], $_POST['portid'], $user, $timestamp, $tulportid);
            $stmt->execute();

            $stmt = $con->prepare('UPDATE switchportok SET mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=?, utolsomodosito=?, utolsomodositasideje=? WHERE port=?');
            $stmt->bind_param('ssssssssi', $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $user, $timestamp, $tulportid);
            $stmt->execute();
        }

        if(!$tulportid && mysqli_num_rows($iftulportchange) == 1)
        {
            $oldportid = mysqli_fetch_assoc($iftulportchange)['id'];

            $modif_id = modId("2", "port", $oldportid);
            
            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, modid)
                SELECT id, port, csatlakozo, csatlakozas, modid
                FROM portok
                WHERE id = $oldportid");
            
            $null = null;
            $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
            $stmt->bind_param('ssi', $null, $modif_id, $oldportid);
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

    if($_GET["action"] == "update" && $_GET["tipus"] == "soho") // Verziókövetés kész
    {
        $sohid = $_POST['id'];
        $portid = $_POST['portid'];

        $modif_id = modId("2", "port", $portid);

        mySQLConnect("INSERT INTO sohoportok_history (sohoportid, eszkoz, port, sebesseg, modid)
            SELECT id, eszkoz, port, sebesseg, modid
            FROM sohoportok
            WHERE id = $sohid");

        mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, modid)
            SELECT id, port, csatlakozo, csatlakozas, modid
            FROM portok
            WHERE id = $portid");        

        $stmt = $con->prepare('UPDATE sohoportok SET sebesseg=?, modid=? WHERE id=?');
        $stmt->bind_param('ssi', $_POST['sebesseg'], $modif_id, $_POST['id']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=?, csatlakozas=?, modid=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['port'], $_POST['csatlakozo'], $_POST['csatlakozas'], $modif_id, $_POST['portid']);
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

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "switch") // Verziókövetés kész
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
                $modif_id = modId("1", "port", $last_id);
                mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

                $stmt = $con->prepare('INSERT INTO switchportok (eszkoz, port, sebesseg, modid) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $_POST['eszkoz'], $last_id, $_POST['accportsebesseg'], $modif_id);
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
                $modif_id = modId("1", "port", $last_id);
                mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

                $stmt = $con->prepare('INSERT INTO switchportok (eszkoz, port, sebesseg, tipus, modid) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssss', $_POST['eszkoz'], $last_id, $_POST['uplportsebesseg'], $tipus, $modif_id);
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
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "soho") // Verziókövetés kész
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
                $modif_id = modId("1", "port", $last_id);
                mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

                $stmt = $con->prepare('INSERT INTO sohoportok (eszkoz, port, sebesseg, modid) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $_POST['eszkoz'], $last_id, $_POST['accportsebesseg'], $modif_id);
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
                $modif_id = modId("1", "port", $last_id);
                mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

                $stmt = $con->prepare('INSERT INTO sohoportok (eszkoz, port, sebesseg, modid) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $_POST['eszkoz'], $last_id, $_POST['uplportsebesseg'], $modif_id);
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
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "vegpont") // Verziókövetés kész
    {
        for($i = $_POST['kezdoport']; $i <= $_POST['zaroport']; $i++)
        {
            $portsorszam = str_pad($i, $_POST['nullara'], "0", STR_PAD_LEFT);
            $port = $_POST['portelotag'] . $portsorszam;

            $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
            $stmt->bind_param('ss', $port, $_POST['csatlakozo']);
            $stmt->execute();

            $last_id = mysqli_insert_id($con);
            $modif_id = modId("1", "port", $last_id);
            mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

            $stmt = $con->prepare('INSERT INTO vegpontiportok (epulet, port, modid) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $_POST['epulet'], $last_id, $modif_id);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            $targeturl = $RootPath . "/" . $_GET['kuldooldal'] . "/" . $_GET['kuldooldalid'] . "?action=edit";
            header("Location: $targeturl");
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "rack") // Verziókövetés kész
    {
        for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
        {
            $modif_id = modId("2", "port", $i);
            mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $i");
            
            $stmt = $con->prepare('INSERT INTO rackportok (rack, port, modid) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $_POST['rack'], $i, $modif_id);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "helyiseg") // Verziókövetés kész
    {
        for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
        {
            $modif_id = modId("2", "port", $i);
            mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $i");
            
            mySQLConnect("INSERT INTO vegpontiportok_history (vegpontiportid, epulet, port, helyiseg, modid)
                SELECT id, epulet, port, helyiseg, modid
                FROM vegpontiportok
                WHERE port = $i");
            
            $stmt = $con->prepare('UPDATE vegpontiportok SET helyiseg=?, modid=? WHERE port=?');
            $stmt->bind_param('ssi', $_POST['helyiseg'], $modif_id, $i);
            $stmt->execute();
        }

        if(mysqli_errno($con) != 0)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "telefonkozpont") // Verziókövetés kész
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
                    $modif_id = modId("1", "port", $last_id);
                    mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

                    $stmt = $con->prepare('INSERT INTO tkozpontportok (eszkoz, port, modid) VALUES (?, ?, ?)');
                    $stmt->bind_param('sss', $_POST['eszkoz'], $last_id, $modif_id);
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
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "new" && $_GET["tipus"] == "mediakonverter") // Verziókövetés kész
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
        $modif_id = modId("1", "port", $last_id);
        mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

        $stmt = $con->prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany, modid) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $eszkoz, $last_id, $konvertermodell['transzpsebesseg'], $konvertermodell['transzpszabvany'], $modif_id);
        $stmt->execute();

        // LAN OLDALI PORT
        $lanszabvany = $konvertermodell['lanszabvany'];
        $lanszabvany = mySQLConnect("SELECT * FROM atviteliszabvanyok WHERE id = $lanszabvany;");
        $lanszabvany = mysqli_fetch_assoc($lanszabvany)['nev'];

        $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
        $stmt->bind_param('ss', $lanszabvany, $konvertermodell['lancsatlakozo']);
        $stmt->execute();

        $last_id = mysqli_insert_id($con);
        $modif_id = modId("1", "port", $last_id);
        mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

        $stmt = $con->prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany, modid) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $eszkoz, $last_id, $konvertermodell['lansebesseg'], $konvertermodell['lanszabvany'], $modif_id);
        $stmt->execute();

        redirectToKuldo();
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
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "delete")
    {
    }
}