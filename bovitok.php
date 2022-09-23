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
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            bovitomodellek.fizikaireteg,
            bovitomodellek.transzpszabvany,
            atviteliszabvanyok.nev AS transzportszabvany,
            fizikairetegek.nev AS technologia,
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            eszkozok.megjegyzes AS megjegyzes,
            raktarak.nev AS raktar,
            portok.id AS portid,
            portok.port AS portnev,
            switchportok.eszkoz AS swpeszk,
            (SELECT nev FROM beepitesek WHERE eszkoz = swpeszk) AS switch,
            (SELECT ipcimek.ipcim AS ip FROM ipcimek INNER JOIN beepitesek ON ipcimek.id = beepitesek.ipcim WHERE beepitesek.eszkoz = swpeszk) AS switchip
        FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN bovitomodellek ON bovitomodellek.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN atviteliszabvanyok ON bovitomodellek.transzpszabvany = atviteliszabvanyok.id
                LEFT JOIN fizikairetegek ON bovitomodellek.fizikaireteg = fizikairetegek.id
                LEFT JOIN portok ON beepitesek.switchport = portok.id
                LEFT JOIN switchportok ON portok.id = switchportok.port
        WHERE modellek.tipus > 25 AND modellek.tipus < 31
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $nembeepitett = array();
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=bovitomodul'">Új bővítomodul</button><?php
    }

    ?><div class="oldalcim">Bővítőmodulok</div>
    <table id="eszkozok">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 's', 'eszkozok')">Gyártó</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'eszkozok')">Modell</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'eszkozok')">Sorozatszám</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'eszkozok')">Eszköztípus</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'eszkozok')">Technológia</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'eszkozok')">Szabvány</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'eszkozok')">Raktár</th>
                <th class="tsorth" onclick="sortTable(9, 's', 'eszkozok')">Beépítési hely</th>
                <th class="tsorth" onclick="sortTable(11, 's', 'eszkozok')">Megjegyzés</th>
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
                    ?><tr class='kattinthatotr' data-href='./<?=$eszktip?>/<?=$eszkoz['id']?>'>
                        <td><?=$eszkoz['gyarto']?></td>
                        <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['sorozatszam']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['technologia']?></td>
                        <td><?=$eszkoz['transzportszabvany']?></td>
                        <td>Beépítve</td>
                        <td><?=$eszkoz['switch']?> (<?=$eszkoz['switchip']?>) - <?=$eszkoz['portnev']?></td><?php
                        if($csoportir)
                        {
                            ?><td><?=$eszkoz['megjegyzes']?></td>
                            <td><a href='<?=$RootPath?>/beepites<?=$beepid?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a></td>
                            <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td><?php
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
                ?><tr style='font-weight: normal' class='kattinthatotr' data-href='./<?=$eszktip?>/<?=$eszkoz['id']?>'>
                    <td><?=$eszkoz['gyarto']?></td>
                        <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['sorozatszam']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['technologia']?></td>
                        <td><?=$eszkoz['transzportszabvany']?></td>
                        <td><?=$eszkoz['raktar']?></td>
                        <td></td><?php
                    if($csoportir)
                    {
                        ?><td><?=$eszkoz['megjegyzes']?></td>
                        <td><a href='<?=$RootPath?>/beepites<?=$beepid?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a></td>
                        <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td><?php
                    }
                ?></tr><?php
            }
        ?></tbody>
    </table><?php
}