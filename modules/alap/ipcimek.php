<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $ipcimekSQL = new MySQLHandler();
    $where = null;
    $bindarr = array();
    if(isset($_GET['kereses']))
    {
        $keres = '%' . $_GET['kereses'] . '%';
        $where = "WHERE ipcim LIKE ? OR vlannev LIKE ? OR eszkoz LIKE ? OR beepitesnev LIKE ? OR megjegyzes LIKE ?";
        $bindarr = array($keres, $keres, $keres, $keres, $keres);
    }

    $ipcimek = $ipcimekSQL->Query("SELECT id, ipcim, vlan, eszkoz, vlannev, beepitesnev, beepitesideje, kiepitesideje, megjegyzes, leadva, sorozatszam, eszkid, beepid
        FROM (
            SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim, ipcimek.vlan AS vlan, ipcimek.eszkoz AS eszkoz, vlanok.nev AS vlannev, beepitesek.nev AS beepitesnev, beepitesideje, kiepitesideje, ipcimek.megjegyzes AS megjegyzes, leadva, sorozatszam, eszkozok.id AS eszkid, beepitesek.id AS beepid
                FROM ipcimek
                    LEFT JOIN vlanok ON ipcimek.vlan = vlanok.id
                    LEFT JOIN beepitesek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
                WHERE (beepitesek.aktivbeepites = 1 OR ipcimek.eszkoz IS NOT NULL)
            UNION
                SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim, ipcimek.vlan AS vlan, ipcimek.eszkoz AS eszkoz, vlanok.nev AS vlannev, beepitesek.nev AS beepitesnev, beepitesideje, kiepitesideje, ipcimek.megjegyzes AS megjegyzes, leadva, sorozatszam, eszkozok.id AS eszkid, beepitesek.id AS beepid
                    FROM ipcimek
                        LEFT JOIN vlanok ON ipcimek.vlan = vlanok.id
                        LEFT JOIN beepitesek ON beepitesek.ipcim = ipcimek.id
                        LEFT JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
            WHERE (beepitesek.aktivbeepites = 0 OR beepitesek.aktivbeepites IS NULL) AND ipcimek.eszkoz IS NULL
        ) AS t
        $where
        GROUP BY id;", $bindarr, true);
    $ipcimek = $ipcimekSQL->NaturalSort('ipcim');

    $ipcimelozmenyek = $ipcimekSQL->Query("SELECT id, ipcim, vlan, eszkoz, vlannev, beepitesnev, beepitesideje, kiepitesideje, megjegyzes, leadva, sorozatszam, eszkid, beepid
        FROM (
            SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim, ipcimek.vlan AS vlan, ipcimek.eszkoz AS eszkoz, vlanok.nev AS vlannev, beepitesek.nev AS beepitesnev, beepitesideje, kiepitesideje, ipcimek.megjegyzes AS megjegyzes, leadva, sorozatszam, eszkozok.id AS eszkid, beepitesek.id AS beepid
                FROM ipcimek
                    LEFT JOIN vlanok ON ipcimek.vlan = vlanok.id
                    LEFT JOIN beepitesek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
        ) AS t
        $where;", $bindarr);

    if($mindir)
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/ipszerkeszt'">Új rezervált IP</button><?php
    }
    ?><div class="oldalcim">Rezervált IP címek listája</div><?php

    $altabla = $zar = false;
    $elozmenyid = 1;

    foreach($ipcimek as $ipcim)
    {
        $ipid = $ipcim['id'];
        $hasznalatban = $szulo = $volthasznalva = false;
        $eszkoz = null;
        $elozmenyek = array();

        foreach($ipcimelozmenyek as $elozmeny)
        {
            if($elozmeny['ipcim'] == $ipcim['ipcim'])
            {
                if($elozmeny['beepid'] != $ipcim['beepid'])
                    $elozmenyek[] = $elozmeny;
                $szulo = true;
            }
        }

        if($ipcim['eszkoz'] || $ipcim['beepid'])
        {
            $eszkoz = $ipcim['beepitesnev'];
            if($ipcim['beepitesnev'] && $ipcim['beepitesideje'] && !$ipcim['kiepitesideje'])
            {
                $hasznalatban = true;
            }

            if($ipcim['eszkoz'] && !$hasznalatban)
            {
                $eszkoz = $ipcim['eszkoz'];
                $hasznalatban = true;
            }
            $volthasznalva = true;
        }

        if(@$tableid != $ipcim['vlan'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $tableid = $ipcim['vlan']
            ?><h1 style="text-transform: capitalize;"><?=$ipcim['vlannev']?> (<?=$ipcim['vlan']?>)</h1>
            <table id="<?=$tableid?>">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', '<?=$tableid?>')">IP cím</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$tableid?>')">VLAN</th>
                    <th class="tsorth" onclick="sortTable(2, 's', '<?=$tableid?>')">Eszköz</th>
                    <th class="tsorth" onclick="sortTable(3, 's', '<?=$tableid?>')">Megjegyzes</th>
                    <th class="tsorth">Előzmények</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }

        ?><tr <?=($hasznalatban) ? "style='font-weight: bold'" : "" ?>>
            <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?>'><?=$ipcim['ipcim']?></td>
            <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?>'><?=$ipcim['vlan']?></td>
            <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?> <?=($ipcim['leadva']) ? "mukodeskeptelen" : "" ?>' ><?=$eszkoz?></td>
            <td class='<?=(!$hasznalatban && $volthasznalva) ? "reszhibas" : "" ?> <?=($ipcim['leadva']) ? "mukodeskeptelen" : "" ?>' ><?=$ipcim['megjegyzes']?></td>
            <td><?=($szulo && count($elozmenyek) > 0) ? "<a style='cursor: pointer' onclick=\"rejtMutat('elozmeny-$elozmenyid')\">+</a>" : "" ?></td>
            <td><?=($csoportir) ? "<a href='$RootPath/ipszerkeszt/$ipid'><img src='$RootPath/images/edit.png' alt='IP cím szerkesztése' title='IP cím szerkesztése'/></a>" : "" ?></td>
        </tr><?php
        if($szulo && count($elozmenyek) > 0)
        {
            $altabla = true;
            ?><tr style="display:none"></tr>
            <tr id="elozmeny-<?=$elozmenyid?>" style="display:none">
                <td colspan=6>
                    <table>
                        <thead>
                            <tr>
                                <td></td>
                                <td>Eszköz</td>
                                <td>Sorozatszám</td>
                                <td>Beépítés ideje</td>
                                <td>Kiépítés ideje</td>
                            </tr>
                        </thead>
                        <tbody><?php
                            foreach($elozmenyek as $elozmeny)
                            {
                                ?><tr>
                                    <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
                                    <td><?=$elozmeny['beepitesnev']?></td>
                                    <td><?=$elozmeny['sorozatszam']?></td>
                                    <td><?=$elozmeny['beepitesideje']?></td>
                                    <td><?=$elozmeny['kiepitesideje']?></td>
                                </tr><?php
                            }
                        ?></tbody>
                    </table>
                </td>
            </tr><?php
            $elozmenyid++;
        }
    }

    ?></tbody>
    </table><?php
    $enablekeres = true;
}