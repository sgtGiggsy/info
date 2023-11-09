<?php
Class API_Call implements API
{
    static function GetList($mezo, $ertek) : array
    {
        $telephelyszures = null;
        if($mezo && $ertek)
        {
            $telephelyszures = "AND telephely = $ertek";
        }
        
        $query = mySQLConnect("SELECT eszkozok.id, ipcimek.ipcim, beepitesek.nev, online
                FROM eszkozok
                    INNER JOIN modellek ON eszkozok.modell = modellek.id
                    LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                    LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid
                    LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                    LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                    LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                WHERE (modellek.tipus = 1 OR modellek.tipus = 2)
                    AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL)
                    AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid)
                        OR aktiveszkoz_allapot.id IS NULL)
                    AND (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)
                    AND ipcimek.vlan = 1
                    $telephelyszures
                ORDER BY ipcimek.ipcim;");
        
        $eredmeny = mysqliToArray($query);

        return $eredmeny;
    }

    static function GetOne($objid)
    {
        $aktiveszkoz = mySQLConnect("SELECT
            eszkozok.id AS eszkid,
            beepitesek.id AS beepid,
            sorozatszam,
            mac,
            poe,
            ssh,
            web,
            portszam,
            uplinkportok,
            szoftver,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            epuletek.id AS epuletid,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            epulettipusok.tipus AS epulettipus,
            telephelyek.telephely AS telephely,
            telephelyek.id AS thelyid,
            helyisegek.id AS helyisegid,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            eszkozok.tulajdonos AS tulajid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.id AS rackid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            raktarak.id AS raktarid,
            raktarak.nev AS raktar,
            hibas,
            eszkozok.megjegyzes AS megjegyzes,
            beepitesek.megjegyzes AS beepmegjegyz,
            (SELECT MIN(id) FROM modositasok WHERE eszkoz = eszkid) AS elsomodositas,
            (SELECT felhasznalo FROM modositasok WHERE id = elsomodositas) AS letrehozoid,
            (SELECT nev FROM felhasznalok WHERE id = letrehozoid) AS letrehozo,
            (SELECT MAX(id) FROM modositasok WHERE eszkoz = eszkid) AS utolsomodositas,
            (SELECT felhasznalo FROM modositasok WHERE id = utolsomodositas) AS utolsomodositoid,
            (SELECT nev FROM felhasznalok WHERE id = utolsomodositoid) AS utolsomodosito,
            (SELECT timestamp FROM modositasok WHERE id = utolsomodositas) AS utolsomodositasideje,
            (SELECT timestamp FROM modositasok WHERE id = elsomodositas) AS letrehozasideje
        FROM eszkozok
                INNER JOIN aktiveszkozok ON eszkozok.id = aktiveszkozok.eszkoz
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
                LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
        WHERE eszkozok.id = $objid AND modellek.tipus < 11
        ORDER BY beepitesek.id DESC;");
        
        return mysqliToArray($aktiveszkoz);
    }

    static function Post($object, $tabla)
    {
        $con = mySQLConnect(false);
        $timestamp = timeStampForSQL();
        $statuscode = 400;
        if($tabla == "allapot")
        {
            $eszkozid = $online = null;
            if(isset($object->eszkid) && isset($object->online))
            {
                $eszkozid = $object->eszkid;
                $online = $object->online;

                $stmt = $con->prepare('INSERT INTO aktiveszkoz_allapot (eszkozid, online, timestamp) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $eszkozid, $online, $timestamp);
                $stmt->execute();

                if(mysqli_errno($con) == 0)
                {
                    $statuscode = 201;
                    if($object->utolso)
                    {
                        mySQLConnect("UPDATE beallitasok SET ertek = current_timestamp() WHERE nev = 'last_switch_check';");
                    }
                    if($object->ertesit)
                    {
                        $cim = $object->cim;
                        $szoveg = $object->szoveg;
                        $tipus = 1;
                        $url = "aktiveszkoz/" . $object->eszkid;

                        $stmt = $con->prepare('INSERT INTO ertesitesek (cim, szoveg, url, tipus) VALUES (?, ?, ?, ?)');
                        $stmt->bind_param('ssss', $cim, $szoveg, $url, $tipus);
                        $stmt->execute();
                    }
                }
            }
        }

        return $statuscode;
    }

    static function Update($object, $tabla)
    {
        // Nem implementáltam, egyelőre nem hiszem, hogy lehet rá szükség bármiért. Ha lesz, majd foglalkozom vele akkor
        return 501;
    }

    static function Delete($objid, $tabla)
    {
        // Nincs implementálva, de válaszként tiltottat ad, hogy még világosabb legyen a tiltás
        return 405;
    }
}