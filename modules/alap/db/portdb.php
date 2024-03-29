<?php

if(isset($csoportir) && $csoportir)
{
    $con = mySQLConnect(false);
    $null = null;
    $timestamp = date('Y-m-d H:i:s');

    purifyPost();

    if($_GET["action"] == "update" && $_GET["tipus"] == "switch") // Verziókövetés kész
    {
        $tulportid = $_POST['csatlakozas'];
        $helyiportid = $_POST['portid'];
        $switchportid = $_POST['id'];

        $modif_id = modId("2", "port", $helyiportid);

        if($tulportid)
        {
            // Mivel switchre direktben nem lesz kötve soho eszköz, itt csak switcheket, és médiakonvertereket ellenőrzünk
            $iftulportswitch = mySQLConnect("SELECT portok.id AS id
                FROM portok
                    LEFT JOIN switchportok ON switchportok.port = portok.id
                    LEFT JOIN mediakonverterportok ON mediakonverterportok.port = portok.id
                WHERE switchportok.port = $tulportid OR mediakonverterportok.port = $tulportid;");
        }
        $iftulportchange = mySQLConnect("SELECT id FROM portok WHERE csatlakozas = $helyiportid;");

        mySQLConnect("INSERT INTO switchportok_history (switchportid, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid)
            SELECT id, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid
            FROM switchportok
            WHERE id = $switchportid");

        mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
            SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
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
            foreach($iftulportchange as $nullid)
            {
                $modif_id = modId("2", "port", $tulportid);
                $idtonull = $nullid['id'];
                mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                FROM portok
                WHERE id = $idtonull");
                
                $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
                $stmt->bind_param('ssi', $null, $modif_id, $idtonull);
                $stmt->execute();
            }

            mySQLConnect("INSERT INTO switchportok_history (switchportid, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid)
                SELECT id, eszkoz, port, mode, vlan, sebesseg, nev, allapot, tipus, modid
                FROM switchportok
                WHERE port = $tulportid");

            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                FROM portok
                WHERE id = $tulportid");
            
            $stmt = $con->prepare('UPDATE portok SET csatlakozo=?, csatlakozas=? WHERE id=?');
            $stmt->bind_param('ssi', $_POST['csatlakozo'], $_POST['portid'], $tulportid);
            $stmt->execute();

            $stmt = $con->prepare('UPDATE switchportok SET mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=? WHERE port=?');
            $stmt->bind_param('ssssssi', $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $tulportid);
            $stmt->execute();
        }

        if(!$tulportid && mysqli_num_rows($iftulportchange) == 1)
        {
            $oldportid = mysqli_fetch_assoc($iftulportchange)['id'];

            $modif_id = modId("2", "port", $oldportid);
            
            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                FROM portok
                WHERE id = $oldportid");
            
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

        mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
            SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
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
                $portpre = "";
                if($_POST['accportpre'] != "-")
                {
                    $portpre = $_POST['accportpre'];
                }
                $port =  $portpre . $i; //$portsorszam;

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
                $portpre = "";
                if($_POST['uplportpre'] != "-")
                {
                    $portpre = $_POST['uplportpre'];
                }
                $port = $portpre . $i; //$portsorszam;

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

    elseif($_GET["action"] == "generate" && $_GET["tipus"] == "transzport") // Verziókövetés kész
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

            $stmt = $con->prepare('INSERT INTO transzportportok (epulet, port, fizikaireteg, modid) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $_POST['epulet'], $last_id, $_POST['fizikaireteg'], $modif_id);
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

    elseif($_GET["action"] == "transzporttarsitas") // Verziókövetés kész
    {
        for($i = 1; $i < 1000; $i++)
        {
            $jelenportmod = null;
            if(isset($_POST['portid-'.$i]))
            {
                $helyiportid = $_POST['portid-'.$i];
                $oldszomszed = $_POST['oldszomszed-'.$i];
                $szomszed = $_POST['szomszed-'.$i];
                $hurok = $_POST['hurok-'.$i];

                // Csak akkor kell írnunk az adatbázist, ha volt szomszédos port, de nem egyezik meg a jelenlegivel
                if($oldszomszed != $szomszed)
                {
                    //// Először a helyi port állapotát frissítjük
                    // A jelen állapot mentése
                    mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                        SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                        FROM portok
                        WHERE id = $helyiportid");

                    // A tényleges módosítás folyamata
                    $modif_id = modId("2", "port", $helyiportid);
                    $jelenportmod = $modif_id;
                    $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
                    $stmt->bind_param('ssi', $szomszed, $modif_id, $helyiportid);
                    $stmt->execute();

                    //// Ezt követően a távoli port állapotát frissítjük
                    //A jelen állapot mentése
                    mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                        SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                        FROM portok
                        WHERE id = $szomszed");
                    
                    // A tényleges módosítás folyamata
                    $modif_id = modId("2", "port", $szomszed);
                    $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
                    $stmt->bind_param('ssi', $helyiportid, $modif_id, $szomszed);
                    $stmt->execute();
                    
                    // Ha volt szomszéd, eltávolítjuk róla a jelenlegi portot
                    if($oldszomszed)
                    {
                        // Elsőként megnézzük, hogy az adott szomszédporthoz nem csatlakozott-e ebben a frissítési kérésben egy másik port
                        $aktualisport = mySQLConnect("SELECT csatlakozas FROM portok WHERE id = $oldszomszed");
                        $aktualisport = mysqli_fetch_assoc($aktualisport)['csatlakozas'];
                        
                        // Ha a távoli portra továbbra is a jelen port van csatlakoztatva, nullázás
                        if($aktualisport != $helyiportid)
                        {
                            //A jelen állapot mentése
                            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                                SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                                FROM portok
                                WHERE id = $oldportid");

                            // A tényleges módosítás folyamata
                            $modif_id = modId("2", "port", $oldportid);
                            $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
                            $stmt->bind_param('ssi', $null, $modif_id, $oldportid);
                            $stmt->execute();
                        }
                    }
                }

                // Itt következik az áthurkolás
                atHurkolas($helyiportid, $hurok, $con, $jelenportmod);
            }
            else
            {
                break;
            }
        }
    }

    elseif($_GET["action"] == "vegponthurkolas") // Verziókövetés kész
    {
        $elemszam = count($_POST['portid']);
        for($i = 1; $i < $elemszam; $i++)
        {
            // Itt következik az áthurkolás
            atHurkolas($_POST['portid'][$i], $_POST['hurok'][$i], $con, null);
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

        for($i = 1; $i <= $_POST['transzportszam']; $i++)
        {
            $portnev = $transzpszabvany . "/" . $i;
            $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
            $stmt->bind_param('ss', $portnev, $konvertermodell['transzpcsatlakozo']);
            $stmt->execute();

            $last_id = mysqli_insert_id($con);
            $modif_id = modId("1", "port", $last_id);
            mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

            $stmt = $con->prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany, modid) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $eszkoz, $last_id, $konvertermodell['transzpsebesseg'], $konvertermodell['transzpszabvany'], $modif_id);
            $stmt->execute();
        }

        // LAN OLDALI PORT
        $lanszabvany = $konvertermodell['lanszabvany'];
        $lanszabvany = mySQLConnect("SELECT * FROM atviteliszabvanyok WHERE id = $lanszabvany;");
        $lanszabvany = mysqli_fetch_assoc($lanszabvany)['nev'];

        for($i = 1; $i <= $_POST['accessportszam']; $i++)
        {
            $portnev = $lanszabvany . "/" . $i;

            $stmt = $con->prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
            $stmt->bind_param('ss', $lanszabvany, $konvertermodell['lancsatlakozo']);
            $stmt->execute();

            $last_id = mysqli_insert_id($con);
            $modif_id = modId("1", "port", $last_id);
            mySQLConnect("UPDATE portok SET modid = $modif_id WHERE id = $last_id");

            $stmt = $con->prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany, modid) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $eszkoz, $last_id, $konvertermodell['lansebesseg'], $konvertermodell['lanszabvany'], $modif_id);
            $stmt->execute();
        }

        redirectToKuldo();
    }

    elseif($_GET["action"] == "update" && $_GET["tipus"] == "mediakonverter")
    {
        $tulportid = $_POST['csatlakozas'];
        $helyiportid = $_POST['portid'];
        $switchportid = $_POST['id'];

        $modif_id = modId("2", "port", $helyiportid);

        if($tulportid)
        {
            $iftulportswitch = mySQLConnect("SELECT portok.id AS id
                FROM portok
                    LEFT JOIN switchportok ON switchportok.port = portok.id
                    LEFT JOIN mediakonverterportok ON mediakonverterportok.port = portok.id
                    LEFT JOIN sohoportok ON sohoportok.port = portok.id
                WHERE switchportok.port = $tulportid OR mediakonverterportok.port = $tulportid OR sohoportok.port = $tulportid;");
        }
        $iftulportchange = mySQLConnect("SELECT id FROM portok WHERE csatlakozas = $helyiportid;");
        
        mySQLConnect("INSERT INTO mediakonverterportok_history (mediakonverterportid, eszkoz, port, sebesseg, szabvany, modid)
            SELECT id, eszkoz, port, sebesseg, szabvany, modid
            FROM mediakonverterportok
            WHERE id = $switchportid");

        mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
            SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
            FROM portok
            WHERE id = $helyiportid");
        
        $stmt = $con->prepare('UPDATE mediakonverterportok SET sebesseg=?, szabvany=?, modid=? WHERE id=?');
        $stmt->bind_param('sssi', $_POST['sebesseg'], $_POST['szabvany'], $modif_id, $_POST['id']);
        $stmt->execute();

        $stmt = $con->prepare('UPDATE portok SET port=?, csatlakozo=?, csatlakozas=?, modid=? WHERE id=?');
        $stmt->bind_param('ssssi', $_POST['port'], $_POST['csatlakozo'], $_POST['csatlakozas'], $modif_id, $_POST['portid']);
        $stmt->execute();

        if($tulportid && mysqli_num_rows($iftulportswitch) == 1)
        {
            foreach($iftulportchange as $nullid)
            {
                $modif_id = modId("2", "port", $tulportid);
                $idtonull = $nullid['id'];
                mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                    SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                    FROM portok
                    WHERE id = $idtonull");
                
                $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
                $stmt->bind_param('ssi', $null, $modif_id, $idtonull);
                $stmt->execute();
            }

            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                FROM portok
                WHERE id = $tulportid");
            
            $stmt = $con->prepare('UPDATE portok SET csatlakozas=? WHERE id=?');
            $stmt->bind_param('si', $_POST['portid'], $tulportid);
            $stmt->execute();
        }

        if(!$tulportid && mysqli_num_rows($iftulportchange) == 1)
        {
            $oldportid = mysqli_fetch_assoc($iftulportchange)['id'];

            $modif_id = modId("2", "port", $oldportid);
            
            mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
                SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
                FROM portok
                WHERE id = $oldportid");
            
            $stmt = $con->prepare('UPDATE portok SET csatlakozas=?, modid=? WHERE id=?');
            $stmt->bind_param('ssi', $null, $modif_id, $oldportid);
            $stmt->execute();
        }

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

    elseif($_GET["action"] == "reset")
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
            $stmt->bind_param('ssi', $null, $modif_id, $i);
            $stmt->execute();

            mySQLConnect("INSERT INTO rackportok_history (rackportid, rack, port, modid)
                SELECT id, rack, port, modid
                FROM rackportok
                WHERE port = $i");

            $stmt = $con->prepare('DELETE FROM rackportok WHERE port=?');
            $stmt->bind_param('i', $i);
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

    elseif($_GET["action"] == "delete")
    {
    }
}