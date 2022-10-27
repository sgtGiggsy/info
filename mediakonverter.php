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
            beepitesek.megjegyzes AS megjegyzes,
            epulettipusok.tipus AS epulettipus,
            telephelyek.telephely AS telephely,
            telephelyek.id AS thelyid,
            helyisegek.id AS helyisegid,
            fizikairetegek.nev AS fizikaireteg,
            transzpszabvany,
            transzpcsatlakozo,
            transzpsebesseg,
            lanszabvany,
            lancsatlakozo,
            lansebesseg,
            vlanok.nev AS vlan,
            raktarak.nev AS raktar,
            (SELECT nev FROM atviteliszabvanyok WHERE id = transzpszabvany) AS transzportszabvany,
            (SELECT nev FROM atviteliszabvanyok WHERE id = lanszabvany) AS lanportszabvany,
            (SELECT sebesseg FROM sebessegek WHERE id = transzpsebesseg) AS transzportsebesseg,
            (SELECT sebesseg FROM sebessegek WHERE id = lansebesseg) AS lanportsebesseg,
            (SELECT nev FROM csatlakozotipusok WHERE id = transzpcsatlakozo) AS transzportcsatlakozo,
            (SELECT nev FROM csatlakozotipusok WHERE id = lancsatlakozo) AS lanportcsatlakozo
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN mediakonvertermodellek ON mediakonvertermodellek.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            LEFT JOIN fizikairetegek ON mediakonvertermodellek.fizikaireteg = fizikairetegek.id
            LEFT JOIN vlanok ON beepitesek.vlan = vlanok.id
            LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
        WHERE eszkozok.id = $id
        ORDER BY beepitesek.id DESC;");
	
	if(mysqli_num_rows($mindeneszkoz) == 0)
    {
        echo "Nincs ilyen sorszámú médiakonverter";
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
                    <span property="name"><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?> (<?=$eszkoz['sorozatszam']?>)</span>
                    <meta property="position" content="4">
                </li>
            </ol>
        </div><?php

    // Szerkesztő gombok
        if($mindir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Eszköz szerkesztése</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick=rejtMutat("elozmenyek")>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }
        
        ?><div class="infobox">
            <div>Tulajdonos</div>
            <div><?=$eszkoz['tulajdonos']?></div>
            <div>Sorozatszám</div>
            <div><?=$eszkoz['sorozatszam']?></div>
            <div>Megjegyzés</div>
            <div><?=$eszkoz['megjegyzes']?></div><?php
            if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
            {
                ?><div>Állapot</div>
                <div>Beépítve</div>
                <div>Beépítés helye</div>
                <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                <div>Beépítés ideje</div>
                <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                <div>Hálózat</div>
                <div><?=$eszkoz['vlan']?></div>
                <?php
            }
            elseif(!$eszkoz['beepitesideje'])
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
                <div>Utolsó beépítési helye</div>
                <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                <div>Utolsó beépítés ideje</div>
                <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                <div>Kiépítés ideje</div>
                <div><?=timeStampToDate($eszkoz['kiepitesideje'])?></div>
                <div>Raktár</div>
                <div><?=$eszkoz['raktar']?></div>
                <div>Hálózat</div>
                <div><?=$eszkoz['vlan']?></div>
                <?php
            }
            ?>
            <div>Gyártó</div>
            <div><?=$eszkoz['gyarto']?></div>
            <div>Modell</div>
            <div><?=$eszkoz['modell'] . $eszkoz['varians']?></div>
            <div>Eszköz fajtája</div>
            <div><?=$eszkoz['tipus']?></div>
            <div>Fizikai adatátvitel módja</div>
            <div><?=$eszkoz['fizikaireteg']?></div>
            <div>Transzport port átviteli szabványa</div>
            <div><?=$eszkoz['transzportszabvany']?></div>
            <div>Transzport port csatlakozója</div>
            <div><?=$eszkoz['transzportcsatlakozo']?></div>
            <div>Transzport port sebessége</div>
            <div><?=$eszkoz['transzportsebesseg']?> Mbit</div>
            <div>LAN oldal átviteli szabványa</div>
            <div><?=$eszkoz['lanportszabvany']?></div>
            <div>LAN oldal csatlakozója</div>
            <div><?=$eszkoz['lanportcsatlakozo']?></div>
            <div>LAN oldali sebesség</div>
            <div><?=$eszkoz['lanportsebesseg']?> Mbit</div>
        </div><?php
	}
}