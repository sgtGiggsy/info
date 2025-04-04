<?php

if(isset($csoportir) && $csoportir)
{
    $portdb = new MySQLHandler();
    $portdb->KeepAlive();
    $timestamp = timeStampForSQL();

    purifyPost();

    if($_GET["action"] == "update")
    {
        $portdb->Query('UPDATE portok SET port=?, csatlakozo=? WHERE id=?',
            $_POST['port'], $_POST['csatlakozo'], $_POST['id']);
        
        if($_POST['jelenport'] && $_POST['csatlakozas'] != $_POST['jelenport'])
        {
            $portok = portDBsorrend($_POST['id'], $_POST['jelenport']);
            $portdb->Query('DELETE FROM port_kapcsolatok WHERE port_1=? AND port_2=?', $portok[0], $portok[1]);
        }

        if($_POST['csatlakozas'])
        {
            $portok = portDBsorrend($_POST['id'], $_POST['csatlakozas']);
            $portdb->Query('INSERT INTO port_kapcsolatok (port_1, port_2) VALUES (?, ?)',
                $portok[0], $portok[1]);
        }
        
        if($_GET["tipus"] == "switch")
        {
            $portdb->Query('UPDATE switchportok SET mode=?, vlan=?, sebesseg=?, nev=?, allapot=?, tipus=?, modid=? WHERE port=?',
            $_POST['mode'], $_POST['vlan'], $_POST['sebesseg'], $_POST['nev'], $_POST['allapot'], $_POST['tipus'], $modif_id, $_POST['id']);
        }
        elseif($_GET["tipus"] == "soho")
        {
            $portdb->Query('UPDATE sohoportok SET sebesseg=? WHERE port=?', $_POST['sebesseg'], $_POST['id']);
        }
        elseif($_GET["tipus"] == "mediakonverter")
        {
            $portdb->Query('UPDATE mediakonverterportok SET sebesseg=?, szabvany=? WHERE port=?',
                $_POST['sebesseg'], $_POST['szabvany'], $_POST['id']);
        }
    }

    elseif($_GET["action"] == "generate")
    {
        $tipport = new MySQLHandler();
        $tipport->KeepAlive();
        $portdb->Prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');

        if($_GET["tipus"] == "switch")
        {
            $tipus = "1"; // Tipus 1 = uplink, Tipus 2 = access
            $csatlakozo = "1";
            $tipport->Prepare('INSERT INTO switchportok (eszkoz, port, sebesseg) VALUES (?, ?, ?)');

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

                    $portdb->Run($port, $csatlakozo);
                    $tipport->Run($_POST['eszkoz'], $portdb->last_insert_id, $_POST['accportsebesseg']);
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

                    $portdb->Run($port, $csatlakozo);
                    $tipport->Run($_POST['eszkoz'], $portdb->last_insert_id, $_POST['accportsebesseg']);
                }
            }
        }
        elseif($_GET["tipus"] == "soho")
        {
            $tipus = "1"; // Tipus 1 = uplink, Tipus 2 = access
            $csatlakozo = "1";
            $tipport->Prepare('INSERT INTO sohoportok (eszkoz, port, sebesseg) VALUES (?, ?, ?)');
            if($_POST['accportpre'])
            {
                for($i = $_POST['kezdoacc']; $i <= $_POST['zaroacc']; $i++)
                {
                    //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $port = $_POST['accportpre'] . $i; //$portsorszam;
    
                    $portdb->Run($port, $csatlakozo);
                    $tipport->Run($_POST['eszkoz'], $portdb->last_insert_id, $_POST['accportsebesseg']);
                }
            }
    
            if($_POST['uplportpre'])
            {
                for($i = $_POST['kezdoupl']; $i <= $_POST['zaroupl']; $i++)
                {
                    
                    //$portsorszam = str_pad($i, 2, "0", STR_PAD_LEFT);
                    $port = $_POST['uplportpre'] . $i; //$portsorszam;
    
                    $portdb->Run($port, $csatlakozo);
                    $tipport->Run($_POST['eszkoz'], $portdb->last_insert_id, $_POST['uplportsebesseg']);
                }
            }
        }

        elseif($_GET["tipus"] == "vegpont")
        {
            $tipport->Prepare('INSERT INTO vegpontiportok (epulet, port) VALUES (?, ?)');
            for($i = $_POST['kezdoport']; $i <= $_POST['zaroport']; $i++)
            {
                $portsorszam = str_pad($i, $_POST['nullara'], "0", STR_PAD_LEFT);
                $port = $_POST['portelotag'] . $portsorszam;

                $portdb->Run($port, $_POST['csatlakozo']);
                $tipport->Run($_POST['epulet'], $portdb->last_insert_id);
            }

            // Kérdéses, hogy kell-e
            $targeturl = $RootPath . "/" . $_GET['kuldooldal'] . "/" . $_GET['kuldooldalid'] . "?action=edit";
            header("Location: $targeturl");
        }

        elseif($_GET["tipus"] == "transzport")
        {
            $portconnection = new MySQLHandler();
            $portconnection->Prepare('INSERT INTO port_kapcsolatok (port_1, port_2) VALUES (?, ?)');
            $tipport->Prepare('INSERT INTO transzportportok (epulet, port, fizikaireteg) VALUES (?, ?, ?)');
            for($i = $_POST['kezdoport']; $i <= $_POST['zaroport']; $i++)
            {
                $portsorszam = str_pad($i, $_POST['nullara'], "0", STR_PAD_LEFT);
                $port1_nev = $_POST['portelotag_1'] . $portsorszam;
                $port2_nev = $_POST['portelotag_2'] . $portsorszam;

                $portdb->Run($port1_nev, $_POST['csatlakozo']);
                $port1_id = $portdb->last_insert_id;
                $tipport->Run($_POST['epulet_1'], $port1_id, $_POST['fizikaireteg']);

                $portdb->Run($port2_nev, $_POST['csatlakozo']);
                $port2_id = $portdb->last_insert_id;
                $tipport->Run($_POST['epulet_2'], $port2_id, $_POST['fizikaireteg']);

                //Mivel a beszúrás sorrendje miatt a $port1_id mindenképp kisebb, mint a $port2_id, ezért nem kell a portDBsorrend() függvény
                $portconnection->Run($port1_id, $port2_id);
            }

            // Kérdéses, hogy kell-e
            $targeturl = $RootPath . "/" . $_GET['kuldooldal'] . "/" . $_GET['kuldooldalid'] . "?action=edit";
            header("Location: $targeturl");
        }

        elseif($_GET["tipus"] == "rack")
        {
            /*
                ! A jelen ellenőrzési megoldással csak az épület portjait ellenőrizzük, de ez valószínűleg elég is.
                ! Csak olyankor okozhat gondot, ha két, épületen belüli rack közötti transzport kapcsolat van.
            */
            $tipport->Prepare('INSERT INTO rackportok (rack, port) VALUES (?, ?)');
            $verify = new MySQLHandler('SELECT port AS id
                    FROM vegpontiportok
                    WHERE epulet = ?
            UNION ALL
                SELECT port AS id
                    FROM transzportportok
                    WHERE epulet = ?;', $_POST['epulet'], $_POST['epulet']);
            $epuletportids = $verify->AsArray();
            for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
            {
                if(in_array($i, $epuletportids))
                {
                    $tipport->Run($_POST['rack'], $i);
                }
            }
        }

        elseif($_GET["tipus"] == "helyiseg")
        {
            $portdb->Prepare('UPDATE vegpontiportok SET helyiseg=? WHERE port=?');
            $verify = new MySQLHandler('SELECT port AS id
                    FROM vegpontiportok
                    WHERE epulet = ?;', $_POST['epulet']);
            $epuletportids = $verify->AsArray();
            for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
            {
                if(in_array($i, $epuletportids))
                {
                    $portdb->Run($_POST['helyiseg'], $i);
                }
            }
        }

        elseif($_GET["tipus"] == "telefonkozpont")
        {
            $tipport->Prepare('INSERT INTO tkozpontportok (eszkoz, port) VALUES (?, ?)');
            for($i = $_POST['kezdoharmadik']; $i <= $_POST['zaroharmadik']; $i++)
            {
                for($j = $_POST['kezdonegyedik']; $j <= $_POST['zaronegyedik']; $j++)
                {
                    $port = $_POST['portpre'] . $i . "-" . $j; //$portsorszam;
                    $portdb->Run($port, null);
                    $tipport->Run($_POST['eszkoz'], $portdb->last_insert_id);
                }
            }
        }

        if(!$portdb->siker || !$swportdb->siker)
        {
            echo "<h2>Portok hozzáadása sikertelen!<br></h2>";
        }
        else
        {
            redirectToKuldo();
        }
    }

    elseif($_GET["action"] == "new")
    {
        $tipport = new MySQLHandler();
        $tipport->KeepAlive();
        $portdb->Prepare('INSERT INTO portok (port, csatlakozo) VALUES (?, ?)');
    
        if($_GET["tipus"] == "mediakonverter")
        {
            $tipport->Prepare('INSERT INTO mediakonverterportok (eszkoz, port, sebesseg, szabvany) VALUES (?, ?, ?, ?)');
            
            //* $eszkoz --> a portdb fájl include-olva van az eszkozdb.php-ban, onnan érkezik
            $konvmod = new MySQLHandler('SELECT eszkozok.modell AS modell, atviteliszabvanyok.nev AS transzpszabvany, mediakonvertermodellek.transzpszabvany AS transzpszabvanyid, mediakonvertermodellek.lanszabvany AS lanszabvanyid,
                    lanszabvany.nev AS lanszabvany, transzpsebesseg, lansebesseg, transzpcsatlakozo, lancsatlakozo
                FROM mediakonvertermodellek
                    INNER JOIN eszkozok ON eszkozok.modell = mediakonvertermodellek.modell
                    INNER JOIN atviteliszabvanyok ON mediakonvertermodellek.transzpszabvany = atviteliszabvanyok.id
                    INNER JOIN atviteliszabvanyok lanszabvany ON mediakonvertermodellek.lanszabvany = lanszabvany.id
                WHERE eszkozok.id = ?;', $eszkoz);
            $konvertermodell = $konvmod->Fetch();

            // TRANSZPORT PORT
            $transzpszabvany = $konvertermodell['transzpszabvany'];

            for($i = 1; $i <= $_POST['transzportszam']; $i++)
            {
                $portnev = $transzpszabvany . "/" . $i;
                $portdb->Run($portnev, $konvertermodell['transzpcsatlakozo']);
                $tipport->Run($eszkoz, $portdb->last_insert_id, $konvertermodell['transzpsebesseg'], $konvertermodell['transzpszabvanyid']);
            }

            // LAN OLDALI PORT
            $lanszabvany = $konvertermodell['lanszabvany'];

            for($i = 1; $i <= $_POST['accessportszam']; $i++)
            {
                $portnev = $lanszabvany . "/" . $i;

                $portdb->Run($portnev, $konvertermodell['lancsatlakozo']);
                $tipport->Run($eszkoz, $portdb->last_insert_id, $konvertermodell['lansebesseg'], $konvertermodell['lanszabvanyid']);
            }
        }
    }

    elseif($_GET["action"] == "transzporttarsitas")
    {
        //! Törölve. Az új megoldás szerint a transzport portok már generáláskor épülethez és egymáshoz kötve jönnek létre
    }

    elseif($_GET["action"] == "clearportassign")
    {
        if(!isset($eszkoz))
        {
            $eszkoz = $_POST['eszkoz'];
            $reload = true;
        }

        $portdb->Query('DELETE FROM port_kapcsolatok WHERE port_1 = ? OR port_2 = ?', $eszkoz, $eszkoz);

        if(@$reload)
        {
            redirectToKuldo();
        }
    }

    elseif($mindir && $_GET["action"] == "reset")
    {
        /*
            ! TODO Valami további védelmet is tenni ide
            ! Jelenleg csak azt vizsgálja meg, hogy az adott portok az épületben vannak-e.
            ! De gyakorlatban lehet ez is elég, mert legfeljebb csak egy épület portjait resetelődhetnek, és valószínű az is a cél
        */
        $portdb->Prepare('UPDATE vegpontiportok SET helyiseg=? WHERE port=?');
        $tipport->Prepare('DELETE FROM rackportok WHERE port=?');
        $verify = new MySQLHandler('SELECT port AS id FROM vegpontiportok WHERE epulet = ?;', $_POST['epulet']);
        $epuletportids = $verify->AsArray();
        for($i = $_POST['elsoport']; $i <= $_POST['utolsoport']; $i++)
        {
            if(in_array($i, $epuletportids))
            {
                $portdb->Run(null, $i);
                $tipport->Run($i);
            }
        }

        if(!$portdb->siker || !$tipport->siker)
        {
            echo "<h2>Portok resetelése sikertelen!<br></h2>";
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