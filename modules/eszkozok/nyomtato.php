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
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
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
            szervezetek.nev AS tulajdonos,
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
            maxmeret,
            raktar
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
            LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
        WHERE eszkozok.id = $id $csoportwhere
        ORDER BY beepitesek.id DESC;");
	
	if(mysqli_num_rows($mindeneszkoz) == 0)
    {
        echo "Nincs ilyen sorszámú aktív eszköz";
    }
    else
    {
		$eszkoz = mysqli_fetch_assoc($mindeneszkoz);

        showBreadcumb($eszkoz);

        // Szerkesztő gombok
        if($mindir)
        {
            ?><div class="szerkgombsor">
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Nyomtató szerkesztése</button>
                <button type='button' onclick="location.href='./<?=$id?>?beepites<?=($eszkoz['beepid'] && !$eszkoz['kiepitesideje']) ? '=' . $eszkoz['beepid'] . '&action=edit' : '&action=addnew' ?>'">
                    <?=($eszkoz['beepid'] && !$eszkoz['kiepitesideje']) ? "Beépítés szerkesztése" : "Új beépítés" ?>
                </button>
            </div><?php
        }

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

        ?><div class="infobox">
            <div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi beépítés adatai" : "Eszköz adatai" ?></div>
            <div class="infoboxbody">
                <div class="infoboxbodytwocol"><?php
                    if($eszkoz['beepitesideje'] && $eszkoz['raktar'])
                    {
                        ?><div>Állapot</div>
                        <div>Raktárban</div><?php
                    }
                    elseif($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
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
                </div>
            </div>
        </div><?php
	}
}