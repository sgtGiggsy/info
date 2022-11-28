<?php

// Elsőként annak ellenőrzése, hogy a felhasználó olvashatja-e,
// majd megvizsgálni, hogy ha olvashatja, de írni szeretné, ahhoz van-e joga
if(!@$csoportolvas || (isset($_GET['action']) && !$csoportir))
{
    getPermissionError();
}
// Ha van valamilyen módosítási kísérlet, ellenőrizni, hogy van-e rá joga a felhasználónak
elseif(isset($_GET['action']) && $csoportir)
{
    $meghiv = true;
    
    // Az eszközszerkesztő oldal includeolása
    include('./modules/eszkozok/includes/eszkozszerkeszt.inc.php');
}
else
{
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
    $bovitok = mySQLConnect("SELECT
            eszkozok.id AS id,
            beepitesek.id AS beepid,
            (SELECT switchportok.eszkoz
                FROM switchportok
                    INNER JOIN beepitesek ON beepitesek.switchport = switchportok.port
                WHERE beepitesek.id = beepid) AS switchid,
            sorozatszam,
            alakulatok.nev AS tulajdonos,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            beepitesek.beepitesideje AS beepitesideje,
            beepitesek.kiepitesideje AS kiepitesideje,
            alakulatok.rovid AS tulajdonos,
            raktarak.nev AS raktar,
            portok.port AS portnev,
            fizikairetegek.nev AS fizikaireteg,
            atviteliszabvanyok.nev AS szabvany,
            sebessegek.sebesseg AS sebesseg,
            csatlakozotipusok.nev AS csatlakozo
        FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN bovitomodellek ON modellek.id = bovitomodellek.modell
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN portok ON beepitesek.switchport = portok.id
                LEFT JOIN switchportok ON portok.id = switchportok.port
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN fizikairetegek ON bovitomodellek.fizikaireteg = fizikairetegek.id
                LEFT JOIN atviteliszabvanyok ON bovitomodellek.transzpszabvany = atviteliszabvanyok.id
                LEFT JOIN sebessegek ON bovitomodellek.transzpsebesseg = sebessegek.id
                LEFT JOIN csatlakozotipusok ON bovitomodellek.transzpcsatlakozo = csatlakozotipusok.id
                LEFT JOIN beepitesek akteszk ON akteszk.eszkoz = switchportok.eszkoz
                LEFT JOIN rackszekrenyek ON akteszk.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON akteszk.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
        WHERE eszkozok.id = $id AND modellek.tipus > 25 AND modellek.tipus < 31 $csoportwhere
        ORDER BY beepitesek.id DESC;");

    if(mysqli_num_rows($bovitok) == 0)
    {
        echo "Nincs ilyen sorszámú bővítőmodul";
    }
    else
    {
        $eszkoz = mysqli_fetch_assoc($bovitok);
        $akteszkid = $eszkoz['switchid'];

        ?><div class="breadcumblist">
            <ol vocab="https://schema.org/" typeof="BreadcrumbList">
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/">
                    <span property="name">Kecskemét Informatika</span></a>
                    <meta property="position" content="1">
                </li>
                <li><b>></b></li><?php

                if($akteszkid)
                {
                    $aktiveszkozok = mySQLConnect("SELECT
                        eszkozok.id AS id,
                        beepitesek.id AS beepid,
                        sorozatszam,
                        mac,
                        portszam,
                        uplinkportok,
                        szoftver,
                        alakulatok.nev AS tulajdonos,
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
                        alakulatok.rovid AS tulajdonos,
                        rackszekrenyek.id AS rackid,
                        rackszekrenyek.nev AS rack,
                        beepitesek.nev AS beepitesinev,
                        ipcimek.ipcim AS ipcim,
                        raktarak.nev AS raktar
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
                            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                            LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                        WHERE eszkozok.id = $akteszkid AND modellek.tipus < 11
                        ORDER BY beepitesek.id DESC;");
                    
                    $aktiveszkoz = mysqli_fetch_assoc($aktiveszkozok);

                        ?><li property="itemListElement" typeof="ListItem">
                            <a property="item" typeof="WebPage"
                                href="<?=$RootPath?>/epuletek/<?=$aktiveszkoz['thelyid']?>">
                            <span property="name"><?=$aktiveszkoz['telephely']?></span></a>
                            <meta property="position" content="2">
                        </li>
                        <li><b>></b></li>
                        <li property="itemListElement" typeof="ListItem">
                            <a property="item" typeof="WebPage"
                                href="<?=$RootPath?>/epulet/<?=$aktiveszkoz['epuletid']?>">
                            <span property="name"><?=$aktiveszkoz['epuletszam']?>. <?=$aktiveszkoz['epulettipus']?></span></a>
                            <meta property="position" content="3">
                        </li>
                        <li><b>></b></li>
                        <li property="itemListElement" typeof="ListItem">
                            <a property="item" typeof="WebPage"
                                href="<?=$RootPath?>/helyiseg/<?=$aktiveszkoz['helyisegid']?>">
                            <span property="name"><?=$aktiveszkoz['helyisegszam']?> (<?=$aktiveszkoz['helyisegnev']?>)</span></a>
                            <meta property="position" content="4">
                        </li>
                        <?php if($aktiveszkoz['rackid'])
                        {
                            ?><li><b>></b></li>
                            <li property="itemListElement" typeof="ListItem">
                                <a property="item" typeof="WebPage"
                                    href="<?=$RootPath?>/rack/<?=$aktiveszkoz['rackid']?>">
                                <span property="name"><?=$aktiveszkoz['rack']?></span></a>
                                <meta property="position" content="4">
                            </li><?php
                        }
                        ?><li><b>></b></li>
                        <li property="itemListElement" typeof="ListItem">
                            <a property="item" typeof="WebPage"
                                href="<?=$RootPath?>/aktiveszkoz/<?=$aktiveszkoz['id']?>">
                            <span property="name"><?=$aktiveszkoz['beepitesinev']?> (<?=$aktiveszkoz['ipcim']?>)</span></a>
                            <meta property="position" content="4">
                        </li>
                        <li><b>></b></li>
                        <li property="itemListElement" typeof="ListItem">
                            <span property="name"><?=$eszkoz['portnev']?></span>
                            <meta property="position" content="4">
                        </li>
                    <?php
                    $epuletid = $aktiveszkoz['epuletid'];
                    $helyisegid = $aktiveszkoz['helyisegid'];
                }
                else
                {
                    ?><li property="itemListElement" typeof="ListItem">
                        <span property="name"><?=$eszkoz['raktar']?></span></a>
                        <meta property="position" content="4">
                    </li><?php
                }
            ?></ol>
        </div><?php
        
    // Szerkesztő gombok
        if($mindir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Bővítőmodul szerkesztése</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick=rejtMutat("elozmenyek")>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }

        ?><div class="oldalcim"><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?> (<?=$eszkoz['sorozatszam']?>)</div>
        <div class="infobox">
            <div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi beépítés adatai" : "Eszköz adatai" ?></div>
            <div class="infoboxbody">
                <div class="infoboxbodytwocol"><?php
                    if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
                    {
                        ?><div>Állapot</div>
                        <div>Beépítve</div>
                        <div>Port</div>
                        <div><?=$eszkoz['portnev']?></div>
                        <div>Beépítési eszköz IP címe</div>
                        <div><?php
                            if($mindir)
                            {
                                ?><a href="telnet://<?=$aktiveszkoz['ipcim']?>"><?php
                            } ?>
                            <?=$aktiveszkoz['ipcim']?><?php
                            if($mindir)
                            {
                                ?></a><?php
                            }
                        ?></div>
                        <div>Beépítési eszköz neve</div>
                        <div><?=$aktiveszkoz['beepitesinev']?></div>
                        <div>Beépítési eszköz  helye</div>
                        <div><?=$aktiveszkoz['epuletszam']?> <?=($aktiveszkoz['epuletnev']) ? "(" . $aktiveszkoz['epuletnev'] . ")" : "" ?> <?=$aktiveszkoz['helyisegszam']?> <?=($aktiveszkoz['helyisegnev']) ? "(" . $aktiveszkoz['helyisegnev'] . ")" : "" ?></div>
                        <div>Beépítési eszköz rackszekrénye</div>
                        <div><?=$aktiveszkoz['rack']?></div>
                        <div>Beépítés ideje</div>
                        <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                        <?php
                    }
                    elseif(!$eszkoz['beepid'])
                    {
                        ?><div>Állapot</div>
                        <div>Új, sosem beépített</div>
                        <div>Raktár</div>
                        <div><?=$eszkoz['raktar']?></div><?php
                    }
                    else
                    {
                        ?><div>Állapot</div>
                        <div>Kiépítve</div>
                        <div>Raktár</div>
                        <div><?=$eszkoz['raktar']?></div><?php
                    }
                    ?><div>Tulajdonos</div>
                    <div><?=($eszkoz['tulajdonos']) ? $eszkoz['tulajdonos'] : "Nem ismert" ?></div>
                    <div>Gyártó</div>
                    <div><?=$eszkoz['gyarto']?></div>
                    <div>Modell</div>
                    <div><?=$eszkoz['modell'] . $eszkoz['varians']?></div>
                    <div>Sorozatszám</div>
                    <div><?=$eszkoz['sorozatszam']?></div>
                    <div>Fizikai adatátvitel módja</div>
                    <div><?=$eszkoz['fizikaireteg']?></div>
                    <div>Átviteli szabvány</div>
                    <div><?=$eszkoz['szabvany']?></div>
                    <div>Csatlakozó</div>
                    <div><?=$eszkoz['csatlakozo']?></div>
                    <div>Sebesség</div>
                    <div><?=$eszkoz['sebesseg']?> Mbit</div>
                </div>
            </div>
        </div><?php

        if(mysqli_num_rows($bovitok) > 1 || $eszkoz['kiepitesideje'])
        {
            ?><div class="oldalcim"><?=(mysqli_num_rows($bovitok) > 2) ? "Korábbi beépítések" : "Korábbi beépítés" ?></div>
            <table id="eszkozok">
                <thead>
                    <tr>
                        <th>Eszköz IP címe</th>
                        <th>Eszköze neve</th>
                        <th>Port</th>
                        <th>Beépítés ideje</th>
                        <th>Kiépítés ideje</th>
                        <th>Beépítés helye</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody><?php
                foreach($bovitok as $x)
                {
                    $akteszkid = $x['switchid'];

                    $aktiveszkozok = mySQLConnect("SELECT
                        eszkozok.id AS id,
                        beepitesek.id AS beepid,
                        sorozatszam,
                        mac,
                        portszam,
                        uplinkportok,
                        szoftver,
                        alakulatok.nev AS tulajdonos,
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
                        alakulatok.rovid AS tulajdonos,
                        rackszekrenyek.id AS rackid,
                        rackszekrenyek.nev AS rack,
                        beepitesek.nev AS beepitesinev,
                        ipcimek.ipcim AS ipcim,
                        raktarak.nev AS raktar
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
                            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                            LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                        WHERE eszkozok.id = $akteszkid AND modellek.tipus < 11
                        ORDER BY beepitesek.id DESC;");
                    
                    $aktiveszkoz = mysqli_fetch_assoc($aktiveszkozok);

                    if($eszkoz['beepid'] != $x['beepid'])
                    {
                        ?><tr>
                            <td><?=$aktiveszkoz['ipcim']?></td>
                            <td><?=$aktiveszkoz['beepitesinev']?></td>
                            <td><?=$x['portnev']?></td>
                            <td><?=$x['beepitesideje']?></td>
                            <td><?=$x['kiepitesideje']?></td>
                            <td><?=$aktiveszkoz['epuletszam']?> <?=($aktiveszkoz['epuletnev']) ? "(" . $aktiveszkoz['epuletnev'] . ")" : "" ?> <?=$aktiveszkoz['helyisegszam']?> <?=($aktiveszkoz['helyisegnev']) ? "(" . $aktiveszkoz['helyisegnev'] . ")" : "" ?>
                            <td><?php if($csoportir)
                            {
                                ?><a href='<?=$RootPath?>/beepites/<?=$x['beepid']?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a><?php
                            } ?></td>
                        </tr><?php
                    }
                }
                ?></tbody>
            </table><?php
        }
        ?><?php
    }
}
?>