<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $rackid = $_GET['id'];
    $helyiseg = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
        FROM helyisegek
            INNER JOIN rackszekrenyek ON rackszekrenyek.helyiseg = helyisegek.id
            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
            INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
        WHERE rackszekrenyek.id = $rackid;");
    $helyiseg = mysqli_fetch_assoc($helyiseg);

    $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam
        FROM rackszekrenyek
            LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
        WHERE rackszekrenyek.id = $rackid;");
    $rack = mysqli_fetch_assoc($rackek);

    $portok = mySQLConnect("SELECT portok.id AS portid, portok.port AS port, IF((SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1), 1, NULL) AS hasznalatban
        FROM rackportok
            INNER JOIN portok ON rackportok.port = portok.id
        WHERE rackportok.rack = $rackid;");

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
        WHERE rackszekrenyek.id = $rackid AND kiepitesideje IS NULL
        ORDER BY pozicio;");

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
                <span property="name"><?=$helyiseg['helyisegszam']?>. helyiség (<?=$helyiseg['helyisegnev']?>)</span></a>
                <meta property="position" content="4">
            </li>
            <li><b>></b></li>
            <li property="itemListElement" typeof="ListItem">
                <span property="name"><?=$rack['nev']?></span>
                <meta property="position" content="4">
            </li>
        </ol>
    </div>

    <?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/rackszerkeszt/$rackid'\">Rack szerkesztése</button>" : "" ?>
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
                    </tr>
                </thead>
                <tbody><?php
                    foreach($eszkozok as $eszkoz)
                    {
                        $beepid = $eszkoz['beepid'];
                        $eszkid = $eszkoz['id'];
                        $eszktip = eszkozTipusValaszto($eszkoz['tipusid'])

                        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/<?=$eszktip?>eszkoz/<?=$eszkoz['id']?>'>
                            <td><?=$eszkoz['ipcim']?></td>
                            <td nowrap><?=$eszkoz['beepitesinev']?></td>
                            <td nowrap><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                            <td><?=$eszkoz['tipus']?></td>
                            <td><?=$eszkoz['pozicio']?></td>
                            <td><?=$eszkoz['tulajdonos']?></td>
                            <td nowrap><?=timeStampToDate($eszkoz['beepitesideje'])?></td>
                            <td><?=($csoportir) ? "<a href='$RootPath/beepites/$beepid'><img src='$RootPath/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a>" : "" ?></td>
                            <td><?=($csoportir) ? "<a href='$RootPath/eszkozszerkeszt/$eszkid?tipus=$eszktip'><img src='$RootPath/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a>" : "" ?></td>
                        </tr><?php
                    }
                ?></tbody>
            </table>
        </div><?php
    }

    if(mysqli_num_rows($portok) > 0)
    {
        ?><div class="oldalcim">Patch portok a szekrényben</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;"><?php
            foreach($portok as $port)
            {
                $portid = $port['portid'];
                ?><div ><?php
                    if($mindir)
                    {
                        ?><a href='<?=$RootPath?>/port/<?=$portid?>'><?php
                    }
                    ?><?=($port['hasznalatban']) ? "<p style='font-weight: bold'>" : "<p style='font-weight: normal'>" ?><?php echo $port['port'] . "</p>";
                    if($mindir)
                    {
                        echo "</a>";
                    }
                ?></div><?php
            }
        ?></div><?php
    }
}