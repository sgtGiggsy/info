<?php

// Elsőként annak ellenőrzése, hogy a felhasználó olvashatja-e,
// majd megvizsgálni, hogy ha olvashatja, de írni szeretné, ahhoz van-e joga
if(!@$csoportolvas || (isset($_GET['action']) && !$csoportir) || (!isset($_GET['action']) && !$id))
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
            varians,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            teljesitmeny,
            szunetmentesek.tipus AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            beepitesek.id AS beepid,
            szervezetek.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            raktarak.nev AS raktar,
            epuletek.id AS epuletid,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            epulettipusok.tipus AS epulettipus,
            telephelyek.telephely AS telephely,
            telephelyek.id AS thelyid,
            helyisegek.id AS helyisegid,
            null AS ipcim,
            hibas
        FROM szunetmentesek
                INNER JOIN eszkozok ON szunetmentesek.eszkoz = eszkozok.id
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
                LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
                LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
        WHERE eszkozok.id = $id $csoportwhere;");
	
	if(mysqli_num_rows($mindeneszkoz) == 0)
    {
        echo "Nincs ilyen sorszámú szünetmentes";
    }
    else
    {
        $eszkoz = mysqli_fetch_assoc($mindeneszkoz);

        $epuletid = $eszkoz['epuletid'];
        $helyisegid = $eszkoz['helyisegid'];

        showBreadcumb($eszkoz);

    // Szerkesztő gombok
        if($csoportir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Eszköz szerkesztése</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick='rejtMutat("elozmenyek")'>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }
        
        ?><div class="infobox">
            <div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi beépítés adatai" : "Eszköz adatai" ?></div>
            <div class="infoboxbody">
                <div class="infoboxbodytwocol">
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
                        <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div><?php
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
                    <div>Szünetmentes kialakítása</div>
                    <div><?=($eszkoz['tipus'] == 1) ? "Asztali" : "Rack-be építhető" ?></div>
                    <div>Teljesítmény</div>
                    <div><?=$eszkoz['teljesitmeny']?> Watt</div>
                </div>
            </div>
        </div><?php
	}
}