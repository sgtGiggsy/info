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
    ?>
    <!-- DATALISTEK -->
    <datalist id="maxmeret">
        <option>A4</option>
        <option>A3</option>
        <option>A2</option>
        <option>A1</option>
        <option>A0</option>
    </datalist>

    <datalist id="scanner">
        <option>Van</option>
        <option>Nincs</option>
    </datalist>

    <datalist id="szinek">
        <option>Színes</option>
        <option>Fekete-Fehér</option>
    </datalist>

    <datalist id="fax">
        <option>Van, beépített</option>
        <option>Alkalmas, modullal</option>
        <option>Nincs</option>
    </datalist>
    
    <div class="oldalcim">Nyomtatók</div><?php
    $tipus = "nyomtatok";
    ?><table id="<?=$tipus?>">
    <thead>
        <tr>
            <th class="tsorth"><p><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="IP cím" title="IP cím"><br><span onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</span></p></th>
            <th class="tsorth"><p><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Eszköznév" title="Eszköznév"><br><span onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</span></p></th>
            <th class="tsorth"><p><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Gyártó" title="Gyártó"><br><span onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</span></p></th>
            <th class="tsorth"><p><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Modell" title="Modell"><br><span onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</span></p></th>
            <th class="tsorth"><p><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="Méret" title="Méret" list="maxmeret"><br><span onclick="sortTable(4, 's', '<?=$tipus?>')">Méret</span></p></th>
            <th class="tsorth"><p><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="Színek" title="Színek" list="szinek"><br><span onclick="sortTable(5, 's', '<?=$tipus?>')">Színek</span></p></th>
            <th class="tsorth"><p><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Scanner" title="Scanner" list="scanner"><br><span onclick="sortTable(6, 's', '<?=$tipus?>')">Szkenner</span></p></th>
            <th class="tsorth"><p><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Fax" title="Fax" list="fax"><br><span onclick="sortTable(7, 's', '<?=$tipus?>')">Fax</span></p></th>
            <th class="tsorth"><p><input type="text" id="f8" onkeyup="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Sorozatszám" title="Sorozatszám"><br><span onclick="sortTable(8, 's', '<?=$tipus?>')">Sorozatszám</span></p></th>
            <th class="tsorth"><p><input type="text" id="f9" onkeyup="filterTable('f9', '<?=$tipus?>', 9)" placeholder="Épület" title="Épület"><br><span onclick="sortTable(9, 's', '<?=$tipus?>')">Épület</span></p></th>
            <th class="tsorth"><p><input type="text" id="f10" onkeyup="filterTable('f10', '<?=$tipus?>', 10)" placeholder="Helyiség" title="Helyiség"><br><span onclick="sortTable(10, 's', '<?=$tipus?>')">Helyiség</span></p></th><?php
            if($csoportir)
            {
                ?><th class="tsorth" onclick="sortTable(11, 's', '<?=$tipus?>')">Admin</th>
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