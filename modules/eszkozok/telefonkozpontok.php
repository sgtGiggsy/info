<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $telefonkozpontok = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            telefonkozpontok.nev AS kozpontnev,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            beepitesek.id AS beepid,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN telefonkozpontok ON telefonkozpontok.eszkoz = eszkozok.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
        WHERE modellek.tipus = 40
        GROUP BY eszkozok.id
        ORDER BY epuletek.szam + 0, helyisegszam + 0, helyisegnev;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonkozpont?action=addnew'">Új telefonközpont</button><?php
    }
    ?><div class="oldalcim">Telefonközpontok</div>

    <?php
    $tipus = "telefonkozpontok";
    ?><div class="PrintArea">
        <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 's', '<?=$tipus?>')">Eszköznév</th>
                <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>')">Gyártó</th>
                <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>')">Modell</th>
                <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>')">Sorozatszám</th>
                <th class="tsorth" onclick="sortTable(4, 's', '<?=$tipus?>')">Épület</th>
                <th class="tsorth" onclick="sortTable(5, 's', '<?=$tipus?>')">Helyiség</th><?php
                if($csoportir)
                {
                    ?><th class="tsorth" onclick="sortTable(6, 's', '<?=$tipus?>')">Megjegyzés</th>
                    <th class="dontprint"></th>
                    <th class="dontprint"></th>
                    <th class="dontprint"></th><?php
                }
            ?></tr>
        </thead>
        <tbody><?php
            foreach($telefonkozpontok as $kozpont)
            {
                ?><tr <?=(!($kozpont['beepitesideje'] && !$kozpont['kiepitesideje'])) ? "style='font-weight: normal'" : "" ?> class='kattinthatotr' data-href='./telefonkozpont/<?=$kozpont['id']?>'>
                    <td><?=$kozpont['beepitesinev']?></td>
                    <td nowrap><?=$kozpont['gyarto']?></td>
                    <td nowrap><?=$kozpont['modell']?><?=$kozpont['varians']?></td>
                    <td><?=$kozpont['sorozatszam']?></td>
                    <td><?=$kozpont['epuletszam']?> <?=($kozpont['epuletnev']) ? "(" . $kozpont['epuletnev'] . ")" : "" ?></td>
                    <td><?=$kozpont['helyisegszam']?> <?=($kozpont['helyisegnev']) ? "(" . $kozpont['helyisegnev'] . ")" : "" ?></td><?php
                    if($csoportir)
                    {
                        ?><td><?=$kozpont['megjegyzes']?></td><?php
                        szerkSor($kozpont['beepid'], $kozpont['id'], "telefonkozpont");
                    }
                ?></tr><?php
            }
            ?></tbody>
        </table>
    </div><?php
}