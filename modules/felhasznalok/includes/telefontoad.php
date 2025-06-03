<?php

$joinmode = 'LEFT';
if($_GET['telefontoad'] == 'notnull')
    $joinmode = 'INNER';

$lista = mySQLConnect("SELECT felhasznalok.id as felhasznaloid, telefonkonyvfelhasznalok.nev AS nev,
        felhasznalonev, osztaly, telefon, beosztas, szervezetek.nev AS szervezet, descript AS adrendf,
        rendfokozatok.nev AS tkonyvrendf, belsoszam, belsoszam2, telefonkonyvcsoportok.nev AS tkonyvosztaly,
        telefonkonyvbeosztasok.nev AS tkonyvbeo, felhasznalok.nev AS adnev
    FROM telefonkonyvfelhasznalok
        INNER JOIN rendfokozatok ON telefonkonyvfelhasznalok.rendfokozat = rendfokozatok.id
        INNER JOIN telefonkonyvbeosztasok ON telefonkonyvbeosztasok.felhid = telefonkonyvfelhasznalok.id
        INNER JOIN telefonkonyvcsoportok ON telefonkonyvbeosztasok.csoport = telefonkonyvcsoportok.id
        $joinmode JOIN felhasznalok ON telefonkonyvfelhasznalok.felhasznalo = felhasznalok.id
        $joinmode JOIN szervezetek ON felhasznalok.szervezet = szervezetek.id
    ORDER BY telefonkonyvfelhasznalok.nev ASC
    LIMIT $start, $megjelenit;");

?><table class="fulltable" id='osszevetotabla'>
    <thead>
        <tr>
            <th><a onclick="copyTableToClipboard('osszevetotabla')" style="cursor: pointer"><?=$icons['clipboard']?></a></th>
            <th>Tkönyv Név</th>
            <th>Tkönyv rendf.</th>
            <th>Tkönyv Telefon</th>
            <th>Tkönyv Osztály</th>
            <th>Tkönyv Beosztás</th>
            <th>Usernév</th>
            <th>AD Név</th>
            <th>AD Rendfokozat</th>
            <th>AD Telefon</th>
            <th>AD Osztály</th>
            <th>AD Beosztás</th>
            <th>Szervezet</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($lista as $x)
    {
        $kattinthatolink = $RootPath . '/felhasznalo/' . $x['felhasznaloid'];
        if(!rendOssze($x['tkonyvrendf'], $x['adrendf']) || !telszamOsszevet($x['telefon'], $x['belsoszam'], $x['belsoszam2']))
        {
            ?><tr class="trlink">
                <td></td>
                <td nowrap><a href="<?=$kattinthatolink?>"><?=$x['nev']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['tkonyvrendf']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['belsoszam']?><?=($x['belsoszam2']) ? "; " . $x['belsoszam2'] : "" ?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['tkonyvosztaly']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['tkonyvbeo']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['felhasznalonev']?></a></td>
                <td nowrap><a href="<?=$kattinthatolink?>"><?=$x['adnev']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['adrendf']?></a></td>
                <td nowrap><a href="<?=$kattinthatolink?>"><?=$x['telefon']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['osztaly']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['beosztas']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$x['szervezet']?></a></td>
            </tr><?php
        }
    }
    ?></tbody>
</table>