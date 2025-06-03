<?php
$lista = mySQLConnect("SELECT felhasznalok.id as felhasznaloid, felhasznalok.nev AS nev, felhasznalonev, email, elsobelepes, osztaly, telefon, beosztas, szervezetek.nev AS szervezet, descript
    FROM felhasznalok
        LEFT JOIN szervezetek ON felhasznalok.szervezet = szervezetek.id
    $where $csoportwhere
    LIMIT $start, $megjelenit;");

?><table class="fulltable">
    <thead>
        <tr>
            <th>Név</th>
            <th>Usernév</th>
            <th>Emailcím</th>
            <th>Telefon</th>
            <th>Szervezet</th>
            <th>Részleg</th>
            <th>Beosztás</th>
            <th>Első bejelentkezés</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($lista as $x)
    {
        $kattinthatolink = $RootPath . '/felhasznalo/' . $x['felhasznaloid'];
        ?><tr class="trlink">
            <td><a href="<?=$kattinthatolink?>"><?=$x['nev']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$x['felhasznalonev']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$x['email']?></a></td>
            <td nowrap><a href="<?=$kattinthatolink?>"><?=$x['telefon']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$x['szervezet']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$x['osztaly']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$x['beosztas']?></a></td>
            <td><a href="<?=$kattinthatolink?>"><?=$x['elsobelepes']?></a></td>
        </tr><?php
    }
    ?></tbody>
</table>