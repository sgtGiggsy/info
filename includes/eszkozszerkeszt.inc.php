<?php

// Ha nem létezik az írós változó, a felhasználó azonnali megállítása
if(!@$meghiv)
{
    getPermissionError();
}
else
{
    // Első lépésként megállapítjuk az eszköz típusát
    $eszkoztipus = $wheretip = $beuszok = null;
    if(isset($_GET['page']))
    {
        $eszkoztipus = $_GET['page'];
        switch($eszkoztipus)
        {
            case "aktiveszkoz": $wheretip = "WHERE modellek.tipus < 6"; break;
            case "sohoeszkoz": $wheretip = "WHERE modellek.tipus > 5 AND modellek.tipus < 11"; break;
            case "szamitogep": $wheretip = "WHERE modellek.tipus = 11"; break;
            case "nyomtato": $wheretip = "WHERE modellek.tipus = 12"; break;
            case "vegponti": $wheretip = "WHERE modellek.tipus > 10 AND modellek.tipus < 20"; break;
            case "mediakonverter": $wheretip = "WHERE modellek.tipus > 20 AND modellek.tipus < 26"; break;
            case "bovitomodul": $wheretip = "WHERE modellek.tipus > 25 AND modellek.tipus < 31"; break;
            case "szerver": $wheretip = "WHERE modellek.tipus > 30 AND modellek.tipus < 40"; break;
            case "telefonkozpont": $wheretip = "WHERE modellek.tipus = 40"; break;
        }
    }
    
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
    if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
    {
        $irhat = true;
        $dbir = true;
    }

    // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
    if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
    {
        $irhat = true;
    }
    
    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        echo "!irhat !dbir";
        getPermissionError();
    }
    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir)
    {
        include("./db/eszkozdb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        afterDBRedirect($con, $last_id);
    }
    else
    {
        $sorozatszamok = mySQLConnect("SELECT sorozatszam FROM eszkozok");
        $modellek = mySQLConnect("SELECT modellek.id AS id, gyartok.nev AS gyarto, modell, eszkoztipusok.nev AS tipus
        FROM modellek
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
        $wheretip
        ORDER BY tipus ASC, gyartok.nev ASC, modell ASC;");

        $raktarak = mySQLConnect("SELECT * FROM raktarak;");
        
        $modell = $sorozatszam = $tulajdonos = $varians = $mac = $portszam = 
        $uplinkportok = $szoftver = $nev = $leadva = $hibas = $raktar =
        $megjegyzes = $poe = $ssh = $web = $felhasznaloszam = $simtipus =
        $telefonszam = $pinkod = $pukkod = $magyarazat = null;

        $button = "Új eszköz";
        $oldalcim = "Új " . $currentpage['menupont'] . " létrehozása";
        $form = "eszkozszerkform";

        if($eszkoztipus == "simkartya")
        {
            $felhasznaloszamok = mySQLConnect("SELECT * FROM simfelhasznaloszamok");
            $simtipusok = mySQLConnect("SELECT * FROM simtipusok");
        }
        
        if(isset($id))
        {
            $oldalcim = "Eszköz szerkesztése";
            $eszkoz = mySQLConnect("SELECT * FROM eszkozok WHERE id = $id;");
            $eszkoz = mysqli_fetch_assoc($eszkoz);

            if($eszkoztipus == "aktiveszkoz" || $eszkoztipus == "sohoeszkoz")
            {
                $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
                if($eszkoztipus == "aktiveszkoz")
                {
                    $aktiveszkoz = mySQLConnect("SELECT * FROM aktiveszkozok WHERE eszkoz = $id;");
                    $aktiveszkoz = mysqli_fetch_assoc($aktiveszkoz);
                    $mac = @$aktiveszkoz['mac'];
                    $poe = @$aktiveszkoz['poe'];
                    $ssh = @$aktiveszkoz['ssh'];
                    $web = @$aktiveszkoz['web'];
                    $portszam = @$aktiveszkoz['portszam'];
                    $uplinkportok = @$aktiveszkoz['uplinkportok'];
                    $szoftver = @$aktiveszkoz['szoftver'];
                }
                else
                {
                    $aktiveszkoz = mySQLConnect("SELECT * FROM sohoeszkozok WHERE eszkoz = $id;");
                    $aktiveszkoz = mysqli_fetch_assoc($aktiveszkoz);
                    $mac = @$aktiveszkoz['mac'];
                    $portszam = @$aktiveszkoz['lanportok'];
                    $uplinkportok = @$aktiveszkoz['wanportok'];
                    $szoftver = @$aktiveszkoz['szoftver'];
                }
                $beuszok = array(array('cimszoveg' => 'Portok generálása', 'formnev' => 'porteszkozhozform'));
            }

            if($eszkoztipus == "telefonkozpont")
            {
                $telefonkozpont = mySQLConnect("SELECT * FROM telefonkozpontok WHERE eszkoz = $id;");
                $telefonkozpont = mysqli_fetch_assoc($telefonkozpont);

                $nev = $telefonkozpont['nev'];

                $beuszok = array(array('cimszoveg' => 'Portok generálása', 'formnev' => 'portkozponthozform'));
            }

            if($eszkoztipus == "simkartya")
            {
                $simkartya = mySQLConnect("SELECT * FROM simkartyak WHERE eszkoz = $id");
                $simkartya = mysqli_fetch_assoc($simkartya);

                $felhasznaloszam = @$simkartya['felhasznaloszam'];
                $simtipus = @$simkartya['tipus'];
                $telefonszam = @$simkartya['telefonszam'];
                $pinkod = @$simkartya['pinkod'];
                $pukkod = @$simkartya['pukkod'];
            }

            $modell = $eszkoz['modell'];
            $sorozatszam = $eszkoz['sorozatszam'];
            $tulajdonos = $eszkoz['tulajdonos'];
            $varians = $eszkoz['varians'];
            $leadva = $eszkoz['leadva'];
            $hibas = $eszkoz['hibas'];
            $raktar = $eszkoz['raktar'];
            $megjegyzes = $eszkoz['megjegyzes'];

            $button = "Szerkesztés";
            $oldalcim = $currentpage['menupont'] . "  szerkesztése";
        }

        include('./templates/edit.tpl.php');
    }
}





