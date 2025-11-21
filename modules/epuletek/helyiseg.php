<?php

$permission = true;
if(!isset($_GET['action']) || $_GET['action'] == "edit")
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

    $helyisegid = $_GET['id'];
    $helyisegek = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
        FROM helyisegek
            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
            INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
        WHERE helyisegek.id = $helyisegid $csoportwhere;");

    $helyiseg = mysqli_fetch_assoc($helyisegek);

    if(mysqli_num_rows($helyisegek) != 1)
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
                        href="<?=$RootPath?>/epuletek/<?=$helyiseg['thelyid']?>">
                    <span property="name"><?=$helyiseg['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epulet/<?=$helyiseg['epid']?>">
                    <span property="name"><?=$helyiseg['epuletszam']?>. <?=$helyiseg['tipus']?></span></a></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=($helyiseg['helyisegszam']) ? $helyiseg['helyisegszam'] . ". helyiség" : "" ?><?=($helyiseg['helyisegszam'] && $helyiseg['helyisegnev']) ? " - " : "" ?><?=$helyiseg['helyisegnev']?></span>
                    <meta property="position" content="4">
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
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
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
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/epuletek/db/helyisegdb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        afterDBRedirect($con);
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $helyisegszam = $helyisegnev = $emelet = $magyarazat = null;
        $beuszok = array();

        $helyisegbutton = "Új helyiség";
        $oldalcim = "Új helyiség hozzáadása";

        $form = $alapform . "helyisegszerkform";

        if(isset($_GET['id']))
        {
            $helyisegid = $_GET['id'];
            $button = "Helyiség szerkesztése";
            $helyiseg = (new MySQLHandler("SELECT * FROM helyisegek WHERE id = ?;", $helyisegid))->Fetch();

            $epid = $helyiseg['epulet'];
            $helyisegszam = $helyiseg['helyisegszam'];
            $helyisegnev = $helyiseg['helyisegnev'];
            $emelet = $helyiseg['emelet'];

            $epuletportok = (new MySQLHandler("SELECT portok.id AS id, portok.port AS port
                FROM portok
                    INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                WHERE epulet = ? AND vegpontiportok.helyiseg IS NULL
            UNION
                SELECT portok.id AS id, portok.port AS port
                FROM portok
                    INNER JOIN transzportportok ON transzportportok.port = portok.id
                WHERE epulet = ?;", $epid, $epid))->Result();

            $helyisegbutton = "Helyiség módosítása";
            $oldalcim = "Helyiség szerkesztése";

            $beuszok[] = array('cimszoveg' => 'Épületportok helyiséghez kötése', 'formnev' => $alapform . 'portokhelyiseghezform');
            if($mindir)
            {
                $beuszok[] = array('cimszoveg' => 'Portok resetelése', 'formnev' => 'modules/alap/forms/portresetform');
            }
        }

        include('./templates/edit.tpl.php');

    }

    // Ha írási művelet nem lesz, ellenőrizni kell, hogy van-e kiválasztott épület. Ha nincs, hiba dobása
    elseif(!isset($id))
    {
        getPermissionError();
    }

    // Akkor futunk ki erre az ágra, ha van olvasási jog, és kiválasztott épület, de más nincs. Ez a sima megjelenítő felület
    else
    {
        $rackek = (new MySQLHandler("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam
            FROM rackszekrenyek
                LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
            WHERE helyiseg = ?;", $helyisegid))->AsArray();
        
        $portok = (new MySQLHandler("SELECT portok.id AS portid, portok.port AS port, IF((SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1), 1, NULL) AS hasznalatban, szam, vlanok.nev AS vlan, hurok.port AS athurkolas
            FROM portok
                LEFT JOIN portok hurok ON portok.athurkolas = hurok.id
                LEFT JOIN rackportok ON rackportok.port = portok.id
                LEFT JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN portok csatlakoz ON portok.id = csatlakoz.csatlakozas
                LEFT JOIN switchportok ON switchportok.port = csatlakoz.id
                LEFT JOIN sohoportok ON sohoportok.port = csatlakoz.id
                LEFT JOIN mediakonverterportok ON mediakonverterportok.port = csatlakoz.id
                LEFT JOIN beepitesek ON sohoportok.eszkoz = beepitesek.eszkoz OR mediakonverterportok.eszkoz = beepitesek.eszkoz
                LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id OR vegpontiportok.helyiseg = helyisegek.id
                LEFT JOIN telefonszamok ON telefonszamok.port = portok.id
                LEFT JOIN vlanok ON switchportok.vlan = vlanok.id OR beepitesek.vlan = vlanok.id
                LEFT JOIN transzportportok ON transzportportok.port = portok.id
            WHERE helyisegek.id = ? AND transzportportok.id IS NULL
            ORDER BY rackportok.rack ASC, portok.id ASC;", $helyisegid))->Result();

        $eszkozok = new MySQLHandler("SELECT
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
            FROM
                eszkozok INNER JOIN
                    modellek ON eszkozok.modell = modellek.id INNER JOIN
                    gyartok ON modellek.gyarto = gyartok.id INNER JOIN
                    eszkoztipusok ON modellek.tipus = eszkoztipusok.id LEFT JOIN
                    beepitesek ON beepitesek.eszkoz = eszkozok.id LEFT JOIN
                    rackszekrenyek ON beepitesek.rack = rackszekrenyek.id LEFT JOIN
                    helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id LEFT JOIN
                    ipcimek ON beepitesek.ipcim = ipcimek.id LEFT JOIN
                    szervezetek ON eszkozok.tulajdonos = szervezetek.id
            WHERE helyisegek.id = ? AND kiepitesideje IS NULL
            ORDER BY rack, pozicio;", $helyisegid);
        if($eszkozok->sorokszama > 0)
        {
            $rackszam = $eszkozok->Fetch()['rackszam'];
        }
        else
        {
            $rackszam = null;
        }

        $oszlopokeszk = array(
            array('nev' => 'IP cím', 'tipus' => 's'),
            array('nev' => 'Eszköznév', 'tipus' => 's'),
            array('nev' => 'Modell', 'tipus' => 's'),
            array('nev' => 'Eszköztípus', 'tipus' => 's'),
            array('nev' => 'Tulajdonos', 'tipus' => 's'),
            array('nev' => 'Beépítve', 'tipus' => 's')
        );

        if(count($rackek) > 0)
        {
            $oszlopokeszk[] = array('nev' => 'Rackszekrény', 'tipus' => 's');
            $oszlopokeszk[] = array('nev' => 'Pozíció', 'tipus' => 'i');

            $oszlopokrack = array(
                array('nev' => 'Azonosító', 'tipus' => 's'),
                array('nev' => 'Gyártó', 'tipus' => 's'),
                array('nev' => 'Unitszám', 'tipus' => 's')
            );
        }

        $oszlopokhelyis = array(
            array('nev' => 'Szám', 'tipus' => 'i'),
            array('nev' => 'Helyiségnév', 'tipus' => 's')
        );

        ?><?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/helyiseg/$helyisegid?action=edit'\">Helyiség szerkesztése</button>" : "" ?>
        <div class="oldalcim"><?=$helyiseg['helyisegszam']?> (<?=$helyiseg['helyisegnev']?>)</div>
        
        <div class="oldalcim">Eszközök a helyiségben</div><?php
        $ujoldalcim = $ablakcim . " - ". $helyiseg['epuletszam'] . ". " . $helyiseg['tipus'] . " " . $helyiseg['helyisegszam'] . ". helyiség (" . $helyiseg['helyisegnev'] . ")";
        if($eszkozok->sorokszama > 0)
        {
            ?><div>
                <table id="eszkozok">
                    <thead>
                        <tr><?php
                            sortTableHeader($oszlopokeszk, "eszkozok");
                            if($csoportir)
                            {
                                ?><th></th>
                                <th></th>
                                <th></th><?php
                            }
                        ?></tr>
                    </thead>
                    <tbody><?php
                        foreach($eszkozok->Result() as $eszkoz)
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
                                if($rackszam > 0)
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

        if(count($rackek) > 0)
        {
            ?><div class="oldalcim">Rackszekrények a helyiségben</div>
            <div>
                <table id="rackek">
                    <thead>
                        <tr><?php
                            sortTableHeader($oszlopokrack, "rackek");
                            if($mindir)
                            {
                                ?><th></th><?php
                            }
                        ?></tr>
                    </thead>
                    <tbody><?php
                        foreach($rackek as $rack)
                        {
                            $rackid = $rack['id'];
                            $kattinthatolink = $RootPath . "/rack/" . $rackid;
                            ?><tr class="trlink">
                                <td><a href="<?=$kattinthatolink?>"><?=$rack['nev']?></a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$rack['gyarto']?></a></td>
                                <td><a href="<?=$kattinthatolink?>"><?=$rack['unitszam']?></a></td><?php
                                if($mindir)
                                {
                                    ?><td><?=($csoportir) ? "<a href='$RootPath/rack/$rackid?action=edit'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td><?php
                                }
                            ?></tr><?php
                        }
                    ?></tbody>
                </table>
            </div><?php
        }
        ?><div class="oldalcim">Transzport portok a helyiségben</div><?php
        transzportPortLista($id, 'helyiseg');

        ?><div class="oldalcim">Végpontok a helyiségben</div><?php
            vegpontLista($portok);
    }
}