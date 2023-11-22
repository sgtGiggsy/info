<?php
include('./modules/allapotjelentesek/includes/functions.php');

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = "WHERE (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)";
    if(isset($_GET['nap']) || (isset($_GET['kezdodatum']) && isset($_GET['zarodatum'])))
    {
        if(isset($_GET['nap']))
        {
            $date = $_GET['nap'];
            if(str_contains($date, "-"))
            {
                $date = str_replace("-", "", $date);
            }
            $where .= " AND DATE(snmp_traps.timestamp) = DATE($date)";
        }
        else
        {
            $kezdo = $_GET['kezdodatum'];
            $zaro = $_GET['zarodatum'];
            if(str_contains($kezdo, "-"))
            {
                $kezdo = str_replace("-", "", $kezdo);
            }
            if(str_contains($zaro, "-"))
            {
                $zaro = str_replace("-", "", $zaro);
            }

            $where .= " AND (DATE(snmp_traps.timestamp) >= DATE($kezdo) AND DATE(snmp_traps.timestamp) <= DATE($zaro))";
        }
    }
    else
    {
        //$where .= " AND DATE(snmp_traps.timestamp) = CURDATE()";
    }

    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                   // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);

        $where .= "AND $csoportwhere";
    }

    $allapotjelzesek = mySQLConnect("SELECT DISTINCT snmp_traps.id,
            snmp_traps.eszkozid AS eszkozid,
            snmp_traps.timestamp AS timestamp,
            snmp_traps.eszkozid AS eszkozid,
            event, eventlocal, systemuptime, rawmessage,
            ipcimek.ipcim, snmpcommunity
        FROM snmp_traps
            LEFT JOIN beepitesek ON beepitesek.eszkoz = snmp_traps.eszkozid
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN aktiveszkozok ON beepitesek.eszkoz = aktiveszkozok.eszkoz
        $where
        ORDER BY snmp_traps.timestamp DESC;");

    $oszlopok = array(
        array('nev' => 'Időpont', 'tipus' => 's'),
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Riasztási esemény', 'tipus' => 's'),
        array('nev' => 'Érintett rendszerelem', 'tipus' => 's'),
        array('nev' => 'Rendszer uptime', 'tipus' => 's'),
        array('nev' => '>', 'tipus' => 's'),
        array('nev' => 'A trap nyers tartalma', 'tipus' => 's')
    );
    
    ?><div class="oldalcim">Eszköz riasztások</div>
    <button onclick="rejtMutat()">Rejt</button>
    <table id="riasztasok">
        <thead>
            <tr><?php
                sortTableHeader($oszlopok, "riasztasok", true, true);
            ?></tr>
        </thead>
        <tbody><?php
            foreach($allapotjelzesek as $riasztas)
            {
                $eszkozid = $riasztas['eszkozid'];
                $linkpre = $linkend = null;
                $sysuptime = secondsToFullFormat($riasztas['systemuptime']);
                //$sysuptime = secondsToFullFormat(5676546);
                //$sysuptime = sprintf('%03d óra, %02d perc, %02d másodperc', ($riasztas['systemuptime']/ 3600),($riasztas['systemuptime']/ 60 % 60), $riasztas['systemuptime']% 60);
                if(!$eszkozid)
                {
                    $kattinthatolink = $RootPath . "/aktiveszkoz/" . $eszkozid;
                    $linkpre = "<a href='$kattinthatolink'>";
                    $linkend = "</a>";
                }

                $processed = processRaw($riasztas['rawmessage']);
                $megjelenik = processMessageBody($processed, $riasztas['ipcim'], $riasztas['snmpcommunity']);
                
                ?><tr class="trlink" data-surgosseg="" data-traptipus="">
                    <td><?=$linkpre?><?=$riasztas['timestamp']?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$riasztas['ipcim']?><?=$linkend?></td>
                    <td title="<?=$riasztas['event']?>"><?=$linkpre?><?=OIDs($riasztas['event'])?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$riasztas['eventlocal']?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$sysuptime?><?=$linkend?></td>
                    <td><?=$linkpre?>&nbsp;<?=$linkend?></td>
                    <td><?=$linkpre?><div class="snmpmessagebody"><?=$megjelenik?></div><?=$linkend?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}