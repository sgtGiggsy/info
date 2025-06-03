<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
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
                LEFT JOIN szervezetek ON eszkozok.tulajdonos = szervezetek.id
        ORDER BY epuletek.szam + 1, sorozatszam;");

    $tipus = 'szunetmentesek';
    $oszlopok = array(
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Teljesítmény', 'tipus' => 's'),
        array('nev' => 'Típus', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's'),
        array('nev' => 'Rack', 'tipus' => 's'),
        array('nev' => 'Tulajdonos', 'tipus' => 's'),
        array('nev' => 'Raktár', 'tipus' => 's'),
        array('nev' => 'Megjegyzés', 'tipus' => 's')
    );

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/szunetmentes?action=addnew'">Új szünetmentes</button><?php
    }

    ?><div class="PrintArea">
        <div class="oldalcim">Szünetmentesek</div>
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
                        $kattinthatolink = './szunetmentes/' . $eszkoz['id'];
                        ?><tr class='trlink<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?>'>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                            <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?> <?=$eszkoz['varians']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['teljesitmeny']?> Watt</a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=($eszkoz['tipus'] == 1) ? "Asztali" : "Rack-be építhető" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['rack']?></a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tulajdonos']?></a></td>
                            <td><a href="<?=$kattinthatolink?>">Beépítve</a></td>
                            <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                            if($csoportir)
                            {
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "szunetmentes");
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    $kattinthatolink = './szunetmentes/' . $eszkoz['id'];
                    ?><tr class='trlink kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>'>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['gyarto']?></a></td>
                        <td nowrap><a href="<?=$kattinthatolink?>"><?=$eszkoz['modell']?> <?=$eszkoz['varians']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['teljesitmeny']?> Watt</a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=($eszkoz['tipus'] == 1) ? "Asztali" : "Rack-be építhető" ?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['sorozatszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['tulajdonos']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['raktar']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></a></td><?php
                        if($csoportir)
                        {
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "szunetmentes");
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}