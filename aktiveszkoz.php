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
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            alakulatok.rovid AS tulajdonos,
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

        $epuletid = $eszkoz['epuletid'];
        $vlanok = mySQLConnect("SELECT * FROM vlanok;");
        $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
        $switchportok = mySQLConnect("SELECT switchportok.id AS id, allapot, eszkoz, mode, nev, sebesseg, tipus, vlan, portok.port, csatlakozo, portok.id AS portid, kapcsolatportok.id AS kapcsolat
            FROM switchportok
                INNER JOIN portok ON switchportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
                WHERE eszkoz = $id;");
        $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, null AS aktiveszkoz, kapcsolatportok.id AS kapcsolat
            FROM portok
                INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epuletid
            UNION
            SELECT portok.id AS id, portok.port AS port, null AS aktiveszkoz, kapcsolatportok.id AS kapcsolat
            FROM portok
                INNER JOIN transzportportok ON transzportportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epuletid
            UNION
            SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, kapcsolatportok.id AS kapcsolat
            FROM portok
                INNER JOIN switchportok ON portok.id = switchportok.port
                INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
                INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                INNER JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                INNER JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
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
                <div><?=$eszkoz['beepitesideje']?></div>
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
                <div><?=$eszkoz['beepitesideje']?></div>
                <div>Kiépítés ideje</div>
                <div><?=$eszkoz['kiepitesideje']?></div>
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
                        <form action="<?=$RootPath?>/switchportdb&action=update" method="post">
                            <input type ="hidden" id="id" name="id" value=<?=$port['id']?>>
                            <input type ="hidden" id="portid" name="portid" value=<?=$port['portid']?>>
                            <td><input type="text" name="port" value="<?=$port['port']?>"></td>
                            <td><input type="text" name="nev" value="<?=$port['nev']?>"></td>
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
                                <select name="epuletport">
                                    <option value="" selected></option><?php
                                    foreach($epuletportok as $x)
                                    {
                                        ?><option value="<?=$x['id']?>" <?=($x['kapcsolat'] && $x['kapcsolat'] == $port['kapcsolat']) ? "selected" : "" ?>><?=$x['aktiveszkoz'] . " " . $x['port']?></option><?php
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