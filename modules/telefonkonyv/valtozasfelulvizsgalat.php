<?php

if(!($globaltelefonkonyvadmin = telefonKonyvAdminCheck($mindir)))
{
    getPermissionError();
}
else
{
    if(count($_POST) > 0 || (isset($_GET['action']) && $_GET['action'] == "discard"))
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvdb.php");
        
        redirectToGyujto("telefonkonyvvaltozasok");
    }

    if(isset($_GET['id']))
    {
        $alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

        $rendfokozatok = mySQLConnect("SELECT * FROM rendfokozatok ORDER BY id");

        $nevelotagok = mySQLConnect("SELECT * FROM nevelotagok");

        $titulusok = mySQLConnect("SELECT * FROM titulusok");

        $felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok ORDER BY nev");

        $csoportok = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL;");

        $magyarazat = $sorrend = $elotag = $nev = $titulus = $rendfokozat = $belsoszam = $belsoszam2 = $kozcelu = 
        $fax = $kozcelufax = $mobil = $csoport = $csoportid = $felhasznalo = $megjegyzes = $modositasoka = $timestamp =
        $bejelento = $modositasoka = $allapot = $felhid = $beosztasnev = $beowhere = null;
        $beosztas = $origbeosztas = 0;

        $button = "Módosítások rögzítése";
        $irhat = true;
        $form = "modules/telefonkonyv/forms/telefonszamfelulvizsgalform";
        $oldalcim = "Számváltozás felülvizsgálata";

        $valtozasid = $_GET['id'];

        $valtozas = mySQLConnect("SELECT telefonkonyvvaltozasok.beosztas AS beosztas,
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
            felhasznalo,
            megjegyzes,
            telefonkonyvvaltozasok.sorrend AS sorrend,
            timestamp,
            felhasznalok.nev AS bejelento,
            modositasoka,
            adminmegjegyzes,
            allapot
        FROM telefonkonyvvaltozasok
            LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
        WHERE telefonkonyvvaltozasok.id = $valtozasid");

        if(mysqli_num_rows($valtozas) == 0)
        {
            echo "<h2>Nincs ilyen azonosítójú módosítás, vagy nincs jogosultsága a megtekintéséhez!</h2>";
            $irhat = false;
        }
        else
        {
            $valtozas = mysqli_fetch_assoc($valtozas);
            $telszamid = $valtozas['beosztas'];
            $felhid = $valtozas['felhid'];
            $where = null;

            if($telszamid)
            {
                $where = "WHERE telefonkonyvbeosztasok.id = $telszamid";
            }
            elseif($felhid)
            {
                $where = "WHERE telefonkonyvfelhasznalok.id = $felhid";
            }

            if($where)
            {
                $telefonszam = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS beosztas,
                        telefonkonyvbeosztasok.nev AS origbeosztasnev,
                        telefonkonyvfelhasznalok.id AS origfelhid,
                        elotag,
                        nevelotagok.nev AS origelotag,
                        telefonkonyvfelhasznalok.nev AS nev,
                        titulusok.nev AS origtitulus,
                        titulus,
                        rendfokozat,
                        rendfokozatok.nev AS origrendfokozat,
                        felhasznalok.nev AS origfelhasznalo,
                        belsoszam,
                        belsoszam2,
                        kozcelu,
                        fax,
                        kozcelufax,
                        mobil,
                        telefonkonyvbeosztasok.sorrend AS origsorrend,
                        telefonkonyvcsoportok.nev AS csoportnev,
                        telefonkonyvcsoportok.id AS csoport,
                        felhasznalo,
                        megjegyzes
                    FROM telefonkonyvbeosztasok
                        LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
                        LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
                        LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
                        LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
                        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                        LEFT JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
                    $where");
                if(mysqli_num_rows($telefonszam) == 0)
                {
                    $telefonszam = false;
                }
                else
                {
                    $telefonszam = mysqli_fetch_assoc($telefonszam);
                }
            }
            else
            {
                $telefonszam = true;
                $origelotag = $origelotagnev = $orignev = $origtitulus = $origrendfokozat = 
                $origtitulusnev = $origrendfokozatnev = $origbelsoszam = $origbelsoszam2 = $origkozcelu =
                $origfax = $origkozcelufax = $origmobil = $origcsoport = $origcsoportnev = $origfelhasznalo =
                $origfelhasznalonev = $origmegjegyzes = $origfelhid = $origbeosztasnev = $origsorrend = null;
            }

            if(!$telefonszam)
            {
                echo "<h2>Nincs ilyen azonosítójú telefonszám, vagy nincs jogosultsága a megtekintéséhez!</h2>";
                $irhat = false;
            }
            else
            {
                $beosztas = $valtozas['beosztas'];
                $elotag = $valtozas['elotag'];
                $nev = $valtozas['nev'];
                $titulus = $valtozas['titulus'];
                $rendfokozat = $valtozas['rendfokozat'];
                $belsoszam = $valtozas['belsoszam'];
                $belsoszam2 = $valtozas['belsoszam2'];
                $kozcelu = $valtozas['kozcelu'];
                $fax = $valtozas['fax'];
                $kozcelufax = $valtozas['kozcelufax'];
                $mobil = $valtozas['mobil'];
                $csoport = $valtozas['csoport'];
                $felhasznalo = $valtozas['felhasznalo'];
                $megjegyzes = $valtozas['megjegyzes'];
                $sorrend = $valtozas['sorrend'];
                $timestamp = $valtozas['timestamp'];
                $bejelento = $valtozas['bejelento'];
                $modositasoka = $valtozas['modositasoka'];
                $allapot = $valtozas['allapot'];
                $felhid = $valtozas['felhid'];
                $beosztasnev = $valtozas['beosztasnev'];
                $adminmegjegyzes = $valtozas['adminmegjegyzes'];

                if($where)
                {
                    $origbeosztas = $telefonszam['beosztas'];
                    $origelotag = $telefonszam['elotag'];
                    $origelotagnev = $telefonszam['origelotag'];
                    $orignev = $telefonszam['nev'];
                    $origtitulus = $telefonszam['titulus'];
                    $origrendfokozat = $telefonszam['rendfokozat'];
                    $origtitulusnev = $telefonszam['origtitulus'];
                    $origrendfokozatnev = $telefonszam['origrendfokozat'];
                    $origbelsoszam = $telefonszam['belsoszam'];
                    $origbelsoszam2 = $telefonszam['belsoszam2'];
                    $origkozcelu = $telefonszam['kozcelu'];
                    $origfax = $telefonszam['fax'];
                    $origkozcelufax = $telefonszam['kozcelufax'];
                    $origmobil = $telefonszam['mobil'];
                    $origcsoport = $telefonszam['csoport'];
                    $origcsoportnev = $telefonszam['csoportnev'];
                    $origfelhasznalo = $telefonszam['felhasznalo'];
                    $origfelhasznalonev = $telefonszam['origfelhasznalo'];
                    $origmegjegyzes = $telefonszam['megjegyzes'];
                    $origfelhid = $telefonszam['origfelhid'];
                    $origbeosztasnev = $telefonszam['origbeosztasnev'];
                    $origsorrend = $telefonszam['origsorrend'];

                    $beowhere =  "OR telefonkonyvbeosztasok.id = $origbeosztas OR telefonkonyvbeosztasok.id = $beosztas";
                }
            }

            $beosztasok = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS id,
                    telefonkonyvbeosztasok.nev AS nev,
                    telefonkonyvcsoportok.nev AS csoportid,
                    telefonkonyvcsoportok.nev AS csoportnev
                FROM telefonkonyvbeosztasok
                    LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                WHERE telefonkonyvcsoportok.id > 1 AND (telefonkonyvbeosztasok.felhid IS NULL $beowhere)
                ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");
        }

        if($irhat)
        {
            include('././templates/edit.tpl.php');
        }
    }
}