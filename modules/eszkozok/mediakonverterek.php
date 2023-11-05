<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("(modellek.tipus > 20 AND modellek.tipus < 26)");
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
            mediakonvertermodellek.fizikaireteg,
            mediakonvertermodellek.transzpszabvany,
            atviteliszabvanyok.nev AS transzportszabvany,
            fizikairetegek.nev AS technologia,
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            vlanok.nev AS vlan,
            raktarak.nev AS raktar,
            hibas
        FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN mediakonvertermodellek ON mediakonvertermodellek.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN vlanok ON beepitesek.vlan = vlanok.id
                LEFT JOIN atviteliszabvanyok ON mediakonvertermodellek.transzpszabvany = atviteliszabvanyok.id
                LEFT JOIN fizikairetegek ON mediakonvertermodellek.fizikaireteg = fizikairetegek.id
        WHERE $where $csoportwhere
        ORDER BY epuletek.szam + 1, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $tipus = 'mediakonverterek';
    $oszlopok = array(
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Eszköztípus', 'tipus' => 's'),
        array('nev' => 'Technológia', 'tipus' => 's'),
        array('nev' => 'Szabvány', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's'),
        array('nev' => 'Rack', 'tipus' => 's'),
        array('nev' => 'Raktár', 'tipus' => 's'),
        array('nev' => 'Hálózat', 'tipus' => 's'),
        array('nev' => 'Megjegyzés', 'tipus' => 's')
    );

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/mediakonverter?action=addnew'">Új médiakonverter</button><?php
    }

    ?><div class="PrintArea">
        <div class="oldalcim">Médiakonverterek <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
        <table id="<?=$tipus?>">
            <thead>
                <tr><?php
                    sortTableHeader($oszlopok, $tipus, true);
                    if($csoportir)
                    {
                        ?><th class="dontprint"></th>
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
                        $kattinthatolink = './mediakonverter/' . $eszkoz['id'];
                        ?><tr class='trlink<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?>'>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                            <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['technologia']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['transzportszabvany']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td>
                            <td><a href="<?=$kattinthatolink?>">Beépítve</a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['vlan']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                            if($csoportir)
                            {
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "mediakonverter");
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    $kattinthatolink = './mediakonverter/' . $eszkoz['id'];
                    ?><tr class='trlink kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['technologia']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['transzportszabvany']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['raktar']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                        if($csoportir)
                        {
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "mediakonverter");
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}