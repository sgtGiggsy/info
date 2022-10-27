<?php

// Elsőként annak ellenőrzése, hogy a felhasználó olvashatja-e,
// majd megvizsgálni, hogy ha olvashatja, de írni szeretné, ahhoz van-e joga
if(!@$mindolvas || (isset($_GET['action']) && !$mindir))
{
    getPermissionError();
}
// Ha van valamilyen módosítási kísérlet, ellenőrizni, hogy van-e rá joga a felhasználónak
elseif(isset($_GET['action']) && $mindir)
{
    $meghiv = true;
    
    // Az eszközszerkesztő oldal includeolása
    include('./includes/eszkozszerkeszt.inc.php');
}
else
{
    $kozpontid = $_GET['id'];
    $helyiseg = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
        FROM helyisegek
            INNER JOIN beepitesek ON beepitesek.helyiseg = helyisegek.id
            INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
            INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
        WHERE beepitesek.eszkoz = $kozpontid;");
    $helyiseg = mysqli_fetch_assoc($helyiseg);

    /*$rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam
        FROM rackszekrenyek
            LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
        WHERE rackszekrenyek.id = $rackid;");
    $rack = mysqli_fetch_assoc($rackek);*/

    $portoksqli = mySQLConnect("SELECT portok.id AS portid, portok.port AS port, IF((SELECT tkozpontport FROM telefonszamok WHERE tkozpontport = portid LIMIT 1), 1, NULL) AS hasznalatban, telefonszamok.tipus AS tipus, telefonszamok.szam AS szam
        FROM tkozpontportok
            LEFT JOIN portok ON tkozpontportok.port = portok.id
            LEFT JOIN telefonszamok ON telefonszamok.tkozpontport = portok.id
        WHERE tkozpontportok.eszkoz = $kozpontid
        ORDER BY portok.port;");
    
    //LENGTH(portok.port),

    $portok = mysqliNaturalSort($portoksqli, 'port');

    $telefonkozpont = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            telefonkozpontok.nev AS kozpontnev,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            beepitesek.id AS beepid,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN telefonkozpontok ON telefonkozpontok.eszkoz = eszkozok.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
        WHERE eszkozok.id = $kozpontid
        ORDER BY epuletek.szam + 0, helyisegszam + 0, helyisegnev;");
    $telefonkozpont = mysqli_fetch_assoc($telefonkozpont);

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
                <span property="name"><?=$telefonkozpont['kozpontnev']?></span>
                <meta property="position" content="4">
            </li>
        </ol>
    </div><?php

// Szerkesztő gombok
    if($mindir)
    {
        ?><div style='display: inline-flex'>
            <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Központ szerkesztése</button><?php
            if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
            {
                ?><button type='button' onclick=rejtMutat("elozmenyek")>Szerkesztési előzmények</button><?php
            }
        ?></div><?php
    }

    ?><div class="oldalcim"><?=$telefonkozpont['kozpontnev']?> Telefonközpont</div><?php

    if(mysqli_num_rows($portoksqli) > 0)
    {
        ?><div class="oldalcim">Lage portok a központban</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;"><?php
            $elozokartya = null;
            foreach($portok as $port)
            {
                $kartya = substr($port['port'], 0, 6);
                //echo $kartya;
                if($kartya != $elozokartya)
                {
                    ?><div style="grid-column-start: 1; grid-column-end: 9"><h2><?=($port['tipus'] < 10) ? "Analóg" : (($port['tipus'] > 9 && $port['tipus'] < 20) ? "Digitális" : "&nbsp" ) ?></div><?php
                }
                $elozokartya = $kartya;

                $portid = $port['portid'];
                ?><div><?php
                    if($mindir)
                    {
                        ?><a href='<?=$RootPath?>/port/<?=$portid?>?tipus=lage' style='text-decoration: none;'><?php
                    }
                    ?><?=($port['hasznalatban']) ? "<p style='font-weight: bold;'>" : "<p style='font-weight: normal;'>" ?><?=$port['port']?><?=($port['szam']) ? " - <small><i>" . $port['szam'] . "</i></small>" : "" ?></p><?php
                    if($mindir)
                    {
                        echo "</a>";
                    }
                ?></div><?php
                
            }
        ?></div><?php
    }
}