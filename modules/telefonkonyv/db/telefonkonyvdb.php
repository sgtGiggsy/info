<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);
    $bejelento = $_SESSION[getenv('SESSION_NAME').'id'];

    purifyPost();

// Szerkesztői akciók //
    if($_GET["action"] == "new" || $_GET["action"] == "update" || $_GET["action"] == "review")
    {
        $modid = mySQLConnect("SELECT MAX(id) AS id FROM telefonkonyvmodositaskorok;");
        $modid = mysqli_fetch_assoc($modid)['id'];

        $telefonkonyvcsoportid = 0;

        if($_POST['csoport'])
        {
            $telefonkonyvcsoportid = $_POST['csoport'];
            $alegysegnev = mySQLConnect("SELECT nev FROM telefonkonyvcsoportok WHERE id = $telefonkonyvcsoportid");
            $alegysegnev = mysqli_fetch_assoc($alegysegnev)['nev'];
        }
        
        $bejelentonev = mySQLConnect("SELECT nev FROM felhasznalok WHERE id = $bejelento");
        $bejelentonev = mysqli_fetch_assoc($bejelentonev)['nev'];

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

            $valtallapot = 1;
            $elemallapot = 1;
            $ujtorolve = null;
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
            

            $stmt = $con->prepare('INSERT INTO telefonkonyvfelhasznalok (elotag, nev, titulus, rendfokozat, mobil, felhasznalo, allapot) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssss', $_POST['elotag'], $nev, $_POST['titulus'], $_POST['rendfokozat'], $mobil, $_POST['felhasznalo'], $elemallapot);
            $stmt->execute();

            $felhid = mysqli_insert_id($con);

            $stmt = $con->prepare('INSERT INTO telefonkonyvbeosztasok_mod (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, felhid, megjegyzes, allapot, torolve) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssssssssss', $_POST['csoport'], $beosztasnev, $_POST['sorrend'], $belsoszam, $belsoszam2, $fax, $kozcelu, $kozcelufax, $felhid, $_POST['megjegyzes'], $elemallapot, $ujtorolve);
            $stmt->execute();

            $beomodid = mysqli_insert_id($con);

            $stmt = $con->prepare('INSERT INTO telefonkonyvvaltozasok (modid, origbeoid, ujbeoid, origfelhid, ujfelhid, bejelento, modositasoka, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssssss', $modid, $_POST['beosztas'], $beomodid, $_POST['origfelhid'], $felhid, $bejelento, $_POST['modositasoka'], $valtallapot);
            $stmt->execute();

            $tvaltozasid1 = mysqli_insert_id($con);

            // Az ág amire akkor van szükség, hogyha a felhasználó korábbi, és új beosztása különbözik
            if($_POST['origbeoid'] && $_POST['beosztas'] != $_POST['origbeoid'])
            {
                $origbeoid = $_POST['origbeoid'];
                $null = null;

                mySQLConnect("INSERT INTO telefonkonyvbeosztasok_mod (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, allapot, torolve)
                    SELECT csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, $elemallapot, $regitorolve
                    FROM telefonkonyvbeosztasok
                    WHERE id = $origbeoid;");
                
                $beoid = mySQLConnect("SELECT MAX(id) AS id FROM telefonkonyvbeosztasok_mod");
                $beoid = mysqli_fetch_assoc($beoid)['id'];

                $stmt = $con->prepare('INSERT INTO telefonkonyvvaltozasok (modid, origbeoid, ujbeoid, origfelhid, ujfelhid, bejelento, modositasoka, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssssssss', $modid, $_POST['origbeoid'], $beoid, $_POST['origfelhid'], $null, $bejelento, $_POST['modositasoka'], $valtallapot);
                $stmt->execute();

                $tvaltozasid2 = mysqli_insert_id($con);
            }
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A változás beküldése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }

            // Értesítési rész
            $valtozas1 = array(
                'cim' => "A(z) $beosztasnev beosztás módosításra került a telefonkönyvben",
                'szoveg' => "$bejelentonev felhasználó módosította a(z) $alegysegnev alegység $beosztasnev beosztását",
                'url' => "valtozasfelulvizsgalat/$tvaltozasid1",
                'tipus' => '88',
                'csoportid' => $telefonkonyvcsoportid
            );

            if(!$globaltelefonkonyvadmin)
                telefonKonyvNotify($valtozas1, $telefonkonyvcsoportid, $bejelento, true);

            if(isset($null))
            {
                $oldbeo = mySQLConnect("SELECT telefonkonyvbeosztasok.nev AS beonev, telefonkonyvcsoportok.nev AS alegyseg, telefonkonyvcsoportok.id AS csoportid
                    FROM telefonkonyvbeosztasok
                        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                    WHERE telefonkonyvbeosztasok.id = $origbeoid");

                $oldbeo = mysqli_fetch_assoc($oldbeo);
                $beosztasnev = $oldbeo['beonev'];
                $alegysegnev = $oldbeo['alegyseg'];
                $oldtelefonkonyvcsoportid = $oldbeo['csoportid'];

                $valtozas2 = array(
                    'cim' => "A(z) $beosztasnev beosztás módosításra került a telefonkönyvben",
                    'szoveg' => "$bejelentonev felhasználó módosította a(z) $alegysegnev alegység $beosztasnev beosztását",
                    'url' => "valtozasfelulvizsgalat/$tvaltozasid2",
                    'tipus' => '88',
                    'csoportid' => $oldtelefonkonyvcsoportid
                );

                if(!$globaltelefonkonyvadmin)
                    telefonKonyvNotify($valtozas2, $oldtelefonkonyvcsoportid, $bejelento, true);
            }
        }

        elseif($_GET["action"] == "review" && $globaltelefonkonyvadmin)
        {
            $timestamp = timeStampForSQL();
            $modositasid = $_POST['id'];
            $idstomod = mySQLConnect("SELECT ujbeoid, ujfelhid, origbeoid, origfelhid FROM telefonkonyvvaltozasok WHERE id = $modositasid");
            $idstomod = mysqli_fetch_assoc($idstomod);
    
            $felhid = $idstomod['ujfelhid'];
            $beoid = $idstomod['ujbeoid'];
            $origbeoid = $idstomod['origbeoid'];
            $origfelhid = $idstomod['origfelhid'];
            $csoportid = $_POST['csoport'];
    
            $stmt = $con->prepare('UPDATE telefonkonyvvaltozasok SET adminmegjegyzes=?, admintimestamp=?, allapot=? WHERE id=?');
            $stmt->bind_param('sssi', $_POST['adminmegjegyzes'], $timestamp, $_POST['allapot'], $_POST['id']);
            $stmt->execute();
    
            if($_POST['allapot'] > 1)
            {
                $elemallapot = 4;
                mySQLConnect("UPDATE telefonkonyvfelhasznalok SET allapot = 2 WHERE id = $origfelhid");
                mySQLConnect("UPDATE telefonkonyvbeosztasok SET allapot = 2 WHERE id = $origbeoid");
            }

            if(isset($_POST['torles']) && $_POST['torles'])
            {
                $stmt = $con->prepare('UPDATE telefonkonyvbeosztasok_mod SET allapot=? WHERE id=?');
                $stmt->bind_param('si', $elemallapot, $idstomod['ujbeoid']);
                $stmt->execute();
            }
            else
            {
                $stmt = $con->prepare('UPDATE telefonkonyvfelhasznalok SET elotag=?, nev=?, titulus=?, rendfokozat=?, mobil=?, felhasznalo=?, allapot=? WHERE id=?');
                $stmt->bind_param('sssssssi', $_POST['elotag'], $nev, $_POST['titulus'], $_POST['rendfokozat'], $_POST['mobil'], $_POST['felhasznalo'], $elemallapot, $felhid);
                $stmt->execute();
        
                $stmt = $con->prepare('UPDATE telefonkonyvbeosztasok_mod SET csoport=?, nev=?, sorrend=?, belsoszam=?, belsoszam2=?, fax=?, kozcelu=?, kozcelufax=?, felhid=?, megjegyzes=?, allapot=? WHERE id=?');
                $stmt->bind_param('sssssssssssi', $_POST['csoport'], $beosztasnev, $_POST['sorrend'], $_POST['belsoszam'], $_POST['belsoszam2'], $_POST['fax'], $_POST['kozcelu'], $_POST['kozcelufax'], $felhid, $_POST['megjegyzes'], $elemallapot, $idstomod['ujbeoid']);
                $stmt->execute();
            }

            switch($_POST['allapot'])
            {
                case '2': $adminesemeny = "helyesbítésekkel elfogadta"; break;
                case '3': $adminesemeny = "elfogadta"; break;
            }
        }
    }

    elseif($_GET["action"] == "discard")
    {
        if($globaltelefonkonyvadmin)
        {
            $modositasid = $_GET['discardid'];

            $ids = mySQLConnect("SELECT ujbeoid, ujfelhid, telefonkonyvbeosztasok_mod.nev AS beonev, telefonkonyvbeosztasok_mod.csoport AS csoportid
                FROM telefonkonyvvaltozasok
                    LEFT JOIN telefonkonyvbeosztasok_mod ON telefonkonyvbeosztasok_mod.id = telefonkonyvvaltozasok.ujbeoid
                WHERE telefonkonyvvaltozasok.id = $modositasid");
            $ids = mysqli_fetch_assoc($ids);
            $ujbeoid = $ids['ujbeoid'];
            $ujfelhid = $ids['ujfelhid'];
            $beosztasnev = $ids['beonev'];
            $csoportid = $ids['csoportid'];
            $adminesemeny = "elvetette";

            mySQLConnect("UPDATE telefonkonyvbeosztasok_mod SET allapot = NULL WHERE id = $ujbeoid");
            mySQLConnect("UPDATE telefonkonyvfelhasznalok SET allapot = NULL WHERE id = $ujfelhid");

            $stmt = $con->prepare('UPDATE telefonkonyvvaltozasok SET allapot=?, adminmegjegyzes=? WHERE id=?');
            $stmt->bind_param('ssi', $allapot, $_GET['adminmegjegyzes'], $_GET['discardid']);
            $stmt->execute();
            if(mysqli_errno($con) != 0)
            {
                echo "<h2>A változás szerkesztése sikertelen!<br></h2>";
                echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
            }
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
            $modlist = mySQLConnect("SELECT origbeoid, ujbeoid, origfelhid, ujfelhid
                    FROM telefonkonyvvaltozasok
                    WHERE modid = $modkorid AND allapot > 1 AND allapot < 4
                    ORDER BY ujfelhid");
            // Azért kell az új felhasználók szerint rendezni, mert így a NULL értékek kerülnek előre,
            // és ha egy beosztásról törölnek valakit, egy másikat pedig beraknak, akkor nem fordulhat elő,
            // hogy a berakás előbb történik meg, mint a törlés (amely esetben az új dolgozót törölné a rendszer)

            if(mysqli_num_rows($modlist) == 0)
            {
                echo "A megadott körben nem történtek módosítások";
                die;
            }
            else
            {
                $elemallapot = 3;
                $modelemallapot = 1;
                foreach($modlist as $modositas)
                {
                    $origbeoid = $modositas['origbeoid'];
                    $ujbeoid = $modositas['ujbeoid'];
                    $origfelhid = $modositas['origfelhid'];
                    $ujfelhid = $modositas['ujfelhid'];

                    $beomodositasok = mySQLConnect("SELECT * FROM telefonkonyvbeosztasok_mod WHERE id = $ujbeoid");
                    $beomodositasok = mysqli_fetch_assoc($beomodositasok);

                    if($origbeoid)
                    {
                        $beooriginals = mySQLConnect("SELECT * FROM telefonkonyvbeosztasok WHERE id = $origbeoid");
                        $beooriginals = mysqli_fetch_assoc($beooriginals);

                        $stmt = $con->prepare('UPDATE telefonkonyvbeosztasok SET csoport=?, nev=?, sorrend=?, belsoszam=?, belsoszam2=?, fax=?, kozcelu=?, kozcelufax=?, felhid=?, megjegyzes=?, allapot=?, torolve=? WHERE id=?');
                        $stmt->bind_param('ssssssssssssi', $beomodositasok['csoport'], $beomodositasok['nev'], $beomodositasok['sorrend'], $beomodositasok['belsoszam'], $beomodositasok['belsoszam2'], $beomodositasok['fax'], $beomodositasok['kozcelu'], $beomodositasok['kozcelufax'], $beomodositasok['felhid'], $beomodositasok['megjegyzes'], $elemallapot, $beomodositasok['torolve'], $origbeoid);
                        $stmt->execute();

                        $stmt = $con->prepare('UPDATE telefonkonyvbeosztasok_mod SET csoport=?, nev=?, sorrend=?, belsoszam=?, belsoszam2=?, fax=?, kozcelu=?, kozcelufax=?, felhid=?, megjegyzes=?, allapot=?, torolve=? WHERE id=?');
                        $stmt->bind_param('ssssssssssssi', $beooriginals['csoport'], $beooriginals['nev'], $beooriginals['sorrend'], $beooriginals['belsoszam'], $beooriginals['belsoszam2'], $beooriginals['fax'], $beooriginals['kozcelu'], $beooriginals['kozcelufax'], $beooriginals['felhid'], $beooriginals['megjegyzes'], $modelemallapot, $beooriginals['torolve'], $ujbeoid);
                        $stmt->execute();
                    }
                    else
                    {
                        $stmt = $con->prepare('INSERT INTO telefonkonyvbeosztasok (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, felhid, megjegyzes, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                        $stmt->bind_param('sssssssssss', $beomodositasok['csoport'], $beomodositasok['nev'], $beomodositasok['sorrend'], $beomodositasok['belsoszam'], $beomodositasok['belsoszam2'], $beomodositasok['fax'], $beomodositasok['kozcelu'], $beomodositasok['kozcelufax'], $beomodositasok['felhid'], $beomodositasok['megjegyzes'], $elemallapot);
                        $stmt->execute();
                    }

                    if($origfelhid) mySQLConnect("UPDATE telefonkonyvfelhasznalok SET allapot = NULL WHERE id = $origfelhid;");
                    if($ujfelhid) mySQLConnect("UPDATE telefonkonyvfelhasznalok SET allapot = 4 WHERE id = $ujfelhid;");
                  /*  mySQLConnect("INSERT INTO telefonkonyvbeosztasok (csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, felhasznalo, allapot)
                        SELECT csoport, nev, sorrend, belsoszam, belsoszam2, fax, kozcelu, kozcelufax, megjegyzes, $elemallapot
                        FROM telefonkonyvbeosztasok_mod
                    WHERE id = $origbeoid;"); */
                }
            }
            
            $timestamp = timeStampForSQL();
            mySQLConnect("UPDATE telefonkonyvmodositaskorok SET lezarva = '$timestamp' WHERE id = $modkorid;");
            mySQLConnect("INSERT INTO telefonkonyvmodositaskorok () VALUES ()");
            mySQLConnect("UPDATE telefonkonyvvaltozasok SET allapot = 4 WHERE modid = $modkorid AND allapot > 1");
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
        $valtozas = array(
            'cim' => "Változás a(z) $beosztasnev beosztás módosításainak állapotában",
            'szoveg' => "A telefonkönyv adminisztrátora $adminesemeny a(z) $beosztasnev beosztás módosításait",
            'url' => "telefonszamvaltozas?modid=$modositasid",
            'tipus' => '88'
        );

        telefonKonyvNotify($valtozas, $csoportid);
    }
}