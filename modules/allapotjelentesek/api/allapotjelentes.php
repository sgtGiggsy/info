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
        $allapotjelzesek = new MySQLHandler("SELECT snmp_traps.id AS id,
            snmp_traps.eszkozid AS eszkozid,
            snmp_traps.timestamp AS timestamp,
            event, port, systemuptime,
            severity, message
        FROM snmp_traps
        WHERE eszkozid = ? AND DATE(snmp_traps.datum) >= DATE_SUB(NOW(), interval 14 DAY)
        ORDER BY snmp_traps.timestamp DESC", $objid);

        return $allapotjelzesek->Result();
    }

    static function Post($object, $tabla)
    {
        $statuscode = 400;
        $port = $body = $severity = $ismeretleneszkip = null;
        //$jelentes = purifyArray($object);

        $eszkoz = new MySQLHandler("SELECT eszkozok.id AS eszkid, snmpcommunity
            FROM eszkozok
                INNER JOIN aktiveszkozok ON eszkozok.id = aktiveszkozok.eszkoz
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            WHERE ipcimek.ipcim = ? AND beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL;", $object->deviceip);
        $eszkoz = $eszkoz->Bind($eszkozid, $community);
        /*
        $rawmessage = "";
        foreach($object->misc as $tovabbi)
        {
            $rawmessage .= "OID: " . $tovabbi->OID . "; Value: " . $tovabbi->TrapVal . "\n";
        }*/

        $tojson = json_encode($object->misc);

        //$rawtoprocess = processRaw($rawmessage);
        //$processed = processMessageBody($rawtoprocess, $object->deviceip, $community);
        $processed = processMessageBody_json($tojson, $object->deviceip, $community);
        $port = $processed['port'];
        $message = $processed['body'];
        $severity = $processed['severity'];

        // Kontraproduktívnak tűnhet a link-down üzenetek figyelmeztetés szintre emelése, de az alapfeltételezés,
        // hogy a végponti irányú portokon a link-down logolás és snmp trap ki van kapcsolva
        switch($object->event)
        {
            case "1.3.6.1.6.3.1.1.5.3" : 
            case "1.3.6.1.4.1.9.9.43.2.0.1" :
            case "1.3.6.1.4.1.9.0.0" :
            case "1.3.6.1.6.3.1.1.5.1" :
            case "1.3.6.1.6.3.1.1.5.2" :
            case "1.3.6.1.4.1.9.6.1.101.0.151" :
            case "1.3.6.1.4.1.9.9.91.2.0.1" : $severity = 2;
        }

        if(!$eszkozid)
        {
            $ismeretleneszkip = $object->deviceip;
        }

        //$eszkozid = 10;
        //$event = $object->event;
        $post = new MySQLHandler();
        $post->Query('INSERT INTO snmp_traps (eszkozid, event, port, systemuptime, severity, message, ismeretleneszkip) VALUES (?, ?, ?, ?, ?, ?, ?)',
            $eszkozid, $object->event, $port, $object->sysuptime, $severity, $message, $ismeretleneszkip);

        if($post->siker)
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