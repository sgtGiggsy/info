<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
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

    $telefonkozpontok = mySQLConnect("SELECT
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
        WHERE modellek.tipus = 40 $csoportwhere
        GROUP BY eszkozok.id
        ORDER BY epuletek.szam + 0, helyisegszam + 0, helyisegnev;");
    $tipus = "telefonkozpontok";
    $oszlopok = array(
        array('nev' => 'Eszköznév', 'tipus' => 's'),
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Modell', 'tipus' => 's'),
        array('nev' => 'Sorozatszám', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's')
    );
    if($csoportir)
    {
        $oszlopok[] = array('nev' => 'Megjegyzés', 'tipus' => 's');
    }

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkozpont?action=addnew'">Új telefonközpont</button><?php
    }
    ?><div class="oldalcim">Telefonközpontok</div>

    <div class="PrintArea">
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
            foreach($telefonkozpontok as $kozpont)
            {
                $kattinthatolink = './telefonkozpont/' . $kozpont['id'];
                ?><tr class='trlink'>
                    <td><a href="<?=$kattinthatolink?>"><?=$kozpont['beepitesinev']?></a></td>
                    <td nowrap><a href="<?=$kattinthatolink?>"><?=$kozpont['gyarto']?></a></td>
                    <td nowrap><a href="<?=$kattinthatolink?>"><?=$kozpont['modell']?><?=$kozpont['varians']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$kozpont['sorozatszam']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$kozpont['epuletszam']?> <?=($kozpont['epuletnev']) ? "(" . $kozpont['epuletnev'] . ")" : "" ?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$kozpont['helyisegszam']?> <?=($kozpont['helyisegnev']) ? "(" . $kozpont['helyisegnev'] . ")" : "" ?></a></td><?php
                    if($csoportir)
                    {
                        ?><td><a href="<?=$kattinthatolink?>"><?=$kozpont['megjegyzes']?></a></td><?php
                        szerkSor($kozpont['beepid'], $kozpont['id'], "telefonkozpont");
                    }
                ?></tr><?php
            }
            ?></tbody>
        </table>
    </div><?php
}