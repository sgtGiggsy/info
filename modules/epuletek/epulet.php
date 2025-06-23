<?php

$permission = true;
if(!isset($_GET['action']) || $_GET['action'] == "edit" || $_GET['action'] == "szamtarsitas" || $_GET['action'] == "transzporttarsitas" || $_GET['action'] == "vegponthurkolas")
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                 // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => null    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
    if(isset($_GET['id']))
    {
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
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit" || $_GET['action'] == "szamtarsitas" || $_GET['action'] == "transzporttarsitas" || $_GET['action'] == "vegponthurkolas")
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
    elseif($irhat && $dbir && count($_POST) > 0 || ((@$_GET['action'] == "szamtarsitas" || @$_GET['action'] == "transzporttarsitas" || @$_GET['action'] == "vegponthurkolas") && count($_POST) > 0))
    {
        if($_GET['action'] == "szamtarsitas")
        {
            include("./modules/epuletek/db/telefonszamokdb.php");
        }
        elseif($_GET['action'] == "transzporttarsitas" || $_GET['action'] == "vegponthurkolas")
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
    elseif($irhat && !$dbir && $_GET['action'] != "szamtarsitas" && $_GET['action'] != "transzporttarsitas" && $_GET['action'] != "vegponthurkolas")
    {
        $szam = $telephely = $nev = $tipus = $emelet = $magyarazat = $naprakesz = $megjegyzes = null;
        $beuszok = array();

        $telephelyek = (new MySQLHandler("SELECT * FROM telephelyek;"))->Result();
        $epulettipusok = (new MySQLHandler("SELECT * FROM epulettipusok;"))->Result();
        $tulajdonosok = (new MySQLHandler("SELECT * FROM szervezetek;"))->Result();
        $csatlakozok = (new MySQLHandler("SELECT * FROM csatlakozotipusok;"))->Result();
        
        $helyisegbutton = "Új helyiség";
        $button = "Új épület";
        $oldalcim = "Új épület hozzáadása";
        $form = $alapform . "epuletform";
        
        if(isset($id))
        {
            $fizikairetegek = (new MySQLHandler("SELECT * FROM fizikairetegek;"))->Result();
            $epulet = (new MySQLHandler("SELECT * FROM epuletek WHERE id = ?;", $id))->Fetch();

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
            if($mindir)
            {
                $beuszok[] = array('cimszoveg' => 'Portok resetelése', 'formnev' => 'modules/alap/forms/portresetform');
            }
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

        $eptkpquery = new MySQLHandler("SELECT telefonkozpont FROM epuletek WHERE id = ?;", $id);
        $epuletkozpont = $eptkpquery->Fetch()['telefonkozpont'];

        if($epuletkozpont < 0) // Debug okokból, élesben a < 0-t KIVENNI!!!
        {
            $where = "WHERE tkozpontportok.eszkoz = $epuletkozpont";
        }
        
        $epuletportok = new MySQLHandler("SELECT portok.id AS id, portok.port AS port
                FROM portok
                    INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                WHERE epulet = ?
                ORDER BY portok.port;", $id);

        $telefonszamok = mySQLConnect("SELECT telefonszamok.id AS id, szam, cimke, telefonszamok.port AS port
                FROM telefonszamok
                    LEFT JOIN tkozpontportok ON telefonszamok.tkozpontport = tkozpontportok.port
                $where
                ORDER BY szam;");

        if($epuletportok->sorokszama > 150)
        {
            $maxhidra = 2;
        }

        $epuletportok = $epuletportok->NaturalSort('port');

        $button = "Portok számmal társítása";
        $oldalcim = "Az épület portjainak telefonszámmal társítása";
        $form = $alapform . "portszamtarsitasform";

        include('./templates/edit.tpl.php');
    }

    elseif($irhat && $_GET['action'] == "transzporttarsitas")
    {
        $magyarazat = null;
        
        $epuletportok = new MySQLHandler("SELECT portok.id AS id, portok.port AS port, portok.csatlakozas AS csatlakozas, athurkolas
                FROM portok
                    INNER JOIN transzportportok ON transzportportok.port = portok.id
                WHERE epulet = ?
                ORDER BY portok.port;", $id);
        
        $masepuletportok = (new MySQLHandler("SELECT portok.id AS id, portok.port AS port, portok.csatlakozas AS csatlakozas, epuletek.szam AS epuletszam
                FROM portok
                    INNER JOIN transzportportok ON transzportportok.port = portok.id
                    LEFT JOIN epuletek ON transzportportok.epulet = epuletek.id
                    LEFT JOIN transzportportok csat ON portok.csatlakozas = csat.port
                WHERE (portok.csatlakozas IS NULL OR csat.epulet = ?) AND transzportportok.epulet != ?
                ORDER BY epuletek.szam, portok.port;", $id, $id))->Result();

        $epuletportok = $epuletportok->NaturalSort('port');

        $button = "Transzport portok társítása";
        $oldalcim = "Az épület transzport portjainak társítása";
        $form = $alapform . "transzporttarsitasform";

        include('./templates/edit.tpl.php');
    }

    elseif($irhat && $_GET['action'] == "vegponthurkolas")
    {
        $magyarazat = null;
        
        $epuletportok = new MySQLHandler("SELECT portok.id AS id, portok.port AS port, portok.csatlakozas AS csatlakozas, athurkolas
                FROM portok
                    INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                WHERE epulet = ?
                ORDER BY portok.port;", $id);

        $epuletportok = $epuletportok->NaturalSort('port');

        $button = "Portok összehurkolása";
        $oldalcim = "Az épület portjainak összehurkolása";
        $form = $alapform . "vegponthurkolasform";

        include('./templates/edit.tpl.php');
    }

    // Akkor futunk ki erre az ágra, ha van olvasási jog, és kiválasztott épület, de más nincs. Ez a sima megjelenítő felület
    else
    {
        
        $helyisegek = (new MySQLHandler("SELECT id, helyisegszam, helyisegnev, emelet
            FROM helyisegek
            WHERE epulet = ?
            ORDER BY emelet ASC, helyisegszam ASC;", $epid))->Result();

        $rackek = (new MySQLHandler("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam, helyisegszam, helyisegnev, emelet
            FROM rackszekrenyek
                INNER JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
            WHERE epulet = ?
            ORDER BY emelet, helyisegszam + 0;", $epid))->Result();

        $portok = (new MySQLHandler("SELECT portok.id AS portid, portok.port AS port, IF((SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1), 1, NULL) AS hasznalatban,
                telefonszamok.szam AS szam, vlanok.nev AS vlan, hurok.port AS athurkolas
            FROM portok
                LEFT JOIN portok hurok ON portok.athurkolas = hurok.id
                LEFT JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN portok csatlakoz ON portok.id = csatlakoz.csatlakozas
                LEFT JOIN switchportok ON switchportok.port = csatlakoz.id
                LEFT JOIN sohoportok ON sohoportok.port = csatlakoz.id
                LEFT JOIN mediakonverterportok ON mediakonverterportok.port = csatlakoz.id
                LEFT JOIN beepitesek ON sohoportok.eszkoz = beepitesek.eszkoz OR mediakonverterportok.eszkoz = beepitesek.eszkoz
                LEFT JOIN epuletek ON vegpontiportok.epulet = epuletek.id
                LEFT JOIN telefonszamok ON telefonszamok.port = portok.id
                LEFT JOIN vlanok ON switchportok.vlan = vlanok.id OR beepitesek.vlan = vlanok.id
            WHERE epuletek.id = ?
            ORDER BY portok.port ASC;", $epid))->Result();

        $eszkozok = (new MySQLHandler("SELECT
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
                szervezetek.rovid AS tulajdonos,
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
                LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
            WHERE helyisegek.epulet = ? AND kiepitesideje IS NULL AND (modellek.tipus < 10 OR (modellek.tipus > 19 AND modellek.tipus < 31))
            ORDER BY rack, pozicio;", $epid))->Result();
        
        $oszlopokeszk = array(
            array('nev' => 'IP cím', 'tipus' => 's'),
            array('nev' => 'Eszköznév', 'tipus' => 's'),
            array('nev' => 'Modell', 'tipus' => 's'),
            array('nev' => 'Eszköztípus', 'tipus' => 's'),
            array('nev' => 'Tulajdonos', 'tipus' => 's'),
            array('nev' => 'Beépítve', 'tipus' => 's')
        );

        if(mysqli_num_rows($rackek) > 0)
        {
            $oszlopokeszk[] = array('nev' => 'Rackszekrény', 'tipus' => 's');
            $oszlopokeszk[] = array('nev' => 'Pozíció', 'tipus' => 'i');

            $oszlopokrack = array(
                array('nev' => 'Emelet', 'tipus' => 's'),
                array('nev' => 'Helyiség', 'tipus' => 's'),
                array('nev' => 'Azonosító', 'tipus' => 's'),
                array('nev' => 'Gyártó', 'tipus' => 's'),
                array('nev' => 'Unitszám', 'tipus' => 's'),
                array('nev' => '', 'tipus' => 's')
            );
        }

        $oszlopokhelyis = array(
            array('nev' => 'Szám', 'tipus' => 'i'),
            array('nev' => 'Helyiségnév', 'tipus' => 's')
        );

        if($csoportir)
        {
            $oszlopokeszk[] = array('nev' => '', 'tipus' => 's');
            $oszlopokeszk[] = array('nev' => '', 'tipus' => 's');
            $oszlopokeszk[] = array('nev' => '', 'tipus' => 's');
            $oszlopokhelyis[] = array('nev' => '&nbsp;', 'tipus' => 's');
        }

        if($csoportir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Épület szerkesztése</button>&nbsp;
                <button type='button' onclick="location.href='./<?=$id?>?action=szamtarsitas'">Épület portjainak telefonszámmal társítása</button>&nbsp;
                <button type='button' onclick="location.href='./<?=$id?>?action=transzporttarsitas'">Épület transzport portjainak társítása</button>&nbsp;
                <button type='button' onclick="location.href='./<?=$id?>?action=vegponthurkolas'">Épület portjainak összehurkolása</button><?php
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
                        <tr><?php
                            sortTableHeader($oszlopokeszk, "eszkozok");
                        ?></tr>
                    </thead>
                    <tbody><?php
                        foreach($eszkozok as $eszkoz)
                        {
                            $beepid = $eszkoz['beepid'];
                            $eszkid = $eszkoz['id'];
                            $eszktip = eszkozTipusValaszto($eszkoz['tipusid']);
                            
                            $kattinthatolink = $RootPath . "/" . $eszktip . "/" . $eszkoz['id'];
                            ?><tr class="trlink">
                                <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                                <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                                <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tulajdonos']?></a></td>
                                <td nowrap><a href="<?=$kattinthatolink?>"><?=timeStampToDate($eszkoz['beepitesideje'])?></a></td><?php
                                if(mysqli_num_rows($rackek) > 0)
                                {
                                    ?><td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td>
                                    <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['pozicio']?></a></td><?php
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
                        <tr><?php
                            sortTableHeader($oszlopokrack, "rackek");
                        ?></tr>
                    </thead>
                    <tbody><?php
                        foreach($rackek as $rack)
                        {
                            $rackid = $rack['id'];
                            $kattinthatolink = $RootPath . "/rack/" . $rackid;
                            ?><tr class="trlink">
                                <td><a href="<?=$kattinthatolink?>"><?=($rack['emelet'] == 0) ? "Földszint" : $rack['emelet'] . ". emelet" ?></a></td>
                                <td nowrap><a href="<?=$kattinthatolink?>"><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$rack['nev']?></a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$rack['gyarto']?></a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$rack['unitszam']?></a></td>
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
                                <tr><?php
                                    sortTableHeader($oszlopokhelyis, $emelet);
                                ?></tr>
                            </thead>
                            <tbody><?php
                            $zar = true;
                    }

                    $kattinthatolink = $RootPath . "/helyiseg/" . $helyiseg['id'];
                    ?><tr class="trlink">
                        <td><a href="<?=$kattinthatolink?>"><?=$helyiseg['helyisegszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$helyiseg['helyisegnev']?></a></td><?php
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