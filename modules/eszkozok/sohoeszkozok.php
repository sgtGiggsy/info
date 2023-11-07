<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("(modellek.tipus > 5 AND modellek.tipus < 11)");
    $where = $szuresek['where'];

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

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
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
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            hibas,
            lanportok,
            modellek.tipus AS eszktip,
            wanportok,
            wifitipusok.nev AS wifi,
            szoftver
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            INNER JOIN sohoeszkozok ON sohoeszkozok.eszkoz = eszkozok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            LEFT JOIN wifitipusok ON wifitipusok.id = sohoeszkozok.wifi
        WHERE $where $csoportwhere
        ORDER BY telephely, epuletek.szam + 1, helyisegszam, pozicio, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $tipus = 'sohoeszkozok';
    $oszlopok = array(
        array('nev' => 'IP cím', 'tipus' => 's'),
        array('nev' => 'Eszköznév', 'tipus' => 's'),
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'LAN Portok', 'tipus' => 's'),
        array('nev' => 'WiFi', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Eszköztípus', 'tipus' => 's'),
        array('nev' => 'Szoftver', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's'),
        array('nev' => 'Rack', 'tipus' => 's')
    );
    if($csoportir)
    {
        $oszlopok[] = array('nev' => 'Megjegyzés', 'tipus' => 's');
    }

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/sohoeszkoz?action=addnew'">Új SOHO eszköz</button><?php
    }

    ?><div class="PrintArea">
        <div class="oldalcim">SOHO eszközök</div>
        <table id="<?=$tipus?>">
        <thead>
                <tr><?php
                    sortTableHeader($oszlopok, $tipus, true);
                    if($csoportir)
                    {
                        ?><th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th><?php
                    }
                ?></tr>
            </thead>
            <tbody><?php
                $nembeepitett = array();
                foreach($mindeneszkoz as $eszkoz)
                {
                    if(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje']))
                    {
                        $nembeepitett[] = $eszkoz;
                    }
                    else
                    {
                        $kattinthatolink = './sohoeszkoz/' . $eszkoz['id'];
                        ?><tr class='trlink<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?>'>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                            <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['lanportok']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['wifi']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['szoftver']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td><?php
                            if($csoportir)
                            {
                                ?><td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "sohoeszkoz");
                                if($eszkoz['eszktip'] == 7)
                                {
                                    ?><td class="dontprint"><a href="telnet://<?=$eszkoz['ipcim']?>"><img src='<?=$RootPath?>/images/ssh.png' alt='Eszköz adminisztrálása' title='Eszköz adminisztrálása'/></a></td>
                                    <td class="dontprint"><a href="http://<?=$eszkoz['ipcim']?>" target="_blank"><img src='<?=$RootPath?>/images/webmanage.png' alt='Webes adminisztráció' title='Webes adminisztráció'/></a></td><?php
                                }
                                else
                                {
                                    ?><td class="dontprint"><a href="<?=$kattinthatolink?>"></a></td>
                                    <td class="dontprint"><a href="<?=$kattinthatolink?>"></a></td><?php
                                }
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    $kattinthatolink = './sohoeszkoz/' . $eszkoz['id'];
                    ?><tr class='trlink kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['ipcim']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['beepitesinev']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['lanportok']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['wifi']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['szoftver']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td><?php
                        if($csoportir)
                        {
                            ?><td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "sohoeszkoz");
                            ?><td><a href="<?=$kattinthatolink?>"></a></td>
                            <td><a href="<?=$kattinthatolink?>"></a></td><?php
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}