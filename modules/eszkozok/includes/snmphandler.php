<?php
include('./modules/eszkozok/classes/devinterface.class.php');

$tesztip = "192.168.1.1";
$tesztcommunity = "public";

$snmpadatok = [];
$obj = snmpwalkoid($tesztip, $tesztcommunity, null);

// Adat feldolgozás rész
foreach($obj as $key => $rawvalue)
{
    //echo "<tr><td>$key</td>";
    //echo "<td>$rawvalue</td></tr>";
    // Az iso.3.6.1.2.1.1.1 a rendszer általános adatait tartalmazza (hardver, os, verziószám)

    // Az iso.3.6.1.2.1.1.2 az eszköz gyártójának SMI azonosítója

    // Az iso.3.6.1.2.1.1.1 a hálózati elemek utolós (újra)indítása óta eltelt idő.

    // Az iso.3.6.1.2.1.1.4 a hálózati eszköz menedzserének neve és elérhetősége szöveges formátumban

    // Az iso.3.6.1.2.1.1.5 elméletben az eszköt FQDN-je

    // Az iso.3.6.1.2.1.1.6 az eszköz fizikális helye

    // Az iso.3.6.1.2.1.2.1 az interface-ek darabszáma

    // Az iso.3.6.1.2.1.4.1 mondja meg, hogy az eszköz routol-e (1 - igen, 2 - nem)

    // Az iso.3.6.1.2.1.4.2 az alapértelmezett TTL-értéket árulja el

    // Az iso.3.6.1.2.1.4.20.1.1 egy IP címről tartalmaz információkat

    // Az iso.3.6.1.2.1.4.20.1.2 egy IP címet társít egy fizikai interface-szel

    // Az iso.3.6.1.2.1.4.20.1.3 egy IP címhez tartozó alhálózati maszkot adja meg

    // Az iso.3.6.1.2.1.4.20.1.4 a broadcast cím LSB-jét adja meg

    // Az iso.3.6.1.2.1.4.20.1.5 a legnagyobb kijavítható hiba mértékét adja meg

    // Az iso.3.6.1.2.1.4.22.1.2 a kulcsban megadott IP címet társítja az értékben szereplő MAC address-szel

    // Az 1.3.6.1.2.1.17.2.1 az eszköz által használt STP verziószámát mondja meg (2 - DEC LANbridge 100, 3 - ieee8021d)

    // Az iso.3.6.1.2.1.17.2.15.1.3 a port jelenlegi STP állapotát mondja meg (disabled(1),blocking(2),listening(3),learning(4),forwarding(5),broken(6))

    // Az iso.3.6.1.2.1.17.2.15.1.4 a port STP engedélyezett - letiltott állapotát mondja meg

    // Az iso.3.6.1.2.1.17.2.15.1.5 a port STP költségét mondja meg

    // Az iso.3.6.1.2.1.17.7.1.4.5.1.1 a port untagged VLAN-ját mondja meg

    // Az iso.3.6.1.2.1.17.7.1.4.5.1.2 a porton engedélyezett VLAN kereteket adja meg (1 - minden keret átengedése, 2 - csak a tagged VLAN-ok átengedése)

    // Az iso.3.6.1.2.1.17.7.1.4.5.1.3 a porton engedélyezett VLAN kereteket adja meg (1 - minden keret átengedése, 2 - csak a tagged VLAN-ok átengedése)

    // Az iso.3.6.1.2.1.25.1.1 a SystemUptime

    // Az iso.3.6.1.2.1.25.1.2 a rendszeridő

    // Az iso.3.6.1.2.1.25.2.2 a rendszer RAM mérete

    // Az iso.3.6.1.2.1.25.2.3.1.1 egy tárhely elem azonosítója

    // Az iso.3.6.1.2.1.25.2.3.1.2 a tárhely OID azonosítója

    // Az iso.3.6.1.2.1.25.2.3.1.3 a tárhely típus szöveges megnevezése

    // Az iso.3.6.1.2.1.25.2.3.1.4 a tárhely blockmérete byteban

    // Az iso.3.6.1.2.1.25.2.3.1.5 a tárhely teljes mérete

    // Az iso.3.6.1.2.1.25.2.3.1.6 a tárhely szabad területe

    // Az iso.3.6.1.2.1.25.2.3.1.7 azon esetek száma, mikor a tárhely írási hibába ütközött a megtelt méret miatt

    // Az iso.3.6.1.2.1.25.3.2.1.1 a rendszerelemek index-száma

    // Az iso.3.6.1.2.1.25.3.2.1.2 az adott rendszerelem OID azonosítója

    // Az iso.3.6.1.2.1.25.3.2.1.3 a rendszerelem szöveges leírása (sorozatszám, gyártó, verziószám)

    // Az iso.3.6.1.2.1.25.3.2.1.4 a rendszerelem productID-je

    // Az iso.3.6.1.2.1.25.3.2.1.5 a rendszerelem jelenlegi állapota (1 - ismeretlen, 2 - működik, 3 - figyelmeztetés, 4 - testing, 5 - down)

    // Az iso.3.6.1.2.1.25.3.2.1.6 a rendszerelem errorszáma

    // Az iso.3.6.1.2.1.25.3.3.1.2 a nem idle állapot százalékos aránya az utolsó egy percben

    // Az iso.3.6.1.2.1.31.1.1.1.1 az interface neve (virtuális, vagy fizikai)

    // Az iso.3.6.1.2.1.31.1.1.1.15 az interface adminisztrátor által adott megnevezése

    // Az iso.3.6.1.2.1.47.1.1.1.1.1 a rendszerelem azonosítója

    // Az iso.3.6.1.2.1.47.1.1.1.1.2 a rendszerelem szöveges leírása

    // Az iso.3.6.1.2.1.47.1.1.1.1.3 a rendszerelem forgalmazója

    // Az iso.3.6.1.2.1.47.1.1.1.1.4 a rendszerelem azonosítója, amelybe ez a jelen rendszerelem be van építve

    // Az iso.3.6.1.2.1.47.1.1.1.1.7 a rendszerelem szöveges megnevezése

    // Az iso.3.6.1.2.1.47.1.1.1.1.8 a rendszerelem verziószáma

    // Az iso.3.6.1.2.1.47.1.1.1.1.9 a rendszerelem firmware-je

    // Az iso.3.6.1.2.1.47.1.1.1.1.10 a rendszerelem firmware-jének verziószáma

    // Az iso.3.6.1.2.1.47.1.1.1.1.11 a rendszerelem sorozatszáma

    // Az iso.3.6.1.2.1.47.1.1.1.1.12 a rendszerelem gyártója

    // Az iso.3.6.1.2.1.47.1.1.1.1.13 a rendszerelem modellszáma

    // Az iso.3.6.1.2.1.47.1.1.1.1.14 a rendszerelem admin által meghatározott neve

    // Az iso.3.6.1.2.1.47.1.1.1.1.15 fizikális erőforrás azonosító (akármi legyen is az)

    // Az iso.3.6.1.2.1.47.1.1.1.1.16 azt adja meg, hogy az elem cserélhető-e (1 - igen, 2 - nem)
    $azonosito = explode(".", $key);
    $id = end($azonosito);

    $valarr = explode(" ", $rawvalue);
    $value = end($valarr);

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
        $intlist[$id]->name = trim($value, "\"");
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


// Adat megjelenítés rész
?><table><?php
$elso = true;
foreach($intlist as $interface)
{
    if($elso)
    {
        echo "<tr>";
        foreach($interface as $x => $y)
        {
            echo "<td>$x</td>";
        }
        echo "</tr>";
        $elso = false;
    }
    echo "<tr>";
    foreach($interface as $x)
    {
        echo "<td>$x</td>";
    }
    echo "</tr>";
}

?></table>