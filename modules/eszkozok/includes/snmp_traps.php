<?php
define("DEBUG_MODE", false);
include("../../../includes/config.inc.php");
include("../../../includes/functions.php");
include("../../../Classes/MySQLHandler.class.php");
include('../../allapotjelentesek/includes/functions.php');
include('../classes/networkelements.class.php');

$id = $_GET['devid'];
$allapotjelzesek = new MySQLHandler("SELECT snmp_traps.id AS id,
            snmp_traps.eszkozid AS eszkozid,
            snmp_traps.timestamp AS timestamp,
            event, port, systemuptime,
            severity, message
        FROM snmp_traps
        WHERE eszkozid = ? AND DATE(snmp_traps.datum) >= DATE_SUB(NOW(), interval 14 DAY)
        ORDER BY snmp_traps.id DESC;", $id);
$allapotjelentdb = $allapotjelzesek->sorokszama;

?><table>
    <thead>
        <tr>
            <th>Sorsz</th>
            <th>Időpont</th>
            <th>Esemény</th>
            <th>Port</th>
            <th style="display: none" class="hiddencol-snmp">Bővebb tartalom</th>
            <th><a onclick="mutatOszlop('snmp')" class="tablenyitcsuk" id="snmp-cursor">></a></th>
        </tr>
    </thead>
    <tbody><?php
        foreach($allapotjelzesek->Result() as $x)
        {
            switch($x['severity'])
            {
                case 2 : $urgclass = " fontos-font"; break;
                case 3 : $urgclass = " surgos-font"; break;
                case 4 : $urgclass = " kritikus-font"; break;
                default: $urgclass = "";
            }
            $jsondata = json_decode($x['message']);
            ?><tr class="<?=$urgclass?>">
                <td><?=$allapotjelentdb--?></td>
                <td style="width: 10ch; white-space: break-spaces"><?=$x['timestamp']?></td>
                <td><?=OIDs($x['event'])?></td>
                <td><?=$x['port']?></td>
                <td style="display: none" class="hiddencol-snmp"><div class="snmpmessagebody"><?php
                    if($jsondata && count($jsondata) > 0)
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
                ?></div>
            </tr><?php
        }
    ?></tbody>
</table>