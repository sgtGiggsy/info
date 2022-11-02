<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE ipcimek.ipcim LIKE '%$keres%' OR ipcimek.vlan LIKE '%$keres%' OR ipcimek.eszkoz LIKE '%$keres%' OR beepitesek.nev LIKE '%$keres%' OR ipcimek.megjegyzes LIKE '%$keres%'";
    }

    $ipcimek = mySQLConnect("SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim, ipcimek.vlan AS vlan, ipcimek.eszkoz AS eszkoz, vlanok.nev AS vlannev, beepitesek.nev AS beepitesnev, beepitesideje, kiepitesideje, ipcimek.megjegyzes AS megjegyzes
        FROM ipcimek
            LEFT JOIN vlanok ON ipcimek.vlan = vlanok.id
            LEFT JOIN beepitesek ON beepitesek.ipcim = ipcimek.id
        $where
        ORDER BY beepitesek.beepitesideje DESC;");
    if($mindir)
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/ipszerkeszt'">Új rezervált IP</button><?php
    }

    $ipcimek = mysqliNaturalSort($ipcimek, 'ipcim')

    ?><div class="oldalcim">Rezervált IP címek listája</div><?php
    $zar = false;
    $elozoip = null;

    foreach($ipcimek as $ipcim)
    {
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
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }
        $ipid = $ipcim['id'];
        $hasznalatban = false;
        
        if($elozoip != $ipcim['ipcim'])
        {
            if($ipcim['eszkoz'] || $ipcim['beepitesnev'])
            {
                if($ipcim['beepitesnev'] && $ipcim['beepitesideje'] && !$ipcim['kiepitesideje'])
                {
                    $hasznalatban = true;
                    $eszkoz = $ipcim['beepitesnev'];
                }
                elseif($ipcim['beepitesnev'] && !$ipcim['beepitesideje'] || $ipcim['kiepitesideje'])
                {
                    $eszkoz = $ipcim['beepitesnev'];
                }

                if($ipcim['eszkoz'] && !$hasznalatban)
                {
                    $eszkoz = $ipcim['eszkoz'];
                    $hasznalatban = true;
                }
            }

            ?><tr <?=($hasznalatban) ? "" : "style='font-weight: normal'" ?> >
                <td><?=$ipcim['ipcim']?></td>
                <td><?=$ipcim['vlan']?></td>
                <td><?=$eszkoz?></td>
                <td><?=$ipcim['megjegyzes']?></td>
                <td><?=($csoportir) ? "<a href='$RootPath/ipszerkeszt/$ipid'><img src='$RootPath/images/edit.png' alt='IP cím szerkesztése' title='IP cím szerkesztése'/></a>" : "" ?></td>
            </tr><?php
            $eszkoz = null;
            $elozoip = $ipcim['ipcim'];
        }
    }
    ?></tbody>
    </table><?php
    $enablekeres = true;
}