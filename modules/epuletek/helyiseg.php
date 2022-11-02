<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
if(!@$mindolvas || (isset($_GET['action']) && !$mindir))
{
    getPermissionError();
}
else
{
    $alapform = "modules/epuletek/forms/";
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
        include("./modules/epuletek/db/helyisegdb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        afterDBRedirect($con);
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $helyisegszam = $helyisegnev = $emelet = $magyarazat = null;
        $beuszok = array();

        $helyisegbutton = "Új helyiség";
        $oldalcim = "Új helyiség hozzáadása";

        $form = $alapform . "helyisegszerkform";

        if(isset($_GET['id']))
        {
            $helyisegid = $_GET['id'];
            $button = "Helyiség szerkesztése";
            $helyiseg = mySQLConnect("SELECT * FROM helyisegek WHERE id = $helyisegid;");
            $helyiseg = mysqli_fetch_assoc($helyiseg);

            $epid = $helyiseg['epulet'];
            $helyisegszam = $helyiseg['helyisegszam'];
            $helyisegnev = $helyiseg['helyisegnev'];
            $emelet = $helyiseg['emelet'];

            $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port
            FROM portok
                INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epid AND vegpontiportok.helyiseg IS NULL
            UNION
            SELECT portok.id AS id, portok.port AS port
            FROM portok
                INNER JOIN transzportportok ON transzportportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epid;");

            $helyisegbutton = "Helyiség módosítása";
            $oldalcim = "Helyiség szerkesztése";

            $beuszok[] = array('cimszoveg' => 'Épületportok helyiséghez kötése', 'formnev' => $alapform . 'portokhelyiseghezform');
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
        $helyisegid = $_GET['id'];
        $helyiseg = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
            FROM helyisegek
                INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
                INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
            WHERE helyisegek.id = $helyisegid;");
        $helyiseg = mysqli_fetch_assoc($helyiseg);

        $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam
            FROM rackszekrenyek
                LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
            WHERE helyiseg = $helyisegid;");
        
        $portok = mySQLConnect("SELECT portok.id AS portid, portok.port AS port, IF((SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1), 1, NULL) AS hasznalatban
            FROM portok
                LEFT JOIN rackportok ON rackportok.port = portok.id
                LEFT JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id OR vegpontiportok.helyiseg = helyisegek.id
            WHERE helyisegek.id = $helyisegid
            ORDER BY rackportok.rack ASC, portok.id ASC;");

        $eszkozok = mySQLConnect("SELECT
                eszkozok.id AS id,
                helyisegek.id AS helyisegid,
                sorozatszam,
                gyartok.nev AS gyarto,
                modellek.modell AS modell,
                varians,
                eszkoztipusok.nev AS tipus,
                modellek.tipus AS tipusid,
                beepitesideje,
                beepitesek.id AS beepid,
                pozicio,
                alakulatok.rovid AS tulajdonos,
                rackszekrenyek.nev AS rack,
                (SELECT count(id) FROM rackszekrenyek WHERE helyiseg = helyisegid) AS rackszam,
                beepitesek.nev AS beepitesinev,
                ipcimek.ipcim AS ipcim
            FROM
                eszkozok INNER JOIN
                    modellek ON eszkozok.modell = modellek.id INNER JOIN
                    gyartok ON modellek.gyarto = gyartok.id INNER JOIN
                    eszkoztipusok ON modellek.tipus = eszkoztipusok.id LEFT JOIN
                    beepitesek ON beepitesek.eszkoz = eszkozok.id LEFT JOIN
                    rackszekrenyek ON beepitesek.rack = rackszekrenyek.id LEFT JOIN
                    helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id LEFT JOIN
                    ipcimek ON beepitesek.ipcim = ipcimek.id LEFT JOIN
                    alakulatok ON eszkozok.tulajdonos = alakulatok.id
            WHERE helyisegek.id = $helyisegid AND kiepitesideje IS NULL
            ORDER BY rack, pozicio;");
        if(mysqli_num_rows($eszkozok) > 0)
        {
            $rackszam = mysqli_fetch_assoc($eszkozok)['rackszam'];
        }
        else
        {
            $rackszam = null;
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
                        href="<?=$RootPath?>/epuletek/<?=$helyiseg['thelyid']?>">
                    <span property="name"><?=$helyiseg['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epulet/<?=$helyiseg['epid']?>">
                    <span property="name"><?=$helyiseg['epuletszam']?>. <?=$helyiseg['tipus']?></span></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$helyiseg['helyisegszam']?>. helyiség (<?=$helyiseg['helyisegnev']?>)</span>
                    <meta property="position" content="4">
                </li>
            </ol>
        </div>

        <?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/helyiseg/$helyisegid?action=edit'\">Helyiség szerkesztése</button>" : "" ?>
        <div class="oldalcim"><?=$helyiseg['helyisegszam']?> (<?=$helyiseg['helyisegnev']?>)</div>
        
        <div class="oldalcim">Eszközök a helyiségben</div><?php
        $ujoldalcim = $ablakcim . " - ". $helyiseg['epuletszam'] . ". " . $helyiseg['tipus'] . " " . $helyiseg['helyisegszam'] . ". helyiség (" . $helyiseg['helyisegnev'] . ")";
        if(mysqli_num_rows($eszkozok) > 0)
        {
            ?><div>
                <table id="eszkozok">
                    <thead>
                        <tr>
                            <th class="tsorth" onclick="sortTable(0, 's', 'eszkozok')">IP cím</th>
                            <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">Eszköznév</th>
                            <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Modell</th>
                            <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Eszköztípus</th>
                            <th class="tsorth" onclick="sortTable(4, 's', 'eszkozok')">Tulajdonos</th>
                            <th class="tsorth" onclick="sortTable(5, 's', 'eszkozok')">Beépítve</th><?php
                            if($rackszam > 0)
                            {
                                ?><th class="tsorth" onclick="sortTable(6, 's', 'eszkozok')">Rackszekrény</th>
                                <th class="tsorth" onclick="sortTable(7, 'i', 'eszkozok')">Pozíció</th><?php
                            }
                            ?><th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        foreach($eszkozok as $eszkoz)
                        {
                            $beepid = $eszkoz['beepid'];
                            $eszkid = $eszkoz['id'];
                            $eszktip = eszkozTipusValaszto($eszkoz['tipusid'])
                            
                            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/<?=$eszktip?>/<?=$eszkoz['id']?>'>
                                <td><?=$eszkoz['ipcim']?></td>
                                <td nowrap><?=$eszkoz['beepitesinev']?></td>
                                <td nowrap><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                                <td><?=$eszkoz['tipus']?></td>
                                <td><?=$eszkoz['tulajdonos']?></td>
                                <td nowrap><?=timeStampToDate($eszkoz['beepitesideje'])?></td><?php
                                if($rackszam > 0)
                                {
                                    ?><td><?=$eszkoz['rack']?></td>
                                    <td><?=$eszkoz['pozicio']?></td><?php
                                }
                                if($csoportir)
                                {
                                    szerkSor($eszkoz['beepid'], $eszkoz['id'], $eszktip);
                                    ?><?php
                                }
                            ?></tr><?php
                        }
                    ?></tbody>
                </table>
            </div><?php
        }

        if(mysqli_num_rows($rackek) > 0)
        {
            ?><div class="oldalcim">Rackszekrények a helyiségben</div>
            <div>
                <table id="rackek">
                    <thead>
                        <tr>
                            <th class="tsorth" onclick="sortTable(0, 's', 'rackek')">Azonosító</th>
                            <th class="tsorth" onclick="sortTable(1, 's', 'rackek')">Gyártó</th>
                            <th class="tsorth" onclick="sortTable(2, 'i', 'rackek')">Unitszám</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        foreach($rackek as $rack)
                        {
                            $rackid = $rack['id']
                            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/rack/<?=$rack['id']?>'>
                                <td><?=$rack['nev']?></td>
                                <td><?=$rack['gyarto']?></td>
                                <td><?=$rack['unitszam']?></td>
                                <td><?=($csoportir) ? "<a href='$RootPath/rack/$rackid?action=edit'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td>
                            </tr><?php
                        }
                    ?></tbody>
                </table>
            </div><?php
        }
        ?><div class="oldalcim">Végpontok a helyiségben</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;"><?php
            foreach($portok as $port)
            {
                $portid = $port['portid'];
                ?><div>
                    <a href='<?=$RootPath?>/port/<?=$portid?>'><?=($port['hasznalatban']) ? "<p style='font-weight: bold'>" : "<p style='font-weight: normal'>" ?><?=$port['port']?></p></a>
                </div><?php
            }
        ?></div><?php
    }
}