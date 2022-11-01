<?php
$form = "modules/eszkozok/forms/";
// Ha nem létezik az írós változó, a felhasználó azonnali megállítása
if(!@$meghiv)
{
    getPermissionError();
}

// A beépítéssel kapcsolatos műveletek lebonyolítása.
// Régen külön oldal volt, most logikusabb módon az eszköz szerkesztésének részévé vált
elseif(isset($_GET['beepites']) && isset($_GET['action']))
{
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
    if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
    {
        $irhat = true;
        $dbir = true;
        if($_GET['action'] == "new")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "uj";
        }
        elseif($_GET['action'] == "update")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "szerkesztes";
        }
    }

    // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
    if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
    {
        $irhat = true;
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        getPermissionError();
    }
    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/eszkozok/db/beepitesdb.php");

        // A kiinduló oldalra visszairányító függvény meghívása.
        redirectToKuldo($dbop);
    }
    else
    {

        $beepid = $beepnev = $beepeszk = $beepip = $beeprack = $beephely = $beeppoz = $beepido = $beepkiep =
        $admin = $pass = $megjegyzes = $vlan = $magyarazat = $beuszok = $tipus = null;

        $switchport = 0;
        $button = "Új beépítés";
        $oldalcim = "Eszköz beépítése";
        $form .= "beepitesform";

        if(isset($_GET['page']))
        {
            $tipus = $_GET['page'];
        }

        if(isset($_GET['id']))
        {
            $beepeszk = $_GET['id'];
        }

        $where = null;
        if(!$_GET['beepites'])
        {
            $where = "WHERE beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL";
        }

        $ipcimek = mySQLConnect("SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim
            FROM ipcimek
                LEFT JOIN beepitesek ON ipcimek.id = beepitesek.ipcim
            $where
            ORDER BY ipcimek.vlan, ipcimek.ipcim;");

        if($_GET['beepites'] && $_GET['action'] == "edit")
        {
            $beepid = $_GET['beepites'];
            $beepitve = mySQLConnect("SELECT beepitesek.id AS beepid,
                    beepitesek.nev AS nev,
                    beepitesek.eszkoz AS eszkid,
                    (SELECT CONCAT(gyartok.nev, ' ', modellek.modell, COALESCE(eszkozok.varians, ''), ' (', eszkozok.sorozatszam, ')')
                        FROM eszkozok
                            LEFT JOIN modellek ON eszkozok.modell = modellek.id
                            LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
                        WHERE eszkozok.id = eszkid) AS eszkoz,
                    beepitesek.ipcim AS ipcimid,
                    ipcimek.ipcim AS ipcim,
                    rackszekrenyek.id AS rackid,
                    rackszekrenyek.nev AS rack,
                    beepitesek.helyiseg AS helyisegid,
                    (SELECT CONCAT(COALESCE(epuletek.szam, ''), ' (', COALESCE(epuletek.nev, ''), ') épület', COALESCE(helyisegek.helyisegszam, ''), ' (', COALESCE(helyisegek.helyisegnev, ''), ')')
                        FROM helyisegek
                            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                        WHERE helyisegek.id = helyisegid) AS helyiseg,
                    pozicio,
                    beepitesideje,
                    kiepitesideje,
                    beepitesek.megjegyzes AS megjegyzes,
                    admin,
                    pass,
                    vlanok.id AS vlanid,
                    vlanok.nev AS vlan,
                    switchport AS switchportid,
                    (SELECT CONCAT(COALESCE(beepitesek.nev, ''), ' (', COALESCE(ipcimek.ipcim, ''), ') ', portok.port) 
                        FROM beepitesek
                            INNER JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
                            INNER JOIN switchportok ON eszkozok.id = switchportok.eszkoz
                            INNER JOIN portok ON switchportok.port = portok.id
                            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                        WHERE portok.id = switchportid
                        ORDER BY beepitesek.id DESC
                        LIMIT 1) AS switchport,
                    muvelet,
                    (SELECT MAX(id) FROM modositasok WHERE beepites = beepid) AS utolsomodositas,
                    (SELECT felhasznalo FROM modositasok WHERE id = utolsomodositas) AS utolsomodositoid,
                    (SELECT nev FROM felhasznalok WHERE id = utolsomodositoid) AS utolsomodosito,
                    (SELECT timestamp FROM modositasok WHERE id = utolsomodositas) AS utolsomodositasideje
                FROM beepitesek
                    LEFT JOIN modositasok ON beepitesek.modid = modositasok.id
                    LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                    LEFT JOIN vlanok ON beepitesek.vlan = vlanok.id
                WHERE beepitesek.id = $beepid;");

            $beepitve = mysqli_fetch_assoc($beepitve);

            $elozmenyek = mySQLConnect("SELECT beepitesek_history.id AS beepid,
                    beepitesek_history.nev AS nev,
                    beepitesek_history.eszkoz AS eszkid,
                    (SELECT CONCAT(gyartok.nev, ' ', modellek.modell, COALESCE(eszkozok.varians, ''), ' (', eszkozok.sorozatszam, ')')
                        FROM eszkozok
                            LEFT JOIN modellek ON eszkozok.modell = modellek.id
                            LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
                        WHERE eszkozok.id = eszkid) AS eszkoz,
                    beepitesek_history.ipcim AS ipcimid,
                    ipcimek.ipcim AS ipcim,
                    rackszekrenyek.id AS rackid,
                    rackszekrenyek.nev AS rack,
                    beepitesek_history.helyiseg AS helyisegid,
                    (SELECT CONCAT(COALESCE(epuletek.szam, ''), ' (', COALESCE(epuletek.nev, ''), ') épület', COALESCE(helyisegek.helyisegszam, ''), ' (', COALESCE(helyisegek.helyisegnev, ''), ')')
                        FROM helyisegek
                            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                        WHERE helyisegek.id = helyisegid) AS helyiseg,
                    pozicio,
                    beepitesideje,
                    kiepitesideje,
                    beepitesek_history.megjegyzes AS megjegyzes,
                    admin,
                    pass,
                    vlanok.id AS vlanid,
                    vlanok.nev AS vlan,
                    switchport AS switchportid,
                    (SELECT CONCAT(COALESCE(beepitesek.nev, ''), ' (', COALESCE(ipcimek.ipcim, ''), ') ', portok.port) 
                        FROM beepitesek
                            INNER JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
                            INNER JOIN switchportok ON eszkozok.id = switchportok.eszkoz
                            INNER JOIN portok ON switchportok.port = portok.id
                            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                        WHERE portok.id = switchportid
                        ORDER BY beepitesek.id DESC
                        LIMIT 1) AS switchport,
                    muvelet,
                    modositasok.felhasznalo AS modositoid,
                    (SELECT nev FROM felhasznalok WHERE id = modositoid) AS modosito,
                    modositasok.timestamp AS modositasideje
                FROM beepitesek_history
                    LEFT JOIN modositasok ON beepitesek_history.modid = modositasok.id
                    LEFT JOIN ipcimek ON beepitesek_history.ipcim = ipcimek.id
                    LEFT JOIN rackszekrenyek ON beepitesek_history.rack = rackszekrenyek.id
                    LEFT JOIN vlanok ON beepitesek_history.vlan = vlanok.id
                WHERE beepitesid = $beepid;");

            $beepeszk = $beepitve['eszkid'];
            $beepnev = $beepitve['nev'];
            $beepip = $beepitve['ipcimid'];
            $beeprack = $beepitve['rackid'];
            $beephely = $beepitve['helyisegid'];
            $beeppoz = $beepitve['pozicio'];
            $beepido = $beepitve['beepitesideje'];
            $beepkiep = $beepitve['kiepitesideje'];
            $admin = $beepitve['admin'];
            $pass = $beepitve['pass'];
            $megjegyzes = $beepitve['megjegyzes'];
            $vlan = $beepitve['vlanid'];
            $switchport = $beepitve['switchportid'];

            $button = "Szerkesztés";
            $oldalcim = "Beépítés szerkesztése";

        // Szerkesztési előzmények
            if(mysqli_num_rows($elozmenyek) > 0)
            {
                ?><button type='button' onclick="rejtMutat('elozmenyek')">Szerkesztési előzmények</button><?php
            }

            if(mysqli_num_rows($elozmenyek) > 0)
            {
                ?><div id="elozmenyek" style="display: none">
                    <div class="oldalcim">Szerkesztési előzmények</div>
                    <table id="verzioelozmenyek">
                        <thead>
                            <th>Létrehozás /<br>Módosítás ideje</th>
                            <th>Létrehozó /<br>Módosító</th>
                            <th>Eszköz</th>
                            <th>Beépítési név</th>
                            <th>IP cím</th>
                            <th>VLAN</th>
                            <th>Rack</th>
                            <th>Helyiség</th>
                            <th>Pozíció</th>
                            <th>Switchport</th>
                            <th>Beépítés ideje</th>
                            <th>Kiépítés ideje</th>
                            <th>Admin</th>
                            <th>Jelszó</th>
                            <th>Megjegyzés</th>
                        </thead>
                        <tbody>
                            <?php
                            $szamoz = 1;
                            $elozoverzio = null;
                            foreach($elozmenyek as $x)
                            {
                                ?><tr style="font-weight: normal;" class='valtottsor-<?=($szamoz % 2 == 0) ? "2" : "1" ?>'><?php
                                    if($szamoz == 1 && $x['muvelet'] == 1)
                                    {
                                        ?><td><?=$x['modositasideje']?></td>
                                        <td><?=$x['modosito']?></td><?php
                                    }
                                    elseif ($szamoz != 1)
                                    {
                                        ?><td><?=$x['modositasideje']?></td>
                                        <td><?=$x['modosito']?></td><?php
                                    }
                                    else
                                    {
                                        ?><td></td>
                                        <td></td><?php
                                    }
                                    ?><td <?=($elozoverzio && $elozoverzio['eszkid'] != $x['eszkid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['eszkoz']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['nev'] != $x['nev']) ? "style='font-weight: bold;'" : "" ?>><?=$x['nev']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['ipcimid'] != $x['ipcimid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['ipcim']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['vlanid'] != $x['vlanid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['vlan']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['rackid'] != $x['rackid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['rack']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['helyisegid'] != $x['helyisegid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['helyiseg']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['pozicio'] != $x['pozicio']) ? "style='font-weight: bold;'" : "" ?>><?=$x['pozicio']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['switchportid'] != $x['switchportid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['switchport']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['beepitesideje'] != $x['beepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$x['beepitesideje']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['kiepitesideje'] != $x['kiepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$x['kiepitesideje']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['admin'] != $x['admin']) ? "style='font-weight: bold;'" : "" ?>><?=$x['admin']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['pass'] != $x['pass']) ? "style='font-weight: bold;'" : "" ?>><?=$x['pass']?></td>
                                    <td <?=($elozoverzio && $elozoverzio['megjegyzes'] != $x['megjegyzes']) ? "style='font-weight: bold;'" : "" ?>><?=$x['megjegyzes']?></td>
                                </tr><?php
                                $szamoz++;
                                $elozoverzio = $x;
                            }
                            ?><tr style="font-weight: normal;" class='valtottsor-<?=($szamoz % 2 == 0) ? "2" : "1" ?>'>
                                <td><?=$beepitve['utolsomodositasideje']?></td>
                                <td><?=$beepitve['utolsomodosito']?></td>
                                <td <?=($elozoverzio['eszkid'] != $beepitve['eszkid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['eszkoz']?></td>
                                <td <?=($elozoverzio['nev'] != $beepitve['nev']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['nev']?></td>
                                <td <?=($elozoverzio['ipcimid'] != $beepitve['ipcimid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['ipcim']?></td>
                                <td <?=($elozoverzio['vlanid'] != $beepitve['vlanid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['vlan']?></td>
                                <td <?=($elozoverzio['rackid'] != $beepitve['rackid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['rack']?></td>
                                <td <?=($elozoverzio['helyisegid'] != $beepitve['helyisegid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['helyiseg']?></td>
                                <td <?=($elozoverzio['pozicio'] != $beepitve['pozicio']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['pozicio']?></td>
                                <td <?=($elozoverzio['switchportid'] != $beepitve['switchportid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['switchport']?></td>
                                <td <?=($elozoverzio['beepitesideje'] != $beepitve['beepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['beepitesideje']?></td>
                                <td <?=($elozoverzio['kiepitesideje'] != $beepitve['kiepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['kiepitesideje']?></td>
                                <td <?=($elozoverzio['admin'] != $beepitve['admin']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['admin']?></td>
                                <td <?=($elozoverzio['pass'] != $beepitve['pass']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['pass']?></td>
                                <td <?=($elozoverzio['megjegyzes'] != $beepitve['megjegyzes']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['megjegyzes']?></td>
                            </tr>
                        </tbody>
                    </table>
                </div><?php
            }

        // Form
        }

        if($switchport !== null)
        {
            $switchportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
            FROM portok
                INNER JOIN switchportok ON portok.id = switchportok.port
                INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
                INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            WHERE switchportok.tipus = 1 AND (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL) AND ((SELECT count(id) FROM beepitesek WHERE switchport = portok.id) = 0 OR portok.id = $switchport)
            ORDER BY telephely, epuletek.szam + 1, helyisegszam, pozicio, aktiveszkoz, id;");
        }

        if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
        {
            include('./templates/edit.tpl.php');
        }
    }








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
        getPermissionError();
    }
    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir)
    {
        include("./modules/eszkozok/db/eszkozdb.php");

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
        $form .= "eszkozszerkform";

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
                $beuszok = array(array('cimszoveg' => 'Portok generálása', 'formnev' => 'modules/eszkozok/forms/porteszkozhozform'));
            }

            if($eszkoztipus == "telefonkozpont")
            {
                $telefonkozpont = mySQLConnect("SELECT * FROM telefonkozpontok WHERE eszkoz = $id;");
                $telefonkozpont = mysqli_fetch_assoc($telefonkozpont);

                $nev = $telefonkozpont['nev'];

                $beuszok = array(array('cimszoveg' => 'Portok generálása', 'formnev' => 'modules/eszkozok/forms/portkozponthozform'));
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

        include('././templates/edit.tpl.php');
    }
}





