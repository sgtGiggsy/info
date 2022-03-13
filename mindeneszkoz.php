<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim
        FROM
            eszkozok INNER JOIN
                modellek ON eszkozok.modell = modellek.id INNER JOIN
                gyartok ON modellek.gyarto = gyartok.id INNER JOIN
                eszkoztipusok ON modellek.tipus = eszkoztipusok.id LEFT JOIN
                beepitesek ON beepitesek.eszkoz = eszkozok.id LEFT JOIN
                rackszekrenyek ON beepitesek.rack = rackszekrenyek.id LEFT JOIN
                helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id LEFT JOIN
                epuletek ON helyisegek.epulet = epuletek.id LEFT JOIN
                ipcimek ON beepitesek.ipcim = ipcimek.id LEFT JOIN
                alakulatok ON eszkozok.tulajdonos = alakulatok.id
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    ?><div class="oldalcim">Minden eszköz</div>
    <table id="eszkozok">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', 'eszkozok')">Sorszám</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">IP cím</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Eszköznév</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Gyártó</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'eszkozok')">Modell</th>
                <th class="tsorth" onclick="sortTable(6, 's', 'eszkozok')">Sorozatszám</th>
                <th class="tsorth" onclick="sortTable(7, 's', 'eszkozok')">Eszköztípus</th>
                <th class="tsorth" onclick="sortTable(8, 's', 'eszkozok')">Épület</th>
                <th class="tsorth" onclick="sortTable(9, 's', 'eszkozok')">Helyiseg</th>
                <th class="tsorth" onclick="sortTable(10, 's', 'eszkozok')">Rackszekrény</th>
                <th class="tsorth" onclick="sortTable(11, 's', 'eszkozok')">Tulajdonos</th>
                <th class="tsorth" onclick="sortTable(12, 's', 'eszkozok')">Beépítve</th>
                <th class="tsorth" onclick="sortTable(13, 's', 'eszkozok')">Kiépítve</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($mindeneszkoz as $eszkoz)
            {
                ?><tr <?=(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])) ? "style='font-weight: normal'" : "" ?> class='kattinthatotr' data-href='./eszkoz/<?=$eszkoz['id']?>'>
                    <td><?=$eszkoz['id']?></td>
                    <td><?=$eszkoz['ipcim']?></td>
                    <td><?=$eszkoz['beepitesinev']?></td>
                    <td><?=$eszkoz['gyarto']?></td>
                    <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                    <td><?=$eszkoz['sorozatszam']?></td>
                    <td><?=$eszkoz['tipus']?></td>
                    <td><?=$eszkoz['epuletszam']?> (<?=$eszkoz['epuletnev']?>)</td>
                    <td><?=$eszkoz['helyisegszam']?> (<?=$eszkoz['helyisegnev']?>)</td>
                    <td><?=$eszkoz['rack']?></td>
                    <td><?=$eszkoz['tulajdonos']?></td>
                    <td nowrap><?=$eszkoz['beepitesideje']?></td>
                    <td nowrap><?=$eszkoz['kiepitesideje']?></td>
                </tr><?php
            }
        ?></tbody>
    </table><?php
}