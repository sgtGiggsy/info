<?php
include('../modules/allapotjelentesek/includes/functions.php');

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
        $port = $body = $severity = null;
        //$jelentes = purifyArray($object);

        $aktiveszkoz = mySQLConnect("SELECT eszkozok.id AS eszkid, ipcimek.ipcim AS ipcim, snmpcommunity
            FROM eszkozok
                INNER JOIN aktiveszkozok ON eszkozok.id = aktiveszkozok.eszkoz
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            WHERE ipcimek.ipcim = '$object->deviceip' AND beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL;");

        $eszkoz = mysqli_fetch_assoc($aktiveszkoz);
        $eszkozid = $eszkoz['eszkid'];
        $community = $eszkoz['snmpcommunity'];
        $rawmessage = "";
        foreach($object->misc as $tovabbi)
        {
            $rawmessage .= "OID: " . $tovabbi->OID . "; Value: " . $tovabbi->TrapVal . "\n";
        }

        $rawtoprocess = processRaw($rawmessage);
        $processed = processMessageBody($rawtoprocess, $object->deviceip, $community);
        if($processed) // Ha van nem feldolgozható része a nyers üzenetnek, null elemet ad vissza a függvény
        {
            $port = $processed['port'];
            $body = $processed['body'];
            $severity = $processed['severity'];
        }

        // Kontraproduktívnak tűnhet a link-down üzenetek figyelmeztetés szintre emelése, de az alapfeltételezés,
        // hogy a végponti irányú portokon a link-down logolás és snmp trap ki van kapcsolva
        if($object->event == "1.3.6.1.6.3.1.1.5.3" || $object->event == "1.3.6.1.4.1.9.9.43.2.0.1"  || $object->event == "1.3.6.1.4.1.9.0.0" || $object->event == "1.3.6.1.6.3.1.1.5.1" || $object->event == "1.3.6.1.6.3.1.1.5.2")
        {
            $severity = 2;
        }

        // A port STP állapot változás túl magas prioritást kap az üzenetek között alapértelmezetten
        if($object->event == "1.3.6.1.4.1.9.6.1.101.0.151")
        {
            $severity = 1;
        }

        if(!$eszkozid)
        {
            $rawmessage .= "IP CIM: " . $object->deviceip;
        }

        //$eszkozid = 10;
        //$event = $object->event;
        $stmt = $con->prepare('INSERT INTO snmp_traps (eszkozid, event, port, rawmessage, processedmessage, systemuptime, severity) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssss', $eszkozid, $object->event, $port, $rawmessage, $body, $object->sysuptime, $severity);
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