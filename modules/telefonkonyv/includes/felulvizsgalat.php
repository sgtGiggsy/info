<?php

$nyithelp = true;
if(!$globaltelefonkonyvadmin)
{
    header("Location: $RootPath/telefonkonyv/valtozas?modid=$id");
    //getPermissionError();
}
else
{
    if(count($_POST) > 0 || (isset($_GET['action']) && $_GET['action'] == "discard"))
    {
        $irhat = true;
        include("./modules/telefonkonyv/db/telefonkonyvdb.php");
        
        redirectToGyujto("valtozasok");
    }

    if($id)
    {
        $alegysegek = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1;");

        $rendfokozatok = mySQLConnect("SELECT * FROM rendfokozatok ORDER BY id");

        $nevelotagok = mySQLConnect("SELECT * FROM nevelotagok");

        $titulusok = mySQLConnect("SELECT * FROM titulusok");

        $felhasznalok = mySQLConnect("SELECT id, nev, felhasznalonev FROM felhasznalok ORDER BY nev");

        $csoportok = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL;");

        $magyarazat = $beowhere = $adminmegjegyzes = $valtozasid = $origbeoid = $beoid = $beosztas = $sorrend =
        $elotag = $elotagid = $felhid = $nev = $titulusid = $titulus = $rendfokozatid = $rendfokozat =
        $belsoszam = $belsoszam2 = $kozcelu = $fax = $kozcelufax = $mobil = $csoport = $aduserid =
        $felhasznalonev = $modosito = $megjegyzes = $allapot = $modositasoka = $modositasideje =
        $origbeosztas = $origsorrend =  $origelotag = $origelotagid = $origfelhid = $orignev = $origcsoportid =
        $origtitulusid = $origtitulus = $origrendfokozatid = $origrendfokozat = $origbelsoszam =
        $origbelsoszam2 = $origkozcelu = $origfax = $origkozcelufax = $origmobil = $origcsoport =
        $origaduserid = $origfelhasznalonev = $origmodosito = $origmegjegyzes = $origallapot = $telefonszam = null;

        $button = "Módosítások rögzítése";
        $irhat = true;
        $form = "modules/telefonkonyv/forms/telefonszamfelulvizsgalform";
        $oldalcim = "Számváltozás felülvizsgálata";

        $valtozasid = $id;

        $valtozas = mySQLConnect("SELECT telefonkonyvvaltozasok.id AS valtozasid,
                telefonkonyvvaltozasok.origbeoid AS origbeoid,
                telefonkonyvbeosztasok_mod.id AS beoid,
                telefonkonyvbeosztasok_mod.nev AS beosztas,
                telefonkonyvbeosztasok_mod.sorrend AS sorrend,
                nevelotagok.nev AS elotag,
                nevelotagok.id AS elotagid,
                telefonkonyvfelhasznalok.id AS felhid,
                telefonkonyvfelhasznalok.nev AS nev,
                titulusok.id AS titulusid,
                titulusok.nev AS titulus,
                rendfokozatok.id AS rendfokozatid,
                rendfokozatok.nev AS rendfokozat,
                telefonkonyvbeosztasok_mod.belsoszam AS belsoszam,
                telefonkonyvbeosztasok_mod.belsoszam2 AS belsoszam2,
                telefonkonyvbeosztasok_mod.kozcelu AS kozcelu,
                telefonkonyvbeosztasok_mod.fax AS fax,
                telefonkonyvbeosztasok_mod.kozcelufax AS kozcelufax,
                telefonkonyvbeosztasok_mod.torolve AS torolve,
                telefonkonyvfelhasznalok.mobil AS mobil,
                telefonkonyvcsoportok.id AS csoportid,
                telefonkonyvcsoportok.nev AS csoport,
                aduser.nev AS adusernev,
                telefonkonyvfelhasznalok.felhasznalo AS aduserid,
                felhasznalok.felhasznalonev AS felhasznalonev,
                felhasznalok.nev AS modosito,
                telefonkonyvbeosztasok_mod.megjegyzes AS megjegyzes,
                telefonkonyvvaltozasok.allapot AS allapot,
                telefonkonyvvaltozasok.modositasoka AS modositasoka,
                telefonkonyvvaltozasok.timestamp AS modositasideje,
                telefonkonyvvaltozasok.adminmegjegyzes AS adminmegjegyzes
            FROM telefonkonyvvaltozasok
                LEFT JOIN telefonkonyvbeosztasok_mod ON telefonkonyvvaltozasok.ujbeoid = telefonkonyvbeosztasok_mod.id
                LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvvaltozasok.ujfelhid = telefonkonyvfelhasznalok.id
                LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
                LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
                LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
                LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok_mod.csoport = telefonkonyvcsoportok.id
                LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
                LEFT JOIN felhasznalok aduser ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
        WHERE telefonkonyvvaltozasok.id = $valtozasid");

        if(mysqli_num_rows($valtozas) == 0)
        {
            echo "<h2>Nincs ilyen azonosítójú módosítás, vagy nincs jogosultsága a megtekintéséhez!</h2>";
            $irhat = false;
        }
        else
        {
            $javascriptfiles[] = "modules/telefonkonyv/includes/telefonkonyv.js";
            $valtozas = mysqli_fetch_assoc($valtozas);

            if($valtozas['origbeoid'])
            {
                $telefonszam = mySQLConnect("SELECT telefonkonyvvaltozasok.id AS valtozasid,
                        telefonkonyvbeosztasok.id AS beoid,
                        telefonkonyvbeosztasok.nev AS beosztas,
                        telefonkonyvbeosztasok.sorrend AS sorrend,
                        nevelotagok.nev AS elotag,
                        nevelotagok.id AS elotagid,
                        telefonkonyvfelhasznalok.id AS felhid,
                        telefonkonyvfelhasznalok.nev AS nev,
                        titulusok.id AS titulusid,
                        titulusok.nev AS titulus,
                        rendfokozatok.id AS rendfokozatid,
                        rendfokozatok.nev AS rendfokozat,
                        telefonkonyvbeosztasok.belsoszam AS belsoszam,
                        telefonkonyvbeosztasok.belsoszam2 AS belsoszam2,
                        telefonkonyvbeosztasok.kozcelu AS kozcelu,
                        telefonkonyvbeosztasok.fax AS fax,
                        telefonkonyvbeosztasok.kozcelufax AS kozcelufax,
                        telefonkonyvfelhasznalok.mobil AS mobil,
                        telefonkonyvcsoportok.id AS csoportid,
                        telefonkonyvcsoportok.nev AS csoport,
                        telefonkonyvfelhasznalok.felhasznalo AS aduserid,
                        aduser.nev AS adusernev,
                        felhasznalok.felhasznalonev AS felhasznalonev,
                        felhasznalok.nev AS bejelento,
                        telefonkonyvbeosztasok.megjegyzes AS megjegyzes,
                        telefonkonyvvaltozasok.allapot AS allapot,
                        telefonkonyvvaltozasok.modositasoka AS modositasoka,
                        telefonkonyvvaltozasok.timestamp AS modositasideje
                    FROM telefonkonyvvaltozasok
                        LEFT JOIN telefonkonyvbeosztasok ON telefonkonyvvaltozasok.origbeoid = telefonkonyvbeosztasok.id
                        LEFT JOIN telefonkonyvfelhasznalok ON telefonkonyvvaltozasok.origfelhid = telefonkonyvfelhasznalok.id
                        LEFT JOIN nevelotagok ON telefonkonyvfelhasznalok.elotag = nevelotagok.id
                        LEFT JOIN titulusok ON telefonkonyvfelhasznalok.titulus = titulusok.id
                        LEFT JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
                        LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                        LEFT JOIN felhasznalok ON telefonkonyvvaltozasok.bejelento = felhasznalok.id
                        LEFT JOIN felhasznalok aduser ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
                WHERE telefonkonyvvaltozasok.id = $valtozasid");
                if(mysqli_num_rows($telefonszam) == 0)
                {
                    $telefonszam = false;
                }
                else
                {
                    $telefonszam = mysqli_fetch_assoc($telefonszam);
                }
            }

            $valtozasid = $valtozas['valtozasid'];
            $origbeoid = $valtozas['origbeoid'];
            $beoid = $valtozas['beoid'];
            $beosztas = $valtozas['beosztas'];
            $sorrend = $valtozas['sorrend'];
            $elotag = $valtozas['elotag'];
            $elotagid = $valtozas['elotagid'];
            $felhid = $valtozas['felhid'];
            $nev = $valtozas['nev'];
            $titulusid = $valtozas['titulusid'];
            $titulus = $valtozas['titulus'];
            $rendfokozatid = $valtozas['rendfokozatid'];
            $rendfokozat = $valtozas['rendfokozat'];
            $belsoszam = $valtozas['belsoszam'];
            $belsoszam2 = $valtozas['belsoszam2'];
            $kozcelu = $valtozas['kozcelu'];
            $fax = $valtozas['fax'];
            $kozcelufax = $valtozas['kozcelufax'];
            $mobil = $valtozas['mobil'];
            $csoportid = $valtozas['csoportid'];
            $csoport = $valtozas['csoport'];
            $adusernev = $valtozas['adusernev'];
            $aduserid = $valtozas['aduserid'];
            $felhasznalonev = $valtozas['felhasznalonev'];
            $modosito = $valtozas['modosito'];
            $megjegyzes = $valtozas['megjegyzes'];
            $allapot = $valtozas['allapot'];
            $modositasoka = $valtozas['modositasoka'];
            $modositasideje = $valtozas['modositasideje'];
            $adminmegjegyzes = $valtozas['adminmegjegyzes'];

            if($telefonszam)
            {
                $origbeoid = $telefonszam['beoid'];
                $origbeosztas = $telefonszam['beosztas'];
                $origsorrend = $telefonszam['sorrend'];
                $origelotag = $telefonszam['elotag'];
                $origelotagid = $telefonszam['elotagid'];
                $origfelhid = $telefonszam['felhid'];
                $orignev = $telefonszam['nev'];
                $origtitulusid = $telefonszam['titulusid'];
                $origtitulus = $telefonszam['titulus'];
                $origrendfokozatid = $telefonszam['rendfokozatid'];
                $origrendfokozat = $telefonszam['rendfokozat'];
                $origbelsoszam = $telefonszam['belsoszam'];
                $origbelsoszam2 = $telefonszam['belsoszam2'];
                $origkozcelu = $telefonszam['kozcelu'];
                $origfax = $telefonszam['fax'];
                $origkozcelufax = $telefonszam['kozcelufax'];
                $origmobil = $telefonszam['mobil'];
                $origcsoportid = $telefonszam['csoportid'];
                $origcsoport = $telefonszam['csoport'];
                $origaduserid = $telefonszam['aduserid'];
                $origadusernev = $telefonszam['adusernev'];
                $origfelhasznalonev = $telefonszam['felhasznalonev'];
                $origmegjegyzes = $telefonszam['megjegyzes'];
                $origallapot =  $telefonszam['allapot'];
                
                $beowhere =  "OR telefonkonyvbeosztasok.id = $origbeoid";
            }

            if($valtozas['torolve'] == 1)
            {
                $oldalcim = "A(z) $csoport alegység $beosztas beosztásának törlése";
            }

            $beosztasok = mySQLConnect("SELECT telefonkonyvbeosztasok.id AS id,
                    telefonkonyvbeosztasok.nev AS nev,
                    telefonkonyvcsoportok.nev AS csoportid,
                    telefonkonyvcsoportok.nev AS csoportnev
                FROM telefonkonyvbeosztasok
                    LEFT JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
                WHERE telefonkonyvcsoportok.id > 1 AND telefonkonyvbeosztasok.allapot > 2 AND (telefonkonyvbeosztasok.felhid IS NULL $beowhere)
                ORDER BY telefonkonyvcsoportok.sorrend, telefonkonyvbeosztasok.sorrend;");
        }

        if($irhat)
        {
            include('././templates/edit.tpl.php');
        }
    }
}