<?php

if(!isset($felhid) && isset($csoportolvas) && !$csoportolvas)
{
    getPermissionError();
}
else
{
    $where = null;
    if(isset($felhid))
    {
        $where = "WHERE felhasznalo = $felhid";
    }
    else
    {
        ?><div class='oldalcim'>Bejelentkezések</div><div class="contentcenter"><?php
    }

    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "szervezet",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => "felhasznalok",                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => null,           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
        if(!$where)
        {
            $where = "WHERE ";
        }
        else
        {
            $csoportwhere = "AND $csoportwhere";
        }
    }

    $lista = mySQLConnect("SELECT bejelentkezesek.id AS id, nev, ipcim, bongeszo, bongeszoverzio, oprendszer, oprendszerverzio, oprendszerarch, timestamp, gepnev, felbontas
            FROM bejelentkezesek
                INNER JOIN felhasznalok ON bejelentkezesek.felhasznalo = felhasznalok.id
            $where $csoportwhere
            ORDER BY bejelentkezesek.id DESC");
    ?><table id='bejelentkezesek' style="max-width: unset;">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 's', 'bejelentkezesek')">Idő</th>
                <?php if(!isset($felhid))
                {
                    ?><th class="tsorth" onclick="sortTable(1, 's', 'bejelentkezesek')">Felhasználó</th><?php
                }
                else { echo "<th></th>"; }
                ?><th class="tsorth" onclick="sortTable(2, 's', 'bejelentkezesek')">IP cím</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'bejelentkezesek')">Gépnév</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'bejelentkezesek')">Oprendszer</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'bejelentkezesek')">Böngésző</th>
                <th class="tsorth" onclick="sortTable(6, 's', 'bejelentkezesek')">Böngésző verzió</th>
                <th class="tsorth" onclick="sortTable(7, 's', 'bejelentkezesek')">Képernyő felbontás</th>
            </tr>
        </thead>
        <tbody><?php
        foreach($lista as $x)
        {
            ?><tr>
                <td><?=$x['timestamp']?></td>
                <?php if(!isset($felhid))
                {
                    ?><td><?=$x['nev']?></td><?php
                }
                else { echo "<td></td>"; }
                ?><td><?=$x['ipcim']?></td>
                <td><?=$x['gepnev']?></td>
                <td><?=$x['oprendszer'] . " " . $x['oprendszerverzio'] . " " . $x['oprendszerarch']?></td>
                <td><?=$x['bongeszo']?></td>
                <td><?=$x['bongeszoverzio']?></td>
                <td><?=$x['felbontas']?></td>
            </tr><?php
        }
        ?></tbody>
        </table><?php
    if(!isset($felhid))
    {
        ?></div><?php
    }
}
?>