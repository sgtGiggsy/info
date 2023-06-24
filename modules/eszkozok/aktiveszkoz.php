<?php

if($id)
{
    $beepszur = null;
    if(isset($_GET['beepites']) && $_GET['beepites'])
    {
        $beepszur = "AND beepitesek.id = " . $_GET['beepites'];
    }

    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

    // Adatbázis műveletek rész    
    $aktiveszkozok = mySQLConnect("SELECT
            eszkozok.id AS eszkid,
            beepitesek.id AS beepid,
            sorozatszam,
            mac,
            poe,
            ssh,
            web,
            portszam,
            uplinkportok,
            szoftver,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            epuletek.id AS epuletid,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            epulettipusok.tipus AS epulettipus,
            telephelyek.telephely AS telephely,
            telephelyek.id AS thelyid,
            helyisegek.id AS helyisegid,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            eszkozok.tulajdonos AS tulajid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.id AS rackid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            raktarak.id AS raktarid,
            raktarak.nev AS raktar,
            hibas,
            eszkozok.megjegyzes AS megjegyzes,
            beepitesek.megjegyzes AS beepmegjegyz,
            (SELECT MIN(id) FROM modositasok WHERE eszkoz = eszkid) AS elsomodositas,
            (SELECT felhasznalo FROM modositasok WHERE id = elsomodositas) AS letrehozoid,
            (SELECT nev FROM felhasznalok WHERE id = letrehozoid) AS letrehozo,
            (SELECT MAX(id) FROM modositasok WHERE eszkoz = eszkid) AS utolsomodositas,
            (SELECT felhasznalo FROM modositasok WHERE id = utolsomodositas) AS utolsomodositoid,
            (SELECT nev FROM felhasznalok WHERE id = utolsomodositoid) AS utolsomodosito,
            (SELECT timestamp FROM modositasok WHERE id = utolsomodositas) AS utolsomodositasideje,
            (SELECT timestamp FROM modositasok WHERE id = elsomodositas) AS letrehozasideje
        FROM eszkozok
                INNER JOIN aktiveszkozok ON eszkozok.id = aktiveszkozok.eszkoz
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
                LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
        WHERE eszkozok.id = $id AND modellek.tipus < 11 $beepszur $csoportwhere
        ORDER BY beepitesek.id DESC;");
        $eszkoz = mysqli_fetch_assoc($aktiveszkozok);
}

if((!$id && !isset($_GET['action'])) || (@$aktiveszkozok && mysqli_num_rows($aktiveszkozok) == 0) || !@$csoportolvas || (count($_POST) > 0 && !@$csoportir))
{
    echo "<h2>Nincs ilyen sorszámú aktív eszköz, vagy nincs jogosultsága a megtekintéséhez!</h2>";
}

else
{
    // A többi megjelenítési és adatbázis részt megelőzően először a breadcumbok betöltése történik meg,
    // mivel arra a betöltés formájától függetlenül mindenképp szükség lesz
    if((@$aktiveszkozok && mysqli_num_rows($aktiveszkozok) != 0) || (isset($_GET['action']) && $_GET['action'] != 'addnew'))
    {
        showBreadcumb($eszkoz);
    }
    
    // Első műveletként annak ellenőrzése, hogy ha a felhasználó írni szeretné az eszközt,
    // akkor rendelkezik-e a szükséges jogosultságokkal
    if(isset($_GET['action']) && !$csoportir)
    {
        getPermissionError();
    }
    // Ha van valamilyen módosítási kísérlet, ellenőrizni, hogy van-e rá joga a felhasználónak
    elseif(isset($_GET['action']) && $csoportir)
    {
        // Ez jelzi a szerkesztő oldalnak, hogy van jogosultsága a felhasználónak írni
        $meghiv = true;
        
        // Az eszközszerkesztő oldal includeolása
        include('./modules/eszkozok/includes/eszkozszerkeszt.inc.php');
    }
    else
    {
        $epuletid = $eszkoz['epuletid'];
        $helyisegid = $eszkoz['helyisegid'];
        $vlanok = mySQLConnect("SELECT * FROM vlanok;");
        $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
        $switchportok = mySQLConnect("SELECT switchportok.id AS id, allapot, eszkoz, mode, nev, sebesseg, tipus, vlan, portok.port, csatlakozo, portok.id AS portid, csatlakozas
            FROM switchportok
                INNER JOIN portok ON switchportok.port = portok.id
                WHERE eszkoz = $id;");
        $bovitok = mySQLConnect("SELECT eszkozok.id AS bovid, portok.id AS portid, portok.port AS port, modellek.modell AS modell, gyartok.nev AS gyarto, sorozatszam, atviteliszabvanyok.nev AS szabvany, sebessegek.sebesseg AS sebessegek
                FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN bovitomodellek ON modellek.id = bovitomodellek.modell
                INNER JOIN sebessegek ON bovitomodellek.transzpsebesseg = sebessegek.id
                INNER JOIN atviteliszabvanyok ON bovitomodellek.transzpszabvany = atviteliszabvanyok.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                INNER JOIN portok ON beepitesek.switchport = portok.id
                INNER JOIN switchportok ON portok.id = switchportok.port
            WHERE switchportok.eszkoz = $id AND beepitesek.kiepitesideje IS NULL
            ORDER BY portok.id;");

        if($_SESSION[getenv('SESSION_NAME').'onlinefigyeles'])
        {
            $allapotelozmenyek = mySQLConnect("SELECT * FROM aktiveszkoz_allapot WHERE eszkozid = $id
                UNION
                    SELECT * FROM aktiveszkoz_allapot_history WHERE eszkozid = $id
                    ORDER BY timestamp DESC");
        }

        if($epuletid)
        {
            $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, null AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                    WHERE epulet = $epuletid
                UNION
                    SELECT portok.id AS id, portok.port AS port, null AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN transzportportok ON transzportportok.port = portok.id
                    WHERE epulet = $epuletid
                UNION
                    SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN switchportok ON portok.id = switchportok.port
                        INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
                        INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                        INNER JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                        INNER JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                    WHERE helyisegek.id = $helyisegid AND eszkozok.id != $id AND beepitesek.kiepitesideje IS NULL
                UNION
                    SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN mediakonverterportok ON portok.id = mediakonverterportok.port
                        INNER JOIN eszkozok ON mediakonverterportok.eszkoz = eszkozok.id
                        INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                        LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                        LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                    WHERE helyisegek.id = $helyisegid AND eszkozok.id != $id AND beepitesek.kiepitesideje IS NULL
                    ORDER BY aktiveszkoz, port;");

            $epuletportok = mysqliNaturalSort($epuletportok, 'port');
        }

        $csatlakozotipusok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
        $elozmenyek = mySQLConnect("SELECT eszkozid,
                akteszkid,
                eszkozok_history.modell AS modellid,
                modellek.modell AS modell,
                gyartok.nev AS gyarto,
                sorozatszam,
                tulajdonos AS tulajid,
                alakulatok.rovid AS tulajdonos,
                varians,
                megjegyzes,
                leadva,
                hibas,
                raktar AS raktarid,
                raktarak.nev AS raktar,
                modositasok.felhasznalo AS modositoid,
                (SELECT nev FROM felhasznalok WHERE id = modositoid) AS modosito,
                modositasok.timestamp AS modositasideje,
                muvelet,
                mac,
                web,
                ssh,
                poe,
                portszam,
                uplinkportok,
                szoftver
            FROM eszkozok_history
                INNER JOIN aktiveszkozok_history ON eszkozok_history.modid = aktiveszkozok_history.modid
                LEFT JOIN modositasok ON eszkozok_history.modid = modositasok.id
                LEFT JOIN raktarak ON eszkozok_history.raktar = raktarak.id
                LEFT JOIN modellek ON eszkozok_history.modell = modellek.id
                LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
                LEFT JOIN alakulatok ON eszkozok_history.tulajdonos = alakulatok.id
            WHERE eszkozok_history.eszkozid = $id
            ORDER BY eszkozok_history.modid");

        // Megjelenés rész
        ?><div class="oldalcim"><?=(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])) ? "" : $eszkoz['ipcim'] ?> <?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?> (<?=$eszkoz['sorozatszam']?>)</div><?php

        ?><div class="dyntripplecol">
        <!-- Infóbox -->
            <div class="infobox">
                <div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi beépítés adatai" : "Eszköz adatai" ?></div>
                <div class="infoboxbody">
                    <div class="infoboxbodytwocol"><?php
                        $ujoldalcim = $ablakcim . " - " . $eszkoz['gyarto'] . " " . $eszkoz['modell'] . $eszkoz['varians'] . " (" . $eszkoz['sorozatszam'] . ")";
                        if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
                        {
                            $ujoldalcim = $ablakcim . " - " . $eszkoz['beepitesinev'] . " (" . $eszkoz['ipcim'] . ")";
                            ?><div>Állapot</div>
                            <div>Beépítve</div><?php
                        }
                        if($eszkoz['beepitesideje'])
                        {
                            ?><div>IP cím</div><?php
                            if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'] && $mindir)
                            {
                                if($eszkoz['web'])
                                {
                                    ?><div><a style="cursor: pointer;" id="manage"><?=$eszkoz['ipcim']?></a></div><?php
                                }
                                else
                                {
                                    ?><div><a href="telnet://<?=$eszkoz['ipcim']?>"><?=$eszkoz['ipcim']?></a></div><?php
                                }
                            }
                            else
                            {
                                ?><div><?=$eszkoz['ipcim']?></div><?php
                            }
                            ?><div>Beépítési név</div>
                            <div><?=$eszkoz['beepitesinev']?></div>
                            <div>Beépítés helye</div>
                            <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                            <div>Rackszekrény</div>
                            <div><?=$eszkoz['rack']?></div>
                            <div>Beépítés ideje</div>
                            <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                            <div>Kiépítés ideje</div>
                            <div><?=timeStampToDate($eszkoz['kiepitesideje'])?></div>
                            <div>Beépítéshez tartozó megjegyzés</div>
                            <div><?=$eszkoz['beepmegjegyz']?></div>
                            <?php
                        }
                        elseif(!$eszkoz['beepid'])
                        {
                            ?><div>Állapot</div>
                            <div>Új, sosem beépített</div><?php
                        }
                        else
                        {
                            ?><div>Állapot</div>
                            <div>Kiépítve</div>
                            <div>Raktár</div>
                            <div><?=$eszkoz['raktar']?></div><?php
                        }
                        ?><div>Gyártó</div>
                        <div><?=$eszkoz['gyarto']?></div>
                        <div>Modell</div>
                        <div><?=$eszkoz['modell'] . $eszkoz['varians']?></div>
                        <div>Sorozatszám</div>
                        <div><?=$eszkoz['sorozatszam']?></div>
                        <div>MAC Address</div>
                        <div><?=$eszkoz['mac']?></div>
                        <div>POE</div>
                        <div><?=($eszkoz['poe']) ? "Képes" : "Nem képes" ?></div>
                        <div>SSH</div>
                        <div><?=($eszkoz['ssh']) ? "Igen" : "Nem" ?></div>
                        <div>Weben menedzselhető</div>
                        <div><?=($eszkoz['web']) ? "Igen" : "Nem" ?></div>
                        <div>Szoftver</div>
                        <div><?=$eszkoz['szoftver']?></div>
                        <div>Access portok</div>
                        <div><?=$eszkoz['portszam']?></div>
                        <div>Uplink portok</div>
                        <div><?=$eszkoz['uplinkportok']?></div>
                        <div>Tulajdonos</div>
                        <div><?=($eszkoz['tulajdonos']) ? $eszkoz['tulajdonos'] : "Nem ismert" ?></div><?php
                        if($eszkoz['hibas'])
                        {
                            ?><div>Hibás</div>
                            <div><?=($eszkoz['hibas'] == 1) ? "Részlegesen" : "Működésképtelen" ?></div><?php
                        }            
                        ?>
                        <div>Eszközhöz tartozó megjegyzés</div>
                        <div><?=$eszkoz['megjegyzes']?></div>
                        <div>Bővítők</div>
                        <div><?php
                        if(mysqli_num_rows($bovitok))
                        {
                            foreach($bovitok as $x)
                            {
                                ?><a href="<?=$RootPath?>/bovitomodul/<?=$x['bovid']?>"><?=$x['port']?> - <?=$x['gyarto']?> <?=$x['modell']?> <?=$x['szabvany']?> (<?=$x['sorozatszam']?>)</a><br><?php
                            }
                        }
                        else
                        {
                            echo "Nincs csatlakoztatott bővítő";
                        }
                        ?></div>
                    </div>
                </div>
            </div><?php
            
        // Állapot előzmények
            if($_SESSION[getenv('SESSION_NAME').'onlinefigyeles'] && mysqli_num_rows($allapotelozmenyek) > 0)
            {
                ?>
                <div class="infobox">
                    <div class="infoboxtitle">Állapot előzmények</div>
                    <div class="infoboxbody">
                        <div class="infoboxbodytwocol">
                            <div><h2>Időpont</h2></div>
                            <div><h2>Állapot</h2></div><?php
                            foreach($allapotelozmenyek as $x)
                            {
                                ?><div class='<?=($x["online"]) ? "online" : "offline" ?>' <?=($x["online"]) ? "" : "style='font-weight: normal'" ?>><?=$x['timestamp']?></div>
                                <div class="<?=($x['online']) ? "online" : "offline" ?>"><?=($x['online']) ? "Online" : "Offline" ?></div><?php
                            }
                        ?></div>
                    </div>
                </div><?php
            }
        ?></div><?php

        // Szerkesztő gombok
        if($mindir)
        {
            $slideup = 1;
            ?><div class="szerkgombsor">
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Eszköz szerkesztése</button>
                <button type='button' onclick="location.href='./<?=$id?>?beepites<?=($eszkoz['beepid'] && !$eszkoz['kiepitesideje']) ? '=' . $eszkoz['beepid'] . '&action=edit' : '&action=addnew' ?>'">
                    <?=($eszkoz['beepid'] && !$eszkoz['kiepitesideje']) ? "Beépítés szerkesztése" : "Új beépítés" ?>
                </button><?php
                if(mysqli_num_rows($aktiveszkozok) > 1 || $eszkoz['kiepitesideje'])
                {
                    ?><button type='button' onclick='showSlideIn("<?=$slideup?>", "slideup-")'>Beépítési előzmények</button><?php
                    $slideup++;
                }
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick='showSlideIn("<?=$slideup?>", "slideup-")'>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }

        // Port táblázat
        ?><div class="PrintArea">
            <div class="oldalcim">Portok</div>
            <table id="switchportok">
                <thead>
                    <tr>
                        <th class="tsorth" onclick="sortTable(0, 's', 'switchportok')">Port</th>
                        <th class="tsorth portnev" onclick="sortTable(1, 's', 'switchportok')">Név</th>
                        <th class="tsorth" onclick="sortTable(2, 's', 'switchportok')">VLAN</th>
                        <th class="tsorth" onclick="sortTable(3, 's', 'switchportok')">Állapot</th>
                        <th class="tsorth dontprint" onclick="sortTable(4, 's', 'switchportok')">Sebesség</th>
                        <th class="tsorth" onclick="sortTable(5, 's', 'switchportok')">Port Mód</th>
                        <th class="tsorth dontprint" onclick="sortTable(6, 's', 'switchportok')">Tipus</th>
                        <th class="tsorth dontprint" onclick="sortTable(7, 's', 'switchportok')">Csatlakozó</th>
                        <th class="tsorth" onclick="sortTable(8, 's', 'switchportok')">Végpont</th><?php
                        if($mindir)
                        {
                            ?><th class="dontprint"></th><?php
                        }
                    ?></tr>
                </thead>
                <tbody><?php
                    $i = 1;
                    foreach($switchportok as $port)
                    {
                        if($mindir)
                        {
                            ?><tr class='transpinput'>
                                <!--<form action="">-->
                                <form action="?page=portdb&action=update&tipus=switch" method="post">
                                    <input type ="hidden" id="id" name="id" value=<?=$port['id']?>>
                                    <input type ="hidden" id="portid" name="portid" value=<?=$port['portid']?>>
                                    <td><input style="width: 10ch;" type="text" name="port" value="<?=$port['port']?>"></td>
                                    <td class="portnev"><input style="width: max-content;" type="text" name="nev" value="<?=$port['nev']?>"></td>
                                    <td>
                                        <select name="vlan" id="vlan-<?=$i?>">
                                            <option value=""></option><?php
                                            foreach($vlanok as $x)
                                            {
                                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['vlan']) ? "selected" : "" ?>><?=$x['id'] . " " . $x['nev']?></option><?php
                                            }
                                        ?></select>
                                    </td>
                                    <td>
                                        <select name="allapot">
                                            <option value="0" <?=($port['allapot'] == "0") ? "selected" : "" ?>>Letiltva</option>
                                            <option value="1" <?=($port['allapot'] == "1") ? "selected" : "" ?>>Engedélyezve</option>
                                        </select>
                                    </td>
                                    <td class="dontprint">
                                        <select name="sebesseg">
                                            <option value=""></option><?php
                                            foreach($sebessegek as $x)
                                            {
                                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['sebesseg']) ? "selected" : "" ?>><?=$x['sebesseg']?></option><?php
                                            }
                                        ?></select>
                                    </td>
                                    <td>
                                        <select name="mode">
                                            <option value="1" <?=($port['mode'] == "1") ? "selected" : "" ?>>Trunk</option>
                                            <option value="2" <?=($port['mode'] == "2") ? "selected" : "" ?>>Access</option>
                                        </select>
                                    </td>
                                    <td class="dontprint">
                                        <select name="tipus">
                                            <option value="1" <?=($port['tipus'] == "1") ? "selected" : "" ?>>Uplink</option>
                                            <option value="2" <?=($port['tipus'] == "2") ? "selected" : "" ?>>Access</option>
                                        </select>
                                    </td>
                                    <td class="dontprint">
                                        <select name="csatlakozo">
                                            <option value=""></option><?php
                                            foreach($csatlakozotipusok as $x)
                                            {
                                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['csatlakozo']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                            }
                                        ?></select>
                                    </td>
                                    <td>
                                        <div class="custom-select"><?php
                                            if(isset($epuletportok))
                                            {
                                                ?><select name="csatlakozas">
                                                    <option value="" selected>&nbsp;</option>
                                                    <option value="" selected>&nbsp;</option><?php
                                                    $elozo = null;
                                                    foreach($epuletportok as $x)
                                                    {
                                                        // Bug, de egyelőre így marad. Ha egy portra előbb kerül kirendezésre a végpont, mint a switchre,
                                                        // duplán jelenik meg itt a listában. Használatot nem befolyásolja.
                                                        if($x['id'] != $elozo /*|| $x['kapcsolat'] && $x['kapcsolat'] == $port['kapcsolat'] */)
                                                        {
                                                            ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['csatlakozas']) ? "selected" : "" ?>><?=$x['aktiveszkoz'] . " " . $x['port']?></option><?php
                                                        }
                                                        $elozo = $x['id'];
                                                    }
                                                ?></select><?php
                                            }
                                        ?></div>
                                    </td>
                                    <td style="width: 6.5em" class="dontprint"><input type="submit" value="Módosítás"></td>
                                </form>
                            </tr><?php
                            $i++;
                        }
                        else
                        {
                            ?><tr>
                                <td><?=$port['port']?></td>
                                <td><?=$port['nev']?></td>
                                <td><?php
                                    foreach($vlanok as $x)
                                    {
                                        ?><?=($x['id'] == $port['vlan']) ? $x['id'] . " " . $x['nev'] : "" ?><?php
                                    }
                                ?></td>
                                <td><?=($port['allapot'] == "0") ? "Letiltva" : "Engedélyezve" ?></td>
                                <td class="dontprint"><?php
                                    foreach($sebessegek as $x)
                                    {
                                        ?><?=($x['id'] == $port['sebesseg']) ? $x['sebesseg'] . " Mbit" : "" ?><?php
                                    }
                                ?></td>
                                <td><?=($port['mode'] == "1") ? "Trunk" : "Access" ?></td>
                                <td class="dontprint"><?=($port['tipus'] == "1") ? "Uplink" : "Access" ?></td>
                                <td class="dontprint"><?php
                                    foreach($csatlakozotipusok as $x)
                                    {
                                        ?><?=($x['id'] == $port['csatlakozo']) ? $x['nev'] : "" ?><?php
                                    }
                                ?></td>
                                <td><?php
                                    $elozo = null;
                                    if(isset($epuletportok))
                                    {
                                        foreach($epuletportok as $x)
                                        {
                                            if($x['id'] != $elozo)
                                            {
                                                ?><?=($x['id'] == $port['csatlakozas']) ? $x['aktiveszkoz'] . " " . $x['port'] : "" ?><?php
                                            }
                                            $elozo = $x['id'];
                                        }
                                    }
                                ?></td>
                            </tr><?php
                        }
                    }
                ?></tbody>
            </table>
        </div><?php
        $cselect = true;
        if($mindir)
        {
            ?><div class="contentcenter">
                <div class="largebutton">
                    <button onclick="showPopup('largebutton-popup')">Minden port egy VLAN-ra állítása</button>
                    <button onclick="sendAllForms()">Az összes módosítás mentése egyszerre</button>
                        
                    <div id="largebutton-popup">
                        <div><button onclick="setVlan('')">&nbsp;</button></div><?php
                        foreach($vlanok as $x)
                        {
                            ?><div><button onclick="setVlan(<?=$x['id']?>)"><?=$x['id'] . " " . $x['nev']?></button></div><?php
                        }
                    ?></div>
                </div>
            </div><?php
        }

        // Betöltésnél rejtett felületek
        // Switch menedzselés felugró
        if($mindir)
        {
            ?><div id="atfedes" class="atfedes">
                <div class="atfedes-content">
                    <span class="close">&times;</span>
                    <p><a href="telnet://<?=$eszkoz['ipcim']?>">Switch menedzselése Telneten keresztül</a></p>
                    <p><a href="http://<?=$eszkoz['ipcim']?>" target="_blank">Switch menedzselése a webes felülettel</a></p>
                </div>
            </div><?php
        }

        // Korábbi beépítések
        $slideup = 1;
        if(mysqli_num_rows($aktiveszkozok) > 1 || $eszkoz['kiepitesideje'])
        {
            ?><div id="slideup-<?=$slideup?>" onmouseleave='showSlideIn("<?=$slideup?>", "slideup-")'>
                <div class="tablecard">
                    <div class="tablecardtitle"><?=(mysqli_num_rows($aktiveszkozok) > 2) ? "Korábbi beépítések" : "Korábbi beépítés" ?></div>
                    <div class="tablecardbody" style="max-height: calc(260px - 3em)">
                        <table id="eszkozok">
                            <thead>
                                <tr>
                                    <th>IP cím</th>
                                    <th>Beépítési név</th>
                                    <th>Beépítés ideje</th>
                                    <th>Kiépítés ideje</th>
                                    <th>Beépítés helye</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody><?php
                            foreach($aktiveszkozok as $x)
                            {
                                if($eszkoz['beepid'] != $x['beepid'] || mysqli_num_rows($aktiveszkozok) == 1)
                                {
                                    ?><tr class='kattinthatotr' data-href='./<?=$id?>?beepites=<?=$x['beepid']?>'>
                                        <td><?=$x['ipcim']?></td>
                                        <td><?=$x['beepitesinev']?></td>
                                        <td><?=$x['beepitesideje']?></td>
                                        <td><?=$x['kiepitesideje']?></td>
                                        <td><?=$x['epuletszam']?> <?=($x['epuletnev']) ? "(" . $x['epuletnev'] . ")" : "" ?> <?=$x['helyisegszam']?> <?=($x['helyisegnev']) ? "(" . $x['helyisegnev'] . ")" : "" ?>
                                        <td><?php if($csoportir)
                                        {
                                            ?><a href='<?=$RootPath?>/<?=$_GET['page']?>/<?=$id?>?beepites=<?=$x['beepid']?>&action=edit'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a><?php
                                        } ?></td>
                                    </tr><?php
                                }
                            }
                            ?></tbody>
                        </table>
                    </div>
                </div>
            </div><?php
            $slideup++;
        }

        // Szerkesztési előzmények megjelenítése
        if(mysqli_num_rows($elozmenyek) > 0)
        {
            ?><div id="slideup-<?=$slideup?>" onmouseleave='showSlideIn("<?=$slideup?>", "slideup-")'>
                <div class="tablecard">
                    <div class="tablecardtitle">Szerkesztési előzmények</div>
                    <div class="tablecardbody" style="max-height: calc(260px - 3em)">
                        <table id="verzioelozmenyek">
                            <thead>
                                <th>Létrehozás / Módosítás ideje</th>
                                <th>Létrehozó / Módosító</th>
                                <th>Modell</th>
                                <th>Sorozatszám</th>
                                <th>MAC</th>
                                <th>Szoftver</th>
                                <th>Access portok</th>
                                <th>Uplink portok</th>
                                <th>PoE</th>
                                <th>SSH</th>
                                <th>Webes felület</th>
                                <th>Tulajdonos</th>
                                <th>Állapot</th>
                                <th>Raktár</th>
                                <th>Megjegyzés</th>
                            </thead>
                            <tbody><?php
                                $elozoverzio = null;
                                $szamoz = 1;
                                foreach($elozmenyek as $x)
                                {
                                    ?><tr><?php
                                        if($szamoz == 1 && $x['muvelet'] == 1)
                                        {
                                            ?><td><?=$x['modositasideje']?></td>
                                            <td><?=$x['modosito']?></td><?php
                                        }
                                        elseif ($szamoz != 1)
                                        {
                                            ?><td><?=$x['modositasideje']?></td>
                                            <td><?=$x['modosito']?></td><?php
                                        }
                                        else
                                        {
                                            ?><td></td>
                                            <td></td><?php
                                        }
                                        ?><td <?=($elozoverzio && $elozoverzio['gyarto'] != $x['gyarto'] && $elozoverzio['modell'] != $x['modell'] && $elozoverzio['varians'] != $x['varians']) ? "style='font-weight: bold;'" : "" ?>><?=$x['gyarto']?> <?=$x['modell']?><?=$x['varians']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['sorozatszam'] != $x['sorozatszam']) ? "style='font-weight: bold;'" : "" ?>><?=$x['sorozatszam']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['mac'] != $x['mac']) ? "style='font-weight: bold;'" : "" ?>><?=$x['mac']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['szoftver'] != $x['szoftver']) ? "style='font-weight: bold;'" : "" ?>><?=$x['szoftver']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['portszam'] != $x['portszam']) ? "style='font-weight: bold;'" : "" ?>><?=$x['portszam']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['uplinkportok'] != $x['uplinkportok']) ? "style='font-weight: bold;'" : "" ?>><?=$x['uplinkportok']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['poe'] != $x['poe']) ? "style='font-weight: bold;'" : "" ?>><?=($x['poe']) ? "Képes" : "Nincs" ?></td>
                                        <td <?=($elozoverzio && $elozoverzio['ssh'] != $x['ssh']) ? "style='font-weight: bold;'" : "" ?>><?=($x['ssh']) ? "Elérhető" : "Nem elérhető" ?></td>
                                        <td <?=($elozoverzio && $elozoverzio['web'] != $x['web']) ? "style='font-weight: bold;'" : "" ?>><?=($x['web']) ? "Van" : "Nincs" ?></td>
                                        <td <?=($elozoverzio && $elozoverzio['tulajid'] != $x['tulajid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['tulajdonos']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['hibas'] != $x['hibas']) ? "style='font-weight: bold;'" : "" ?>><?php switch($x['hibas']) { case 1: echo "Részlegesen működőképes"; Break; case 2: echo "Működésképtelen"; Break; default: echo "Működőképes"; } ?></td>
                                        <td <?=($elozoverzio && $elozoverzio['raktarid'] != $x['raktarid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['raktar']?></td>
                                        <td <?=($elozoverzio && $elozoverzio['megjegyzes'] != $x['megjegyzes']) ? "style='font-weight: bold;'" : "" ?>><?=$x['megjegyzes']?></td>
                                    </tr><?php
                                    $szamoz++;
                                    $elozoverzio = $x;
                                }
                                ?><tr style="font-style: italic;">
                                    <td><?=$eszkoz['utolsomodositasideje']?></td>
                                    <td><?=$eszkoz['utolsomodosito']?></td>
                                    <td <?=($elozoverzio['gyarto'] != $eszkoz['gyarto'] && $elozoverzio['modell'] != $eszkoz['modell'] && $elozoverzio['varians'] != $eszkoz['varians']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                                    <td <?=($elozoverzio['sorozatszam'] != $eszkoz['sorozatszam']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['sorozatszam']?></td>
                                    <td <?=($elozoverzio['mac'] != $eszkoz['mac']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['mac']?></td>
                                    <td <?=($elozoverzio['szoftver'] != $eszkoz['szoftver']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['szoftver']?></td>
                                    <td <?=($elozoverzio['portszam'] != $eszkoz['portszam']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['portszam']?></td>
                                    <td <?=($elozoverzio['uplinkportok'] != $eszkoz['uplinkportok']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['uplinkportok']?></td>
                                    <td <?=($elozoverzio['poe'] != $eszkoz['poe']) ? "style='font-weight: bold;'" : "" ?>><?=($eszkoz['poe']) ? "Képes" : "Nincs" ?></td>
                                    <td <?=($elozoverzio['ssh'] != $eszkoz['ssh']) ? "style='font-weight: bold;'" : "" ?>><?=($eszkoz['ssh']) ? "Elérhető" : "Nem elérhető" ?></td>
                                    <td <?=($elozoverzio['web'] != $eszkoz['web']) ? "style='font-weight: bold;'" : "" ?>><?=($eszkoz['web']) ? "Van" : "Nincs" ?></td>
                                    <td <?=($elozoverzio['tulajid'] != $eszkoz['tulajid']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['tulajdonos']?></td>
                                    <td <?=($elozoverzio['hibas'] != $eszkoz['hibas']) ? "style='font-weight: bold;'" : "" ?>><?php switch($eszkoz['hibas']) { case 1: echo "Részlegesen működőképes"; Break; case 2: echo "Működésképtelen"; Break; default: echo "Működőképes"; } ?></td>
                                    <td <?=($elozoverzio['raktarid'] != $eszkoz['raktarid']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['raktar']?></td>
                                    <td <?=($elozoverzio['megjegyzes'] != $eszkoz['megjegyzes']) ? "style='font-weight: bold;'" : "" ?>><?=$eszkoz['megjegyzes']?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><?php
        }
            

        ?><script>
        $("form").on("submit", function (e) {
            var dataString = $(this).serialize();

            $.ajax({
                type: "POST",
                data: dataString,
                url: "<?=$RootPath?>/portdb?action=update&tipus=switch",
                success: function () {
                    showToaster("Port szerkesztése sikeres...");
                }
            });
            e.preventDefault();
        });

        function sendAllForms()
        {
            var forms = document.getElementsByTagName("FORM");
            var elemszam = forms.length;
            showProgressOverlay();
            for (var i = 0; i < elemszam; i++) {
                var dataString = $(forms[i]).serialize();
                $.ajax({
                    type: "POST",
                    data: dataString,
                    url: "<?=$RootPath?>/portdb?action=update&tipus=switch",
                    success: function () {
                        if(i == elemszam)
                        {
                            hideProgressOverlay();
                        }
                    }
                });
            }
        }

        function setVlan(id) {
            for(var i = 1; i < 1000; i++) {
                if(document.getElementById("vlan-" + i)) {
                    select = document.getElementById("vlan-" + i);
                    select.value = id;
                }
                else {
                    break;
                }
            }
            showPopup('largebutton-popup');
        }

        <?php
        if($eszkoz['web'])
        {
            ?>
            var atfedes = document.getElementById("atfedes");
            var btn = document.getElementById("manage");
            var span = document.getElementsByClassName("close")[0];

            btn.onclick = function() {
                atfedes.style.display = "block";
            }

            span.onclick = function() {
                atfedes.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == atfedes) {
                    atfedes.style.display = "none";
                }
            }
            <?php
        }
    ?></script><?php
    }
}
?>