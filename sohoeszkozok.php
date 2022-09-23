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
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes
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
        WHERE modellek.tipus = 1 OR modellek.tipus = 2
        GROUP BY eszkozok.id
        ORDER BY epuletek.szam + 1, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $nembeepitett = array();
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=aktiv'">Új aktív eszköz</button><?php
    }

    ?><div class="oldalcim">Aktív eszközök</div>
    <table id="eszkozok">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 's', 'eszkozok')">IP cím</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">Eszköznév</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Gyártó</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Modell</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'eszkozok')">Sorozatszám</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'eszkozok')">Eszköztípus</th>
                <th class="tsorth" onclick="sortTable(6, 's', 'eszkozok')">Épület</th>
                <th class="tsorth" onclick="sortTable(7, 's', 'eszkozok')">Helyiség</th>
                <th class="tsorth" onclick="sortTable(8, 's', 'eszkozok')">Rack</th>
                <th class="tsorth" onclick="sortTable(9, 's', 'eszkozok')">Megjegyzés</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php

            foreach($mindeneszkoz as $eszkoz)
            {
                if(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje']))
                {
                    $nembeepitett[] = $eszkoz;
                }
                else
                {
                    $beepid = $eszkoz['beepid'];
                    $eszkid = $eszkoz['id'];
                    if($eszkoz['beepid'])
                    {
                        $beepid = "/" . $eszkoz['beepid'];
                    }
                    else
                    {
                        $beepid = "?eszkoz=$eszkid";
                    }

                    $eszktip = eszkozTipusValaszto($eszkoz['tipusid'])
                    ?><tr class='kattinthatotr' data-href='./aktiveszkoz/<?=$eszkoz['id']?>'>
                        <td><?=$eszkoz['ipcim']?></td>
                        <td><?=$eszkoz['beepitesinev']?></td>
                        <td><?=$eszkoz['gyarto']?></td>
                        <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['sorozatszam']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                        <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                        <td><?=$eszkoz['rack']?></td><?php
                        if($csoportir)
                        {
                            ?><td><?=$eszkoz['megjegyzes']?></td>
                            <td><a href='<?=$RootPath?>/beepites<?=$beepid?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a></td>
                            <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td>
                            <td><a href="telnet://<?=$eszkoz['ipcim']?>"><img src='<?=$RootPath?>/images/ssh.png' alt='Eszköz adminisztrálása' title='Eszköz adminisztrálása'/></a></td><?php
                        }
                    ?></tr><?php
                }
            }
            foreach($nembeepitett as $eszkoz)
            {
                $beepid = $eszkoz['beepid'];
                $eszkid = $eszkoz['id'];

                $eszkid = $eszkoz['id'];
                if($eszkoz['beepid'])
                {
                    $beepid = "/" . $eszkoz['beepid'];
                }
                else
                {
                    $beepid = "?eszkoz=$eszkid";
                }

                $eszktip = eszkozTipusValaszto($eszkoz['tipusid'])
                ?><tr style='font-weight: normal' class='kattinthatotr' data-href='./aktiveszkoz/<?=$eszkoz['id']?>'>
                    <td><?=$eszkoz['ipcim']?></td>
                    <td><?=$eszkoz['beepitesinev']?></td>
                    <td><?=$eszkoz['gyarto']?></td>
                    <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                    <td><?=$eszkoz['sorozatszam']?></td>
                    <td><?=$eszkoz['tipus']?></td>
                    <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                    <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                    <td><?=$eszkoz['rack']?></td><?php
                    if($csoportir)
                    {
                        ?><td><?=$eszkoz['megjegyzes']?></td>
                        <td><a href='<?=$RootPath?>/beepites<?=$beepid?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a></td>
                        <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td>
                        <td></td><?php
                    }
                ?></tr><?php
            }
        ?></tbody>
    </table><?php
}