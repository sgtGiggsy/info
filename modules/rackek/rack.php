<?php

if(!@$csoportolvas || (isset($_GET['action']) && !$csoportolvas))
{
	getPermissionError();
}
elseif(isset($_GET['action']) && ($_GET['action'] == "addnew" || $_GET['action'] == "edit") && $csoportir)
{
    $racknev = $rackhely = $rackgyarto = $rackunitszam = $magyarazat = $beuszok = null;
    $irhat = true;
    $button = "Új rack";
    $oldalcim = "Új rack létrehozása";
    $form = "modules/rackek/forms/rackform";

    $ipcimek = mySQLConnect("SELECT * FROM ipcimek ORDER BY ipcim ASC");
    if(isset($id))
    {
        $rack = mySQLConnect("SELECT rackszekrenyek.nev AS nev, rackszekrenyek.helyiseg AS helyiseg, gyarto, unitszam, helyisegek.epulet AS epulet
            FROM rackszekrenyek
                LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
            WHERE rackszekrenyek.id = $id;");
        $rack = mysqli_fetch_assoc($rack);

        $racknev = $rack['nev'];
        $rackhely = $rack['helyiseg'];
        $rackgyarto = $rack['gyarto'];
        $rackunitszam = $rack['unitszam'];
        $epulet = $rack['epulet'];

        $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port
            FROM portok
                INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epulet
            UNION
            SELECT portok.id AS id, portok.port AS port
            FROM portok
                INNER JOIN transzportportok ON transzportportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epulet;");

        $button = "Szerkesztés";
        $oldalcim = "Rack szerkesztése";
        $beuszok = array(array('cimszoveg' => 'Portok rackhez kötése', 'formnev' => 'modules/rackek/forms/portrackhezform'));
        if($mindir)
        {
            $beuszok[] = array('cimszoveg' => 'Portok resetelése', 'formnev' => 'modules/alap/forms/portresetform');
        }
    }
    
    include('./templates/edit.tpl.php');
}
elseif(count($_POST) > 0 && $mindir && (@$_GET['action'] == "new" || @$_GET['action'] == "update" || @$_GET['action'] == "delete"))
{
    $irhat = true;
    include("./modules/rackek/db/rackdb.php");
    afterDBRedirect($con);
}
elseif(!isset($_GET['action']))
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
    $helyiseg = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
        FROM helyisegek
            INNER JOIN rackszekrenyek ON rackszekrenyek.helyiseg = helyisegek.id
            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
            INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
        WHERE rackszekrenyek.id = $id;");
    $helyiseg = mysqli_fetch_assoc($helyiseg);

    $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam
        FROM rackszekrenyek
            LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
            LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
        WHERE rackszekrenyek.id = $id $csoportwhere;");
    $rack = mysqli_fetch_assoc($rackek);

    $portok = mySQLConnect("SELECT portok.id AS portid, portok.port AS port, IF((SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1), 1, NULL) AS hasznalatban, szam, vlanok.nev AS vlan, hurok.port AS athurkolas
        FROM portok
            LEFT JOIN portok hurok ON portok.athurkolas = hurok.id
            LEFT JOIN rackportok ON rackportok.port = portok.id
            LEFT JOIN portok csatlakoz ON portok.id = csatlakoz.csatlakozas
            LEFT JOIN switchportok ON switchportok.port = csatlakoz.id
            LEFT JOIN sohoportok ON sohoportok.port = csatlakoz.id
            LEFT JOIN mediakonverterportok ON mediakonverterportok.port = csatlakoz.id
            LEFT JOIN beepitesek ON sohoportok.eszkoz = beepitesek.eszkoz OR mediakonverterportok.eszkoz = beepitesek.eszkoz
            LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
            LEFT JOIN telefonszamok ON telefonszamok.port = portok.id
            LEFT JOIN vlanok ON switchportok.vlan = vlanok.id OR beepitesek.vlan = vlanok.id
            LEFT JOIN transzportportok ON transzportportok.port = portok.id
        WHERE rackportok.rack = $id AND transzportportok.id IS NULL
        ORDER BY portok.port ASC;");

    $eszkozok = mySQLConnect("SELECT
            eszkozok.id AS id,
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
                alakulatok ON eszkozok.tulajdonos = alakulatok.id
        WHERE rackszekrenyek.id = $id AND kiepitesideje IS NULL
        ORDER BY pozicio;");

    if(mysqli_num_rows($rackek) != 1)
    {
        getPermissionError();
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
                    <span property="name"><?=$helyiseg['epuletszam']?>. <?=$helyiseg['tipus']?></span></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>">
                    <span property="name"><?=($helyiseg['helyisegszam']) ? $helyiseg['helyisegszam'] . ". helyiség" : "" ?><?=($helyiseg['helyisegszam'] && $helyiseg['helyisegnev']) ? " - " : "" ?><?=$helyiseg['helyisegnev']?></span></a>
                    <meta property="position" content="4">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$rack['nev']?></span>
                    <meta property="position" content="4">
                </li>
            </ol>
        </div>

        <?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/rack/$id?action=edit'\">Rack szerkesztése</button>" : "" ?>
        <div class="oldalcim"><?=$rack['nev']?> Rack</div><?php
        if(mysqli_num_rows($eszkozok) > 0)
        {
            ?><div class="oldalcim">Eszközök a szekrényben</div>
            <div>
                <table id="eszkozok">
                    <thead>
                        <tr>
                            <th class="tsorth" onclick="sortTable(0, 's', 'eszkozok')">IP cím</th>
                            <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">Eszköznév</th>
                            <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Modell</th>
                            <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Eszköztípus</th>
                            <th class="tsorth" onclick="sortTable(4, 'i', 'eszkozok')">Pozíció</th>
                            <th class="tsorth" onclick="sortTable(5, 's', 'eszkozok')">Tulajdonos</th>
                            <th class="tsorth" onclick="sortTable(6, 's', 'eszkozok')">Beépítve</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        foreach($eszkozok as $eszkoz)
                        {
                            $beepid = $eszkoz['beepid'];
                            $eszkid = $eszkoz['id'];
                            $eszktip = eszkozTipusValaszto($eszkoz['tipusid']);

                            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/<?=$eszktip?>/<?=$eszkoz['id']?>'>
                                <td><?=$eszkoz['ipcim']?></td>
                                <td nowrap><?=$eszkoz['beepitesinev']?></td>
                                <td nowrap><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                                <td><?=$eszkoz['tipus']?></td>
                                <td><?=$eszkoz['pozicio']?></td>
                                <td><?=$eszkoz['tulajdonos']?></td>
                                <td nowrap><?=timeStampToDate($eszkoz['beepitesideje'])?></td><?php
                                if($csoportir)
                                {
                                    szerkSor($eszkoz['beepid'], $eszkoz['id'], $eszktip);
                                    
                                }
                                else
                                {
                                    ?><td></td>
                                    <td></td>
                                    <td></td><?php
                                }
                            ?></tr><?php
                        }
                    ?></tbody>
                </table>
            </div><?php
        }

        ?><div class="oldalcim">Transzport portok a szekrényben</div><?php
                transzportPortLista($id, 'rack');

        ?><div class="oldalcim">Patch portok a szekrényben</div><?php
            vegpontLista($portok);
    }
}