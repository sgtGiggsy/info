<?php
$tevekenysegek = mySQLConnect("SELECT felhasznalotevekenysegek.id AS id,
            felhasznalotevekenysegek.felhasznalo AS felhasznalo,
            felhasznalok.nev AS nev, felhasznalonev, ipcim, timestamp, menupont, almenu, elemid, params, gyanus
        FROM felhasznalotevekenysegek
        $where
        ORDER BY timestamp DESC
        LIMIT $start, $megjelenit;");

?><table id='tevekenysegek'>
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, 's', 'tevekenysegek')">Idő</th>
            <th class="tsorth" onclick="sortTable(1, 's', 'tevekenysegek')">Felhasználó</th>
            <th class="tsorth" onclick="sortTable(2, 's', 'tevekenysegek')">IP cím</th>
            <th class="tsorth" onclick="sortTable(3, 's', 'tevekenysegek')">Menüpont</th>
            <th class="tsorth" onclick="sortTable(4, 's', 'tevekenysegek')">Almenü</th>
            <th class="tsorth" onclick="sortTable(5, 's', 'tevekenysegek')">Elem azonosító</th>
            <th class="tsorth" onclick="sortTable(6, 's', 'tevekenysegek')">Címsori paraméterek</th>
            <th class="tsorth" onclick="sortTable(7, 's', 'tevekenysegek')">Gyanús tevékenységek</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($tevekenysegek as $x)
    {
        ?><tr>
            <td><?=$x['timestamp']?></td>
            <td><?=($x['felhasznalo']) ? $x['nev'] . " (" . $x['felhasznalonev'] . ")" :  "Látogató" ?></td>
            <td><?=$x['ipcim']?></td>
            <td><?=$x['menupont']?></td>
            <td><?=$x['almenu']?></td>
            <td><?=$x['elemid']?></td>
            <td><?php
                if($x['params'])
                {
                    foreach(get_object_vars(json_decode($x['params'])) as $key => $value)
                    {
                        ?><?=$key?><?=($value) ? ": " . $value : "" ?><br><?php
                    }
                }
            ?></td>
            <td><?=$x['gyanus']?></td>
        </tr><?php
    }
    ?></tbody>
</table>