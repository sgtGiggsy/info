<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] <= 10)
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $lista = mySQLConnect("SELECT * FROM failedlogins ORDER BY id DESC");
    ?><div class='oldalcim'>Bejelentkezési hibák</div>
    <div>
    <table id='bejelentkezesihibak'>
        <thead style="font-size: 1.3em; font-weight: bold">
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', 'bejelentkezesihibak')">ID</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'bejelentkezesihibak')">Felhasználónév</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'bejelentkezesihibak')">IP cím</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'bejelentkezesihibak')">Próbálkozás ideje</th>
            </tr>
        </thead>
        <tbody><?php
        foreach($lista as $x)
        {
            ?><tr>
                <td><?=$x['id']?></td>
                <td><?=$x['felhasznalonev']?></td>
                <td><?=$x['ipcim']?></td>
                <td><?=$x['probalkozasideje']?></td>
            </tr><?php
        }
        ?></tbody>
        </table>
    </div><?php
}
?>
</body>
</html>