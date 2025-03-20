<?php
Class API_Call implements API
{
    static function GetList($mezo, $ertek) : array
    {
        $query = mySQLConnect("SELECT id, gepnev, meghajto FROM `hkrgepek` WHERE utolsofrissites < STR_TO_DATE((SELECT ertek FROM beallitasok WHERE nev = 'lasthkrupdate'), '%Y-%m-%d %H:%i:%s');");

        return mysqliToArray($query);
    }

    static function GetItem($objid)
    {
        $msqlres = mySQLConnect("SELECT * FROM `beallitasok` WHERE nev = 'lasthkrupdate';")->fetch_assoc();
        $beallitasok[$msqlres['nev']] = $msqlres['ertek'];
        
        return $beallitasok;
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
        $con = mySQLConnect(false);
        $timestamp = timeStampForSQL();
        $updated = false;
        $statuscode = 400;
        
        if(isset($object->sikeresfutas))
        {
            $stmt = $con->prepare("UPDATE beallitasok SET ertek=? WHERE nev='lasthkrupdate'");
            $stmt->bind_param('s', $timestamp);
            $updated = true;
        }
        elseif(isset($object->kesz) && isset($object->id))
        {
            $siker = $object->kesz ? 1 : 0;
            $objid = $object->id;
            if($object->kesz)
            {
                $stmt = $con->prepare("UPDATE hkrgepek SET utolsoeredmeny=?, utolsofrissites=? WHERE id=?");
                $stmt->bind_param('isi', $siker, $timestamp, $objid);
            }
            else
            {
                $stmt = $con->prepare("UPDATE hkrgepek SET utolsoeredmeny=? WHERE id=?");
                $stmt->bind_param('ii', $siker, $objid);
            }
            $updated = true;
        }
        if($updated)
            $stmt->execute();

        if(mysqli_errno($con) == 0 && $updated)
        {
            $statuscode = 200;
        }
        else
        {
            echo json_encode(
                array(
                    'valasz' => 'Nincs feldolgozható objektum'
                )
            );
            $statuscode = 204;
        }
        
        return $statuscode;
    }

    static function Delete($objid, $tabla)
    {
        // Nincs implementálva, de válaszként tiltottat ad, hogy még világosabb legyen a tiltás
        return 405;
    }
}