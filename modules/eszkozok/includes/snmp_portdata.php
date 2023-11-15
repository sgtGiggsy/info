<?php
/*
* Alapvető működés megvalósítva
TODO Megoldani, hogy kész lekérdezés után le lehessen kérni további mezőket
*/
include("../../../includes/config.inc.php");
include("../../../includes/functions.php");
include('../classes/devinterface.class.php');

function clearKey($key)
{
    $azonosito = explode(".", $key);
    return end($azonosito);
}

function clearValue($value)
{
    $valarr = explode(": ", $value);
    return trim(end($valarr), "\"");
}

$ipaddress = $_GET['ip'];
$community = $_GET['community'];

$snmpadatok = [];
//$obj = snmpwalkoid($tesztip, $tesztcommunity, null);
$ids = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.2.2.1.1");

// Adat feldolgozás rész
if($ids)
{
    $types = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.2.2.1.3");
    $speeds = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.2.2.1.5");
    $adminstates = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.2.2.1.7");
    $opstates = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.2.2.1.8");
    $names = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.31.1.1.1.1");
    $descs = @snmp2_real_walk($ipaddress, $community, "iso.3.6.1.2.1.31.1.1.1.18");
    $vlans = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.4.1.9.9.68.1.2.2.1.2');

    foreach($ids as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $temp = new DevInterface;
        $temp->id = $id;
        $intlist[$id] = $temp;
    }

    foreach($types as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $intlist[$id]->type = $value;
    }

    foreach($adminstates as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $intlist[$id]->adminstate = $value;
    }

    foreach($speeds as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $intlist[$id]->speed = $value;
    }

    foreach($opstates as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $intlist[$id]->opstate = $value;
    }

    foreach($names as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $intlist[$id]->shortname = $value;
    }

    foreach($descs as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);
        $intlist[$id]->description = $value;
    }

    if($vlans)
    {
        foreach($vlans as $key => $rawvalue)
        {
            $id = clearKey($key);
            $value = clearValue($rawvalue);
            $intlist[$id]->vlan = $value;
        }
    }
    else
    {
        $vlans = snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.17.7.1.4.5.1.1');
        if($vlans)
        {
            foreach($vlans as $key => $rawvalue)
            {
                $id = clearKey($key);
                $value = clearValue($rawvalue);
                $intlist[$id]->vlan = $value;
            }
        }
    }

    // Adat megjelenítés rész
    ?><table>
        <thead>
            <tr>
                <th>Port</th>
                <th>Leírás</th>
                <th>VLAN</th>
                <th>Admin állapot</th>
                <th>Operatív állapot</th>
                <th>Sebesség</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($intlist as $interface)
            {
                if($interface->type == 6 && $interface->opstate != 6)
                {
                    $class = null;
                    switch($interface->opstate)
                    {
                        case 1 : $class = "online kiepitett"; break;
                        case 2 : if($interface->adminstate == 2) $class = "offline"; else $class = "ures"; break;
                    }

                    ?><tr class="<?=$class?>">
                        <td><?=$interface->shortname?></td>
                        <td><?=$interface->description?></td>
                        <td><?=$interface->vlan?></td>
                        <td><?=$interface->PortAdminState()?></td>
                        <td><?=$interface->PortOperativeState()?></td>
                        <td><?=$interface->PortSebesseg()?></td>
                    </tr><?php
                }
            }
        ?></tbody>
    </table><?php
}
else
{
    ?><div class="devportload">
        <div>Az eszköz lekérdezése sikertelen!</div>
    </div><?php
}