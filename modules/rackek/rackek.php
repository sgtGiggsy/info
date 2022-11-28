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
            'tipus' => "telephely",                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
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

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/rack?action=addnew'">Új rackszekrény</button><?php
    }
    
    ?><div class="oldalcim">Rackszekrények</div>
    <table id="rackek">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', 'rackek')">Sorszám</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'rackek')">Rackszekrény</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'rackek')">Gyártó</th>
                <th class="tsorth" onclick="sortTable(3, 'i', 'rackek')">Unitszám</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'rackek')">Helyiség</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'rackek')">Épület</th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php
            foreach($rackek as $rack)
            {
                $rackid = $rack['id'];
                ?><tr class='kattinthatotr' data-href='./rack/<?=$rack['id']?>'>
                    <td><?=$rack['id']?></td>
                    <td><?=$rack['rack']?></td>
                    <td><?=$rack['gyarto']?></td>
                    <td><?=$rack['unitszam']?></td>
                    <td><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</td>
                    <td><?=$rack['epuletszam']?> (<?=$rack['epuletnev']?>)</td>
                    <td><?=($csoportir) ? "<a href='$RootPath/rack/$rackid?action=edit'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}