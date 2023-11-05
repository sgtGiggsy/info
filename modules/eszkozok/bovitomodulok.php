<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("(modellek.tipus > 25 AND modellek.tipus < 31)");
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
            beepitesek.beepitesideje AS beepitesideje,
            beepitesek.kiepitesideje AS kiepitesideje,
            modellek.tipus AS tipusid,
            bovitomodellek.fizikaireteg,
            bovitomodellek.transzpszabvany,
            atviteliszabvanyok.nev AS transzportszabvany,
            fizikairetegek.nev AS technologia,
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            eszkozok.megjegyzes AS megjegyzes,
            raktarak.nev AS raktar,
            portok.id AS portid,
            portok.port AS portnev,
            switchportok.eszkoz AS swpeszk,
            hibas,
            (SELECT nev FROM beepitesek WHERE eszkoz = swpeszk ORDER BY beepitesek.id DESC LIMIT 1) AS switch,
            (SELECT ipcimek.ipcim AS ip FROM ipcimek INNER JOIN beepitesek ON ipcimek.id = beepitesek.ipcim WHERE beepitesek.eszkoz = swpeszk ORDER BY beepitesek.id DESC LIMIT 1) AS switchip
        FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN bovitomodellek ON bovitomodellek.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN atviteliszabvanyok ON bovitomodellek.transzpszabvany = atviteliszabvanyok.id
                LEFT JOIN fizikairetegek ON bovitomodellek.fizikaireteg = fizikairetegek.id
                LEFT JOIN portok ON beepitesek.switchport = portok.id
                LEFT JOIN switchportok ON portok.id = switchportok.port
                LEFT JOIN beepitesek akteszk ON akteszk.eszkoz = switchportok.eszkoz
                LEFT JOIN rackszekrenyek ON akteszk.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON akteszk.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
        WHERE $where $csoportwhere
        ORDER BY switchportok.eszkoz, portok.id, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $tipus = 'bovitok';
    $oszlopok = array(
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Eszköztípus', 'tipus' => 's'),
        array('nev' => 'Technológia', 'tipus' => 's'),
        array('nev' => 'Szabvány', 'tipus' => 's'),
        array('nev' => 'Raktár', 'tipus' => 's'),
        array('nev' => 'Beépítési hely', 'tipus' => 's'),
        array('nev' => 'Megjegyzés', 'tipus' => 's')
    );

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/bovitomodul?action=addnew'">Új bővítomodul</button><?php
    }

    ?><div class="PrintArea">
        <div class="oldalcim">Bővítőmodulok <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
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
                        $kattinthatolink = './bovitomodul/' . $eszkoz['id'];
                        ?><tr class='trlink<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?>'>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                            <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['technologia']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['transzportszabvany']?></a></td>
                            <td><a href="<?=$kattinthatolink?>">Beépítve</a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['switch']?> (<?=$eszkoz['switchip']?>) - <?=$eszkoz['portnev']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?></a></td><?php
                            if($csoportir)
                            {
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "bovitomodul");
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    $kattinthatolink = './bovitomodul/' . $eszkoz['id'];
                    ?><tr class='trlink kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?><?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tipus']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['technologia']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['transzportszabvany']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['raktar']?></a></td>
                        <td><a href="<?=$kattinthatolink?>">Raktárban</a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?></a></td><?php
                        if($csoportir)
                        {
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "bovitomodul");
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}