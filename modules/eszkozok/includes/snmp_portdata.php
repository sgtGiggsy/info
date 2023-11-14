<?php
include("../../../includes/config.inc.php");
include("../../../includes/functions.php");
include('../classes/devinterface.class.php');

$tesztip = "192.168.1.1";
$tesztcommunity = "public";

$ipaddress = $_GET['ip'];
$community = $_GET['community'];

$snmpadatok = [];
//$obj = snmpwalkoid($tesztip, $tesztcommunity, null);
$obj = @snmprealwalk($ipaddress, $community, "iso.3.6.1.2.1.2.2.1");
$obj2 = @snmprealwalk($ipaddress, $community, "iso.3.6.1.2.1.31.1.1.1");

// Adat feldolgozás rész
if($obj)
{
    foreach($obj as $key => $rawvalue)
    {
        $azonosito = explode(".", $key);
        $id = end($azonosito);

        $valarr = explode(": ", $rawvalue);
        $value = trim(end($valarr), "\"");

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.1."))
        {
            // Az iso.3.6.1.2.1.2.2.1.1 az interface-ek sorszáma
            $temp = new DevInterface;
            $temp->id = $id;

            $intlist[$id] = $temp;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.2."))
        {
            // Az iso.3.6.1.2.1.2.2.1.2 az interface-ek neve
            $intlist[$id]->name = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.3."))
        {
            // Az iso.3.6.1.2.1.2.2.1.3 az interface-ek típusa (wifi, eth, pppoe...) (71 - wifi, 6, 7, 11, 69 - ethernet (6 a hivatalos), 209 - bridge, 23 - pppoe, 15 - fddi, 48 - modem, 55 - 100BaseVG, 56 - optika, 94 - adsl, 97 - vdsl) - Az ifType iana szabvány tartalmazza az összeset
            $intlist[$id]->type = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.4."))
        {
            // Az iso.3.6.1.2.1.2.2.1.4 az interface MTU-ja (legnagyobb csomagméret) byte-ban megadva
            $intlist[$id]->mtu = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.5."))
        {
            $intlist[$id]->speed = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.6."))
        {
            // Az iso.3.6.1.2.1.2.2.1.6 az interface MAC address-e
            $rawmac = explode("Hex-STRING: ", $rawvalue);
            $mac = null;
            if(isset($rawmac[1]))
            {
                $mac = trim($rawmac[1], " ");
                $mac = str_replace(" ", ":", $mac);
            }
            //print_r($rawmac);
            $intlist[$id]->mac = $mac;
        }
        
        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.7"))
        {
            // Az iso.3.6.1.2.1.2.2.1.7 az interface-ek adminisztrációs up-down állapota (1 - up, 2 - down, 3 - testing)
            $intlist[$id]->adminstate = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.8"))
        {
            // Az iso.3.6.1.2.1.2.2.1.8 az interface-ek jelen operatív up-down állapota (1 - up, 2 - down, 3 - testing, 4 - megállapíthatatlan, 5 - nincs csatlakozva, 6 - komponens hiányzik, 7 - alsóbb szintű elem által down állapotba helyezve)
            $intlist[$id]->opstate = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.9."))
        {
            // Az iso.3.6.1.2.1.2.2.1.9 az interface jelen állapotba lépésének ideje (nap-óra-perc-másodperc, illetve timetic formátumban is megadva)
            $rawstrarr = explode("Timeticks: (", $rawvalue);
            $rawsecs = explode(")", $rawstrarr[1]);
            $seconds = $rawsecs[0]/100;
            $intlist[$id]->statesince = $seconds;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.10."))
        {
            // Az iso.3.6.1.2.1.2.2.1.10 az interface beérkező byte-jainak száma az eszköz utolsó bekapcsolása (vagy állapot resetelése) óta
            $intlist[$id]->rx = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.11."))
        {
            //Az iso.3.6.1.2.1.2.2.1.11 az interfacere érkező unicast csomagok száma
            $intlist[$id]->rxuni = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.12."))
        {
            // Az iso.3.6.1.2.1.2.2.1.12 az interfacere érkező multicast vagy broadcast csomagok száma
            $intlist[$id]->rxbroad = $value;
        }
        
        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.13."))
        {
            // Az iso.3.6.1.2.1.2.2.1.13 az interfacere befutó eldobott nem sérült/hibás csomagok száma
            $intlist[$id]->rxdroppedhealthy = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.14."))
        {
            // Az iso.3.6.1.2.1.2.2.1.14 az interfacere befutó hibás csomagok száma
            $intlist[$id]->rxdroppeddamaged = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.15."))
        {
            // Az iso.3.6.1.2.1.2.2.1.15 az interfacere befutó ismeretlen protokol miatt eldobott csomagok száma
            $intlist[$id]->rxunknown = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.16."))
        {
            // Az iso.3.6.1.2.1.2.2.1.16 az interfaceről kimenő adatmennyiség byteban megadva
            $intlist[$id]->tx = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.17."))
        {
            //// Az iso.3.6.1.2.1.2.2.1.17 az interfaceről kimenő unicast csomagok száma
            $intlist[$id]->txuni = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.18."))
        {
            // Az iso.3.6.1.2.1.2.2.1.18 az interfaceről kimenő multicast vagy broadcast csomagok száma
            $intlist[$id]->txbroad = $value;
        }
        
        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.19."))
        {
            // Az iso.3.6.1.2.1.2.2.1.19 az interfaceről kimenő eldobott nem sérült/hibás csomagok száma
            $intlist[$id]->txdroppedhealthy = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.20."))
        {
            // Az iso.3.6.1.2.1.2.2.1.20 az interfaceről kimenő hibás csomagok száma
            $intlist[$id]->txdroppeddamaged = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.2.2.1.21."))
        {
            // Az iso.3.6.1.2.1.2.2.1.21 az interfaceről kimenő csomagok várólistája (csomagszámban megadva)
            $intlist[$id]->txwaitlist = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.17.7.1.4.5.1.1."))
        {
            // Az iso.3.6.1.2.1.17.7.1.4.5.1.1 a port untagged VLAN-ját mondja meg
            $intlist[$id]->vlan = $value;
        }
    }

    foreach($obj2 as $key => $rawvalue)
    {
        //echo "<tr><td>$key</td>";
        //echo "<td>$rawvalue</td></tr>";
        
        $azonosito = explode(".", $key);
        $id = end($azonosito);

        $valarr = explode(": ", $rawvalue);
        $value = trim(end($valarr), "\"");

        if(str_contains($key, "iso.3.6.1.2.1.31.1.1.1.1."))
        {
            // iso.3.6.1.2.1.31.1.1.1.1 az interface neve (virtuális, vagy fizikai)
            $intlist[$id]->shortname = $value;
        }

        if(str_contains($key, "iso.3.6.1.2.1.31.1.1.1.18."))
        {
            // Az iso.3.6.1.2.1.31.1.1.1.18 az interface leírása
            $intlist[$id]->description = $value;
        }
    }

    // Adat megjelenítés rész
    ?><table>
        <thead>
            <tr>
                <th>Port</th>
                <th>Leírás</th>
                <th>Admin állapot</th>
                <th>Operatív állapot</th>
                <th>Sebesség</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($intlist as $interface)
            {
                if($interface->type == 6)
                {
                    ?><tr>
                        <td><?=$interface->shortname?></td>
                        <td><?=$interface->description?></td>
                        <td><?=$interface->PortAdminState()?></td>
                        <td><?=$interface->PortOperativeState()?></td>
                        <td><?=$interface->PortSebessegMbit()?></td>
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