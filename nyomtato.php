<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
	$mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
			epuletek.id AS epuletid,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            alakulatok.nev AS tulajdonos,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            beepitesek.nev AS beepitesinev,
            beepitesek.id AS beepid,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            epulettipusok.tipus AS epulettipus,
			telephelyek.telephely AS telephely,
            telephelyek.id AS thelyid,
            helyisegek.id AS helyisegid,
            szines,
            scanner,
            fax,
            admin,
            pass,
            defadmin,
            defpass,
            maxmeret
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN nyomtatomodellek ON nyomtatomodellek.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
			LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
			LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
        WHERE eszkozok.id = $id
        ORDER BY beepitesek.id DESC;");
	
	if(mysqli_num_rows($mindeneszkoz) == 0)
    {
        echo "Nincs ilyen sorszámú aktív eszköz";
    }
    else
    {
		$eszkoz = mysqli_fetch_assoc($mindeneszkoz);

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
                        href="<?=$RootPath?>/epuletek/<?=$eszkoz['thelyid']?>">
                    <span property="name"><?=$eszkoz['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epulet/<?=$eszkoz['epuletid']?>">
                    <span property="name"><?=$eszkoz['epuletszam']?>. <?=$eszkoz['epulettipus']?></span></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/helyiseg/<?=$eszkoz['helyisegid']?>">
                    <span property="name"><?=$eszkoz['helyisegszam']?> (<?=$eszkoz['helyisegnev']?>)</span></a>
                    <meta property="position" content="4">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$eszkoz['beepitesinev']?> (<?=$eszkoz['ipcim']?>)</span>
                    <meta property="position" content="4">
                </li>
            </ol>
        </div><?php

        switch($eszkoz['maxmeret'])
        {
            case 1: $maxmeret = "A4"; break;
            case 2: $maxmeret = "A3"; break;
            case 3: $maxmeret = "A2"; break;
            case 4: $maxmeret = "A1"; break;
            case 5: $maxmeret = "A0"; break;
            default: $maxmeret = "A4";
        }
        switch($eszkoz['szines'])
        {
            case 1: $szines = "Színes"; break;
            default: $szines = "Fekete-Fehér";
        }
        switch($eszkoz['scanner'])
        {
            case 1: $scanner = "Van"; break;
            default: $scanner = "Nincs";
        }
        switch($eszkoz['fax'])
        {
            case 1: $fax = "Van, beépített"; break;
            case 2: $fax = "Alkalmas, modullal"; break;
            default: $fax = "Nincs";
        }

        ?><div class="infobox"><?php
        if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
        {
            ?><div>Állapot</div>
            <div>Beépítve</div>
            <div>IP cím</div>
            <div><?=$eszkoz['ipcim']?></div>
            <div>Beépítési név</div>
            <div><?=$eszkoz['beepitesinev']?></div>
            <div>Beépítés helye</div>
            <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
            <div>Beépítés ideje</div>
            <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
            <?php
        }
        elseif(!$eszkoz['beepitesideje'])
        {
            ?><div>Állapot</div>
            <div>Új, sosem beépített</div><?php
        }
        else
        {
            ?><div>Állapot</div>
            <div>Kiépítve</div>
            <div>Utolsó IP cím</div>
            <div><?=$eszkoz['ipcim']?></div>
            <div>Utolsó beépítési név</div>
            <div><?=$eszkoz['beepitesinev']?></div>
            <div>Utolsó beépítési helye</div>
            <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
            <div>Utolsó beépítés ideje</div>
            <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
            <div>Kiépítés ideje</div>
            <div><?=timeStampToDate($eszkoz['kiepitesideje'])?></div>
            <?php
        }
        ?><div>Gyártó</div>
        <div><?=$eszkoz['gyarto']?></div>
        <div>Modell</div>
        <div><?=$eszkoz['modell'] . $eszkoz['varians']?></div>
        <div>Sorozatszám</div>
        <div><?=$eszkoz['sorozatszam']?></div>
        <div>Színmód</div>
        <div><?=$szines?></div>
        <div>Max nyomtatási méret</div>
        <div><?=$maxmeret?></div>
        <div>Szkenner</div>
        <div><?=$scanner?></div>
        <div>Fax</div>
        <div><?=$fax?></div>
        <div>Tulajdonos</div>
        <div><?=$eszkoz['tulajdonos']?></div>
        </div><?php
	}
}