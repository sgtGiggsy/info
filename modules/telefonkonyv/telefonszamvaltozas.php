<?php

if(@!$csoportir)
{
    getPermissionError();
}
else
{
    $globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);

    $belsoelohivo = $_SESSION[getenv('SESSION_NAME').'belsoelohivo'];
    $varosielohivo = $_SESSION[getenv('SESSION_NAME').'varosielohivo'];
    $mobilelohivo = $_SESSION[getenv('SESSION_NAME').'mobilelohivo'];

    $csillaggaljelolt = "A csillaggal jelölt mezők kitöltése kötelező.";

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvdb.php");
        
        redirectToGyujto("telefonkonyv");
    }

    $beowhere = null;
    if($id)
    {
        $beowhere = "OR telefonkonyvbeosztasok.id = $id";
    }

    $beosztasok = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS id,
                telefonkonyvbeosztasok.nev AS nev,
                telefonkonyvcsoportok.nev AS csoportid,
                telefonkonyvcsoportok.nev AS csoportnev
            FROM telefonkonyvbeosztasok
                LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
            WHERE telefonkonyvcsoportok.id > 1 AND (telefonkonyvbeosztasok.felhid IS NULL $beowhere)
            ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");

    $rendfokozatok = mySQLConnect("SELECT * FROM rendfokozatok ORDER BY id");

    $nevelotagok = mySQLConnect("SELECT * FROM nevelotagok");

    $titulusok = mySQLConnect("SELECT * FROM titulusok");

    $felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok ORDER BY nev");

    $tkonyvfelhasznalok = mySQLConnect("SELECT nev FROM telefonkonyvfelhasznalok;");

    $csoportok = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

    $modkerwhere = $magyarazat = $beosztas = $felhid = $beosztasnev = $elotag = $nev = $titulus = $rendfokozat = $belsoszam = $belsoszam2 = $kozcelu = 
    $fax = $kozcelufax = $mobil = $csoport = $csoportid = $felhasznalo = $megjegyzes = $modositasoka = $felhid = $adminmegjegyzes = null;
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
            $telefonszam = mysqli_fetch_assoc($modositasok);
        }
    }    
    elseif(isset($_GET['modid']))
    {
        $modid = $_GET['modid'];
        $modositasok = mySQLConnect("SELECT telefonkonyvvaltozasok.beosztas AS beosztas,
                telefonkonyvvaltozasok.beosztasnev AS beosztasnev,
                felhid,
                telefonkonyvvaltozasok.elotag AS elotag,
                telefonkonyvvaltozasok.nev AS nev,
                telefonkonyvvaltozasok.titulus AS titulus,
                telefonkonyvvaltozasok.rendfokozat,
                belsoszam,
                belsoszam2,
                kozcelu,
                fax,
                kozcelufax,
                telefonkonyvvaltozasok.mobil AS mobil,
                telefonkonyvvaltozasok.felhasznalo AS felhasznalo,
                sorrend,
                csoport,
                telefonkonyvvaltozasok.megjegyzes AS megjegyzes
            FROM telefonkonyvvaltozasok
            WHERE telefonkonyvvaltozasok.id = $modid
                AND telefonkonyvvaltozasok.modid = (SELECT id FROM telefonkonyvmodositaskorok ORDER BY id DESC LIMIT 1)
                AND telefonkonyvvaltozasok.allapot > 1 AND telefonkonyvvaltozasok.allapot < 4;");

        if(mysqli_num_rows($modositasok) == 0)
        {
            echo "<h2>Nincs ilyen azonosítójú telefonszám, vagy nincs jogosultsága a szerkesztéséhez!</h2>";
            $irhat = false;
        }
        else
        {
            $telefonszam = mysqli_fetch_assoc($modositasok);
        }
    }

    if($irhat)
    {
        $beosztasnev = $telefonszam['beosztasnev'];
        $beosztas = $telefonszam['beosztas'];
        $felhid = $telefonszam['felhid'];
        $elotag = $telefonszam['elotag'];
        $nev = $telefonszam['nev'];
        $titulus = $telefonszam['titulus'];
        $rendfokozat = $telefonszam['rendfokozat'];
        $felhasznalo = $telefonszam['felhasznalo'];
        $megjegyzes = $telefonszam['megjegyzes'];
        $csoport = $telefonszam['csoport'];
        $sorrend = $telefonszam['sorrend'];
        if($telefonszam['felhid'])
        {
            
        }
        if($telefonszam['beosztas'])
        {
            
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

        $oldalcim = "Telefonszám szerkesztése";

        ?><datalist id="tkonyvfelhasznalok"><?php
            foreach($tkonyvfelhasznalok as $felhasznalo)
            {
                ?><option><?=$felhasznalo['nev']?></option><?php
            }
        ?></datalist><?php

        if($beosztas || $felhid)
        {
            $beowhere = "(telefonkonyvvaltozasok.beosztas = $beosztas OR telefonkonyvvaltozasok.felhid = $felhid) AND";
        }


        $modositaskerelmek = mySQLConnect("SELECT felhasznalok.nev AS bejelento, timestamp
                FROM telefonkonyvvaltozasok
                    INNER JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
                WHERE $beowhere telefonkonyvvaltozasok.modid = (SELECT id FROM telefonkonyvmodositaskorok ORDER BY id DESC LIMIT 1)
                    AND telefonkonyvvaltozasok.allapot < 2;");

        if(mysqli_num_rows($modositaskerelmek) > 0)
        {
            $modositasadatok = mysqli_fetch_assoc($modositaskerelmek);

            $onloadfelugro = "A kért felhasználóra, vagy beosztásra már küldött be elfogadásra váró módosítási kérelmet " . $modositasadatok['bejelento'] . " " . $modositasadatok['timestamp'] . "-kor. Biztosan szeretnél másik kérelmet beadni?";
        }

        include('././templates/edit.tpl.php');
    }
}