<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id,
            rackszekrenyek.nev AS rack,
            gyartok.nev AS gyarto,
            unitszam,
            helyisegnev,
            helyisegszam,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam
        FROM
            rackszekrenyek INNER JOIN
                helyisegek ON rackszekrenyek.helyiseg = helyisegek.id LEFT JOIN
                epuletek ON helyisegek.epulet = epuletek.id LEFT JOIN
                gyartok ON rackszekrenyek.gyarto = gyartok.id
        ORDER BY epuletszam, helyisegszam, helyisegnev, rack;");
    
    ?><?=($mindir) ? "<a href='$RootPath/rackszerkeszt'>Új rackszekrény hozzáadása</a>" : "" ?>
    <div class="oldalcim">Rackszekrények</div>
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
                ?><tr class='kattinthatotr' data-href='./rack/<?=$rack['id']?>'>
                    <td><?=$rack['id']?></td>
                    <td><?=$rack['rack']?></td>
                    <td><?=$rack['gyarto']?></td>
                    <td><?=$rack['unitszam']?></td>
                    <td><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</td>
                    <td><?=$rack['epuletszam']?> (<?=$rack['epuletnev']?>)</td>
                    <td><a href='./rackszerkeszt/<?=$rack['id']?>'>Szerkesztés</a></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
    if($sajatir)
    {
        ?><a href='./rackszerkeszt'>Új rack felvitele</a><?php
    }
}