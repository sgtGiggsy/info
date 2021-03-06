<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] <= 10)
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $where = null;
    if(isset($felhid))
    {
        $where = "WHERE felhasznalo = $felhid";
    }
    $lista = mySQLConnect("SELECT bejelentkezesek.id AS id, nev, ipcim, bongeszo, bongeszoverzio, oprendszer, oprendszerverzio, oprendszerarch, timestamp
            FROM bejelentkezesek
                INNER JOIN felhasznalok ON bejelentkezesek.felhasznalo = felhasznalok.id
            $where
            ORDER BY bejelentkezesek.id DESC");
    ?><div class='oldalcim'>Bejelentkezések</div>
    <div>
    <table id='bejelentkezesek'>
        <thead style="font-size: 1.3em; font-weight: bold">
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', 'bejelentkezesek')">ID</th>
                <?php if(!isset($felhid))
                {
                    ?><th class="tsorth" onclick="sortTable(1, 's', 'bejelentkezesek')">Felhasználó</th><?php
                }
                else { echo "<th></th>"; }
                ?><th class="tsorth" onclick="sortTable(2, 's', 'bejelentkezesek')">IP cím</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'bejelentkezesek')">Oprendszer</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'bejelentkezesek')">Böngésző</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'bejelentkezesek')">Böngésző verzió</th>
                <th class="tsorth" onclick="sortTable(6, 's', 'bejelentkezesek')">Idő</th>
            </tr>
        </thead>
        <tbody><?php
        foreach($lista as $x)
        {
            ?><tr>
                <td><?=$x['id']?></td>
                <?php if(!isset($felhid))
                {
                    ?><td><?=$x['nev']?></td><?php
                }
                else { echo "<td></td>"; }
                ?><td><?=$x['ipcim']?></td>
                <td><?=$x['oprendszer'] . " " . $x['oprendszerverzio'] . " " . $x['oprendszerarch']?></td>
                <td><?=$x['bongeszo']?></td>
                <td><?=$x['bongeszoverzio']?></td>
                <td><?=$x['timestamp']?></td>
            </tr><?php
        }
        ?></tbody>
        </table>
    </div><?php
}
?>