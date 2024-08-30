<?php

// Elsőként annak ellenőrzése, hogy a felhasználó olvashatja-e,
// majd megvizsgálni, hogy ha olvashatja, de írni szeretné, ahhoz van-e joga
if(!@$csoportolvas || (isset($_GET['action']) && !$csoportir))
{
    getPermissionError();
}
// Ha van valamilyen módosítási kísérlet, ellenőrizni, hogy van-e rá joga a felhasználónak
elseif(isset($_GET['action']) && $csoportir)
{
    $meghiv = true;
    
    // Az eszközszerkesztő oldal includeolása
    include('./modules/eszkozok/includes/eszkozszerkeszt.inc.php');
}
else
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

    $aktiveszkozok = mySQLConnect("SELECT
            eszkozok.id AS id,
            beepitesek.id AS beepid,
            sorozatszam,
            mac,
            lanportok AS portszam,
            wanportok AS uplinkportok,
            szoftver,
            szervezetek.nev AS tulajdonos,
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
            szervezetek.rovid AS tulajdonos,
            rackszekrenyek.id AS rackid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            raktarak.nev AS raktar,
            vlanok.nev AS vlan,
            wifitipusok.nev AS wifi
        FROM eszkozok
                INNER JOIN sohoeszkozok ON eszkozok.id = sohoeszkozok.eszkoz
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
                LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN vlanok ON vlanok.id = beepitesek.vlan
                LEFT JOIN wifitipusok ON wifitipusok.id = sohoeszkozok.wifi
        WHERE eszkozok.id = $id AND modellek.tipus > 5 AND modellek.tipus < 11 $csoportwhere
        ORDER BY beepitesek.id DESC;");

    if(mysqli_num_rows($aktiveszkozok) == 0)
    {
        echo "Nincs ilyen sorszámú soho eszköz";
    }
    else
    {
        $javascriptfiles[] = "modules/eszkozok/includes/sohoeszkoz.js";
        $eszkoz = mysqli_fetch_assoc($aktiveszkozok);

        showBreadcumb($eszkoz);

        $epuletid = $eszkoz['epuletid'];
        $helyisegid = $eszkoz['helyisegid'];
        $vlanok = mySQLConnect("SELECT * FROM vlanok;");
        $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
        $switchportok = mySQLConnect("SELECT sohoportok.id AS id, eszkoz, sebesseg, portok.port, csatlakozo, portok.id AS portid, csatlakozas
            FROM sohoportok
                INNER JOIN portok ON sohoportok.port = portok.id
                WHERE eszkoz = $id;");
        if($epuletid)
        {
            $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
                FROM portok
                    LEFT JOIN vegpontiportok ON vegpontiportok.port = portok.id
                    LEFT JOIN transzportportok ON transzportportok.port = portok.id
                    LEFT JOIN switchportok ON switchportok.port = portok.id
                    LEFT JOIN mediakonverterportok ON mediakonverterportok.port = portok.id
                    LEFT JOIN beepitesek ON switchportok.eszkoz = beepitesek.eszkoz OR mediakonverterportok.eszkoz = beepitesek.eszkoz
                    LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                    LEFT JOIN helyisegek ON beepitesek.helyiseg = beepitesek.helyiseg OR rackszekrenyek.helyiseg
                WHERE vegpontiportok.epulet = $epuletid OR transzportportok.epulet = $epuletid OR helyisegek.epulet = $epuletid
                ORDER BY aktiveszkoz, id;");
        }
        
        $csatlakozotipusok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
        // Szerkesztő gombok
        if($csoportir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Eszköz szerkesztése</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick='rejtMutat("elozmenyek")'>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }

        ?><div class="oldalcim"><?=(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])) ? "" : $eszkoz['ipcim'] ?> <?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?> (<?=$eszkoz['sorozatszam']?>)</div>
        <div class="infobox">
            <div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi beépítés adatai" : "Eszköz adatai" ?></div>
            <div class="infoboxbody">
                <div class="infoboxbodytwocol"><?php
                    if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
                    {
                        ?><div>Állapot</div>
                        <div>Beépítve</div>
                        <div>IP cím</div>
                        <div><?php
                            if($mindir)
                            {
                                ?><a href="telnet://<?=$eszkoz['ipcim']?>"><?php
                            } ?>
                            <?=$eszkoz['ipcim']?><?php
                            if($mindir)
                            {
                                ?></a><?php
                            }
                        ?></div>
                        <div>VLAN</div>
                        <div><?=$eszkoz['vlan']?></div>
                        <div>Beépítési név</div>
                        <div><?=$eszkoz['beepitesinev']?></div>
                        <div>Beépítés helye</div>
                        <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                        <div>Rackszekrény</div>
                        <div><?=$eszkoz['rack']?></div>
                        <div>Beépítés ideje</div>
                        <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
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
                    <div>WiFi</div>
                    <div><?=($eszkoz['wifi']) ? $eszkoz['wifi'] : "Nincs" ?></div>
                    <div>Szoftver</div>
                    <div><?=$eszkoz['szoftver']?></div>
                    <div>LAN portok</div>
                    <div><?=$eszkoz['portszam']?></div>
                    <div>WAN portok</div>
                    <div><?=$eszkoz['uplinkportok']?></div>
                    <div>Tulajdonos</div>
                    <div><?=$eszkoz['tulajdonos']?></div>
                </div>
            </div>
        </div><?php

        if(mysqli_num_rows($aktiveszkozok) > 1 || $eszkoz['kiepitesideje'])
        {
            ?><div class="oldalcim"><?=(mysqli_num_rows($aktiveszkozok) > 2) ? "Korábbi beépítések" : "Korábbi beépítés" ?></div>
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
                        ?><tr>
                            <td><?=$x['ipcim']?></td>
                            <td><?=$x['beepitesinev']?></td>
                            <td><?=$x['beepitesideje']?></td>
                            <td><?=$x['kiepitesideje']?></td>
                            <td><?=$x['epuletszam']?> <?=($x['epuletnev']) ? "(" . $x['epuletnev'] . ")" : "" ?> <?=$x['helyisegszam']?> <?=($x['helyisegnev']) ? "(" . $x['helyisegnev'] . ")" : "" ?>
                            <td><?php if($csoportir)
                            {
                                ?><a href='<?=$RootPath?>/beepites/<?=$x['beepid']?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a><?php
                            } ?></td>
                        </tr><?php
                    }
                }
                ?></tbody>
            </table><?php
        }

        ?><div class="oldalcim">Portok</div>
        <table id="switchportok">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', 'switchportok')">Port</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'switchportok')">Sebesség</th>
                    <th class="tsorth" onclick="sortTable(2, 's', 'switchportok')">Csatlakozó</th>
                    <th class="tsorth" onclick="sortTable(3, 's', 'switchportok')">Végpont</th>
                </tr>
            </thead>
            <tbody><?php
                foreach($switchportok as $port)
                {
                    ?><tr>
                        <!--<form action="">-->
                        <form action="?page=portdb&action=update&tipus=switch" method="post">
                            <input type ="hidden" id="id" name="id" value=<?=$port['id']?>>
                            <input type ="hidden" id="portid" name="portid" value=<?=$port['portid']?>>
                            <td><input style="width: 10ch;" type="text" name="port" value="<?=$port['port']?>"></td>
                            <td>
                                <select name="sebesseg">
                                    <option value=""></option><?php
                                    foreach($sebessegek as $x)
                                    {
                                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['sebesseg']) ? "selected" : "" ?>><?=$x['sebesseg']?></option><?php
                                    }
                                ?></select>
                            </td>
                            <td>
                                <select name="csatlakozo">
                                    <option value=""></option><?php
                                    foreach($csatlakozotipusok as $x)
                                    {
                                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['csatlakozo']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                    }
                                ?></select>
                            </td>
                            <td>
                                <select name="csatlakozas">
                                    <option value="" selected></option><?php
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
                                ?></select>
                            </td>
                            <td><input type="submit" value="Módosítás"></td>
                        </form>
                    </tr><?php
                }
            ?></tbody>
        </table><?php
    }
}
?>