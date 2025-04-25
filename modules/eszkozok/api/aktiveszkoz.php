<?php
Class API_Call implements API
{
    static function GetList($mezo, $ertek) : array
    {
        $telephelyszures = null;
        $ertekek = array();
        if($mezo && $ertek)
        {
            $telephelyszures = "AND (epuletek.telephely = ? OR beepulet.telephely = ?)";
            $ertekek = array($ertek, $ertek);
            ///$telephelyszures = "AND telephely = $ertek";
        }
        
        // Szó szerint százszoros sebességkülönbség van ezesetben az OR-ral joinolás, illetve az unióképzés között
        /*
        $query = mySQLConnect("SELECT eszkozok.id, ipcimek.ipcim, beepitesek.nev, online
                FROM eszkozok
                    INNER JOIN modellek ON eszkozok.modell = modellek.id
                    LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                    LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid
                    LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                    LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
                    LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                WHERE (modellek.tipus = 1 OR modellek.tipus = 2)
                    AND aktivbeepites = 1
                    AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL)
                    AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid)
                        OR aktiveszkoz_allapot.id IS NULL)
                    AND ipcimek.vlan = 1
                    $telephelyszures
        UNION SELECT eszkozok.id, ipcimek.ipcim, beepitesek.nev, online
                FROM eszkozok
                    INNER JOIN modellek ON eszkozok.modell = modellek.id
                    LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                    LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid
                    LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                    LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id
                    LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                WHERE (modellek.tipus = 1 OR modellek.tipus = 2)
                    AND aktivbeepites = 1
                    AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL)
                    AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid)
                        OR aktiveszkoz_allapot.id IS NULL)
                    AND ipcimek.vlan = 1
                    $telephelyszures
                ORDER BY ipcim;");*/

        $aktiveszkozdb = new MySQLHandler("SELECT eszkozok.id, ipcimek.ipcim, beepitesek.nev, online
                FROM eszkozok
                    INNER JOIN modellek ON eszkozok.modell = modellek.id
                    LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                    LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid
                    LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                    LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
                    LEFT JOIN helyisegek beephely ON beepitesek.helyiseg = beephely.id
                    LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                    LEFT JOIN epuletek beepulet ON beephely.epulet = beepulet.id
                WHERE (modellek.tipus = 1 OR modellek.tipus = 2)
                    AND aktivbeepites = 1
                    AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid)
                        OR aktiveszkoz_allapot.id IS NULL)
                    AND ipcimek.vlan = 1
                    $telephelyszures;", ...$ertekek);
        
        //$eredmeny = mysqliToArray($query);

        return $aktiveszkozdb->AsArray();
    }

    static function GetItem($objid)
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
            szervezetek.rovid AS tulajdonos,
            rackszekrenyek.id AS rackid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            raktarak.id AS raktarid,
            raktarak.nev AS raktar,
            hibas,
            eszkozok.megjegyzes AS megjegyzes,
            beepitesek.megjegyzes AS beepmegjegyz
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
                LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
        WHERE eszkozok.id = $objid AND modellek.tipus < 11
        ORDER BY beepitesek.id DESC;");
        
        return mysqliToArray($aktiveszkoz);
    }

    static function Post($object, $filter)
    {
        $con = mySQLConnect(false);
        $timestamp = timeStampForSQL();
        $statuscode = 400;
        if($filter == "allapotlista")
        {
            $statuscode = 201;
            $updatefreq = mySQLConnect("SELECT ertek FROM beallitasok WHERE nev = 'switch_update_frequency';")->fetch_assoc()['ertek'];
            $utolsoupdate = mysqli_num_rows(mySQLConnect("SELECT ertek FROM beallitasok WHERE nev = 'last_switch_update' AND ertek < date_sub(now(), INTERVAL $updatefreq HOUR);"));
            if($utolsoupdate > 0)
            {
                mySQLConnect("UPDATE beallitasok SET ertek = current_timestamp() WHERE nev = 'last_switch_update';");
            }

            $stmt = $con->prepare('INSERT INTO aktiveszkoz_allapot (eszkozid, online, timestamp) VALUES (?, ?, ?)');
            $felhasznalok = Ertesites::GetFelhasznalok(1);

            foreach($object as $eszkoz)
            {
                if($utolsoupdate > 0 || $eszkoz->cim)
                {
                    $stmt->bind_param('sss', $eszkoz->eszkid, $eszkoz->online, $timestamp);
                    $stmt->execute();
                    
                    if($eszkoz->cim)
                    {
                        $ertesites = new Ertesites($eszkoz->cim, $eszkoz->szoveg, "aktiveszkoz/" . $eszkoz->eszkid);
                        $ertesites->tipus = 1;
                        $ertesites->SetFelhasznalok($felhasznalok);
                        $ertesites->Ment();
                    }
                }
            }
            
            mySQLConnect("UPDATE beallitasok SET ertek = current_timestamp() WHERE nev = 'last_switch_check';");
        }

        return $statuscode;
    }

    static function Update($object, $tabla)
    {
        // Nem implementáltam. Egyelőre nem hiszem, hogy lehet rá szükség bármiért. Ha lesz, majd foglalkozom vele akkor
        return 501;
    }

    static function Delete($objid, $tabla)
    {
        // Nincs implementálva, de válaszként tiltottat ad, hogy még világosabb legyen a tiltás
        return 405;
    }
}