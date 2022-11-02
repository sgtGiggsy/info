<?php
if(isset($mindolvas) && $mindolvas)
{
    getPermissionError();
}
else
{
    $lista = mySQLConnect("SELECT * FROM failedlogins ORDER BY id DESC");
    ?><div class='oldalcim'>Bejelentkezési hibák</div>
    <div>
    <table id='bejelentkezesihibak'>
        <thead>
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