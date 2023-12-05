<?php
/* //* "1.3.6.1.4.1.9.2.9.3.1.1" = kapcsolattípus, lehet "1.3.6.1.4.1.9.2.9.3.1.1.1.1" is, az IP cím az OID-ben: van switch IP . port . kapcsolódó eszköz IP
    1 : unknown
    2 : pad
    3 : stream
    4 : rlogin
    5 : telnet
    6 : tcp
    7 : lat
    8 : mop
    9 : slip
    10 : xremote
    11 : rshell
    */

    /* //* "1.3.6.1.2.1.6.13.1.1" = kapcsolat állapot, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP
    1 : closed
    2 : listen
    3 : synSent
    4 : synReceived
    5 : established
    6 : finWait1
    7 : finWait2
    8 : closeWait
    9 : lastAck
    10 : closing
    11 : timeWait
    12 : deleteTCB
    */

    /* //* "1.3.6.1.4.1.9.2.6.1.1.5" = kapcsolat hossza, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP

    */

    /* //* "1.3.6.1.4.1.9.2.6.1.1.1" = az eszköz felé küldött byte-ok száma, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP

    */

    /* //* "1.3.6.1.4.1.9.2.6.1.1.2" = az eszköz által küldött byte-ok száma, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP

    */

    /* //* "1.3.6.1.4.1.9.2.9.2.1.18" = a kapcsolatban résztvevő TACACS felhasználónév
    */

    /* //* "1.3.6.1.4.1.9.9.43.1.1.1" = az előző konfigurációváltozás óta eltelt tickek száma (negatív érték)
    */

    /* //* "1.3.6.1.4.1.9.9.43.1.1.6.1.6" = kapcsolódó terminál típusa
    1 : notApplicable
    2 : unknown
    3 : console
    4 : terminal
    5 : virtual
    6 : auxiliary
    */

    /* //* "1.3.6.1.4.1.9.9.43.1.1.6.1.8" = a terminál felhasználó
    */

    /* //!1.3.6.1.4.1.9.9.46.1.6.1.1.14" = port trunk állapota. Az OID-fabeli azonosítója az utolsó tagban (a .14 után, pl.: .14.5) van
    1 : trunking
    2 : notTrunking
    */

    /* //* "1.3.6.1.4.1.9.9.43.1.1.6.1.3" = a változás csatornája
    1 : commandLine
    2 : snmp
    */

    /* //* "1.3.6.1.4.1.9.9.43.1.1.6.1.4" = a beállítás változás forrása
    1:erase
    2:commandSource
    3:running
    4:startup
    5:local
    6:networkTftp
    7:networkRcp
    8:networkFtp
    9:networkScp
    */

    /* //* "1.3.6.1.4.1.9.9.43.1.1.6.1.5" = a beállítás változás iránya
    1:erase
    2:commandSource
    3:running
    4:startup
    5:local
    6:networkTftp
    7:networkRcp
    8:networkFtp
    9:networkScp
    */

    /* //* "1.3.6.1.4.1.9.9.41.1.2.3.1.2" = az üzenetet genráló elem típusa
    */

    /* //* "1.3.6.1.4.1.9.9.41.1.2.3.1.3" = az üzenet sürgőssége
    0:emerg
    1:alert
    2:crit
    3:err
    4:warning
    5:notice
    6:info
    7:debug
    */

    /* //* "1.3.6.1.4.1.9.9.41.1.2.3.1.4" = az üzenettípus megnevezése
    */

    /* //* "1.3.6.1.4.1.9.9.41.1.2.3.1.5" = az üzenet szövege
    */

    /* //* "1.3.6.1.4.1.9.9.41.1.2.3.1.6" = a rendszer uptime az üzenet generálódásának idejében
    */

    /* //* "1.3.6.1.2.1.2.2.1.1" = az üzenetet generáló port ifindexe
    */

    /* //* "1.3.6.1.2.1.2.2.1.2" = az üzenetet generáló port neve
    */

    /* //* "1.3.6.1.2.1.2.2.1.3" = port típusának IANA azonosítója
    */

    /* //* "1.3.6.1.4.1.9.2.2.1.1.20" = portváltozás szöveges leírása
    */

    /* //* "1.3.6.1.4.1.9.6.1.101.2.3.1" = a trap szöveges leírása
    */

    /* //* "1.3.6.1.4.1.9.6.1.101.2.3.2" = a trap súlyossága
    info(0),
    warning(1),
    error(2),
    fatal-error(3)
    */

    /* //* "1.3.6.1.4.1.9.6.1.101.57.2.8.1" = a trapet generáló port ifindexe
    */

    /* //* "1.3.6.1.4.1.9.6.1.101.57.2.8.2" = a trapet generáló port vlan-ja
    */

    /* //* "1.3.6.1.2.1.47.1.4.1" = a sysUptime a trap generálódásának idején
    */

    /* //* "1.3.6.1.2.1.1.3" = a sysUptime a trap generálódásának idején
    */

    /* "1.3.6.1.4.1.9.2.1.2" = ?
*/

function kapcsolatTipus($tipus)
{
    switch($tipus)
    {
        case 1 : return "ismeretlen";
        case 2 : return "pad";
        case 3 : return "stream";
        case 4 : return "rlogin";
        case 5 : return "telnet";
        case 6 : return "tcp";
        case 7 : return "lat";
        case 8 : return "mop";
        case 9 : return "slip";
        case 10 : return "xremote";
        case 11 : return "rshell";
    }
}

function kapcsolatAllapot($allapot)
{
    switch($allapot)
    {
        case 1 : return "lezárult";
        case 2 : return "figyel";
        case 3 : return "synSent";
        case 4 : return "synReceived";
        case 5 : return "létesült";
        case 6 : return "finWait1";
        case 7 : return "finWait2";
        case 8 : return "closeWait";
        case 9 : return "lastAck";
        case 10 : return "lezárul";
        case 11 : return "timeWait";
        case 12 : return "deleteTCB";
    }
}

function terminalTipus($tipus)
{
    switch($tipus)
    {
        case 1 : return "nem értelmezhető";
        case 2 : return "ismeretlen";
        case 3 : return "konzol";
        case 4 : return "terminál";
        case 5 : return "virtuális";
        case 6 : return "auxiliary";
    }
}

function valtozasIranyok($irany)
{
    switch($irany)
    {
        case 1 : return "törlés";
        case 2 : return "bevitt parancs";
        case 3 : return "futó";
        case 4 : return "induló";
        case 5 : return "helyi";
        case 6 : return "TFTP";
        case 7 : return "RCP";
        case 8 : return "FTP";
        case 9 : return "SCP";
    }
}

function logSeverity($severity)
{
    switch($severity)
    {
        case 0 : return "vészhelyzet";
        case 1 : return "riasztás";
        case 2 : return "kritikus";
        case 3 : return "hiba";
        case 4 : return "figyelmeztetés";
        case 5 : return "értesítés";
        case 6 : return "informális";
        case 7 : return "debug";
    }
}

function trapSeverity($severity)
{
    switch($severity)
    {
        case 0 : return "informális";
        case 1 : return "figyelmeztetés";
        case 2 : return "hiba";
        case 3 : return "súlyos hiba";
    }
}

function messageSeverity($type, $severity)
{
    if($type == "log")
    {
        switch($severity)
        {
            case 0 : $severity = 5; break;
            case 1 : $severity = 3; break;
            case 2 : $severity = 4; break;
            case 3 : $severity = 3; break;
            case 4 : $severity = 2; break;
            case 5 : $severity = 1; break;
            case 6 : $severity = 1; break;
            case 7 : $severity = 0; break;
        }
    }

    if($type == "trap")
    {
        switch($severity)
        {
            case 0 : $severity = 1; break;
            case 1 : $severity = 2; break;
            case 2 : $severity = 3; break;
            case 3 : $severity = 4; break;
        }
    }

    return $severity;
}

function identifyIANA($type) {
    switch($type)
    {
        case "6" : return "Ethernet";
        case "7" : return "Ethernet";
        case "11" : return "Ethernet";
        case "69" : return "Ethernet";
        case "71" : return "WiFi";
        case "15" : return "FDDI";
        case "23" : return "PPPoE";
        case "48" : return "Modem";
        case "53" : return "Virtuális";
        case "55" : return "100BaseSV";
        case "56" : return "Fiber";
        case "94" : return "ADSL";
        case "97" : return "VDSL";
        case "209" : return "Bridge";
        default : return null;
    }
}

function tapAllapot($allapot)
{
    switch($allapot)
    {
        case 1 : return "normális";
        case 2 : return "figyelmet igényel";
        case 3 : return "kritikus";
        case 4 : return "leállítva";
        case 5 : return "nincs csatlakoztatva";
        case 6 : return "nem működik";
    }
}

function snmpUzenetKuldoIPTipus($tipus)
{
    switch($tipus)
    {
        case 0 : return "ismeretlen";
        case 1 : return "ipv4";
        case 2 : return "ipv6";
        case 3 : return "ipv4z";
        case 4 : return "ipv6z";
        case 16 : return "dns";
    }
}

function intAdminStatus($status)
{
    switch($status)
    {
        case 1 : return "engedélyezve";
        case 2 : return "letiltva";
        case 3 : return "tesztel";
    }
}

function portOperativeState($state)
{
    switch($state)
    {
        case "1" : return "Up";
        case "2" : return "Down";
        case "3" : return "Testing";
        case "4" : return "Ismeretlen";
        case "5" : return "Nincs csatlakozva";
        case "6" : return "Hiányzik";
        case "7" : return "Automatikusan lekapcsolva";
    }
}

function OIDs($oid)
{
    switch($oid)
    {
        case "1.3.6.1.6.3.1.1.5.1" : return "Rendszer újraindult";
        case "1.3.6.1.6.3.1.1.5.2" : return "Rendszer újraindult";
        case "1.3.6.1.6.3.1.1.5.3" : return "Port állapota offline";
        case "1.3.6.1.6.3.1.1.5.4" : return "Port állapota online";
        case "1.3.6.1.6.3.1.1.5.5" : return "Autentikációs hiba";
        case "1.3.6.1.6.3.1.1.5" : return "Általános SNMP üzenet";
        case "1.3.6.1.4.1.9.10.56.2.0.1" : return "Változás az autentikációs beállításokban";
        case "1.3.6.1.4.1.9.9.41.2.0.1": return "Log bejegyzés készült az eszközön";
        case "1.3.6.1.4.1.9.6.1.101.0.151": return "Port STP állapota tanulásról továbbításra váltott";
        case "1.3.6.1.4.1.9.6.1.101.0.152": return "Port STP állapota továbbításról blokkolásra váltott";
        case "1.3.6.1.4.1.9.9.43.2.0.1": return "Változás az eszköz beállításaiban";
        case "1.3.6.1.4.1.9.0.1": return "Virtuális konzol kapcsolat lezárult";
        case "1.3.6.1.4.1.9.6.1.101.0.218": return "Egyetlen eszköz van csatlakoztatva a porthoz";
        case "1.3.6.1.4.1.9.6.1.101.0.217": return "Több eszköz van csatlakoztatva a porthoz";
        case "1.3.6.1.4.1.9.9.43.2.0.2": return "Az eszköz konfigurációja frissült";
        case "1.3.6.1.4.1.9.6.1.101.0.180": return "Másolási folyamat befejeződött";
        case "1.3.6.1.4.1.9.9.13.3.0.5": return "Redundáns táp hibája";
        case "1.3.6.1.4.1.9.9.46.2.0.7": return "Port dinamikus trunk állapotának változása";
        case "1.3.6.1.2.1.47.2.0.1": return "Rendszerelem inicializáció";
        case "1.3.6.1.4.1.9.0.0": return "A rendszer újraindul";
        case "1.3.6.1.2.1.17.0.2": return "Feszítőfa (STP) topológia változás";
        case "1.3.6.1.4.1.9.9.10.1.3.0.5": return "Cserélhető FLASH eszköz csatlakoztatva";
        case "1.3.6.1.4.1.9.9.10.1.3.0.4": return "Változás egy cserélhető FLASH eszköz állapotában";
        case "": return "";
        case "": return "";
        case "": return "";
        default : return $oid;
    }
}

//* Ez a fügvény az adatbázisból vett, stringként elmentett üzenet OID<->érték kombinációkat alakítja a processMessageBody függvény számára feldolgozható objektumok tömbjévé
function processRaw($rawmessage)
{
    $returnarr = array();
    $sorok = explode("\n", $rawmessage);
    foreach($sorok as $sor)
    {
        $ertekek = explode("; ", $sor);
        if($ertekek[0] && isset($ertekek[1])) // Az utolsó sor üres, ezért ha null, akkor ne foglalkozzunk vele
        {
            $oid = str_replace("OID: ", "", $ertekek[0]);
            $value = str_replace("Value: ", "", $ertekek[1]);
            $value = str_replace("Value:", "", $value); // null érték esetén nincs szóköz a Value: után
            $temp = new stdClass();
            $temp->OID = $oid;
            $temp->TrapVal = $value;
            $returnarr[] = $temp;
        }
    }
    return $returnarr;
}

function rawToJson($rawmessage)
{
    $returnarr = array();
    $sorok = explode("\n", $rawmessage);
    foreach($sorok as $sor)
    {
        $ertekek = explode("; ", $sor);
        if($ertekek[0] && isset($ertekek[1])) // Az utolsó sor üres, ezért ha null, akkor ne foglalkozzunk vele
        {
            $oid = str_replace("OID: ", "", $ertekek[0]);
            $value = str_replace("Value: ", "", $ertekek[1]);
            $value = str_replace("Value:", "", $value); // null érték esetén nincs szóköz a Value: után
            $temp = new stdClass();
            $temp->OID = $oid;
            $temp->TrapVal = $value;
            $returnarr[] = $temp;
        }
    }
    return json_encode($returnarr);
}

function processMessageBody($body, $devip, $community)
{
    $veglegesuzenet = $port = "";
    $severity = 1;

    foreach($body as $element)
    {
        $megtalalt = $rendszerelemifid = false;

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.9.3.1.1"))
        {
            /* "1.3.6.1.4.1.9.2.9.3.1.1" = kapcsolattípus, lehet "1.3.6.1.4.1.9.2.9.3.1.1.1.1" is */
            $veglegesuzenet .= "<div>Kapcsolat típusa:</div><div>" . kapcsolatTipus($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.6.13.1.1"))
        {
            /* "1.3.6.1.2.1.6.13.1.1" = kapcsolat állapot, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP */
            $ipcimportipcim = str_replace("1.3.6.1.2.1.6.13.1.1.", "", $element->OID);
            $mindentag = explode(".", $ipcimportipcim);
            $veglegesuzenet .= "<div>A kapcsolat állapota:</div><div>" . kapcsolatAllapot($element->TrapVal) . "</div>";
            $veglegesuzenet .= "<div>Kapcsolódó eszköz címe:</div><div>{$mindentag[5]}.{$mindentag[6]}.{$mindentag[7]}.{$mindentag[8]}" . "</div>";
            $veglegesuzenet .= "<div>A kapcsolódáshoz használt port:</div><div>$mindentag[4]". "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.6.1.1.5"))
        {
            $veglegesuzenet .= "<div>Kapcsolat hossza:</div><div>" . round(($element->TrapVal / 100), 2) . " másodperc</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.6.1.1.1"))
        {
            $veglegesuzenet .= "<div>Fogadott adat:</div><div>" . $element->TrapVal . "Byte</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.6.1.1.2"))
        {
            $veglegesuzenet .= "<div>Küldött adat:</div><div>" . $element->TrapVal . "Byte</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.9.2.1.18.1"))
        {
            $veglegesuzenet .= "<div>Kapcsolódó felhasználó:</div><div>" . (($element->TrapVal) ? $element->TrapVal : "Ismeretlen") . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.1"))
        {
            $veglegesuzenet .= "<div>Az előző konfiguráció módosítás óta eltelt idő:</div><div>" . secondsToFullFormat($element->TrapVal / 1000) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.6"))
        {
            $veglegesuzenet .= "<div>Terminál típusa:</div><div>" . terminalTipus($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.8"))
        {
            $veglegesuzenet .= "<div>Kapcsolódó felhasználó:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.46.1.6.1.1.14"))
        {
            $veglegesuzenet .= "<div>A port trunking állapota:</div><div>" . (($element->TrapVal = 1) ? "trunk" : "nem trunk") . "</div>";
            $megtalalt = true;
            $rendszerelemifid = str_replace("1.3.6.1.4.1.9.9.46.1.6.1.1.14.", "", $element->OID);
            
            //! Megírni a névlekérést
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.3"))
        {
            $veglegesuzenet .= "<div>A módosítást kezdeményező csatorna:</div><div>" . (($element->TrapVal = 1) ? "parancssor" : "SNMP") . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.4"))
        {
            $veglegesuzenet .= "<div>A konfiguráció forrása:</div><div>" . valtozasIranyok($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.5"))
        {
            $veglegesuzenet .= "<div>A konfiguráció célja:</div><div>" . valtozasIranyok($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.3"))
        {
            $veglegesuzenet .= "<div>Az üzenet fontossága:</div><div>" . logSeverity($element->TrapVal) . "</div>";
            $severity = messageSeverity("log", $element->TrapVal);
            $megtalalt = true;
        }
        
        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.4"))
        {
            $veglegesuzenet .= "<div>Üzenet típusa:</div><div>" . $element->TrapVal . "</div>";
            if($element->TrapVal == "UPDOWN")
            {
                $severity = 1;
            }
            $megtalalt = true;
        }
        
        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.5"))
        {
            $veglegesuzenet .= "<div>Üzenet szövege:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.6"))
        {
            $veglegesuzenet .= "<div>Az elem aktuális uptime-ja:</div><div>" . secondsToFullFormat($element->TrapVal / 100) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.1") || str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.57.2.8.1"))
        {
            //$veglegesuzenet .= "<div>Port ifindexe:</div><div>" . $element->TrapVal . "</div>";
            $rendszerelemifid = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.2"))
        {
            //$veglegesuzenet .= "<div>Port neve:</div><div>" . $element->TrapVal . "</div>";
            $port = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.3"))
        {
            $veglegesuzenet .= "<div>Port típusa:</div><div>" . identifyIANA($element->TrapVal) . "</div>";
            $megtalalt = true;
        }
        
        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.2.1.1.20") || str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.2.3.1"))
        {
            $veglegesuzenet .= "<div>Szöveges leírás:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.2.3.2"))
        {
            $veglegesuzenet .= "<div>Az értesítés súlyossága:</div><div>" . trapSeverity($element->TrapVal) . "</div>";
            $severity = messageSeverity("trap", $element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.57.2.8.2"))
        {
            $veglegesuzenet .= "<div>Port VLAN:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.47.1.4.1"))
        {
            $veglegesuzenet .= "<div>Utolsó módosítás ideje:</div><div>" . secondsToFullFormat($element->TrapVal / 1000) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.1.3"))
        {
            $veglegesuzenet .= "<div>Idő a legutolsó újraindulás óta:</div><div>" . secondsToFullFormat($element->TrapVal / 100) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.1.2"))
        {
            $veglegesuzenet .= "<div>Az újraindulás oka:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.2"))
        {
            $veglegesuzenet .= "<div>Az értesítést generáló elem neve:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.13.1.5.1.3"))
        {
            $veglegesuzenet .= "<div>A hálózati táp állapota:</div><div>" . tapAllapot($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.13.1.5.1.2"))
        {
            $veglegesuzenet .= "<div>A táp megnevezése:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.412.1.1.2"))
        {
            $veglegesuzenet .= "<div>Nem autentikus SNMP üzenet forrása:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.1.5"))
        {
            $veglegesuzenet .= "<div>Beazonosítási hibát okozó SNMP üzenet forrása:</div><div>" . $element->TrapVal . "</div>";
            $severity = 3;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.412.1.1.1"))
        {
            $veglegesuzenet .= "<div>SNMP üzenetet küldő eszköz IP típusa:</div><div>" . snmpUzenetKuldoIPTipus($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.7"))
        {
            $veglegesuzenet .= "<div>Interface adminisztratív állapota:</div><div>" . intAdminStatus($element->TrapVal) . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.9.2.1.18"))
        {
            $veglegesuzenet .= "<div>TACACS felhasználónév:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.8"))
        {
            $veglegesuzenet .= "<div>Port operatív állapota:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.10.1.1.2.1.3"))
        {
            $veglegesuzenet .= "<div>Cerélhető eszköz minimális partíciómérete:</div><div>" . ($element->TrapVal / 1024) . " kByte</div>";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.10.1.1.2.1.7"))
        {
            $veglegesuzenet .= "<div>Cserélhető eszköz megnevezése:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }
        /* //! csomag: 31*13*8 + 384
        if(str_contains($element->OID, ""))
        {
            $veglegesuzenet .= "<div>:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }
        if(str_contains($element->OID, ""))
        {
            $veglegesuzenet .= "<div>:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }
        if(str_contains($element->OID, ""))
        {
            $veglegesuzenet .= "<div>:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        */

        //echo $element->OID . "</div>";

        if($rendszerelemifid && !$port)
        {
            $ping = $objectnev = "";
            $ping = exec("ping -n 1 -w 200 $devip", $output);
            $elerheto = !str_contains($ping, "100% loss");
            if($elerheto)
            {
                $objectnev = @snmp2_get($devip, $community, "iso.3.6.1.2.1.31.1.1.1.1.$rendszerelemifid");
                $portexpl = explode(": ", $objectnev);
                $port = trim($portexpl[1], "\"");
                //$veglegesuzenet .= "<div>Port:</div><div>" . $port . "</div>";
            }
        }

        $port = str_replace("GigabitEthernet", "gi", $port);
        $port = str_replace("FastEthernet", "fa", $port);
        $port = str_replace("fa", "Fa", $port);
        $port = str_replace("gi", "Gi", $port);
        $port = str_replace("LongReachEthernet", "Lo", $port);

        // A legjobb megoldás ha félig megfejtett nyers üzeneteket nem írjuk ki/tesszük be az adatbázisba
        if(!$megtalalt)
            return null;
    }
    //echo $veglegesuzenet;
    $returnarr = array(
        "body" => $veglegesuzenet,
        "port" => $port,
        "severity" => $severity
    );
    return $returnarr;
}

function processMessageBody_json($body, $devip, $community)
{
    $port = "";
    $return_json = null;
    $severity = 1;

    foreach(json_decode($body) as $element)
    {
        $element->TrapVal = str_replace(["\r", "\n"], "", $element->TrapVal);
        $tempobj['oid'] = $element->OID;
        $tempobj['rawval'] = $element->TrapVal;
        $megtalalt = $rendszerelemifid = $hozzaadott = false;

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.9.3.1.1"))
        {
            /* "1.3.6.1.4.1.9.2.9.3.1.1" = kapcsolattípus, lehet "1.3.6.1.4.1.9.2.9.3.1.1.1.1" is */
            $tempobj['szoveg'] = "Kapcsolat típusa";
            $tempobj['ertek'] = kapcsolatTipus($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.6.13.1.1"))
        {
            /* "1.3.6.1.2.1.6.13.1.1" = kapcsolat állapot, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP */
            $ipcimportipcim = str_replace("1.3.6.1.2.1.6.13.1.1.", "", $element->OID);
            $mindentag = explode(".", $ipcimportipcim);
            $tempobj['szoveg'] = "A kapcsolat állapota";
            $tempobj['ertek'] = kapcsolatAllapot($element->TrapVal);
            $tempkapcsuzenet['szoveg'] = "Kapcsolódó eszköz címe";
            $tempkapcsuzenet['ertek'] = "{$mindentag[5]}.{$mindentag[6]}.{$mindentag[7]}.{$mindentag[8]}";
            $tempkapcsport['szoveg'] = "A kapcsolódáshoz használt port";
            $tempkapcsport['ertek'] = $mindentag[4];
            $return_json[] = $tempobj;
            $return_json[] = $tempkapcsuzenet;
            $return_json[] = $tempkapcsport;
            $megtalalt = $hozzaadott = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.6.1.1.5"))
        {
            $tempobj['szoveg'] = "Kapcsolat hossza";
            $tempobj['ertek'] = secondsToFullFormat(round(($element->TrapVal / 100), 2));
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.6.1.1.1"))
        {
            $tempobj['szoveg'] = "Fogadott adat";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.6.1.1.2"))
        {
            $tempobj['szoveg'] = "Küldött adat";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.9.2.1.18.1"))
        {
            $tempobj['szoveg'] = "Kapcsolódó felhasználó";
            $tempobj['ertek'] = (($element->TrapVal) ? $element->TrapVal : "Ismeretlen");
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.1"))
        {
            $tempobj['szoveg'] = "Az előző konfiguráció módosítás óta eltelt idő";
            $tempobj['ertek'] = secondsToFullFormat($element->TrapVal / 1000);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.6"))
        {
            $tempobj['szoveg'] = "Terminál típusa";
            $tempobj['ertek'] = terminalTipus($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.8"))
        {
            $tempobj['szoveg'] = "Kapcsolódó felhasználó";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.46.1.6.1.1.14"))
        {
            $tempobj['szoveg'] = "A port trunking állapota";
            $tempobj['ertek'] = (($element->TrapVal = 1) ? "trunk" : "nem trunk");
            $megtalalt = true;
            $rendszerelemifid = str_replace("1.3.6.1.4.1.9.9.46.1.6.1.1.14.", "", $element->OID);
            
            //! Megírni a névlekérést
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.3"))
        {
            $tempobj['szoveg'] = "A módosítást kezdeményező csatorna";
            $tempobj['ertek'] = (($element->TrapVal = 1) ? "parancssor" : "SNMP");
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.4"))
        {
            $tempobj['szoveg'] = "A konfiguráció forrása";
            $tempobj['ertek'] = valtozasIranyok($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.43.1.1.6.1.5"))
        {
            $tempobj['szoveg'] = "A konfiguráció célja";
            $tempobj['ertek'] = valtozasIranyok($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.3"))
        {
            $tempobj['szoveg'] = "Az üzenet fontossága";
            $tempobj['ertek'] = logSeverity($element->TrapVal);
            $severity = messageSeverity("log", $element->TrapVal);
            $megtalalt = true;
        }
        
        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.4"))
        {
            $tempobj['szoveg'] = "Üzenet típusa";
            $tempobj['ertek'] = $element->TrapVal;
            if($element->TrapVal == "UPDOWN")
            {
                $severity = 1;
            }
            $megtalalt = true;
        }
        
        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.5"))
        {
            $tempobj['szoveg'] = "Üzenet szövege";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.6"))
        {
            $tempobj['szoveg'] = "Az elem aktuális uptime-ja";
            $tempobj['ertek'] = secondsToFullFormat($element->TrapVal / 100);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.1") || str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.57.2.8.1"))
        {
            //$veglegesuzenet .= "<div>Port ifindexe:</div><div>" . $element->TrapVal . "</div>";
            $rendszerelemifid = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.2"))
        {
            //$veglegesuzenet .= "<div>Port neve:</div><div>" . $element->TrapVal . "</div>";
            $port = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.3"))
        {
            $tempobj['szoveg'] = "Port típusa";
            $tempobj['ertek'] = identifyIANA($element->TrapVal);
            $megtalalt = true;
        }
        
        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.2.1.1.20") || str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.2.3.1"))
        {
            $tempobj['szoveg'] = "Szöveges leírás";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.2.3.2"))
        {
            $tempobj['szoveg'] = "Az értesítés súlyossága";
            $tempobj['ertek'] = trapSeverity($element->TrapVal);
            $severity = messageSeverity("trap", $element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.6.1.101.57.2.8.2"))
        {
            $tempobj['szoveg'] = "Port VLAN";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.47.1.4.1"))
        {
            $tempobj['szoveg'] = "Utolsó módosítás ideje";
            $tempobj['ertek'] = secondsToFullFormat($element->TrapVal / 1000);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.1.3"))
        {
            $tempobj['szoveg'] = "Idő a legutolsó újraindulás óta";
            $tempobj['ertek'] = secondsToFullFormat($element->TrapVal / 100);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.1.2"))
        {
            $tempobj['szoveg'] = "Az újraindulás oka";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.41.1.2.3.1.2"))
        {
            $tempobj['szoveg'] = "Az értesítést generáló elem neve";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.13.1.5.1.3"))
        {
            $tempobj['szoveg'] = "A hálózati táp állapota";
            $tempobj['ertek'] = tapAllapot($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.13.1.5.1.2"))
        {
            $tempobj['szoveg'] = "A táp megnevezése";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.412.1.1.2"))
        {
            $tempobj['szoveg'] = "Nem autentikált SNMP üzenet forrása";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.1.5"))
        {
            $tempobj['szoveg'] = "Beazonosítási hibát okozó SNMP üzenet forrása";
            $tempobj['ertek'] = $element->TrapVal;
            $severity = 3;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.412.1.1.1"))
        {
            $tempobj['szoveg'] = "SNMP üzenetet küldő eszköz IP típusa";
            $tempobj['ertek'] = snmpUzenetKuldoIPTipus($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.7"))
        {
            $tempobj['szoveg'] = "Interface adminisztratív állapota";
            $tempobj['ertek'] = intAdminStatus($element->TrapVal);
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.2.9.2.1.18"))
        {
            $tempobj['szoveg'] = "TACACS felhasználónév";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.2.1.2.2.1.8"))
        {
            $tempobj['szoveg'] = "Port operatív állapota";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.10.1.1.2.1.3"))
        {
            $tempobj['szoveg'] = "Cerélhető eszköz minimális partíciómérete";
            $tempobj['ertek'] = ($element->TrapVal / 1024) . " kByte";
            $megtalalt = true;
        }

        if(str_contains($element->OID, "1.3.6.1.4.1.9.9.10.1.1.2.1.7"))
        {
            $tempobj['szoveg'] = "Cserélhető eszköz megnevezése";
            $tempobj['ertek'] = $element->TrapVal;
            $megtalalt = true;
        }
        /* //! csomag: 31*13*8 + 384
        if(str_contains($element->OID, ""))
        {
            $veglegesuzenet .= "<div>:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }
        if(str_contains($element->OID, ""))
        {
            $veglegesuzenet .= "<div>:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }
        if(str_contains($element->OID, ""))
        {
            $veglegesuzenet .= "<div>:</div><div>" . $element->TrapVal . "</div>";
            $megtalalt = true;
        }

        */

        //echo $element->OID . "</div>";

        if($rendszerelemifid && !$port)
        {
            $ping = $objectnev = "";
            $ping = exec("ping -n 1 -w 200 $devip", $output);
            $elerheto = !str_contains($ping, "100% loss");
            if($elerheto)
            {
                $objectnev = @snmp2_get($devip, $community, "iso.3.6.1.2.1.31.1.1.1.1.$rendszerelemifid");
                $portexpl = explode(": ", $objectnev);
                $port = trim($portexpl[1], "\"");
                //$veglegesuzenet .= "<div>Port:</div><div>" . $port . "</div>";
            }
        }

        $port = str_replace("GigabitEthernet", "gi", $port);
        $port = str_replace("FastEthernet", "fa", $port);
        $port = str_replace("fa", "Fa", $port);
        $port = str_replace("gi", "Gi", $port);
        $port = str_replace("LongReachEthernet", "Lo", $port);

        // A legjobb megoldás ha félig megfejtett nyers üzeneteket nem írjuk ki/tesszük be az adatbázisba
        if($megtalalt && !$hozzaadott)
            $return_json[] = $tempobj;
            
    }
    //echo $veglegesuzenet;
    $returnarr = array(
        "body" => json_encode($return_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS),
        "port" => $port,
        "severity" => $severity
    );
    return $returnarr;
}