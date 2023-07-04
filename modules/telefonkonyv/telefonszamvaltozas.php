<?php

if(@!$csoportir)
{
    getPermissionError();
}
else
{
    $globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);

    $tkonyvwhereadmin = array(
        'where' => false,
        'mezonev' => false,
        'and' => true
    );

    $tkonyvwherecsoport = array(
        'where' => false,
        'mezonev' => true,
        'and' => true
    );

    $belsoelohivo = $_SESSION[getenv('SESSION_NAME').'belsoelohivo'];
    $varosielohivo = $_SESSION[getenv('SESSION_NAME').'varosielohivo'];
    $mobilelohivo = $_SESSION[getenv('SESSION_NAME').'mobilelohivo'];
    $where = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwhereadmin);
    $wherecsoport = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwherecsoport);
    $csillaggaljelolt = "A csillaggal jelölt mezők kitöltése kötelező.";

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvdb.php");
        
        redirectToGyujto("telefonkonyv");
    }

    $rendfokozatok = mySQLConnect("SELECT * FROM rendfokozatok ORDER BY id");

    $nevelotagok = mySQLConnect("SELECT * FROM nevelotagok");

    $titulusok = mySQLConnect("SELECT * FROM titulusok");

    $felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok ORDER BY nev");

    $tkonyvfelhasznalok = mySQLConnect("SELECT nev FROM telefonkonyvfelhasznalok;");

    $csoportok = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL $wherecsoport;");

    $modkerwhere = $magyarazat = $beosztas = $felhid = $beosztasnev = $elotag = $nev = $titulus = $rendfokozat = $belsoszam = $belsoszam2 = $kozcelu = $fax = $kozcelufax =
    $mobil = $csoport = $csoportid = $felhasznalo = $megjegyzes = $modositasoka = $felhid = $adminmegjegyzes = $admintimestamp = $modwhere = $meglevobeo = $addnew = $zarozar = null;
    $sorrend = 9999;

    $button = "Mentés";
    $irhat = true;
    $form = "modules/telefonkonyv/forms/telefonszamszerkesztform";
    $oldalcim = "Új szám felvitele";

    if(isset($_GET['id']))
    {
        $telszamid = $_GET['id'];
        $telefonszam = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS beosztas,
            telefonkonyvbeosztasok.nev AS beosztasnev,
            telefonkonyvfelhasznalok.id AS felhid,
            elotag,
            telefonkonyvfelhasznalok.nev AS nev,
            titulus,
            rendfokozat,
            belsoszam,
            belsoszam2,
            kozcelu,
            fax,
            kozcelufax,
            mobil,
            felhasznalo,
            sorrend,
            csoport,
            megjegyzes
        FROM telefonkonyvbeosztasok
            LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
        WHERE telefonkonyvbeosztasok.id = $telszamid");

        if(mysqli_num_rows($telefonszam) == 0)
        {
            echo "<h2>Nincs ilyen azonosítójú telefonszám, vagy nincs jogosultsága a szerkesztéséhez!</h2>";
            $irhat = false;
        }
        else
        {
            $telefonszam = mysqli_fetch_assoc($telefonszam);
            $beosztasnev = $telefonszam['beosztasnev'];
            $elotag = $telefonszam['elotag'];
            $nev = $telefonszam['nev'];
            $titulus = $telefonszam['titulus'];
            $rendfokozat = $telefonszam['rendfokozat'];
            $felhasznalo = $telefonszam['felhasznalo'];
            $megjegyzes = $telefonszam['megjegyzes'];
            $csoport = $telefonszam['csoport'];
            $sorrend = $telefonszam['sorrend'];
            if(isset($telefonszam['admintimestamp']))
            {
                $admintimestamp = $telefonszam['admintimestamp'];
            }
            $felhid = 0;
            $beosztas = 0;
            if($telefonszam['felhid'])
            {
                $felhid = $telefonszam['felhid'];
            }
            if($telefonszam['beosztas'])
            {
                $beosztas = $telefonszam['beosztas'];
            }

            if(isset($telefonszam['adminmegjegyzes']) && $telefonszam['adminmegjegyzes'])
            {
                $adminmegjegyzes = $telefonszam['adminmegjegyzes'];
            }

            //$adminmegjegyzes = $telefonszam['adminmegjegyzes'];


            // Telefonszámok
            if($telefonszam['belsoszam'])
            {
                $belsoszam = telSzamSzetvalaszt($telefonszam['belsoszam'], 6, 4);
                $belsoszam2 = telSzamSzetvalaszt($telefonszam['belsoszam2'], 6, 4);
                $belsoelohivo = $belsoszam['elotag'];
                $belsoszam = $belsoszam['telszam'];
                $belsoszam2 = $belsoszam2['telszam'];
            }
            
            if($telefonszam['fax'])
            {
                $fax = telSzamSzetvalaszt($telefonszam['fax'], 6, 4);
                $fax = $fax['telszam'];
            }

            if($telefonszam['kozcelu'])
            {
                $kozcelu = telSzamSzetvalaszt($telefonszam['kozcelu'], 8, 6);
                $varosielohivo = $kozcelu['elotag'];
                $kozcelu = formatTelnum($kozcelu['telszam']);
            }

            if($telefonszam['kozcelufax'])
            {
                $kozcelufax = telSzamSzetvalaszt($telefonszam['kozcelufax'], 8, 6);
                $varosielohivo = $kozcelufax['elotag'];
                $kozcelufax = formatTelnum($kozcelufax['telszam']);
            }

            if($telefonszam['mobil'])
            {
                $mobil = telSzamSzetvalaszt($telefonszam['mobil'], 8, 7);
                $mobilelohivo = $mobil['elotag'];
                $mobil = formatTelnum($mobil['telszam']);
            }
        }
    }
    elseif(isset($_GET['modid']))
    {
        $modid = $_GET['modid'];

        $modositasok = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS beosztas,
                telefonkonyvbeosztasok.nev AS beosztasnev,
                telefonkonyvfelhasznalok.id AS felhid,
                elotag,
                telefonkonyvfelhasznalok.nev AS nev,
                titulus,
                rendfokozat,
                belsoszam,
                belsoszam2,
                kozcelu,
                fax,
                kozcelufax,
                mobil,
                felhasznalo,
                sorrend,
                csoport,
                megjegyzes
            FROM telefonkonyvbeosztasok
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
                LEFT JOIN telefonkonyvvaltozasok ON telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok.id
            WHERE telefonkonyvvaltozasok.id = $modid
                $where
                AND telefonkonyvvaltozasok.allapot < 2");

        if(mysqli_num_rows($modositasok) == 0)
        {
            echo "<h2>Erre a számra ebben a hónapban már lett leadva véglegesített módosítási kérelem,
                nem létezik, vagy nincs jogosultsága a szerkesztéséhez!</h2>";
            $irhat = false;
        }
        else
        {
            $telefonszam = mysqli_fetch_assoc($modositasok);
        }
    }
    elseif(isset($_GET['action']) && $_GET['action'] == "addnew")
    {
        $addnew = true;
    }

    if($irhat)
    {
        if($beosztas)
        {
            $meglevobeo = "telefonkonyvbeosztasok.id = $beosztas OR (";
            $zarozar = ")";
        }
        $beowhere = "telefonkonyvbeosztasok.felhid IS NULL";

        if(isset($modid) && $modid)
        {
            $beowhere = "(" . $beowhere . " OR telefonkonyvvaltozasok.id = $modid" . ")";
        }
        
        $beosztasok = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS id,
                telefonkonyvbeosztasok.nev AS nev,
                telefonkonyvcsoportok.nev AS csoportid,
                telefonkonyvcsoportok.nev AS csoportnev
            FROM telefonkonyvbeosztasok
                LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
                LEFT JOIN telefonkonyvvaltozasok ON (telefonkonyvvaltozasok.origbeoid = telefonkonyvbeosztasok.id OR telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok.id)
            WHERE $meglevobeo
                telefonkonyvcsoportok.id > 1
                AND telefonkonyvbeosztasok.allapot > 1
                AND $beowhere
                $where
                $zarozar
            ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");

        $oldalcim = "Telefonszám szerkesztése";

        ?><datalist id="tkonyvfelhasznalok"><?php
            foreach($tkonyvfelhasznalok as $felhasznalo)
            {
                ?><option><?=$felhasznalo['nev']?></option><?php
            }
        ?></datalist><?php

        if(isset($modid) && $modid)
        {
            $modwhere = "telefonkonyvvaltozasok.id = $modid AND";
        }
        elseif($beosztas || $felhid)
        {
            $modwhere = "(telefonkonyvvaltozasok.origbeoid = $beosztas && telefonkonyvvaltozasok.origbeoid != 0) AND";
        }

        $modositaskerelmek = mySQLConnect("SELECT felhasznalok.nev AS bejelento, timestamp
                FROM telefonkonyvvaltozasok
                    INNER JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
                WHERE $modwhere telefonkonyvvaltozasok.modid = (SELECT MAX(id) FROM telefonkonyvmodositaskorok)
                    AND telefonkonyvvaltozasok.allapot < 2;");

        if(mysqli_num_rows($modositaskerelmek) > 0)
        {
            $modositasadatok = mysqli_fetch_assoc($modositaskerelmek);

            $onloadfelugro = "A kért felhasználóra, vagy beosztásra már küldött be elfogadásra váró módosítási kérelmet " . $modositasadatok['bejelento'] . " " . $modositasadatok['timestamp'] . "-kor. Biztosan szeretnél másik kérelmet beadni?";
        }

        include('././templates/edit.tpl.php');
    }
}