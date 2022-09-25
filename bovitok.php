<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("(modellek.tipus > 25 AND modellek.tipus < 31)");
    $where = $szuresek['where'];

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
            hibas,
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
        WHERE $where
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $tipus = 'bovitok';
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=bovitomodul'">Új bővítomodul</button><?php
    }

    ?><div class="oldalcim">Bővítőmodulok <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth"><p><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="Gyártó" title="Gyártó"><br><span onclick="sortTable(0, 's', '<?=$tipus?>')">Gyártó</span></p></th>
                <th class="tsorth"><p><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Modell" title="Modell"><br><span onclick="sortTable(1, 's', '<?=$tipus?>')">Modell</span></p></th>
                <th class="tsorth"><p><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Sorozatszám" title="Sorozatszám"><br><span onclick="sortTable(2, 's', '<?=$tipus?>')">Sorozatszám</span></p></th>
                <th class="tsorth"><p><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Eszköztípus" title="Eszköztípus"><br><span onclick="sortTable(3, 's', '<?=$tipus?>')">Eszköztípus</span></p></th>
                <th class="tsorth"><p><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="Technológia" title="Technológia"><br><span onclick="sortTable(4, 's', '<?=$tipus?>')">Technológia</span></p></th>
                <th class="tsorth"><p><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="Szabvány" title="Szabvány"><br><span onclick="sortTable(5, 's', '<?=$tipus?>')">Szabvány</span></p></th>
                <th class="tsorth"><p><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Raktár" title="Raktár"><br><span onclick="sortTable(6, 's', '<?=$tipus?>')">Raktár</span></p></th>
                <th class="tsorth"><p><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Beépítési hely" title="Beépítési hely"><br><span onclick="sortTable(7, 's', '<?=$tipus?>')">Beépítési hely</span></p></th><?php
                if($csoportir)
                {
                    ?><th class="tsorth"><p><input type="text" id="f8" onkeyup="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Megjegyzés" title="Megjegyzés"><br><span onclick="sortTable(8, 's', '<?=$tipus?>')">Megjegyzés</span></p></th>
                    <th></th>
                    <th></th>
                    <th></th><?php
                }
            ?></tr>
        </thead>
        <tbody><?php
            $nembeepitett = array();
            foreach($mindeneszkoz as $eszkoz)
            {
                if(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje']))
                {
                    $nembeepitett[] = $eszkoz;
                }
                else
                {
                    ?><tr class='kattinthatotr' data-href='./bovitomodul/<?=$eszkoz['id']?>'>
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
                            <?php szerkSor($eszkoz['beepid'], $eszkoz['id'], "bovitomodul");
                        }
                    ?></tr><?php
                }
            }
            foreach($nembeepitett as $eszkoz)
            {
                ?><tr style='font-weight: normal <?= ($eszkoz['hibas']) ? "; text-decoration: line-through" : "" ?>' class='kattinthatotr' data-href='./<?=$eszktip?>/<?=$eszkoz['id']?>'>
                    <td><?=$eszkoz['gyarto']?></td>
                        <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['sorozatszam']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['technologia']?></td>
                        <td><?=$eszkoz['transzportszabvany']?></td>
                        <td><?=$eszkoz['raktar']?></td>
                        <td>Raktárban</td><?php
                    if($csoportir)
                    {
                        ?><td><?=$eszkoz['megjegyzes']?></td>
                        <?php szerkSor($eszkoz['beepid'], $eszkoz['id'], "bovitomodul");
                    }
                ?></tr><?php
            }
        ?></tbody>
    </table><?php
}