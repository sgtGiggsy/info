<?php
// Erre a quickapprove miatt van szükség, mert olyankor direktben kerül meghívásra
// ez az állomány, és a módosításhoz szükséges ellenőrzések nem történnek meg
if(isset($irhat) && $irhat)
{
    purifyPost();
    $sql = new MySQLHandler();
    $sql->KeepAlive();
    $bejelento = $_SESSION['id'];
    @$modositasid = $_POST['id'];
    $csoportid = 1;
    if($_POST['csoport'])
        $csoportid = $_POST['csoport'];
    $ertesitendok = Ertesites::GetFelhasznalok(4,
                "INNER JOIN telefonkonyvadminok ON felhasznalok.id = telefonkonyvadminok.felhasznalo",
                "AND felhasznalok.id != ? AND (csoport = ? OR csoport = 1)",
                array($felhasznaloid, $csoportid));
// Változás állapotok:
    // NULL = Elvetve
    // 1    = Beküldve, ellenőrizetlenül
    // 2    = Részlegesen elfogadva
    // 3    = Elfogadva
    // 4    = Lezárva

    // Elem állapotok:
    // NULL = Törölve
    // 1    = Új, ellenőrizetlen
    // 2    = Régi, felülírt
    // 3    = Régi, megjelenik
    // 4    = Új, megjelenik

// Szerkesztői akciók //
    if($_GET["action"] == "new" || $_GET["action"] == "update" || $_GET["action"] == "review")
    {
        $sql->Query("SELECT MAX(id) AS id FROM telefonkonyvmodositaskorok;");
        $sql->Bind($modid);

        if($_POST['csoport'])
        {
            $sql->Query("SELECT nev FROM telefonkonyvcsoportok WHERE id = ?", $csoportid);
            $sql->Bind($alegysegnev);
        }
        
        $sql->Query("SELECT nev FROM felhasznalok WHERE id = ?", $bejelento);
        $sql->Bind($bejelentonev);

        if(!isset($_POST['torles']) || !$_POST['torles'])
        {
            $beosztasnev = mb_strtoupper($_POST['beosztasnev']);
            $nev = mb_strtoupper($_POST['nev']);
        }
        
        if($_GET['action'] != "review")
        {
            if($_POST['belsoszam'])
                $belsoszam = $_POST['belsoelohivo'] . $_POST['belsoszam'];

            if($_POST['belsoszam2'])
                $belsoszam2 = $_POST['belsoelohivo'] . $_POST['belsoszam2'];

            if($_POST['fax'])
                $fax = $_POST['belsoelohivo'] . $_POST['fax'];

            if($_POST['kozcelu'])
                $kozcelu = $_POST['varosielohivo'] . str_replace("-", "", $_POST['kozcelu']);

            if($_POST['kozcelufax'])
                $kozcelufax = $_POST['varosielohivo'] . str_replace("-", "", $_POST['kozcelufax']);

            if($_POST['mobil'])
                $mobil = $_POST['mobilelohivo'] . str_replace("-", "", $_POST['mobil']);
        }
        
        if($_GET["action"] == "new")
        {
            $valtallapot = 1;
            $elemallapot = 1;
            $ujtorolve = $felhid = null;
            $regitorolve = "NULL";
            if($_POST['removebeo'] == 1)
            {
                if($_POST['origbeoid'] && $_POST['beosztas'] != $_POST['origbeoid'])
                {
                    $regitorolve = 1;
                }
                else
                {
                    $ujtorolve = 1;
                }
            }

            // Ha nincs $nev, azt jelenti, hogy a felhasználó törölve lett.
            // Ezesetben nincs szükség felhasználó adatbáziobjektum létrehozására
            if($nev)
            {
                $sql->Query('INSERT INTO telefonkonyvfelhasznalok (elotag, nev, titulus, rendfokozat, mobil, felhasznalo, allapot) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    $_POST['elotag'], $nev, $_POST['titulus'], $_POST['rendfokozat'], $mobil, $_POST['felhasznalo'], $elemallapot);

                $felhid = $sql->last_insert_id;
            }

            $sql->Query('INSERT INTO telefonkonyvbeosztasok_mod (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, felhid, megjegyzes, allapot, torolve) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $_POST['csoport'], $beosztasnev, $_POST['sorrend'], $belsoszam, $belsoszam2, $fax, $kozcelu, $kozcelufax, $felhid, $_POST['megjegyzes'], $elemallapot, $ujtorolve);

            $beomodid = $sql->last_insert_id;

            $sql->Query('INSERT INTO telefonkonyvvaltozasok (modid, origbeoid, ujbeoid, origfelhid, ujfelhid, bejelento, modositasoka, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                $modid, $_POST['beosztas'], $beomodid, $_POST['origfelhid'], $felhid, $bejelento, $_POST['modositasoka'], $valtallapot);

            $tvaltozasid1 = $sql->last_insert_id;

            // Az ág amire akkor van szükség, hogyha a felhasználó korábbi, és új beosztása különbözik
            if($_POST['origbeoid'] && $_POST['beosztas'] != $_POST['origbeoid'])
            {
                $origbeoid = $_POST['origbeoid'];
                $null = null;

                $sql->Query("INSERT INTO telefonkonyvbeosztasok_mod (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, allapot, torolve)
                    SELECT csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, $elemallapot, $regitorolve
                    FROM telefonkonyvbeosztasok
                    WHERE id = ?;", $origbeoid);
                
                $sql->Query("SELECT MAX(id) AS id FROM telefonkonyvbeosztasok_mod");
                $sql->Bind($beoid);

                $sql->Query('INSERT INTO telefonkonyvvaltozasok (modid, origbeoid, ujbeoid, origfelhid, ujfelhid, bejelento, modositasoka, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    $modid, $_POST['origbeoid'], $beoid, $_POST['origfelhid'], $null, $bejelento, $_POST['modositasoka'], $valtallapot);

                $sql->Bind($tvaltozasid2);
            }
            if(!$sql->siker)
            {
                echo "<h2>A változás beküldése sikertelen!<br></h2>";
            }

            // Értesítési rész
            $ertesites = new Ertesites("A(z) $beosztasnev beosztás módosításra került a telefonkönyvben",
                "$bejelentonev felhasználó módosította a(z) $alegysegnev alegység $beosztasnev beosztását",
                "telefonkonyv/felulvizsgalat/$tvaltozasid1");
            $ertesites->SetFelhasznalok($ertesitendok);
            $ertesites->Ment();

            if(isset($null))
            {
                $sql->Query("SELECT telefonkonyvbeosztasok.nev AS beonev, telefonkonyvcsoportok.nev AS alegyseg, telefonkonyvcsoportok.id AS csoportid
                    FROM telefonkonyvbeosztasok
                        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                    WHERE telefonkonyvbeosztasok.id = ?", $origbeoid);

                $sql->Bind($beosztasnev, $alegysegnev, $oldtelefonkonyvcsoportid);

                $ertesites = new Ertesites("A(z) $beosztasnev beosztás módosításra került a telefonkönyvben",
                    "$bejelentonev felhasználó módosította a(z) $alegysegnev alegység $beosztasnev beosztását",
                    "telefonkonyv/felulvizsgalat/$tvaltozasid2");

                $ertesites->SetFelhasznalok($ertesitendok);
                $ertesites->Ment();
            }
        }

        elseif($_GET["action"] == "review" && $globaltelefonkonyvadmin)
        {
            $timestamp = timeStampForSQL();

            $sql->Query("SELECT ujbeoid, ujfelhid, origbeoid, origfelhid FROM telefonkonyvvaltozasok WHERE id = ?", $modositasid);
            $sql->Bind($beoid, $felhid, $origbeoid, $origfelhid);
    
            $sql->Prepare("UPDATE telefonkonyvvaltozasok SET adminmegjegyzes=?, admintimestamp=?, allapot=? WHERE id=?");
            $sql->Run($_POST['adminmegjegyzes'], $timestamp, $_POST['allapot'], $_POST['id']);
    
            if($_POST['allapot'] > 1)
            {
                $elemallapot = 4;
                $sql->Query("UPDATE telefonkonyvfelhasznalok SET allapot = 2 WHERE id = ?", $origfelhid);
                $sql->Query("UPDATE telefonkonyvbeosztasok SET allapot = 2 WHERE id = ?", $origfelhid);
            }

            if(isset($_POST['torles']) && $_POST['torles'])
            {
                $sql->Query('UPDATE telefonkonyvbeosztasok_mod SET allapot=? WHERE id=?',
                    $elemallapot, $beoid);
            }
            else
            {
                $sql->Query('UPDATE telefonkonyvfelhasznalok SET nev=?, elotag=?, titulus=?, rendfokozat=?, mobil=?, felhasznalo=?, allapot=? WHERE id=?',
                    $nev, $_POST['elotag'], $_POST['titulus'], $_POST['rendfokozat'], $_POST['mobil'], $_POST['felhasznalo'], $elemallapot, $felhid);
        
                $sql->Query('UPDATE telefonkonyvbeosztasok_mod SET csoport=?, nev=?, sorrend=?, belsoszam=?, belsoszam2=?, fax=?, kozcelu=?, kozcelufax=?, felhid=?, megjegyzes=?, allapot=? WHERE id=?',
                    $_POST['csoport'], $beosztasnev, $_POST['sorrend'], $_POST['belsoszam'], $_POST['belsoszam2'], $_POST['fax'], $_POST['kozcelu'], $_POST['kozcelufax'], $felhid, $_POST['megjegyzes'], $elemallapot, $beoid);
            }

            switch($_POST['allapot'])
            {
                case '2': $adminesemeny = "helyesbítésekkel elfogadta"; break;
                case '3': $adminesemeny = "elfogadta"; break;
            }
        }
    }

    elseif($_GET["action"] == "quickapprove" && $globaltelefonkonyvadmin)
    {
        $allapot = null;
        $beosztasnev = mb_strtoupper($_POST['beosztasnev']);
        if($_POST['allapot'] == 1)
        {
            $allapot = 3;
        }
        $timestamp = timeStampForSQL();
        $success = true;
        $sql->Query("SELECT ujbeoid, ujfelhid, origbeoid, origfelhid FROM telefonkonyvvaltozasok WHERE id = ?", $modositasid);
        $sql->Bind($beoid, $felhid, $origbeoid, $origfelhid);

        $sql->Query('UPDATE telefonkonyvvaltozasok SET admintimestamp=?, allapot=? WHERE id=?',
            $timestamp, $allapot, $_POST['id']);
        if(!$sql->siker)
            $success = false;

        if($allapot == 3)
        {
            $elemallapot = 4;
            $sql->Query("UPDATE telefonkonyvfelhasznalok SET allapot = 2 WHERE id = ?", $origfelhid);
            $sql->Query("UPDATE telefonkonyvbeosztasok SET allapot = 2 WHERE id = ?", $origbeoid);
        }
        else
        {
            $elemallapot = null;
            $sql->Query("UPDATE telefonkonyvfelhasznalok SET allapot = 3 WHERE id = ?", $origfelhid);
            $sql->Query("UPDATE telefonkonyvbeosztasok SET allapot = 3 WHERE id = ?", $origbeoid);
        }

        $sql->Query('UPDATE telefonkonyvfelhasznalok SET allapot=? WHERE id=?', $elemallapot, $felhid);
        if(!$sql->siker)
            $success = false;

        $sql->Query('UPDATE telefonkonyvbeosztasok_mod SET allapot=? WHERE id=?', $elemallapot, $beoid);

        if(!$sql->siker)
            $success = false;

        switch($_POST['allapot'] && $success)
        {
            case '0': $adminesemeny = "elvetette"; break;
            case '1': $adminesemeny = "elfogadta"; break;
        }
        if(!$success)
            http_response_code(304);
    }

    elseif($_GET["action"] == "discard" && isset($_GET['discardid']))
    {
        if($globaltelefonkonyvadmin)
        {
            $modositasid = $_GET['discardid'];
            @$adminmegjegyzes = $_GET['adminmegjegyzes'];
            $adminesemeny = "elvetette";

            $sql->Query("SELECT ujbeoid, ujfelhid, telefonkonyvbeosztasok_mod.nev AS beonev
                FROM telefonkonyvvaltozasok
                    LEFT JOIN telefonkonyvbeosztasok_mod ON telefonkonyvbeosztasok_mod.id = telefonkonyvvaltozasok.ujbeoid
                WHERE telefonkonyvvaltozasok.id = ?", $modositasid);
            $sql->Bind($ujbeoid, $ujfelhid, $beosztasnev);

            $sql->Query("UPDATE telefonkonyvbeosztasok_mod SET allapot = NULL WHERE id = ?", $ujbeoid);
            $sql->Query("UPDATE telefonkonyvfelhasznalok SET allapot = NULL WHERE id = ?", $ujfelhid);

            $sql->Query('UPDATE telefonkonyvvaltozasok SET allapot = NULL, adminmegjegyzes=? WHERE id=?',
                $adminmegjegyzes, $modositasid);
        }
        else
        {
            echo "<h2>A kért folyamathoz magasabb felhasználói szint szükséges!</h2>";
        }
    }

    elseif($_GET["action"] == "confirmchanges")
    {
        if($globaltelefonkonyvadmin)
        {
            $sql->Query("SELECT origbeoid, ujbeoid, origfelhid, ujfelhid
                    FROM telefonkonyvvaltozasok
                    WHERE modid = ? AND allapot > 1 AND allapot < 4
                    ORDER BY ujfelhid", $modkorid);
            // Azért kell az új felhasználók szerint rendezni, mert így a NULL értékek kerülnek előre,
            // és ha egy beosztásról törölnek valakit, egy másikat pedig beraknak, akkor nem fordulhat elő,
            // hogy a berakás előbb történik meg, mint a törlés (amely esetben az új dolgozót törölné a rendszer)

            if($sql->sorokszama == 0)
            {
                echo "A megadott körben nem történtek módosítások";
                die;
            }
            else
            {
                $elemallapot = 3;
                $modelemallapot = 1;
                // Tudom, hogy nem elegáns, nem is hatákony, de ekkora darabszámnál nem okozhat jelentősebb bajt
                foreach($modlist as $modositas)
                {
                    $origbeoid = $modositas['origbeoid'];
                    $ujbeoid = $modositas['ujbeoid'];
                    $origfelhid = $modositas['origfelhid'];
                    $ujfelhid = $modositas['ujfelhid'];

                    $sql->Query("SELECT * FROM telefonkonyvbeosztasok_mod WHERE id = ?", $ujbeoid);
                    $beomodositasok = $sql->Fetch();
                    $ujsorrend = $beomodositasok['sorrend'];
                    $ujelemcsoportja = $beomodositasok['csoport'];

                    if($origbeoid)
                    {
                        $$sql->Query("SELECT * FROM telefonkonyvbeosztasok WHERE id = ?", $origbeoid);
                        $beooriginals = $sql->Fetch();
                        $beoorigsorrend = $beooriginals['sorrend'];

                        if($ujsorrend != $beoorigsorrend)
                        {
                            $sql->Query("UPDATE telefonkonyvbeosztasok SET sorrend = sorrend + 1 WHERE sorrend > ? AND csoport = ?;", $ujsorrend, $ujelemcsoportja);
                        }

                        $sql->Query('UPDATE telefonkonyvbeosztasok SET csoport=?, nev=?, sorrend=?, belsoszam=?, belsoszam2=?, fax=?, kozcelu=?, kozcelufax=?, felhid=?, megjegyzes=?, allapot=?, torolve=? WHERE id=?',
                            $beomodositasok['csoport'], $beomodositasok['nev'], $ujsorrend, $beomodositasok['belsoszam'], $beomodositasok['belsoszam2'], $beomodositasok['fax'], $beomodositasok['kozcelu'], $beomodositasok['kozcelufax'], $beomodositasok['felhid'], $beomodositasok['megjegyzes'], $elemallapot, $beomodositasok['torolve'], $origbeoid);

                        // A korábbi eredeti állapotot átteszük a módosításhoz használt rekorba
                        $sql->Query('UPDATE telefonkonyvbeosztasok_mod SET csoport=?, nev=?, sorrend=?, belsoszam=?, belsoszam2=?, fax=?, kozcelu=?, kozcelufax=?, felhid=?, megjegyzes=?, allapot=?, torolve=? WHERE id=?',
                            $beooriginals['csoport'], $beooriginals['nev'], $beooriginals['sorrend'], $beooriginals['belsoszam'], $beooriginals['belsoszam2'], $beooriginals['fax'], $beooriginals['kozcelu'], $beooriginals['kozcelufax'], $beooriginals['felhid'], $beooriginals['megjegyzes'], $modelemallapot, $beooriginals['torolve'], $ujbeoid);

                        if($ujsorrend != $beoorigsorrend)
                        {
                            $sql->Query("UPDATE telefonkonyvbeosztasok SET sorrend = sorrend - 1 WHERE sorrend > ? AND csoport = ?;", $beoorigsorrend, $ujelemcsoportja);
                        }
                    }
                    else
                    {
                        // Először a legegyszerűbb forgatókönyvet vesszük, mikor az új elem a csoport legutolsó helyére kerül
                        if($ujsorrend == 999999)
                        {
                            $sql->Query("SELECT MAX(sorrend) AS utolsoelem FROM telefonkonyvbeosztasok WHERE csoport = ?", $ujelemcsoportja);
                            $ujsorrend = $sql->Fetch()['utolsoelem'];
                        }
                        else
                        {
                            $sql->Query("UPDATE telefonkonyvbeosztasok SET sorrend = sorrend + 1 WHERE sorrend > ? AND csoport = ?;", $ujsorrend, $ujelemcsoportja);
                        }

                        $sql->Query('INSERT INTO telefonkonyvbeosztasok (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, felhid, megjegyzes, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            $ujelemcsoportja, $beomodositasok['nev'], $ujsorrend, $beomodositasok['belsoszam'], $beomodositasok['belsoszam2'], $beomodositasok['fax'], $beomodositasok['kozcelu'], $beomodositasok['kozcelufax'], $beomodositasok['felhid'], $beomodositasok['megjegyzes'], $elemallapot);
                    }

                    if($origfelhid)
                        $sql->Query("UPDATE telefonkonyvfelhasznalok SET allapot = NULL WHERE id = ?;", $origfelhid);
                    if($ujfelhid)
                        $sql->Query("UPDATE telefonkonyvfelhasznalok SET allapot = 4 WHERE id = ?;", $ujfelhid);
                  /*  mySQLConnect("INSERT INTO telefonkonyvbeosztasok (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, felhasznalo, allapot)
                        SELECT csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, $elemallapot
                        FROM telefonkonyvbeosztasok_mod
                    WHERE id = $origbeoid;"); */
                }
            }
            
            $timestamp = timeStampForSQL();
            $sql->Query("UPDATE telefonkonyvmodositaskorok SET lezarva = ? WHERE id = ?;", $timestamp, $modkorid);
            $sql->Query("INSERT INTO telefonkonyvmodositaskorok () VALUES ()");
            $sql->Query("UPDATE telefonkonyvvaltozasok SET allapot = 4 WHERE modid = ? AND allapot > 1", $modkorid);
            //die;

            /*
            mySQLConnect("UPDATE telefonkonyvvaltozasok
                    LEFT JOIN telefonkonyvbeosztasok ON telefonkonyvbeosztasok.id = telefonkonyvvaltozasok.beosztas
                    LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvfelhasznalok.id = telefonkonyvvaltozasok.felhid
                    LEFT JOIN telefonkonyvmodositaskorok ON telefonkonyvvaltozasok.modid = telefonkonyvmodositaskorok.id
                SET telefonkonyvfelhasznalok.beosztas = telefonkonyvvaltozasok.beosztas,
                    telefonkonyvfelhasznalok.elotag = telefonkonyvvaltozasok.elotag,
                    telefonkonyvfelhasznalok.nev = telefonkonyvvaltozasok.nev,
                    telefonkonyvfelhasznalok.titulus = telefonkonyvvaltozasok.titulus,
                    telefonkonyvfelhasznalok.rendfokozat = telefonkonyvvaltozasok.rendfokozat,
                    telefonkonyvbeosztasok.belsoszam = telefonkonyvvaltozasok.belsoszam,
                    telefonkonyvbeosztasok.belsoszam2 = telefonkonyvvaltozasok.belsoszam2,
                    telefonkonyvbeosztasok.kozcelu = telefonkonyvvaltozasok.kozcelu,
                    telefonkonyvbeosztasok.fax = telefonkonyvvaltozasok.fax,
                    telefonkonyvbeosztasok.kozcelufax = telefonkonyvvaltozasok.kozcelufax,
                    telefonkonyvfelhasznalok.mobil = telefonkonyvvaltozasok.mobil,
                    telefonkonyvbeosztasok.csoport = telefonkonyvvaltozasok.csoport,
                    telefonkonyvfelhasznalok.felhasznalo = telefonkonyvvaltozasok.felhasznalo,
                    telefonkonyvbeosztasok.sorrend = telefonkonyvvaltozasok.sorrend,
                    telefonkonyvfelhasznalok.megjegyzes = telefonkonyvvaltozasok.megjegyzes,
                    telefonkonyvvaltozasok.allapot = 4,
                    telefonkonyvmodositaskorok.lezarva = $bejelento,
                    telefonkonyvmodositaskorok.timestamp = CURRENT_TIMESTAMP
                WHERE telefonkonyvvaltozasok.allapot < 4 AND telefonkonyvvaltozasok.allapot > 1;");
                */
        }
        else
        {
            echo "<h2>A kért folyamathoz magasabb felhasználói szint szükséges!</h2>";
        }
    }

    if(isset($adminesemeny))
    {
        $ertesites = new Ertesites("Változás a(z) $beosztasnev beosztás módosításainak állapotában",
            "A telefonkönyv adminisztrátora $adminesemeny a(z) $beosztasnev beosztás módosításait",
            "telefonszamvaltozas?modid=$modositasid");
        $ertesites->SetFelhasznalok($ertesitendok);
        $ertesites->Ment();
    }
    $sql->Close();
}