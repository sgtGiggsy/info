<?php

/* "1.3.6.1.4.1.9.2.9.3.1.1" = kapcsolattípus, lehet "1.3.6.1.4.1.9.2.9.3.1.1.1.1" is, az IP cím az OID-ben: van switch IP . port . kapcsolódó eszköz IP
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

/* "1.3.6.1.2.1.6.13.1.1" = kapcsolat állapot, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP
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

/* "1.3.6.1.4.1.9.2.6.1.1.5" = kapcsolat hossza, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP

*/

/* "1.3.6.1.4.1.9.2.6.1.1.1" = az eszköz felé küldött byte-ok száma, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP

*/

/* "1.3.6.1.4.1.9.2.6.1.1.2" = az eszköz által küldött byte-ok száma, az IP cím az OID-ben van: switch IP . port . kapcsolódó eszköz IP

*/

/* "1.3.6.1.4.1.9.2.9.2.1.18" = a kapcsolatban résztvevő TACACS felhasználónév
*/

/* "1.3.6.1.4.1.9.9.43.1.1.1" = az előző konfigurációváltozás óta eltelt tickek száma (negatív érték)
*/

/* "1.3.6.1.4.1.9.9.43.1.1.6.1.6" = kapcsolód terminál típusa
1 : notApplicable
2 : unknown
3 : console
4 : terminal
5 : virtual
6 : auxiliary
*/

/* "1.3.6.1.4.1.9.9.43.1.1.6.1.8" = a terminál felhasználó
*/

/* "1.3.6.1.4.1.9.9.46.1.6.1.1.14" = port trunk állapota. Az OID-fabeli azonosítója az utolsó tagban (a .14 után, pl.: .14.5) van
1 : trunking
2 : notTrunking
*/

/* "1.3.6.1.4.1.9.9.43.1.1.6.1.3" = a változás csatornája
1 : commandLine
2 : snmp
*/

/* "1.3.6.1.4.1.9.9.43.1.1.6.1.4" = a beállítás változás forrása
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

/* "1.3.6.1.4.1.9.9.43.1.1.6.1.5" = a beállítás változás iránya
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

/* "1.3.6.1.4.1.9.9.41.1.2.3.1.2" = az üzenetet genráló elem típusa
*/

/* "1.3.6.1.4.1.9.9.41.1.2.3.1.3" = az üzenet sürgőssége
0:emerg
1:alert
2:crit
3:err
4:warning
5:notice
6:info
7:debug
*/

/* "1.3.6.1.4.1.9.9.41.1.2.3.1.4" = az üzenettípus megnevezése
*/

/* "1.3.6.1.4.1.9.9.41.1.2.3.1.5" = az üzenet szövege
*/

/* "1.3.6.1.4.1.9.9.41.1.2.3.1.6" = a rendszer uptime az üzenet generálódásának idejében
*/

/* "1.3.6.1.2.1.2.2.1.1" = az üzenetet generáló port ifindexe
*/

/* "1.3.6.1.2.1.2.2.1.2" = az üzenetet generáló port neve
*/

/* "1.3.6.1.2.1.2.2.1.3" = port típusának IANA azonosítója
*/

/* "1.3.6.1.4.1.9.2.2.1.1.20" = portváltozás szöveges leírása
*/

/* "1.3.6.1.4.1.9.6.1.101.2.3.1" = a trap szöveges leírása
*/

/* "1.3.6.1.4.1.9.6.1.101.2.3.2" = a trap súlyossága
info(0),
warning(1),
error(2),
fatal-error(3)
    */

/* "1.3.6.1.4.1.9.6.1.101.57.2.8.1" = a trapet generáló port ifindexe
*/

/* "1.3.6.1.4.1.9.6.1.101.57.2.8.2" = a trapet generáló port vlan-ja
*/
Class API_Call implements API
{
    static function GetList($mezo, $ertek) : array
    {
        //? Szükség lenne ennek az implementálására?
        return array();
    }

    static function GetItem($objid)
    {
        //? Szükség lenne ennek az implementálására?
        return null;
    }

    static function Post($object, $tabla)
    {
        $con = mySQLConnect(false);
        $statuscode = 400;
        //$jelentes = purifyArray($object);

        $aktiveszkoz = mySQLConnect("SELECT eszkozok.id AS eszkid, ipcimek.ipcim AS ipcim
            FROM eszkozok
                INNER JOIN aktiveszkozok ON eszkozok.id = aktiveszkozok.eszkoz
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            WHERE ipcimek.ipcim = '$object->deviceip' AND beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL;");

        $eszkozid = mysqli_fetch_assoc($aktiveszkoz)['eszkid'];
        $misc = "";
        foreach($object->misc as $tovabbi)
        {
            $misc .= "OID: " . $tovabbi->OID . " Value: " . $tovabbi->TrapVal . "\n";
        }
        if(!$eszkozid)
        {
            $misc .= "IP CIM: " . $object->deviceip;
        }

        //$eszkozid = 10;
        //$event = $object->event;

        $stmt = $con->prepare('INSERT INTO snmp_traps (eszkozid, event, eventlocal, misc, systemuptime) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $eszkozid, $object->event, $object->eventlocal, $misc, $object->sysuptime);
        $stmt->execute();

        if(mysqli_errno($con) == 0)
        {
            $statuscode = 201;
        }

        return $statuscode;
    }

    static function Update($object, $tabla)
    {
        //! Nem implementáltam. Egyelőre nem hiszem, hogy lehet rá szükség bármiért. Ha lesz, majd foglalkozom vele akkor
        return 501;
    }

    static function Delete($objid, $tabla)
    {
        //! Nincs implementálva, de válaszként tiltottat ad, hogy még világosabb legyen a tiltás
        return 405;
    }
}