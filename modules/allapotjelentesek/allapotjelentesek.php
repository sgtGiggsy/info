<?php
include('./modules/allapotjelentesek/includes/functions.php');

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $enablekeres = true;
    $javascriptfiles[] = "modules/allapotjelentesek/includes/allapotjelentesek.js";
    $severityfilter = "minden";
    if(isset($_GET['trapfontossag']))
    {
        $severityfilter = $_GET['trapfontossag'];
        $_SESSION[getenv('SESSION_NAME').'trapfontossag'] = $severityfilter;
    }
    elseif(isset($_SESSION[getenv('SESSION_NAME').'trapfontossag']))
    {
        $severityfilter = $_SESSION[getenv('SESSION_NAME').'trapfontossag'];
    }

    $fontossagszur = null;
    if($severityfilter != "minden")
    {
        $fontossagszur = " AND ";
        switch($severityfilter)
        {
            case "informalis" : $fontossagszur .= "snmp_traps.severity = 1"; break;
            case "figyelmeztetes" : $fontossagszur .= "snmp_traps.severity = 2"; break;
            case "hiba" : $fontossagszur .= "snmp_traps.severity = 3"; break;
            case "kritikus" : $fontossagszur .= "snmp_traps.severity = 4"; break;
        }
    }

    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE ipcimek.ipcim LIKE '%$keres%'";
    }
    else
    {
        $where = "WHERE (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)";
    }
    $where .= $fontossagszur;
    
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
    elseif(!isset($_GET['kereses']))
    {
        $where .= " AND DATE(snmp_traps.timestamp) = CURDATE()";
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

    $allapotjelzesek = mySQLConnect("SELECT DISTINCT snmp_traps.id AS id,
            snmp_traps.eszkozid AS eszkozid,
            snmp_traps.timestamp AS timestamp,
            snmp_traps.eszkozid AS eszkozid,
            event, port, systemuptime, rawmessage,
            ipcimek.ipcim, snmpcommunity, processedmessage, severity, beepitesek.nev AS beepnev
        FROM snmp_traps
            LEFT JOIN beepitesek ON beepitesek.eszkoz = snmp_traps.eszkozid
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN aktiveszkozok ON beepitesek.eszkoz = aktiveszkozok.eszkoz
        $where
        ORDER BY snmp_traps.timestamp DESC;");

    $bejegyzesdb = mysqli_num_rows($allapotjelzesek);
    $oszlopok = array(
        array('nev' => 'Sorsz', 'tipus' => 'i'),
        array('nev' => 'Időpont', 'tipus' => 's'),
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Riasztási esemény', 'tipus' => 's'),
        array('nev' => 'Port', 'tipus' => 's'),
        array('nev' => 'Rendszer uptime', 'tipus' => 's'),
        array('nev' => 'A trap tartalma', 'tipus' => 's')
    );
    
    ?><div class="oldalcim">Eszköz riasztások
        <div class="szuresvalaszto">Értesítések szűrése
            <select id="severityfilter" name="severityfilter" onchange="severityFilter();">
                <option value="minden" <?=($severityfilter == "minden") ? "selected" : "" ?>>Minden mutatása</option>
                <option value="informalis" <?=($severityfilter == "informalis") ? "selected" : "" ?>>Informális</option>
                <option value="figyelmeztetes" <?=($severityfilter == "figyelmeztetes") ? "selected" : "" ?>>Figyelmeztetés</option>
                <option value="hiba" <?=($severityfilter == "hiba") ? "selected" : "" ?>>Hiba</option>
                <option value="kritikus" <?=($severityfilter == "kritikus") ? "selected" : "" ?>>Kritikus hiba</option>
            </select>
        </div>
    </div>
    <table id="riasztasok" class="sorhover telefonkonyvtabla">
        <thead>
            <tr><?php
                sortTableHeader($oszlopok, "riasztasok", true, true);
            ?></tr>
        </thead>
        <tbody><?php
            foreach($allapotjelzesek as $riasztas)
            {
                $linkpre = $linkend = null;
                $trapid = $riasztas['id'];
                $eszkozid = $riasztas['eszkozid'];
                $port = $riasztas['port'];
                $body = $riasztas['processedmessage'];
                $severity = $riasztas['severity'];
                $sysuptime = secondsToFullFormat($riasztas['systemuptime']);
                if($eszkozid)
                {
                    $kattinthatolink = $RootPath . "/aktiveszkoz/" . $eszkozid;
                    $linkpre = "<a href='$kattinthatolink'>";
                    $linkend = "</a>";
                }

                // Ez arra jó, hogy ha korábban egy üzenet "nem lett lefordítva" emberi nyelvre, akkor most megteszi
                if(!$riasztas['processedmessage'] && !$riasztas['port'])
                {
                    $processed = processRaw($riasztas['rawmessage']);
                    $fullyprocessed = processMessageBody($processed, $riasztas['ipcim'], $riasztas['snmpcommunity']);
                    if($fullyprocessed)
                    {
                        $port = $fullyprocessed['port'];
                        $body = $fullyprocessed['body'];
                        $severity = $fullyprocessed['severity'];
                        mySQLConnect("UPDATE snmp_traps SET processedmessage = '$body', port = '$port', severity = '$severity' WHERE id = $trapid;");
                    }
                }
                switch($severity)
                {
                    case 2 : $urgclass = " fontos-font"; break;
                    case 3 : $urgclass = " surgos-font"; break;
                    case 4 : $urgclass = " kritikus-font"; break;
                    default: $urgclass = "";
                }
                
                /*
                else
                    {
                        $port = str_replace("GigabitEthernet", "gi", $port);
                        $port = str_replace("FastEthernet", "fa", $port);
                        $port = str_replace("fa", "Fa", $port);
                        $port = str_replace("gi", "Gi", $port);
                        $port = str_replace("LongReachEthernet", "Lo", $port);
                        mySQLConnect("UPDATE snmp_traps SET port = '$port' WHERE id = $trapid;");
                    }
                */
                
                ?><tr class="trlink<?=$urgclass?>" data-surgosseg="<?=$severity?>">
                    <td><?=$linkpre?><?=$bejegyzesdb--?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$riasztas['timestamp']?><?=$linkend?></td>
                    <td title="<?=$riasztas['beepnev']?>"><?=$linkpre?><?=$riasztas['ipcim']?><?=$linkend?></td>
                    <td title="<?=$riasztas['event']?>"><?=$linkpre?><?=OIDs($riasztas['event'])?><?=$linkend?></td>
                    <td><?=$linkpre?><?=($port) ? $port : "&nbsp" ?><?=$linkend?></td>
                    <td><?=$linkpre?><?=$sysuptime?><?=$linkend?></td>
                    <td title="<?=$riasztas['rawmessage']?>"><?=$linkpre?><div class="snmpmessagebody"><?=$body?></div><?=$linkend?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}