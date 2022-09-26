<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("(modellek.tipus > 20 AND modellek.tipus < 26)");
    $where = $szuresek['where'];

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
            mediakonvertermodellek.fizikaireteg,
            mediakonvertermodellek.transzpszabvany,
            atviteliszabvanyok.nev AS transzportszabvany,
            fizikairetegek.nev AS technologia,
            beepitesek.id AS beepid,
            alakulatok.rovid AS tulajdonos,
            rackszekrenyek.nev AS rack,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            vlanok.nev AS vlan,
            raktarak.nev AS raktar,
            hibas
        FROM eszkozok
                INNER JOIN modellek ON eszkozok.modell = modellek.id
                INNER JOIN mediakonvertermodellek ON mediakonvertermodellek.modell = modellek.id
                INNER JOIN gyartok ON modellek.gyarto = gyartok.id
                INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
                LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN vlanok ON beepitesek.vlan = vlanok.id
                LEFT JOIN atviteliszabvanyok ON mediakonvertermodellek.transzpszabvany = atviteliszabvanyok.id
                LEFT JOIN fizikairetegek ON mediakonvertermodellek.fizikaireteg = fizikairetegek.id
        WHERE $where
        ORDER BY epuletek.szam + 1, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=mediakonverter'">Új médiakonverter</button><?php
    }

    $tipus = 'mediakonverterek';
    ?><div class="PrintArea">
        <div class="oldalcim">Médiakonverterek <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
        <table id="<?=$tipus?>">
            <thead>
                <tr>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="Gyártó" title="Gyártó"><br></span><span onclick="sortTable(0, 's', '<?=$tipus?>')">Gyártó</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Modell" title="Modell"><br></span><span onclick="sortTable(1, 's', '<?=$tipus?>')">Modell</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Sorozatszám" title="Sorozatszám"><br></span><span onclick="sortTable(2, 's', '<?=$tipus?>')">Sorozatszám</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Eszköztípus" title="Eszköztípus"><br></span><span onclick="sortTable(3, 's', '<?=$tipus?>')">Eszköztípus</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="Technológia" title="Technológia"><br></span><span onclick="sortTable(4, 's', '<?=$tipus?>')">Technológia</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="Szabvány" title="Szabvány"><br></span><span onclick="sortTable(5, 's', '<?=$tipus?>')">Szabvány</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Épület" title="Épület"><br></span><span onclick="sortTable(6, 's', '<?=$tipus?>')">Épület</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Helyiség" title="Helyiség"><br></span><span onclick="sortTable(7, 's', '<?=$tipus?>')">Helyiség</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f8" onkeyup="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Rack" title="Rack"><br></span><span onclick="sortTable(8, 's', '<?=$tipus?>')">Rack</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f9" onkeyup="filterTable('f9', '<?=$tipus?>', 9)" placeholder="Raktár" title="Raktár"><br></span><span onclick="sortTable(9, 's', '<?=$tipus?>')">Raktár</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f10" onkeyup="filterTable('f10', '<?=$tipus?>', 10)" placeholder="Hálózat" title="Hálózat"><br></span><span onclick="sortTable(10, 's', '<?=$tipus?>')">Hálózat</span></p></th>
                    <?php
                    if($csoportir)
                    {
                        ?><th class="tsorth"><p><span class="dontprint"><input type="text" id="f11" onkeyup="filterTable('f11', '<?=$tipus?>', 11)" placeholder="Megjegyzés" title="Megjegyzés"><br></span><span onclick="sortTable(11, 's', '<?=$tipus?>')">Megjegyzés</span></p></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th><?php
                    }
                ?></tr>
            </thead>
            <tbody><?php
                $nembeepitett = array();
                $szamoz = 1;
                foreach($mindeneszkoz as $eszkoz)
                {
                    if(!($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje']))
                    {
                        $nembeepitett[] = $eszkoz;
                    }
                    else
                    {
                        ?><tr class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' data-href='./mediakonverter/<?=$eszkoz['id']?>'>
                            <td><?=$eszkoz['gyarto']?></td>
                            <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                            <td><?=$eszkoz['sorozatszam']?></td>
                            <td><?=$eszkoz['tipus']?></td>
                            <td><?=$eszkoz['technologia']?></td>
                            <td><?=$eszkoz['transzportszabvany']?></td>
                            <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['rack']?></td>
                            <td>Beépítve</td>
                            <td><?=$eszkoz['vlan']?></td><?php
                            if($csoportir)
                            {
                                ?><td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td>
                                <?php szerkSor($eszkoz['beepid'], $eszkoz['id'], "mediakonverter");
                            }
                        ?></tr><?php
                        $szamoz++;
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    ?><tr style='font-weight: normal <?=($eszkoz['hibas']) ? "; text-decoration: line-through" : "" ?>' class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' data-href='./mediakonverter/<?=$eszkoz['id']?>'>
                        <td><?=$eszkoz['gyarto']?></td>
                            <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                            <td><?=$eszkoz['sorozatszam']?></td>
                            <td><?=$eszkoz['tipus']?></td>
                            <td><?=$eszkoz['technologia']?></td>
                            <td><?=$eszkoz['transzportszabvany']?></td>
                            <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['rack']?></td>
                            <td><?=$eszkoz['raktar']?></td>
                            <td><?=$eszkoz['vlan']?></td><?php
                        if($csoportir)
                        {
                            ?><td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td>
                            <?php szerkSor($eszkoz['beepid'], $eszkoz['id'], "mediakonverter");
                        }
                    ?></tr><?php
                    $szamoz++;
                }
            ?></tbody>
        </table>
    </div><?php
}