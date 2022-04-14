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
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=telefonkozpont'">Új telefonközpont</button><?php
    }
    ?><div class="oldalcim">Telefonközpontok</div>

    <?php
    $tipus = "nyomtatok";
    ?><table id="<?=$tipus?>">
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, 's', '<?=$tipus?>')">Eszköznév</th>
            <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>')">Gyártó</th>
            <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>')">Modell</th>
            <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>')">Sorozatszám</th>
            <th class="tsorth" onclick="sortTable(4, 's', '<?=$tipus?>')">Épület</th>
            <th class="tsorth" onclick="sortTable(5, 's', '<?=$tipus?>')">Helyiség</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody><?php
        foreach($telefonkozpontok as $kozpont)
        {
            $kozpontid = $kozpont['id'];
            if($kozpont['beepid'])
            {
                $beepid = "/" . $kozpont['beepid'];
            }
            else
            {
                $beepid = "?eszkoz=$kozpontid";
            }
            
            
            ?><tr <?=(!($kozpont['beepitesideje'] && !$kozpont['kiepitesideje'])) ? "style='font-weight: normal'" : "" ?> class='kattinthatotr' data-href='./telefonkozpont/<?=$kozpont['id']?>'>
                <td><?=$kozpont['beepitesinev']?></td>
                <td nowrap><?=$kozpont['gyarto']?></td>
                <td nowrap><?=$kozpont['modell']?><?=$kozpont['varians']?></td>
                <td><?=$kozpont['sorozatszam']?></td>
                <td><?=$kozpont['epuletszam']?> <?=($kozpont['epuletnev']) ? "(" . $kozpont['epuletnev'] . ")" : "" ?></td>
                <td><?=$kozpont['helyisegszam']?> <?=($kozpont['helyisegnev']) ? "(" . $kozpont['helyisegnev'] . ")" : "" ?></td>
                <td><?=($csoportir) ? "<a href='$RootPath/beepites$beepid'><img src='$RootPath/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a>" : "" ?></td>
                <td><?=($csoportir) ? "<a href='$RootPath/eszkozszerkeszt/$kozpontid?tipus=telefonkozpont'><img src='$RootPath/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a>" : "" ?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}