<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
if(!@$mindolvas || (isset($_GET['action']) && !$mindir))
{
    getPermissionError();
}
else
{
    $magyarazat = null;
    $alapform = "modules/raktarak/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
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
        include("./modules/raktarak/db/raktardb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        afterDBRedirect($con);
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $nev = $alakulat = $helyiseg = null;

        $button = "Új raktár";
        $oldalcim = "Új raktár hozzáadása";

        $form = $alapform . "raktarszerkform";

        if(isset($_GET['id']))
        {
            $raktar = mySQLConnect("SELECT * FROM raktarak WHERE id = $id;");
            $raktar = mysqli_fetch_assoc($raktar);

            $nev = $raktar['nev'];
            $helyiseg = $raktar['helyiseg'];
            $alakulat = $raktar['alakulat'];

            $button = "Szerkesztés";
            $oldalcim = "Raktár szerkesztése";
        }

        include('./templates/edit.tpl.php');

    }

    // Ha írási művelet nem lesz, ellenőrizni kell, hogy van-e kiválasztott épület. Ha nincs, hiba dobása
    elseif(!isset($id))
    {
        getPermissionError();
    }

    // Akkor futunk ki erre az ágra, ha van olvasási jog, és kiválasztott épület, de más nincs. Ez a sima megjelenítő felület
    else
    {
        $szuresek = getWhere("raktarak.id = $id AND ((beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL) OR beepitesek.id IS NULL)");
        $where = $szuresek['where'];

        $mindeneszkoz = mySQLConnect("SELECT
                eszkozok.id AS id,
                beepitesek.id AS beepid,
                sorozatszam,
                gyartok.nev AS gyarto,
                modellek.modell AS modell,
                varians,
                eszkoztipusok.nev AS tipus,
                beepitesideje,
                kiepitesideje,
                modellek.tipus AS tipusid,
                alakulatok.rovid AS tulajdonos,
                beepitesek.nev AS beepitesinev,
                ipcimek.ipcim AS ipcim,
                beepitesek.megjegyzes AS megjegyzes,
                eszkozok.megjegyzes AS emegjegyzes,
                hibas
            FROM eszkozok
                INNER JOIN raktarak ON eszkozok.raktar = raktarak.id
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            WHERE $where
            ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

        $raktar = mySQLConnect("SELECT raktarak.id AS id,
                    raktarak.nev AS raktar,
                    epuletek.id AS epid,
                    epuletek.nev AS epuletnev,
                    epuletek.szam AS epuletszam,
                    epulettipusok.tipus AS tipus,
                    helyisegek.id AS helyisegid,
                    helyisegszam,
                    helyisegnev,
                    alakulatok.rovid AS alakulat,
                    telephelyek.id AS thelyid,
                    telephelyek.telephely AS telephely
                FROM raktarak
                    INNER JOIN helyisegek ON raktarak.helyiseg = helyisegek.id
                    INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                    INNER JOIN alakulatok ON raktarak.alakulat = alakulatok.id
                    INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
                    INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
                WHERE raktarak.id = $id");
        $raktar = mysqli_fetch_assoc($raktar);
        $oszlopok = array(
            array('nev' => 'IP cím', 'tipus' => 's'),
            array('nev' => 'Eszköznév', 'tipus' => 's'),
            array('nev' => 'Gyártó', 'tipus' => 's'),
            array('nev' => 'Modell', 'tipus' => 's'),
            array('nev' => 'Sorozatszám', 'tipus' => 's'),
            array('nev' => 'Tulajdonos', 'tipus' => 's'),
            array('nev' => 'Beépítve', 'tipus' => 's'),
            array('nev' => 'Kiépítve', 'tipus' => 's')
        );
        if($csoportir)
        {
            $oszlopok[] = array('nev' => 'Megjegyzés', 'tipus' => 's');
            $oszlopok[] = array('nev' => '&nbsp;', 'tipus' => 's');
            $oszlopok[] = array('nev' => '&nbsp;', 'tipus' => 's');
            $oszlopok[] = array('nev' => '&nbsp;', 'tipus' => 's');
        }

        ?><div class="breadcumblist">
            <ol vocab="https://schema.org/" typeof="BreadcrumbList">
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/">
                    <span property="name">Kecskemét Informatika</span></a>
                    <meta property="position" content="1">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epuletek/<?=$raktar['thelyid']?>">
                    <span property="name"><?=$raktar['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epulet/<?=$raktar['epid']?>">
                    <span property="name"><?=$raktar['epuletszam']?>. <?=$raktar['tipus']?></span></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/helyiseg/<?=$raktar['helyisegid']?>">
                    <span property="name"><?=$raktar['helyisegszam']?>. helyiség (<?=$raktar['helyisegnev']?>)</span></a>
                    <meta property="position" content="4">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$raktar['raktar']?></span>
                    <meta property="position" content="5">
                </li>
            </ol>
        </div>

        <?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/raktar/$id?action=edit'\">Raktár szerkesztése</button>" : "" ?>
        <div class="PrintArea">
            <div class="oldalcim"><?=$raktar['raktar']?> raktár <?=$szuresek['szures']?> <?=raktarKeszlet(null, $szuresek['filter'])?></div>
            <div class="raktarak"><?php
                $zar = false;
                foreach($mindeneszkoz as $eszkoz)
                {
                    if(@$tipus != $eszkoz['tipus'])
                    {
                        if($zar)
                        {
                            ?></tbody>
                            </table><?php
                        }

                        $tipus = $eszkoz['tipus']
                        ?><h1 style="text-transform: capitalize;"><?=$tipus?></h1>
                        <table id="<?=$tipus?>">
                        <thead><?php
                            sortTableHeader($oszlopok, $tipus);
                            ?></tr>
                        </thead>
                        <tbody><?php
                        $zar = true;
                    }

                    $eszkid = $eszkoz['id'];
                    $eszktip = eszkozTipusValaszto($eszkoz['tipusid']);
                    $kattinthatolink = $RootPath . '/' . $eszktip . '/' . $eszkoz['id'];
                    ?><tr class='trlink <?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tulajdonos']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=timeStampToDate($eszkoz['beepitesideje'])?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=timeStampToDate($eszkoz['kiepitesideje'])?></a></td><?php
                        if($csoportir)
                        {
                            ?><td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], $eszktip);
                        }
                    ?></tr><?php
                }
                ?></tbody>
                </table>
            </div>
        </div><?php
    }
}