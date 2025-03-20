<?php
include('./modules/allapotjelentesek/includes/functions.php');

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $enablekeres = true;
    $nezet = "lista";
    $fontossagszur = $kezdodatum = $zarodatum = $keres = null;
    $paramarr = array();
    $szurtmegjelenit = OID_LIST;
    $javascriptfiles[] = "modules/allapotjelentesek/includes/allapotjelentesek.js";
    $severityfilter = "minden";
    if(isset($_GET['trapfontossag']))
    {
        $severityfilter = $_GET['trapfontossag'];
        $_SESSION['trapfontossag'] = $severityfilter;
    }
    elseif(isset($_SESSION['trapfontossag']))
    {
        $severityfilter = $_SESSION['trapfontossag'];
    }

    if(isset($_GET['nezet']) && ($_GET['nezet'] == "tablazatos" || $_GET['nezet'] == "lista"))
    {
        $nezet = $_GET['nezet'];
        $_SESSION['nezet'] = $nezet;
    }
    elseif(isset($_SESSION['nezet']))
    {
        $nezet = $_SESSION['nezet'];
    }

    if(isset($_POST['kiszurtoid']))
    {
        $_SESSION['kiszurtoids'] = array();
        foreach($_POST['kiszurtoid'] as $oid)
        {
            $_SESSION['kiszurtoids'][] = $oid;
        }
    }

    if(isset($_POST['szurTorol']))
    {
        $_SESSION['kiszurtoids'] = array();
    }

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
        $keres = "%" . $_GET['kereses'] . "%";
        $paramarr[] = $keres;
        $where = "WHERE ipcimek.ipcim LIKE ?";
    }
    else
    {
        $where = "WHERE (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)";
    }
    $where .= $fontossagszur;

    if(isset($_SESSION['kiszurtoids']) && count($_SESSION['kiszurtoids']) > 0 && !isset($_POST['szurTorol']))
    {
        $oidstring = "";
        foreach($_SESSION['kiszurtoids'] as $oidelem)
        {
            $paramarr[] = $oidelem;
            $oidstring .= "?, ";
        }
        $oidstring = trim($oidstring, ", ");

        $where .= " AND (event NOT IN ($oidstring))";

        $szurtmegjelenit = array();
        foreach(OID_LIST as $key => $value)
        {
            if(in_array($key, $_SESSION['kiszurtoids']))
            {
                $szurtmegjelenit[$key] = $value;
            }
        }
        foreach(OID_LIST as $key => $value)
        {
            if(!in_array($key, $_SESSION['kiszurtoids']))
            {
                $szurtmegjelenit[$key] = $value;
            }
        }
    }
    
    if(isset($_GET['nap']) || (isset($_GET['kezdodatum']) && isset($_GET['zarodatum'])))
    {
        if(isset($_GET['nap']))
        {
            $date = $_GET['nap'];
            if(str_contains($date, "-"))
            {
                $date = str_replace("-", "", $date);
            }
            $paramarr[] = $date;
            $where .= " AND snmp_traps.datum = DATE(?)";
        }
        else
        {
            $kezdo = $kezdodatum = $_GET['kezdodatum'];
            $zaro = $zarodatum = $_GET['zarodatum'];
            if(!$kezdo)
            {
                $kezdo = "1970-01-01";
            }
            if(!$zaro)
            {
                $zaro = date("Y-m-d");
            }
            if(str_contains($kezdo, "-"))
            {
                $kezdo = str_replace("-", "", $kezdo);
            }
            if(str_contains($zaro, "-"))
            {
                $zaro = str_replace("-", "", $zaro);
            }

            $paramarr[] = $kezdo;
            $paramarr[] = $zaro;
            $where .= " AND (DATE(snmp_traps.datum) >= DATE(?) AND DATE(snmp_traps.datum) <= DATE(?))";
        }
    }
    elseif(!isset($_GET['kereses']))
    {
        $where .= " AND snmp_traps.datum = ?";
        $paramarr[] = thisDate();
    }

    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                   // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);

        $where .= "AND $csoportwhere";
    }

    $allapotjelzesek = new MySQLHandler("SELECT snmp_traps.id AS id,
            snmp_traps.eszkozid AS eszkozid,
            snmp_traps.timestamp AS timestamp,
            event, port, systemuptime,
            ipcimek.ipcim, snmpcommunity, severity, beepitesek.nev AS beepnev, message
        FROM snmp_traps
            LEFT JOIN beepitesek ON beepitesek.eszkoz = snmp_traps.eszkozid
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN aktiveszkozok ON beepitesek.eszkoz = aktiveszkozok.eszkoz
        $where
        ORDER BY snmp_traps.timestamp DESC;", ...$paramarr);
    $bejegyzesdb = $allapotjelzesek->sorokszama;
    $allapotjelzesek = $allapotjelzesek->Result();

    if(isset($date))
    {
        $ma = new DateTime($_GET['nap']);
    }
    else
    {
        $ma = new DateTime();
    }
    $megjelen = $ma->format('Y F d.');
    $tegnap = $ma->modify("-1 day");
    $tegnap = $tegnap->format("Y-m-d");
    $holnap = $ma->modify("+2 day");
    $holnap = $holnap->format("Y-m-d");

    $oszlopok = array(
        array('nev' => 'Sorsz', 'tipus' => 'i'),
        array('nev' => 'Időpont', 'tipus' => 's'),
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Riasztási esemény', 'tipus' => 's'),
        array('nev' => 'Port', 'tipus' => 's'),
        array('nev' => 'Rendszer uptime', 'tipus' => 's'),
        array('nev' => 'A trap tartalma', 'tipus' => 's')
    );

    
    if(isset($date) && $date != date("Ymd"))
    {
        ?><h1 style="text-align: center; padding-bottom: 0.5em"><?=$megjelen?></h1><br><?php
    }
    
    ?><div class="prevselnext">
        <div><?php
            if(!isset($_GET['kezdodatum']))
            {
                ?><button type='button' onclick="location.href='?nap=<?=$tegnap?>'"><< Előző nap</button><?php
            }
        ?></div>
        <div>
            <form action="" method="GET">
                <input type="date" name="kezdodatum" value="<?=$kezdodatum?>">
                <input type="date" name="zarodatum" value="<?=$zarodatum?>">
                <input type="submit" value="Kigyüjt">
            </form>
        </div>
        <div><?php
            if(isset($date) && $date != date("Ymd"))
            {
                ?><button class="right" type='button' onclick="location.href='?nap=<?=$holnap?>'">Következő nap >></button><?php
            }
        ?></div>
    </div>
    <div class="oldalcim">Eszköz riasztások
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
    <div class="tripplecolumn szuresoptions">
            <div class="left doublecolumnnormalpad">
                <div>
                    <label for="esemeny">Riasztási esemény</label>
                </div>
                <div>
                    <input id="esemeny" name="esemeny" type="text" onkeyup="listaSzur('esemeny', 'eventshort')" />
                </div>
                <div>
                    <label for="eszkozip">Eszköz IP</label>
                </div>
                <div>
                    <input id="eszkozip" name="eszkozip" type="text"  onkeyup="listaSzur('eszkozip', 'ip')"/>
                </div>
                <div>
                    <label for="bovitett">Bővebb szöveg</label>
                </div>
                <div>
                    <input id="bovitett" name="bovitett" type="text"  onkeyup="listaSzur('bovitett', 'snmpmessage')"/>
                </div>
            </div>
            <div>
                <label>Elrejtett típusok:</label>
                <form action = ".\allapotjelentesek" method="post">
                    <div class="oidszur" id="oidszur"><?php
                        foreach($szurtmegjelenit as $key => $value)
                        {
                            ?><div>
                                <label>
                                    <input type="checkbox" name="kiszurtoid[]" value=<?=$key?> <?=(isset($_SESSION['kiszurtoids']) && in_array($key, $_SESSION['kiszurtoids'])) ? "checked" : "" ?>>
                                    <?=$value?>
                                </label>
                            </div><?php
                        }
                    ?><div class="boxnyit" onclick="rejtettNyit()"></div>
                    </div>
                    <div class="contentleft">
                        
                    </div>
                    <div class="twocolgrid">
                        <div class="submit"><input type="submit" name="beKuld" value="Szűrés"></div>
                        <div class="submit"><input type="submit" name="szurTorol" value="Szűrések törlése"></div>
                    </div>
                </form>

            </div>
            <div class="right">
                <label for="nezet">Az eredmények megjelenítésének módja</label>
                <select id="nezet" name="nezet" onchange="eredmenyekNezet();">
                    <option value="tablazatos" <?=($nezet == 'tablazatos') ? "selected" : "" ?>>Táblázatos</option>
                    <option value="lista" <?=($nezet == 'lista') ? "selected" : "" ?>>Lista</option>
                </select>
            </div>
        </div><?php

    if($nezet == "tablazatos")
    {
        ?><table id="riasztasok" class="sorhover telefonkonyvtabla">
            <thead>
                <tr><?php
                    sortTableHeader($oszlopok, "riasztasok", false, false);
                ?></tr>
            </thead>
            <tbody><?php
                foreach($allapotjelzesek as $riasztas)
                {
                    $linkpre = $linkend = null;
                    $trapid = $riasztas['id'];
                    $eszkozid = $riasztas['eszkozid'];
                    $port = $riasztas['port'];
                    //$body = $riasztas['processedmessage'];
                    $severity = $riasztas['severity'];
                    $sysuptime = secondsToFullFormat($riasztas['systemuptime']);
                    $jsondata = json_decode($riasztas['message']);
                    
                    switch($severity)
                    {
                        case 2 : $urgclass = " fontos-font"; break;
                        case 3 : $urgclass = " surgos-font"; break;
                        case 4 : $urgclass = " kritikus-font"; break;
                        default: $urgclass = "";
                    }

                    if($eszkozid)
                    {
                        $kattinthatolink = $RootPath . "/aktiveszkoz/" . $eszkozid;
                        $linkpre = "<a href='$kattinthatolink'>";
                        $linkend = "</a>";
                    }

                    // Ez arra jó, hogy ha korábban egy üzenet "nem lett lefordítva" emberi nyelvre, akkor most megteszi
                    /*
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
                        <td><?=$linkpre?><div class="snmpmessagebody"><?php
                        if($jsondata)
                        {
                            foreach($jsondata as $adatelem)
                            {
                                if(isset($adatelem->szoveg))
                                {
                                    ?><div><?=$adatelem->szoveg?></div>
                                    <div><?=$adatelem->ertek?></div><?php
                                }
                            }
                        }
                        ?></div><?=$linkend?></td>
                        </tr><?php
                    }
            ?></tbody>
        </table><?php
    }
    elseif($nezet == "lista")
    {
        ?><div class="allapotjelentesek"><?php
            foreach($allapotjelzesek as $riasztas)
            {
                $linkpre = $linkend = null;
                $trapid = $riasztas['id'];
                $eszkozid = $riasztas['eszkozid'];
                $port = $riasztas['port'];
                $severity = $riasztas['severity'];
                $kattinthatolink = $RootPath . "/aktiveszkoz/" . $eszkozid;
                $sysuptime = secondsToFullFormat($riasztas['systemuptime']);
                $jsondata = json_decode($riasztas['message']);
                
                switch($severity)
                {
                    case 2 : $urgclass = "fontos"; break;
                    case 3 : $urgclass = "surgos"; break;
                    case 4 : $urgclass = "kritikus"; break;
                    default: $urgclass = "allapotsorszam";
                }

                ?><a href="<?=$kattinthatolink?>" class="allapotelem" data-surgosseg="<?=$severity?>">
                    <div class="allapotelemdiv">
                        <div class="<?=$urgclass?> allapotelemparent"><?=$bejegyzesdb--?></div>
                        <div class="allapotelemparent">
                            <div class="eventdevice" id="ip"><?=$riasztas['beepnev']?> - <?=$riasztas['ipcim']?><?=($port) ? " - " . $port . " port" : "" ?></div>
                            <div class="eventtitle" id="eventshort"><?=OIDs($riasztas['event'])?></div>
                            <div class="eventuptime">A rendszer aktuális utolsó indítása óta eltelt idő:&nbsp;&nbsp;&nbsp;<?=$sysuptime?></div>
                            <div class="eventtime"><?=$riasztas['timestamp']?></div>
                        </div>
                        <div class="snmpmessagebody" id="snmpmessage"><?php
                        if($jsondata)
                        {
                            foreach($jsondata as $adatelem)
                            {
                                if(isset($adatelem->szoveg))
                                {
                                    ?><div><?=$adatelem->szoveg?></div>
                                    <div><?=$adatelem->ertek?></div>
                                    <?php
                                }
                            }
                        }
                        ?></div>
                    </div>
                </a><?php
            }
        ?></div><?php
    }
}