<?php

if(@!$csoportir)
{
    getPermissionError();
}
else
{
    $magyarazat = $beosztas = $modid = $felhid = $beosztasnev = $elotag = $nev = $titulus = $rendfokozat = $belsoszam = $belsoszam2 = $kozcelu = $fax = $kozcelufax = $currbeoid =
    $mobil = $csoport = $csoportid = $felhasznalo = $megjegyzes = $modositasoka = $felhid = $adminmegjegyzes = $admintimestamp = $modwhere = $meglevobeo = $addnew = $zarozar = null;
    $sorrend = 9999;
    $allapot = 0;
    
    $globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir);
    
    if(!$globaltelefonkonyvadmin)
        $csoportjogok = telefonKonyvCsoportjogok();

    if(isset($_GET['id']) && $_GET['id'])
        $currbeoid = $_GET['id'];

    $tkonyvwhereadmin = array(
        'where' => false,
        'mezonev' => false,
        'and' => true
    );

    $tkonyvwheremod = array(
        'where' => false,
        'mezonev' => "telefonkonyvbeosztasok_mod",
        'and' => true
    );

    $belsoelohivo = $_SESSION[getenv('SESSION_NAME').'belsoelohivo'];
    $varosielohivo = $_SESSION[getenv('SESSION_NAME').'varosielohivo'];
    $mobilelohivo = $_SESSION[getenv('SESSION_NAME').'mobilelohivo'];
    $where = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwhereadmin);
    $wheremod = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwheremod);
    $csillaggaljelolt = "A csillaggal jelölt mezők kitöltése kötelező.";

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvdb.php");
        
        redirectToGyujto("telefonkonyv");
    }

    $button = "Mentés";
    $irhat = true;
    $form = "modules/telefonkonyv/forms/telefonszamszerkesztform";
    $oldalcim = "Új szám felvitele";

    if(isset($_GET['id']))
    {
        $oldalcim = "Telefonszám szerkesztése";
        
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
        }
    }
    elseif(isset($_GET['modid']))
    {
        $modid = $_GET['modid'];

        $oldalcim = "Beküldött módosítás állapota: ";

        $modositasok = mySQLConnect("SELECT telefonkonyvvaltozasok.origbeoid AS beosztas,
                telefonkonyvbeosztasok_mod.nev AS beosztasnev,
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
                megjegyzes,
                adminmegjegyzes,
                telefonkonyvvaltozasok.allapot AS allapot
            FROM telefonkonyvbeosztasok_mod
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok_mod.felhid = telefonkonyvfelhasznalok.id
                LEFT JOIN telefonkonyvvaltozasok ON telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok_mod.id
            WHERE telefonkonyvvaltozasok.id = $modid
                $wheremod;");
        $telefonszam = mysqli_fetch_assoc($modositasok);
        $allapot = $telefonszam['allapot'];

        if((!@$telefonszam['beosztas'] && $telefonszam['beosztas'] != 0) || (!$globaltelefonkonyvadmin && !in_array($telefonszam['csoport'], $csoportjogok)))
        {
            echo "<h2>Erre a számra ebben a hónapban már lett leadva véglegesített módosítási kérelem,
                nem létezik, vagy nincs jogosultsága a szerkesztéséhez!</h2>";
            $irhat = false;
        }

        switch($allapot)
        {
            case 1: $oldalcim .= "Beküldve, még nem került ellenőrzésre"; break;
            case 2: $oldalcim .= "Változtatásokkal elfogadásra került"; break;
            case 3: $oldalcim .= "Elfogadásra került"; break;
            case 4: $oldalcim .= "Véglegesen rögzítve"; break;
            default: $oldalcim .= "Adminisztrátor által elvetve";
        }
    }
    elseif(isset($_GET['action']) && $_GET['action'] == "addnew")
    {
        $addnew = true;
    }

    if($irhat)
    {
        $telkonyvcsoport = null;
        if(isset($telefonszam['csoport']))
        {
            $telkonyvcsoport = $telefonszam['csoport'];
        }
        $tkonyvwherecsoport = array(
            'where' => false,
            'mezonev' => true,
            'and' => true,
            'currcsopid' => $telkonyvcsoport
        );

        $wherecsoport = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwherecsoport);
        
        $rendfokozatok = mySQLConnect("SELECT * FROM rendfokozatok ORDER BY id");

        $nevelotagok = mySQLConnect("SELECT * FROM nevelotagok");
    
        $titulusok = mySQLConnect("SELECT * FROM titulusok");
    
        $felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok ORDER BY nev");
    
        $tkonyvfelhasznalok = mySQLConnect("SELECT nev FROM telefonkonyvfelhasznalok;");
    
        $csoportquery = "SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL $wherecsoport;";
        //var_dump($csoportquery);
        $csoportok = mySQLConnect($csoportquery);

        if(isset($telefonszam) && $telefonszam)
        {
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

        $beosztasok = getBeosztasList($where, $beosztas, $modid);

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

        if(mysqli_num_rows($modositaskerelmek) > 0 && !isset($_GET['modid']) && !isset($_GET['action']))
        {
            $modositasadatok = mysqli_fetch_assoc($modositaskerelmek);

            $onloadfelugro = "A kiválasztott felhasználóra, vagy beosztásra már küldött be elfogadásra váró módosítási kérelmet " . $modositasadatok['bejelento'] . " " . $modositasadatok['timestamp'] . "-kor. Biztosan szeretnél másik kérelmet beadni?";
        }

        if(isset($csoportjogok) && !in_array($csoport, $csoportjogok) && !(isset($_GET['action']) && $_GET['action'] == "addnew"))
        {
            $onloadfelugro = "A kiválasztott felhasználó, vagy beosztás nem egy általad kezelt alegységhez tartozik. Biztosan őt szeretnéd szerkeszteni?";
        }

        include('././templates/edit.tpl.php');
    }
}