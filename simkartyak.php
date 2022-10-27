<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere();
    $where = $szuresek['where'];

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            telefonszam,
            pinkod,
            pukkod,
            simkartyak.tipus AS tipid,
            simtipusok.nev AS tipus,
            simkartyak.felhasznaloszam AS felhasznaloszamid,
            simfelhasznaloszamok.nev AS felhasznaloszam,
            alakulatok.rovid AS tulajdonos,
            eszkozok.megjegyzes AS megjegyzes,
            raktarak.nev as raktar,
            hibas
        FROM eszkozok
                INNER JOIN simkartyak ON eszkozok.id = simkartyak.eszkoz
                LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
                LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
                LEFT JOIN simtipusok ON simkartyak.tipus = simtipusok.id
                LEFT JOIN simfelhasznaloszamok ON simkartyak.felhasznaloszam = simfelhasznaloszamok.id;");

    $tipus = 'simkartyak';
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/simkartya?action=addnew'">Új SIMkártya</button><?php
    }

    ?><div class="PrintArea">
        <div class="oldalcim">SIM kártyák <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
        <table id="<?=$tipus?>">
            <thead>
                <tr>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="IMEI szám" title="IMEI szám"><br></span><span onclick="sortTable(0, 's', '<?=$tipus?>')">IMEI szám</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Telefonszám" title="Telefonszám"><br></span><span onclick="sortTable(1, 's', '<?=$tipus?>')">Telefonszám</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Típus" title="Típus"><br></span><span onclick="sortTable(2, 's', '<?=$tipus?>')">Típus</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Felhasználószám" title="Felhasználószám"><br></span><span onclick="sortTable(3, 's', '<?=$tipus?>')">Felhasználószám</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="PIN kód" title="PIN kód"><br></span><span onclick="sortTable(4, 's', '<?=$tipus?>')">PIN kód</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="PUK kód" title="PUK kód"><br></span><span onclick="sortTable(5, 's', '<?=$tipus?>')">PUK kód</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Raktár" title="Raktár"><br></span><span onclick="sortTable(6, 's', '<?=$tipus?>')">Raktár</span></p></th><?php
                    if($csoportir)
                    {
                        ?><th class="tsorth"><p><span class="dontprint"><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Megjegyzés" title="Megjegyzés"><br></span><span onclick="sortTable(7, 's', '<?=$tipus?>')">Megjegyzés</span></p></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th><?php
                    }
                ?></tr>
            </thead>
            <tbody><?php
                $nembeepitett = array();
                $szamoz = 1;
                foreach($mindeneszkoz as $sim)
                {
                    ?><tr class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' data-href='./eszkozszerkeszt/<?=$sim['id']?>?tipus=simkartya'>
                        <td><?=$sim['sorozatszam']?></td>
                        <td><?=$sim['telefonszam']?></td>
                        <td><?=$sim['tipus']?></td>
                        <td><?=$sim['felhasznaloszam']?></td>
                        <td><?=$sim['pinkod']?></td>
                        <td><?=$sim['pukkod']?></td>
                        <td><?=$sim['raktar']?></td><?php
                        if($csoportir)
                        {
                            ?><td><?=$sim['megjegyzes']?></td>
                            <td><!-- Szerződés szerkesztése placeholder --></td>
                            <td><!-- Új szerződés placeholder --></td>
                            <td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$sim['id']?>?tipus=simkartya'><img src='<?=$RootPath?>/images/edit.png' alt='SIM kártya szerkesztése' title='SIM kártya szerkesztése'/></a></td><?php
                        }
                    ?></tr><?php
                    $szamoz++;
                }
            ?></tbody>
        </table>
    </div><?php
}