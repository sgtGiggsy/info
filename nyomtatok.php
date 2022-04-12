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
            modellek.tipus AS tipusid,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            beepitesek.id AS beepid,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            szines,
            scanner,
            fax,
            admin,
            pass,
            defadmin,
            defpass,
            maxmeret
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN nyomtatomodellek ON nyomtatomodellek.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
        WHERE modellek.tipus = 12
        GROUP BY eszkozok.id
        ORDER BY epuletek.szam + 0, helyisegszam + 0, helyisegnev;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=nyomtato'">Új nyomtató</button><?php
    }
    ?><div class="oldalcim">Nyomtatók</div>
    <input type="text" id="filter" onkeyup="filterTable('filter', 'nyomtatok', 2)" placeholder="Szűrés" title="Szűrés">
    
    <?php
    $tipus = "nyomtatok";
    ?><table id="<?=$tipus?>">
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</th>
            <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</th>
            <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</th>
            <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</th>
            <th class="tsorth" onclick="sortTable(4, 's', '<?=$tipus?>')">Max méret</th>
            <th class="tsorth" onclick="sortTable(5, 's', '<?=$tipus?>')">Színek</th>
            <th class="tsorth" onclick="sortTable(6, 's', '<?=$tipus?>')">Szkenner</th>
            <th class="tsorth" onclick="sortTable(7, 's', '<?=$tipus?>')">Fax</th>
            <th class="tsorth" onclick="sortTable(8, 's', '<?=$tipus?>')">Sorozatszám</th>
            <th class="tsorth" onclick="sortTable(9, 's', '<?=$tipus?>')">Épület</th>
            <th class="tsorth" onclick="sortTable(10, 's', '<?=$tipus?>')">Helyiség</th><?php
            if($csoportir)
            {
                ?><th class="tsorth" onclick="sortTable(11, 's', '<?=$tipus?>')">Admin user</th>
                <th class="tsorth" onclick="sortTable(12, 's', '<?=$tipus?>')">Jelszó</th>
                <th class="tsorth" onclick="sortTable(13, 's', '<?=$tipus?>')">Megjegyzés</th><?php
            }
            ?>
            <th></th>
        </tr>
    </thead>
    <tbody><?php
        foreach($mindeneszkoz as $eszkoz)
        {
            $eszkid = $eszkoz['id'];
            if($eszkoz['beepid'])
            {
                $beepid = "/" . $eszkoz['beepid'];
            }
            else
            {
                $beepid = "?eszkoz=$eszkid";
            }
            
            $eszktip = eszkozTipusValaszto($eszkoz['tipusid']);
            switch($eszkoz['maxmeret'])
            {
                case 1: $maxmeret = "A4"; break;
                case 2: $maxmeret = "A3"; break;
                case 3: $maxmeret = "A2"; break;
                case 4: $maxmeret = "A1"; break;
                case 5: $maxmeret = "A0"; break;
                default: $maxmeret = "A4";
            }
            switch($eszkoz['szines'])
            {
                case 1: $szines = "Színes"; break;
                default: $szines = "Fekete-Fehér";
            }
            switch($eszkoz['scanner'])
            {
                case 1: $scanner = "Van"; break;
                default: $scanner = "Nincs";
            }
            switch($eszkoz['fax'])
            {
                case 1: $fax = "Van, beépített"; break;
                case 2: $fax = "Alkalmas, modullal"; break;
                default: $fax = "Nincs";
            }
            ?><tr <?=(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])) ? "style='font-weight: normal'" : "" ?> class='kattinthatotr' data-href='./nyomtato/<?=$eszkoz['id']?>'>
                <td><?=$eszkoz['ipcim']?></td>
                <td><?=$eszkoz['beepitesinev']?></td>
                <td nowrap><?=$eszkoz['gyarto']?></td>
                <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                <td><?=$maxmeret?></td>
                <td><?=$szines?></td>
                <td><?=$scanner?></td>
                <td><?=$fax?></td>
                <td><?=$eszkoz['sorozatszam']?></td>
                <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td><?php
                if($csoportir)
                {
                    ?><td><?=($eszkoz['admin']) ? $eszkoz['admin'] : $eszkoz['defadmin'] ?></td>
                    <td><?=($eszkoz['pass']) ? $eszkoz['pass'] : $eszkoz['defpass'] ?></td>
                    <td><?=$eszkoz['megjegyzes']?></td><?php
                }
                ?>
                <td><?=($csoportir) ? "<a href='$RootPath/beepites$beepid'><img src='$RootPath/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a>" : "" ?></td>
                <td><?=($csoportir) ? "<a href='$RootPath/eszkozszerkeszt/$eszkid?tipus=$eszktip'><img src='$RootPath/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a>" : "" ?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}