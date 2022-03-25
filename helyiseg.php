<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $helyisegid = $_GET['id'];
    $helyiseg = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
        FROM helyisegek
            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
            INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
        WHERE helyisegek.id = $helyisegid;");
    $helyiseg = mysqli_fetch_assoc($helyiseg);

    $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam
        FROM rackszekrenyek
            LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
        WHERE helyiseg = $helyisegid;");

    $eszkozok = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            beepitesideje,
            pozicio,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
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
        WHERE helyisegek.id = $helyisegid AND kiepitesideje IS NULL
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
                <span property="name"><?=$helyiseg['helyisegszam']?> (<?=$helyiseg['helyisegnev']?>)</span>
                <meta property="position" content="4">
            </li>
        </ol>
    </div>

    <?=($mindir) ? "<a href='$RootPath/helyisegszerkeszt/$helyisegid'>Helyiség szerkesztése</a>" : "" ?>
    <div class="oldalcim"><?=$helyiseg['helyisegszam']?> (<?=$helyiseg['helyisegnev']?>)</div>
    
    <div class="oldalcim">Eszközök a helyiségben</div>
    <div>
        <table id="eszkozok">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', 'eszkozok')">IP cím</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">Eszköznév</th>
                    <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Modell</th>
                    <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Eszköztípus</th>
                    <th class="tsorth" onclick="sortTable(4, 's', 'eszkozok')">Rackszekrény</th>
                    <th class="tsorth" onclick="sortTable(5, 'i', 'eszkozok')">Pozíció</th>
                    <th class="tsorth" onclick="sortTable(6, 's', 'eszkozok')">Tulajdonos</th>
                    <th class="tsorth" onclick="sortTable(7, 's', 'eszkozok')">Beépítve</th>
                </tr>
            </thead>
            <tbody><?php
                foreach($eszkozok as $eszkoz)
                {
                    ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/eszkoz/<?=$eszkoz['id']?>'>
                        <td><?=$eszkoz['ipcim']?></td>
                        <td nowrap><?=$eszkoz['beepitesinev']?></td>
                        <td nowrap><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['rack']?></td>
                        <td><?=$eszkoz['pozicio']?></td>
                        <td><?=$eszkoz['tulajdonos']?></td>
                        <td nowrap><?=$eszkoz['beepitesideje']?></td>
                    </tr><?php
                }
            ?></tbody>
        </table>
    </div><?php

    if(mysqli_num_rows($rackek) > 0)
    {
        ?><div class="oldalcim">Rackszekrények a helyiségben</div>
        <div>
            <table id="rackek">
                <thead>
                    <tr>
                        <th class="tsorth" onclick="sortTable(0, 's', 'rackek')">Azonosító</th>
                        <th class="tsorth" onclick="sortTable(1, 's', 'rackek')">Gyártó</th>
                        <th class="tsorth" onclick="sortTable(2, 'i', 'rackek')">Unitszám</th>
                    </tr>
                </thead>
                <tbody><?php
                    foreach($rackek as $rack)
                    {
                        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/rack/<?=$rack['id']?>'>
                            <td><?=$rack['nev']?></td>
                            <td><?=$rack['gyarto']?></td>
                            <td><?=$rack['unitszam']?></td>
                        </tr><?php
                    }
                ?></tbody>
            </table>
        </div><?php
    }
    ?><div class="oldalcim">Végpontok a helyiségben</div>
    
    <?php
}