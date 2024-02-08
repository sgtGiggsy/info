<?php
/*
* Alapvető működés megvalósítva
TODO Megoldani, hogy kész lekérdezés után le lehessen kérni további mezőket
*/
include("../../../includes/config.inc.php");
include("../../../includes/functions.php");
include('../classes/networkelements.class.php');

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
    $bovitok = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.47.1.1.1.1.16');
    $vanbovito = false;

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
        $vlans = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.17.7.1.4.5.1.1');
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

    foreach($bovitok as $key => $rawvalue)
    {
        $id = clearKey($key);
        $value = clearValue($rawvalue);

        // Az 1-es ID mindig az "anyaeszköz", ami néha tévesen FRU-ként (field replacable unit) van jelölve.
        if($id != 1 && $value == 1)
        {
            $temp = new DevPluggable;
            $temp->id = $id;
            $pluggable[$id] = $temp;
            $vanbovito = true;
        }
    }

    if($vanbovito)
    {
        $containers = [];
        $bovitotypes = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.47.1.1.1.1.2');
        $bovitoalters = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.47.1.1.1.1.7');
        $bovitoserials = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.47.1.1.1.1.11');
        $bovitomodels = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.47.1.1.1.1.13');
        $bovitoports = @snmp2_real_walk($ipaddress, $community, 'iso.3.6.1.2.1.47.1.1.1.1.4');

        foreach($bovitotypes as $key => $rawvalue)
        {
            $id = clearKey($key);
            $value = clearValue($rawvalue);
            if(isset($pluggable[$id]))
            {
                $pluggable[$id]->type = $value;
            }
        }

        foreach($bovitoalters as $key => $rawvalue)
        {
            $id = clearKey($key);
            $value = clearValue($rawvalue);
            /* 
            * Csúnya, hack megoldás. A típus mezőben van, hogy port neveket találni, miközben a bővítő !!! NEM !!! az interface ID-jához van kötve,
            * hanem az interfacet képviselő rendszerelem ID-jához. Ez azért baj, mert az interface-t képviselő rendszerelem ID-ja nincs kapcsolatban az interface ID-jával.
            * A rendszerelem viszont tudja a saját nevét, ami nagyjából megegyezik az interface nevével, amit képvisel.
            * Ezért ilyen esetben elmentjük a rendszerelemek típusnevét ID szerint, és később ezt az ID-t keressük ki, hogy összevessük a rendszerelem nevét az interfacek nevével.
            */
            $value = str_replace("GigabitEthernet", "Gi", $value);
            $value = str_replace("FastEthernet", "Fe", $value);
            $containers[$id] = $value;
        }

        foreach($bovitoserials as $key => $rawvalue)
        {
            $id = clearKey($key);
            $value = clearValue($rawvalue);
            if(isset($pluggable[$id]))
                $pluggable[$id]->serial = $value;
        }

        foreach($bovitomodels as $key => $rawvalue)
        {
            $id = clearKey($key);
            $value = clearValue($rawvalue);
            if(isset($pluggable[$id]))
                $pluggable[$id]->model = $value;
        }

        foreach($bovitoports as $key => $rawvalue)
        {
            $id = clearKey($key);
            $value = clearValue($rawvalue);
            if(isset($pluggable[$id]))
                $pluggable[$id]->portid = $value;
        }

        foreach($intlist as $interface)
        {
            //echo $interface->id . " " . $interface->shortname . "<br>";
            foreach($pluggable as $bovito)
            {
                //print_r($bovito);
                //echo "<br><br>";
                if($interface->id == $bovito->portid)
                {
                    $interface->bovitotipus = $bovito->type;
                    $interface->bovitomodel = $bovito->model;
                    $interface->bovitosorozatszam = $bovito->serial;
                    break;
                }
                elseif(isset($containers[$bovito->portid]) && str_contains($containers[$bovito->portid], $interface->shortname))
                {
                    // Az összevetésre azért van szükség, mert az str_contains megtalálja pl a Gi1/0/49-re dugott bővítőt
                    // a Gi1/0/4-es porton is. Mivel a $containers[$bovito->portid] nem egy az egyben a port nevét adja vissza
                    // Így ebben az str_contains-szel érdemes keresni, és utána szűkteni a találatokat.
                    $egyez = false;
                    $pontos = explode(" ", $containers[$bovito->portid]);
                    foreach($pontos as $bovitoportnev)
                    {
                        if($bovitoportnev == $interface->shortname)
                            $egyez = true;
                    }
                    if($egyez)
                    {
                        $interface->bovitotipus = $bovito->type;
                        $interface->bovitomodel = $bovito->model;
                        $interface->bovitosorozatszam = $bovito->serial;
                    }
                    break;
                }
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
                <th>Sebesség</th><?php
                if($vanbovito)
                {
                    ?><th style="display:none" class="hiddencol-bovito">Bővítőszabvány</th>
                    <th style="display:none" class="hiddencol-bovito">Modell</th>
                    <th style="display:none" class="hiddencol-bovito">Sorozatszám</th>
                    <th><a onclick="mutatOszlop('bovito')" class="tablenyitcsuk" id="bovito-cursor">></a></th><?php
                }
            ?></tr>
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
                        <td><?=$interface->PortSebesseg()?></td><?php
                        if($vanbovito)
                        {
                            ?><td style="display:none" class="hiddencol-bovito"><?=$interface->bovitotipus?></td>
                            <td style="display:none" class="hiddencol-bovito"><?=$interface->bovitomodel?></td>
                            <td style="display:none" class="hiddencol-bovito"><?=$interface->bovitosorozatszam?></td><?php
                        }
                    ?></tr><?php
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