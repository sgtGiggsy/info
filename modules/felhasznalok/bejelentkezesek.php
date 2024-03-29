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
        ?><div class='oldalcim'>Bejelentkezések</div><div class="contentcenter"><div><?php
    }

    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "alakulat",                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => "felhasznalok",                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => null,           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "alakulat"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
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

    $lista = mySQLConnect("SELECT bejelentkezesek.id AS id, nev, ipcim, bongeszo, bongeszoverzio, oprendszer, oprendszerverzio, oprendszerarch, timestamp
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
                <th class="tsorth" onclick="sortTable(3, 's', 'bejelentkezesek')">Oprendszer</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'bejelentkezesek')">Böngésző</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'bejelentkezesek')">Böngésző verzió</th>
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
                <td><?=$x['oprendszer'] . " " . $x['oprendszerverzio'] . " " . $x['oprendszerarch']?></td>
                <td><?=$x['bongeszo']?></td>
                <td><?=$x['bongeszoverzio']?></td>
            </tr><?php
        }
        ?></tbody>
        </table><?php
    if(!isset($felhid))
    {
        ?></div></div><?php
    }
}
?>