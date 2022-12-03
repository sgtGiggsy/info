<?php

$permission = true;
if(!isset($_GET['action']) || $_GET['action'] == "edit" || $_GET['action'] == "szamtarsitas" || $_GET['action'] == "transzporttarsitas")
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                 // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => null    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
    $epid = $_GET['id'];
    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam AS epuletszam, epuletek.nev AS nev, telephelyek.telephely AS telephely, telephelyek.id AS thelyid, epulettipusok.tipus AS tipus
        FROM epuletek
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
        WHERE epuletek.id = $epid $csoportwhere;");

    $epulet = mysqli_fetch_assoc($epuletek);

    if(mysqli_num_rows($epuletek) != 1)
    {
        $permission = false;
    }
    else
    {
        ?><div class="breadcumblist">
            <ol vocab="https://schema.org/" typeof="BreadcrumbList">
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/">
                    <span property="name">Kecskemét Informatika</span></a>
                    <meta property="position" content="1">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epuletek/<?=$epulet['thelyid']?>">
                    <span property="name"><?=$epulet['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem"><?php
                    if(isset($_GET['action']))
                    {
                        ?><a property="item" typeof="WebPage"
                                href="<?=$RootPath?>/epulet/<?=$epulet['id']?>"><?php
                    }
                    ?><span property="name"><?=$epulet['epuletszam']?>. <?=$epulet['tipus']?></span><?=(isset($_GET['action'])) ? "</a>" : "" ?>
                    <meta property="position" content="3">
                </li>
            </ol>
        </div><?php
    }
}

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
if(!@$csoportolvas || (isset($_GET['action']) && !$csoportir) || !$permission)
{
    getPermissionError();
}
else
{
    $alapform = "modules/epuletek/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit" || $_GET['action'] == "szamtarsitas" || $_GET['action'] == "transzporttarsitas")
        {
            $irhat = true;
        }
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt, de olvasási joggal továbbenged
    if(!$irhat && !$dbir && !$csoportolvas)
    {
        getPermissionError();
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0 || ((@$_GET['action'] == "szamtarsitas" || @$_GET['action'] == "transzporttarsitas") && count($_POST) > 0))
    {
        if($_GET['action'] == "szamtarsitas")
        {
            include("./modules/epuletek/db/telefonszamokdb.php");
        }
        elseif($_GET['action'] == "transzporttarsitas")
        {
            include("./modules/alap/db/portdb.php");
            redirectToKuldo();
        }
        else
        {
            include("./modules/epuletek/db/epuletdb.php");

            // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
            afterDBRedirect($con);
        }
    }

    // Ha a kért művelet nem jár adatbázisművelettel és nem a telefonszámok társítását elvégző felület, a szerkesztési felület meghívása
    elseif($irhat && !$dbir && $_GET['action'] != "szamtarsitas" && $_GET['action'] != "transzporttarsitas")
    {
        $szam = $telephely = $nev = $tipus = $emelet = $magyarazat = $naprakesz = $megjegyzes = null;
        $beuszok = array();

        $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
        $epulettipusok = mySQLConnect("SELECT * FROM epulettipusok;");
        $tulajdonosok = mySQLConnect("SELECT * FROM alakulatok;");
        $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
        
        $helyisegbutton = "Új helyiség";
        $button = "Új épület";
        $oldalcim = "Új épület hozzáadása";
        $form = $alapform . "epuletform";
        
        if(isset($id))
        {
            $fizikairetegek = mySQLConnect("SELECT * FROM fizikairetegek;");
            $epulet = mySQLConnect("SELECT * FROM epuletek WHERE id = $id;");
            $epulet = mysqli_fetch_assoc($epulet);

            $szam = $epulet['szam'];
            $telephely = $epulet['telephely'];
            $nev = $epulet['nev'];
            $tipus = $epulet['tipus'];
            $megjegyzes = $epulet['megjegyzes'];
            $naprakesz = $epulet['naprakesz'];

            $button = "Szerkesztés";
            $oldalcim = "Épület szerkesztése";

            // Azért van erre szükség, mert a helyisegszerkform-ot a Helyiség oldal is meghívhatja, ahol más értéket vesz fel az $id változó
            $epid = $id;

            $beuszok[] = array('cimszoveg' => 'Új helyiség létrehozása', 'formnev' => $alapform . 'helyisegszerkform');
            $beuszok[] = array('cimszoveg' => 'Helyiségek generálása', 'formnev' => $alapform . 'helyiseggenform');
            $beuszok[] = array('cimszoveg' => 'Végpontok generálása', 'formnev' => $alapform . 'vegpontgenform');
            $beuszok[] = array('cimszoveg' => 'Transzport portok generálása', 'formnev' => $alapform . 'transzportgenform');
        }

        include('./templates/edit.tpl.php');
    }

    // A folytatáshoz llenőrizni kell, hogy van-e kiválasztott épület. Ha nincs, hiba dobása
    elseif(!isset($id))
    {
        getPermissionError();
    }

    // A számok végpontokkal társítását elvégző felület meghívása
    elseif($irhat && $_GET['action'] == "szamtarsitas")
    {
        $where = $magyarazat = null;
        $maxhidra = 4;

        $eptkpquery = mySQLConnect("SELECT telefonkozpont FROM epuletek WHERE id = $id");
        $epuletkozpont = mysqli_fetch_assoc($eptkpquery)['telefonkozpont'];

        if($epuletkozpont < 0) // Debug okokból, élesben a < 0-t KIVENNI!!!
        {
            $where = "WHERE tkozpontportok.eszkoz = $epuletkozpont";
        }
        
        $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port
                FROM portok
                    INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                WHERE epulet = $id
                ORDER BY portok.port;");
        $telefonszamok = mySQLConnect("SELECT telefonszamok.id AS id, szam, cimke, telefonszamok.port AS port
                FROM telefonszamok
                    LEFT JOIN tkozpontportok ON telefonszamok.tkozpontport = tkozpontportok.port
                $where
                ORDER BY szam;");

        if(mysqli_num_rows($epuletportok) > 150)
        {
            $maxhidra = 2;
        }

        $epuletportok = mysqliNaturalSort($epuletportok, 'port');

        $button = "Portok számmal társítása";
        $oldalcim = "Az épület portjainak telefonszámmal társítása";
        $form = $alapform . "portszamtarsitasform";

        include('./templates/edit.tpl.php');
    }

    elseif($irhat && $_GET['action'] == "transzporttarsitas")
    {
        $magyarazat = null;
        
        $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, portok.csatlakozas AS csatlakozas, port1, port2
                FROM portok
                    INNER JOIN transzportportok ON transzportportok.port = portok.id
                    LEFT JOIN athurkolasok ON portok.id = athurkolasok.port1 OR portok.id = athurkolasok.port2
                WHERE epulet = $id
                ORDER BY portok.port;");
        
        $masepuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, portok.csatlakozas AS csatlakozas, epuletek.szam AS epuletszam
                FROM portok
                    INNER JOIN transzportportok ON transzportportok.port = portok.id
                    LEFT JOIN epuletek ON transzportportok.epulet = epuletek.id
                ORDER BY epuletek.szam, portok.port;");

        $epuletportok = mysqliNaturalSort($epuletportok, 'port');

        $button = "Transzport portok társítása";
        $oldalcim = "Az épület transzport portjainak társítása";
        $form = $alapform . "transzporttarsitasform";

        include('./templates/edit.tpl.php');
    }

    // Akkor futunk ki erre az ágra, ha van olvasási jog, és kiválasztott épület, de más nincs. Ez a sima megjelenítő felület
    else
    {
        
        $helyisegek = mySQLConnect("SELECT id, helyisegszam, helyisegnev, emelet
            FROM helyisegek
            WHERE epulet = $epid
            ORDER BY emelet ASC, helyisegszam ASC;");
        $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam, helyisegszam, helyisegnev, emelet
            FROM rackszekrenyek
                INNER JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
            WHERE epulet = $epid
            ORDER BY emelet, helyisegszam + 0;");

        $transzportportok = mySQLConnect("SELECT DISTINCT portok.id AS portid, portok.port AS port,
                eszkozok.id AS hasznalatban,
                tultransz.port AS tulport,
                epuletek.szam AS epuletszam,
                epuletek.nev AS epuletnev,
                beepitesek.nev AS beepitesnev,
                netdevlocal.port AS helyieszkport,
                remotebeep.nev AS szomszednev,
                netdevremote.port AS szomszedeszkport,
                transzportportok.fizikaireteg AS fizikaireteg,
                IF((port1 = portok.id), port2, port1) AS hurokid,
                (SELECT port FROM portok WHERE id = hurokid) AS huroktuloldal
            FROM portok
                INNER JOIN transzportportok ON transzportportok.port = portok.id
                LEFT JOIN transzportportok tuloldal ON portok.csatlakozas = tuloldal.port
                LEFT JOIN epuletek ON tuloldal.epulet = epuletek.id
                LEFT JOIN portok tultransz ON tuloldal.port = tultransz.id
                LEFT JOIN portok netdevlocal ON portok.id = netdevlocal.csatlakozas
                LEFT JOIN portok netdevremote ON portok.csatlakozas = netdevremote.csatlakozas
                LEFT JOIN switchportok ON switchportok.port = netdevlocal.id
                LEFT JOIN sohoportok ON sohoportok.port = netdevlocal.id
                LEFT JOIN mediakonverterportok ON mediakonverterportok.port = netdevlocal.id
                LEFT JOIN eszkozok ON switchportok.eszkoz = eszkozok.id OR mediakonverterportok.eszkoz = eszkozok.id OR sohoportok.eszkoz = eszkozok.id
                LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                LEFT JOIN switchportok remoteswport ON remoteswport.port = netdevremote.id
                LEFT JOIN sohoportok remotesohoport ON remotesohoport.port = netdevremote.id
                LEFT JOIN mediakonverterportok remotemkport ON remotemkport.port = netdevremote.id
                LEFT JOIN eszkozok remoteeszk ON remoteswport.eszkoz = remoteeszk.id OR remotemkport.eszkoz = remoteeszk.id OR remotesohoport.eszkoz = remoteeszk.id
                LEFT JOIN beepitesek remotebeep ON remoteeszk.id = remotebeep.eszkoz
                LEFT JOIN athurkolasok ON athurkolasok.port1 = portok.id OR athurkolasok.port2 = portok.id
            WHERE transzportportok.epulet = $id AND beepitesek.kiepitesideje IS NULL AND remotebeep.kiepitesideje IS NULL
            GROUP BY portok.id
            ORDER BY portok.port;");

        $portok = mySQLConnect("SELECT portok.id AS portid, portok.port AS port, IF((SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1), 1, NULL) AS hasznalatban,
                telefonszamok.szam AS szam, vlanok.nev AS vlan
            FROM portok
                LEFT JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN portok csatlakoz ON portok.id = csatlakoz.csatlakozas
                LEFT JOIN switchportok ON switchportok.port = csatlakoz.id
                LEFT JOIN sohoportok ON sohoportok.port = csatlakoz.id
                LEFT JOIN mediakonverterportok ON mediakonverterportok.port = csatlakoz.id
                LEFT JOIN beepitesek ON sohoportok.eszkoz = beepitesek.eszkoz OR mediakonverterportok.eszkoz = beepitesek.eszkoz
                LEFT JOIN epuletek ON vegpontiportok.epulet = epuletek.id
                LEFT JOIN telefonszamok ON telefonszamok.port = portok.id
                LEFT JOIN vlanok ON switchportok.vlan = vlanok.id OR beepitesek.vlan = vlanok.id
            WHERE epuletek.id = $epid
            ORDER BY portok.port ASC;");

        $eszkozok = mySQLConnect("SELECT
                eszkozok.id AS id,
                helyisegek.id AS helyisegid,
                sorozatszam,
                gyartok.nev AS gyarto,
                modellek.modell AS modell,
                varians,
                eszkoztipusok.nev AS tipus,
                modellek.tipus AS tipusid,
                beepitesideje,
                beepitesek.id AS beepid,
                pozicio,
                alakulatok.rovid AS tulajdonos,
                rackszekrenyek.nev AS rack,
                (SELECT count(id) FROM rackszekrenyek WHERE helyiseg = helyisegid) AS rackszam,
                beepitesek.nev AS beepitesinev,
                ipcimek.ipcim AS ipcim
            FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            WHERE helyisegek.epulet = $epid AND kiepitesideje IS NULL AND (modellek.tipus < 10 OR (modellek.tipus > 19 AND modellek.tipus < 31))
            ORDER BY rack, pozicio;");

        if($csoportir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Épület szerkesztése</button>&nbsp;
                <button type='button' onclick="location.href='./<?=$id?>?action=szamtarsitas'">Épület portjainak telefonszámmal társítása</button>&nbsp;
                <button type='button' onclick="location.href='./<?=$id?>?action=transzporttarsitas'">Épület transzport portjainak társítása</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick='rejtMutat("elozmenyek")'>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }

        ?><div class="oldalcim"><?=$epulet['telephely']?> - <?=$epulet['epuletszam']?>. <?=$epulet['tipus']?> (<?=$epulet['nev']?>)</div><?php
        $ujoldalcim = $ablakcim . " - ". $epulet['telephely'] . " - " . $epulet['epuletszam'] . ". " . $epulet['tipus'] . " (" . $epulet['nev'] . ")";

        if(mysqli_num_rows($eszkozok) > 0)
        {
            $rackszam = mysqli_fetch_assoc($eszkozok)['rackszam'];
            ?><div class="oldalcim">Hálózati eszközök az épületben</div>
            <div>
                <table id="eszkozok">
                    <thead>
                        <tr>
                            <th class="tsorth" onclick="sortTable(0, 's', 'eszkozok')">IP cím</th>
                            <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">Eszköznév</th>
                            <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Modell</th>
                            <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Eszköztípus</th>
                            <th class="tsorth" onclick="sortTable(4, 's', 'eszkozok')">Tulajdonos</th>
                            <th class="tsorth" onclick="sortTable(5, 's', 'eszkozok')">Beépítve</th><?php
                            if($rackszam > 0)
                            {
                                ?><th class="tsorth" onclick="sortTable(6, 's', 'eszkozok')">Rackszekrény</th>
                                <th class="tsorth" onclick="sortTable(7, 'i', 'eszkozok')">Pozíció</th><?php
                            }
                            if($csoportir)
                            {
                                ?><th></th>
                                <th></th>
                                <th></th><?php
                            }
                        ?></tr>
                    </thead>
                    <tbody><?php
                        foreach($eszkozok as $eszkoz)
                        {
                            $beepid = $eszkoz['beepid'];
                            $eszkid = $eszkoz['id'];
                            $eszktip = eszkozTipusValaszto($eszkoz['tipusid'])
                            
                            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/<?=$eszktip?>/<?=$eszkoz['id']?>'>
                                <td><?=$eszkoz['ipcim']?></td>
                                <td nowrap><?=$eszkoz['beepitesinev']?></td>
                                <td nowrap><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                                <td><?=$eszkoz['tipus']?></td>
                                <td><?=$eszkoz['tulajdonos']?></td>
                                <td nowrap><?=timeStampToDate($eszkoz['beepitesideje'])?></td><?php
                                if($rackszam > 0)
                                {
                                    ?><td><?=$eszkoz['rack']?></td>
                                    <td><?=$eszkoz['pozicio']?></td><?php
                                }
                                if($csoportir)
                                {
                                    szerkSor($eszkoz['beepid'], $eszkoz['id'], $eszktip);
                                    ?><?php
                                }
                            ?></tr><?php
                        }
                    ?></tbody>
                </table>
            </div><?php
        }

        if(mysqli_num_rows($rackek) > 0)
        {
            ?><div class="oldalcim">Rackszekrények az épületben</div>
            <div>
                <table id="rackek">
                    <thead>
                        <tr>
                            <th class="tsorth" onclick="sortTable(0, 's', 'rackek')">Emelet</th>
                            <th class="tsorth" onclick="sortTable(1, 's', 'rackek')">Helyiség</th>
                            <th class="tsorth" onclick="sortTable(2, 's', 'rackek')">Azonosító</th>
                            <th class="tsorth" onclick="sortTable(3, 's', 'rackek')">Gyártó</th>
                            <th class="tsorth" onclick="sortTable(4, 'i', 'rackek')">Unitszám</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        foreach($rackek as $rack)
                        {
                            $rackid = $rack['id']
                            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/rack/<?=$rack['id']?>'>
                                <td><?=($rack['emelet'] == 0) ? "Földszint" : $rack['emelet'] . ". emelet" ?></td>
                                <td nowrap><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</td>
                                <td><?=$rack['nev']?></td>
                                <td><?=$rack['gyarto']?></td>
                                <td><?=$rack['unitszam']?></td>
                                <td><?=($csoportir) ? "<a href='$RootPath/rack/$rackid?action=edit'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td>
                            </tr><?php
                        }
                    ?></tbody>
                </table>
            </div><?php
        }

        ?><div class="oldalcim">Helyiségek</div>
        <div class="szintlist"><?php
            if(mysqli_num_rows($helyisegek) > 0)
            {
                $zar = false;
                foreach($helyisegek as $helyiseg)
                {
                    if(@$emelet != $helyiseg['emelet'])
                    {
                        if($zar)
                        {
                            ?></tbody>
                            </table>
                            </div><?php
                        }

                        $emelet = $helyiseg['emelet'];
                        ?><div>
                            <h1><?=($helyiseg['emelet'] == 0) ? "Földszint" : $helyiseg['emelet'] . ". emelet" ?></h1>
                            <table id="<?=$emelet?>">
                            <thead>
                                <tr>
                                    <th style="width: 20%" class="tsorth" onclick="sortTable(0, 'i', '<?=$emelet?>')">Szám</th>
                                    <th style="width: 70%" class="tsorth" onclick="sortTable(1, 's', '<?=$emelet?>')">Helyiségnév</th><?php
                                    if($mindir)
                                    {
                                        ?><th style="width: 10%"></th><?php
                                    }
                                ?></tr>
                            </thead>
                            <tbody><?php
                            $zar = true;
                    }

                    ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>'>
                        <td><?=$helyiseg['helyisegszam']?></td>
                        <td><?=$helyiseg['helyisegnev']?></td><?php
                        if($mindir)
                        {
                            ?><td><a href='<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Helyiség szerkesztése' title='Helyiség szerkesztése'/></a></td><?php
                        }
                    ?></tr><?php
                }
                ?></tbody>
                </table>
                </div><?php
            }
        ?></div>
        <div class="oldalcim">Transzport portok az épületben</div><?php
            transzportPortLista($id);
        ?><div class="oldalcim">Végpontok az épületben</div><?php
            vegpontLista($portok);
        
    }
}