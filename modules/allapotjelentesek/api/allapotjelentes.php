<?php

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
            $misc .= "OID: " . $tovabbi->OID . "; Value: " . $tovabbi->TrapVal . "\n";
        }
        if(!$eszkozid)
        {
            $misc .= "IP CIM: " . $object->deviceip;
        }

        //$eszkozid = 10;
        //$event = $object->event;

        $stmt = $con->prepare('INSERT INTO snmp_traps (eszkozid, event, eventlocal, rawmessage, systemuptime) VALUES (?, ?, ?, ?, ?)');
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