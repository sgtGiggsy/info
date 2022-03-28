<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $aktiveszkozok = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            mac,
            portszam,
            uplinkportok,
            szoftver,
            alakulatok.nev AS tulajdonos,
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
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.id AS rackid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim
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
        WHERE eszkozok.id = $id AND modellek.tipus < 11
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    if(mysqli_num_rows($aktiveszkozok) == 0)
    {
        echo "Nincs ilyen sorszámú aktív eszköz";
    }
    else
    {
        $eszkoz = mysqli_fetch_assoc($aktiveszkozok);

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
                        href="<?=$RootPath?>/epuletek/<?=$eszkoz['thelyid']?>">
                    <span property="name"><?=$eszkoz['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epulet/<?=$eszkoz['epuletid']?>">
                    <span property="name"><?=$eszkoz['epuletszam']?>. <?=$eszkoz['epulettipus']?></span></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/helyiseg/<?=$eszkoz['helyisegid']?>">
                    <span property="name"><?=$eszkoz['helyisegszam']?> (<?=$eszkoz['helyisegnev']?>)</span></a>
                    <meta property="position" content="4">
                </li>
                <?php if($eszkoz['rackid'])
                {
                    ?><li><b>></b></li>
                    <li property="itemListElement" typeof="ListItem">
                        <a property="item" typeof="WebPage"
                            href="<?=$RootPath?>/rack/<?=$eszkoz['rackid']?>">
                        <span property="name"><?=$eszkoz['rack']?></span></a>
                        <meta property="position" content="4">
                    </li><?php
                }
                ?><li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$eszkoz['beepitesinev']?> (<?=$eszkoz['ipcim']?>)</span>
                    <meta property="position" content="4">
                </li>
            </ol>
        </div><?php

        $epuletid = $eszkoz['epuletid'];
        $vlanok = mySQLConnect("SELECT * FROM vlanok;");
        $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
        $switchportok = mySQLConnect("SELECT switchportok.id AS id, allapot, eszkoz, mode, nev, sebesseg, tipus, vlan, portok.port, csatlakozo, portok.id AS portid, csatlakozas
            FROM switchportok
                INNER JOIN portok ON switchportok.port = portok.id
                WHERE eszkoz = $id;");
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
            WHERE helyisegek.epulet = $epuletid AND eszkozok.id != $id;");
        $csatlakozotipusok = mySQLConnect("SELECT * FROM csatlakozotipusok;");

        ?><div class="oldalcim"><?=(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])) ? "" : $eszkoz['ipcim'] ?> <?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?> (<?=$eszkoz['sorozatszam']?>)</div>
        <?=($mindir) ? "<a href='$RootPath/eszkozszerkeszt/$id?tipus=aktiv'>Eszköz szerkesztése</a>" : "" ?>
        <div class="infobox"><?php
            if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
            {
                ?><div>Állapot</div>
                <div>Beépítve</div>
                <div>IP cím</div>
                <div><?=$eszkoz['ipcim']?></div>
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
            elseif(!$eszkoz['beepitesideje'])
            {
                ?><div>Állapot</div>
                <div>Új, sosem beépített</div><?php
            }
            else
            {
                ?><div>Állapot</div>
                <div>Kiépítve</div>
                <div>Utolsó IP cím</div>
                <div><?=$eszkoz['ipcim']?></div>
                <div>Utolsó beépítési név</div>
                <div><?=$eszkoz['beepitesinev']?></div>
                <div>Utolsó beépítési helye</div>
                <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                <div>Rackszekrény</div>
                <div><?=$eszkoz['rack']?></div>
                <div>Utolsó beépítés ideje</div>
                <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                <div>Kiépítés ideje</div>
                <div><?=timeStampToDate($eszkoz['kiepitesideje'])?></div>
                <?php
            }
            ?><div>Gyártó</div>
            <div><?=$eszkoz['gyarto']?></div>
            <div>Modell</div>
            <div><?=$eszkoz['modell'] . $eszkoz['varians']?></div>
            <div>Sorozatszám</div>
            <div><?=$eszkoz['sorozatszam']?></div>
            <div>MAC Address</div>
            <div><?=$eszkoz['mac']?></div>
            <div>Szoftver</div>
            <div><?=$eszkoz['szoftver']?></div>
            <div>Access portok</div>
            <div><?=$eszkoz['portszam']?></div>
            <div>Uplink portok</div>
            <div><?=$eszkoz['uplinkportok']?></div>
            <div>Tulajdonos</div>
            <div><?=$eszkoz['tulajdonos']?></div>
        </div>
        <div class="oldalcim">Portok</div>
        <table id="switchportok">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', 'switchportok')">Port</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'switchportok')">Név</th>
                    <th class="tsorth" onclick="sortTable(2, 's', 'switchportok')">VLAN</th>
                    <th class="tsorth" onclick="sortTable(3, 's', 'switchportok')">Állapot</th>
                    <th class="tsorth" onclick="sortTable(4, 's', 'switchportok')">Sebesség</th>
                    <th class="tsorth" onclick="sortTable(5, 's', 'switchportok')">Port Mód</th>
                    <th class="tsorth" onclick="sortTable(6, 's', 'switchportok')">Tipus</th>
                    <th class="tsorth" onclick="sortTable(7, 's', 'switchportok')">Csatlakozó</th>
                    <th class="tsorth" onclick="sortTable(8, 's', 'switchportok')">Végpont</th>
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
                            <td><input style="width: 16ch;" type="text" name="nev" value="<?=$port['nev']?>"></td>
                            <td>
                                <select name="vlan">
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
                                <select name="mode">
                                    <option value="1" <?=($port['mode'] == "1") ? "selected" : "" ?>>Trunk</option>
                                    <option value="2" <?=($port['mode'] == "2") ? "selected" : "" ?>>Access</option>
                                </select>
                            </td>
                            <td>
                                <select name="tipus">
                                    <option value="1" <?=($port['tipus'] == "1") ? "selected" : "" ?>>Uplink</option>
                                    <option value="2" <?=($port['tipus'] == "2") ? "selected" : "" ?>>Access</option>
                                </select>
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
</script>