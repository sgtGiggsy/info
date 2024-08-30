<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => null,                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "tulajdonos"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);

        $where = "WHERE $csoportwhere";
    }
    
    $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id,
            rackszekrenyek.nev AS rack,
            gyartok.nev AS gyarto,
            unitszam,
            helyisegnev,
            helyisegszam,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam
        FROM rackszekrenyek
            LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
        $where
        ORDER BY epuletszam, helyisegszam, helyisegnev, rack;");

    $oszlopokrack = array(
        array('nev' => 'Rackszekrény', 'tipus' => 's'),
        array('nev' => 'Gyártó', 'tipus' => 's'),
        array('nev' => 'Unitszám', 'tipus' => 's'),
        array('nev' => 'Épület', 'tipus' => 's'),
        array('nev' => 'Helyiség', 'tipus' => 's'),
        array('nev' => '', 'tipus' => 's')
    );

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/rack?action=addnew'">Új rackszekrény</button><?php
    }
    
    ?><div class="oldalcim">Rackszekrények</div>
    <table id="rackek">
        <thead>
            <tr><?php
                sortTableHeader($oszlopokrack, "rackek");
            ?></tr>
        </thead>
        <tbody><?php
            foreach($rackek as $rack)
            {
                $rackid = $rack['id'];
                $kattinthatolink = $RootPath . "/rack/" . $rackid;
                ?><tr class="trlink">
                    <td><a href="<?=$kattinthatolink?>"><?=$rack['rack']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$rack['gyarto']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$rack['unitszam']?></a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$rack['epuletszam']?> (<?=$rack['epuletnev']?>)</a></td>
                    <td><a href="<?=$kattinthatolink?>"><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</a></td>
                    <td><?=($csoportir) ? "<a href='$RootPath/rack/$rackid?action=edit'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}