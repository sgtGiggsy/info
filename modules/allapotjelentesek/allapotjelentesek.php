<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    function OIDs($oid)
    {
        switch($oid)
        {
            case "1.3.6.1.6.3.1.1.5" : return "Beazonosítatlan"; break;
            case "1.3.6.1.6.3.1.1.5.1" : return "Rendszer újraindult áramkimaradás után"; break;
            case "1.3.6.1.6.3.1.1.5.2" : return "Rendszer újraindult"; break;
            case "1.3.6.1.6.3.1.1.5.3" : return "Link kapcsolata megszakadt"; break;
            case "1.3.6.1.6.3.1.1.5.4" : return "Link kapcsolódott"; break;
            case "1.3.6.1.6.3.1.1.5.5" : return "Autentikációs hiba"; break;
            case "1.3.6.1.4.1.9.10.56.2.0.1" : return "Változás az autentikációs beállításokban"; break;
            default : return $oid;
        }
    }

    $where = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);

        $where = "WHERE $csoportwhere";
    }

    $allapotjelzesek = mySQLConnect("SELECT snmp_traps.timestamp AS timestamp,
            snmp_traps.eszkozid AS eszkozid,
            event, eventlocal, systemuptime, misc,
            ipcimek.ipcim
        FROM snmp_traps
            LEFT JOIN beepitesek ON beepitesek.eszkoz = snmp_traps.eszkozid
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
        $where
        ORDER BY snmp_traps.timestamp DESC;");

    $oszlopok = array(
        array('nev' => 'Időpont', 'tipus' => 's'),
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Riasztási esemény', 'tipus' => 's'),
        array('nev' => 'Érintett rendszerelem', 'tipus' => 's'),
        array('nev' => 'Rendszer uptime', 'tipus' => 's'),
        array('nev' => 'Kisegítő info', 'tipus' => 's')
    );
    
    ?><div class="oldalcim">Eszköz riasztások</div>
    <table id="riasztasok">
        <thead>
            <tr><?php
                sortTableHeader($oszlopok, "riasztasok");
            ?></tr>
        </thead>
        <tbody><?php
            foreach($allapotjelzesek as $riasztas)
            {
                $eszkozid = $riasztas['eszkozid'];
                $linkpre = $linkend = null;
                //$sysuptime = secondsToFullFormat($riasztas['systemuptime']);
                $sysuptime = secondsToFullFormat(5676546);
                //$sysuptime = sprintf('%03d óra, %02d perc, %02d másodperc', ($riasztas['systemuptime']/ 3600),($riasztas['systemuptime']/ 60 % 60), $riasztas['systemuptime']% 60);
                if($eszkozid)
                {
                    $kattinthatolink = $RootPath . "/aktiveszkoz/" . $eszkozid;
                    $linkpre = "<a href='$kattinthatolink'>";
                    $linkend = "</a>";
                }
                
                ?><tr class="trlink">
                    <td><?=$linkpre?><?=$riasztas['timestamp']?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$riasztas['ipcim']?><?=$linkend?></td>
                    <td><?=$linkpre?><?=OIDs($riasztas['event'])?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$riasztas['eventlocal']?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$sysuptime?><?=$linkend?></td>
                    <td><?=$linkpre?><pre><?=$riasztas['misc']?></pre><?=$linkend?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}