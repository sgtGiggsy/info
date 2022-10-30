<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
if(!@$mindolvas || (isset($_GET['action']) && !$mindir))
{
    getPermissionError();
}
else
{
    $magyarazat = null;
    $alapform = "modules/telefonszamok/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete" || isset($_POST['beKuld']))
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "csvimport" || $_GET['action'] == "edit")
        {
            $irhat = true;
        }
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt, de olvasási joggal továbbenged
    if(!$irhat && !$dbir && !$mindolvas)
    {
        getPermissionError();
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        if(isset($_POST['beKuld']))
        {
            // Az irhat false-ra állítása, hogy csak akkor legyen true, ha a fájl importálás sikeres
            $irhat = false;
            $filetypes = array('.csv');
            $mediatype = array('application/vnd.ms-excel', 'text/csv');

            $fajl = $_FILES["csvinput"];
            //print_r($fajl);
            if (!in_array($fajl['type'], $mediatype))
            {
                $uzenet = "A fájl típusa nem megengedett: " . $fajl['name'];
            }
            else
            {
                $fajlnev = str_replace(".", time() . ".", $fajl['name']);
                $finalfile = $UPLOAD_FOLDER.strtolower($fajlnev);
                if(file_exists($finalfile))
                {
                    $uzenet = "A feltölteni kívánt fájl már létezik: " . $fajlnev;
                }
                else
                {
                    move_uploaded_file($fajl['tmp_name'], $finalfile);
                    $uzenet = 'A fájl feltöltése sikeresen megtörtént: ' . $fajlnev;
                    $irhat = true;
                }
            }
        }
        
        if($irhat)
        {
            include("./modules/telefonszamok/db/telefonszamdb.php");

            // Az errorcount-ot a CSV import művelet állítja elő. Amennyiben van errorcount,
            // úgy CSV importálás történt, de ha az errorcount értéke nulla,
            // úgy az importálás sikeres volt.
            if(isset($errorcount) && $errorcount == 0)
            {
                // Ha CSV importálás történt, úgy nincs automatikus visszairányítás sehova.
            }
            elseif(!isset($errorcount))
            {
                // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
                afterDBRedirect($con);
            }
        }
        // Ha az irhat változó itt false, akkor nem sikerült a CSV feltöltése.
        else
        {
            echo "<h2>" . $uzenet . "</h2>";
        }
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $id = $szam = $cimke = $port = $jog = $tkozpontport = $megjegyzes = $tipus = $magyarazat = null;

        if(isset($_GET['id']))
        {
            $telefonszamid = $_GET['id'];
            $telefonszamszerk = mySQLConnect("SELECT * FROM telefonszamok WHERE id = $telefonszamid;");
            $telefonszamszerk = mysqli_fetch_assoc($telefonszamszerk);

            $portok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev
                FROM portok
                    INNER JOIN vegpontiportok ON portok.id = vegpontiportok.port
                    LEFT JOIN epuletek ON vegpontiportok.epulet = epuletek.id
                ORDER BY epuletek.szam + 0, LENGTH(portok.port), portok.port;");
            $jogok = mySQLConnect("SELECT * FROM telefonjogosultsagok;");
            $tkozpontportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, telefonkozpontok.nev AS kozpontnev
                FROM portok
                    INNER JOIN tkozpontportok ON portok.id = tkozpontportok.port
                    INNER JOIN telefonkozpontok ON tkozpontportok.eszkoz = telefonkozpontok.eszkoz
                ORDER BY telefonkozpontok.nev, LENGTH(portok.port), portok.port;");
            $tipusok = mySQLConnect("SELECT * FROM telefonkeszulektipusok;");

            $id = $telefonszamszerk['id'];
            $szam = $telefonszamszerk['szam'];
            $cimke = $telefonszamszerk['cimke'];
            $port = $telefonszamszerk['port'];
            $jog = $telefonszamszerk['jog'];
            $tkozpontport = $telefonszamszerk['tkozpontport'];
            $megjegyzes = $telefonszamszerk['megjegyzes'];
            $tipus = $telefonszamszerk['tipus'];

            $button = "Telefonszám szerkesztése";
            $oldalcim = "A(z) " . $szam . "-s telefonszám szerkesztése";

            $form = $alapform . "telefonszamform";
        }

        elseif ($_GET['action'] == "csvimport")
        {
            $button = "Importálás megkezdése";
            $oldalcim = "Telefonszámok és portok importálása a központból";

            $form = $alapform . "telefonszamcsvimport";
        }

        include('./templates/edit.tpl.php');

    }

    // Ha írási művelet nem lesz, ellenőrizni kell, hogy van-e kiválasztott telefonszám. Ha nincs, hiba dobása
    elseif(!isset($id))
    {
        getPermissionError();
    }

    // Akkor futunk ki erre az ágra, ha van olvasási jog, és kiválasztott telefonszám, de más nincs. Ez a sima megjelenítő felület
    else
    {
        $telefonszam = mySQLConnect("SELECT telefonszamok.szam AS szam, cimke, manualis,
                telefonszamok.megjegyzes AS megjegyzes,
                tkozpontportok.megjegyzes AS portmegjegyzes,
                portok.port AS kozpontport,
                telefonjogosultsagok.nev AS jogosultsag,
                telefonkeszulektipusok.nev AS keszulektipus,
                vport.port AS vegpont,
                epuletek.szam AS epuletszam,
                epuletek.nev AS epuletnev,
                epulettipusok.tipus AS epulettipus,
                helyisegek.helyisegszam AS helyisegszam,
                helyisegek.helyisegnev AS helyisegnev,
                beepitesek.nev AS kozpont
            FROM telefonszamok
                LEFT JOIN portok ON telefonszamok.tkozpontport = portok.id
                LEFT JOIN tkozpontportok ON tkozpontportok.port = telefonszamok.tkozpontport
                LEFT JOIN beepitesek ON tkozpontportok.eszkoz = beepitesek.eszkoz
                LEFT JOIN telefonkeszulektipusok ON telefonszamok.tipus = telefonkeszulektipusok.id
                LEFT JOIN telefonjogosultsagok ON telefonszamok.jog = telefonjogosultsagok.id
                LEFT JOIN portok vport ON telefonszamok.port = vport.id
                LEFT JOIN vegpontiportok ON vport.id = vegpontiportok.port
                LEFT JOIN epuletek ON vegpontiportok.epulet = epuletek.id
                LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
                LEFT JOIN helyisegek ON vegpontiportok.helyiseg = helyisegek.id
            WHERE telefonszamok.id = $id;");
        $telefonszam = mysqli_fetch_assoc($telefonszam);

        ?><div class="infobox">
            <div class="infoboxtitle"><?=$telefonszam['szam']?><?php
                if($mindir)
                {
                    ?><a class="help" href="<?=$RootPath?>/telefonszam/<?=$id?>?action=edit"><img src='<?=$RootPath?>/images/edit.png' alt='Telefonszám szerkesztése' title='Telefonszám szerkesztése'/></a><?php
                }
            ?></div>
            <div class="infoboxbody">
                <div class="infoboxbodytwocol">
                    <div>Telefonszám</div><div><?=$telefonszam['szam']?></div>
                    <div>Címke</div><div><?=$telefonszam['cimke']?></div>
                    <div>Lage port</div><div><?=$telefonszam['kozpont']?> központ, <?=$telefonszam['kozpontport']?></div>
                    <div>Jogosultság</div><div><?=$telefonszam['jogosultsag']?></div>
                    <div>Készüléktípus</div><div><?=$telefonszam['keszulektipus']?></div>
                    <div>Végpont</div><div><?=$telefonszam['epuletszam']?> <?=$telefonszam['epulettipus']?> <?=$telefonszam['helyisegszam']?><?=($telefonszam['helyisegszam']) ? ". helyiség" : "" ?> <?=$telefonszam['vegpont']?><?=($telefonszam['vegpont']) ? "-s port" : "" ?></div>
                    <div>Szám megjegyzése</div><div><?=$telefonszam['megjegyzes']?></div>
                    <div>Port megjegyzése</div><div><?=$telefonszam['portmegjegyzes']?></div>
                    <div>Módosítva importálás óta</div><div><?=($telefonszam['manualis'] == 1) ? "Igen" : "Nem" ?></div>
                </div>
            </div>
        </div><?php
    }
}