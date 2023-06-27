<?php

if(isset($irhat) && $irhat)
{
    $con = mySQLConnect(false);
    $bejelento = $_SESSION[getenv('SESSION_NAME').'id'];
    
    $modid = mySQLConnect("SELECT id FROM telefonkonyvmodositaskorok WHERE lezarva IS NULL ORDER BY id DESC LIMIT 1;");
    $modid = mysqli_fetch_assoc($modid)['id'];

    purifyPost();

    if($_GET["action"] == "new")
    {
        // Állapotok:
        // NULL = Elvetve
        // 1    = Beküldve, ellenőrizetlenül
        // 2    = Részlegesen elfogadva
        // 3    = Elfogadva
        // 4    = Lezárva

        $allapot = 1;

        $beosztasnev = mb_strtoupper($_POST['beosztasnev']);
        $nev = mb_strtoupper($_POST['nev']);

        if($_POST['belsoszam'])
        {
            $belsoszam = $_POST['belsoelohivo'] . $_POST['belsoszam'];
        }

        if($_POST['belsoszam2'])
        {
            $belsoszam2 = $_POST['belsoelohivo'] . $_POST['belsoszam2'];
        }

        if($_POST['fax'])
        {
            $fax = $_POST['belsoelohivo'] . $_POST['fax'];
        }

        if($_POST['kozcelu'])
        {
            $kozcelu = $_POST['varosielohivo'] . str_replace("-", "", $_POST['kozcelu']);
        }

        if($_POST['kozcelufax'])
        {
            $kozcelufax = $_POST['varosielohivo'] . str_replace("-", "", $_POST['kozcelufax']);
        }

        if($_POST['mobil'])
        {
            $mobil = $_POST['mobilelohivo'] . str_replace("-", "", $_POST['mobil']);
        }
        
        $stmt = $con->prepare('INSERT INTO telefonkonyvvaltozasok (felhid, modid, beosztas, beosztasnev, elotag, nev, titulus, rendfokozat, belsoszam, belsoszam2, kozcelu, fax, kozcelufax, mobil, csoport, felhasznalo, sorrend, megjegyzes, bejelento, modositasoka, allapot) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssssssssssssssss', $_POST['felhid'], $modid, $_POST['beosztas'], $beosztasnev, $_POST['elotag'], $nev, $_POST['titulus'], $_POST['rendfokozat'], $belsoszam, $belsoszam2, $kozcelu, $fax, $kozcelufax, $mobil, $_POST['csoport'], $_POST['felhasznalo'], $_POST['sorrend'], $_POST['megjegyzes'], $bejelento, $_POST['modositasoka'], $allapot);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A változás beküldése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
    }

    elseif($_GET["action"] == "review")
    {
        $timestamp = timeStampForSQL();
        $stmt = $con->prepare('UPDATE telefonkonyvvaltozasok SET beosztas=?, beosztasnev=?, elotag=?, nev=?, titulus=?, rendfokozat=?, belsoszam=?, belsoszam2=?, kozcelu=?, fax=?, kozcelufax=?, mobil=?, csoport=?, felhasznalo=?, sorrend=?, megjegyzes=?, adminmegjegyzes=?, admintimestamp=?, allapot=? WHERE id=?');
        $stmt->bind_param('sssssssssssssssssssi', $_POST['beosztas'], $_POST['beosztasnev'], $_POST['elotag'], $_POST['nev'], $_POST['titulus'], $_POST['rendfokozat'], $_POST['belsoszam'], $_POST['belsoszam2'], $_POST['kozcelu'], $_POST['fax'], $_POST['kozcelufax'], $_POST['mobil'], $_POST['csoport'], $_POST['felhasznalo'], $_POST['sorrend'], $_POST['megjegyzes'], $_POST['adminmegjegyzes'], $timestamp, $_POST['allapot'], $_POST['id']);
        $stmt->execute();
        if(mysqli_errno($con) != 0)
        {
            echo "<h2>A változás szerkesztése sikertelen!<br></h2>";
            echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
        }
        else
        {
            header("Location: $backtosender");
        }
    }

    elseif($_GET["action"] == "discard")
    {
        if($globaltelefonkonyvadmin)
        {
            $allapot = null;
            $stmt = $con->prepare('UPDATE telefonkonyvvaltozasok SET allapot=? WHERE id=?');
            $stmt->bind_param('si', $allapot, $_GET['discardid']);
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
        }
        else
        {
            echo "<h2>A kért folyamathoz magasabb felhasználói szint szükséges!</h2>";
        }
    }
}