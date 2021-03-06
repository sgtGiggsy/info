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
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
        GROUP BY eszkozok.id
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    ?><div class="oldalcim">Minden eszköz</div><?php
    $zar = false;
    foreach($mindeneszkoz as $eszkoz)
    {
        if(@$tipus != $eszkoz['tipus'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $tipus = $eszkoz['tipus']
            ?><h1 style="text-transform: capitalize;"><?=$tipus?></h1>
            <table id="<?=$tipus?>">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</th>
                    <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</th>
                    <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</th>
                    <th class="tsorth" onclick="sortTable(4, 's', '<?=$tipus?>')">Sorozatszám</th>
                    <th class="tsorth" onclick="sortTable(5, 's', '<?=$tipus?>')">Épület</th>
                    <th class="tsorth" onclick="sortTable(6, 's', '<?=$tipus?>')">Helyiség</th>
                    <th class="tsorth" onclick="sortTable(7, 's', '<?=$tipus?>')">Rack</th>
                    <th class="tsorth" onclick="sortTable(8, 's', '<?=$tipus?>')">Tulajdonos</th>
                    <th class="tsorth" onclick="sortTable(9, 's', '<?=$tipus?>')">Beépítve</th>
                    <th class="tsorth" onclick="sortTable(10, 's', '<?=$tipus?>')">Kiépítve</th><?php
                    if($csoportir)
                    {
                        ?><th class="tsorth" onclick="sortTable(11, 's', '<?=$tipus?>')">Megjegyzés</th>
                        <th></th><?php
                    }
                ?></tr>
            </thead>
            <tbody><?php
            $zar = true;
        }
        

        $eszkid = $eszkoz['id'];
        if($eszkoz['tipusid'] < 11)
        {
            $eszktip = "aktiveszkoz";
        }
        else
        {
            $eszktip = eszkozTipusValaszto($eszkoz['tipusid']);
        }

        ?><tr <?=(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])) ? "style='font-weight: normal'" : "" ?> class='kattinthatotr' data-href='./<?=$eszktip?>/<?=$eszkoz['id']?>'>
            <td><?=$eszkoz['ipcim']?></td>
            <td><?=$eszkoz['beepitesinev']?></td>
            <td><?=$eszkoz['gyarto']?></td>
            <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
            <td><?=$eszkoz['sorozatszam']?></td>
            <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
            <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
            <td><?=$eszkoz['rack']?></td>
            <td><?=$eszkoz['tulajdonos']?></td>
            <td nowrap><?=timeStampToDate($eszkoz['beepitesideje'])?></td>
            <td nowrap><?=timeStampToDate($eszkoz['kiepitesideje'])?></td><?php
            if($csoportir)
            {
                ?><td><?=$eszkoz['megjegyzes']?></td>
                <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td><?php
            }
        ?></tr><?php
    }
    ?></tbody>
    </table><?php
}