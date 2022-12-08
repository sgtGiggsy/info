<?php

if(!$csoportolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("(modellek.tipus = 6 OR modellek.tipus = 7)");
    $where = $szuresek['where'];

    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }

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
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            hibas,
            lanportok,
            modellek.tipus AS eszktip,
            wanportok,
            wifitipusok.nev AS wifi
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            INNER JOIN sohoeszkozok ON sohoeszkozok.eszkoz = eszkozok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            LEFT JOIN wifitipusok ON wifitipusok.id = sohoeszkozok.wifi
        WHERE $where $csoportwhere
        ORDER BY telephely, epuletek.szam + 1, helyisegszam, pozicio, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/sohoeszkoz?action=addnew'">Új SOHO eszköz</button><?php
    }

    $tipus = 'sohoeszkozok';
    ?><div class="PrintArea">
        <div class="oldalcim">SOHO eszközök</div>
        <table id="<?=$tipus?>">
        <thead>
                <tr>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="IP cím" title="IP cím"><br></span><span onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Eszköznév" title="Eszköznév"><br></span><span onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Gyártó" title="Gyártó"><br></span><span onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Modell" title="Modell"><br></span><span onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="LAN portok" title="LAN portok"><br></span><span onclick="sortTable(4, 'i', '<?=$tipus?>')">LAN portok</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="WiFi" title="WiFi"><br></span><span onclick="sortTable(5, 's', '<?=$tipus?>')">WiFi</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Sorozatszám" title="Sorozatszám"><br></span><span onclick="sortTable(6, 's', '<?=$tipus?>')">Sorozatszám</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Eszköztípus" title="Eszköztípus"><br></span><span onclick="sortTable(7, 's', '<?=$tipus?>')">Eszköztípus</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f8" onkeyup="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Épület" title="Épület"><br></span><span onclick="sortTable(8, 's', '<?=$tipus?>')">Épület</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f9" onkeyup="filterTable('f9', '<?=$tipus?>', 9)" placeholder="Helyiség" title="Helyiség"><br></span><span onclick="sortTable(9, 's', '<?=$tipus?>')">Helyiség</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f10" onkeyup="filterTable('f10', '<?=$tipus?>', 10)" placeholder="Rack" title="Rack"><br></span><span onclick="sortTable(10, 's', '<?=$tipus?>')">Rack</span></p></th><?php
                    if($csoportir)
                    {
                        ?><th class="tsorth"><p><span class="dontprint"><input type="text" id="f11" onkeyup="filterTable('f11', '<?=$tipus?>', 11)" placeholder="Megjegyzés" title="Megjegyzés"><br></span><span onclick="sortTable(11, 's', '<?=$tipus?>')">Megjegyzés</span></p></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th>
                        <th class="dontprint"></th><?php
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
                        ?><tr class='kattinthatotr' data-href='./sohoeszkoz/<?=$eszkoz['id']?>'>
                            <td><?=$eszkoz['ipcim']?></td>
                            <td><?=$eszkoz['beepitesinev']?></td>
                            <td><?=$eszkoz['gyarto']?></td>
                            <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                            <td><?=$eszkoz['lanportok']?></td>
                            <td><?=$eszkoz['wifi']?></td>
                            <td><?=$eszkoz['sorozatszam']?></td>
                            <td><?=$eszkoz['tipus']?></td>
                            <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['rack']?></td>
                            <td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td><?php
                            if($csoportir)
                            {
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "sohoeszkoz");
                                if($eszkoz['eszktip'] == 7)
                                {
                                    ?><td class="dontprint"><a href="telnet://<?=$eszkoz['ipcim']?>"><img src='<?=$RootPath?>/images/ssh.png' alt='Eszköz adminisztrálása' title='Eszköz adminisztrálása'/></a></td>
                                    <td class="dontprint"><a href="#" onclick='window.open("http://<?=$eszkoz['ipcim']?>");return false;'><img src='<?=$RootPath?>/images/webmanage.png' alt='Webes adminisztráció' title='Webes adminisztráció'/></a></td><?php
                                }
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    ?><tr style='font-weight: normal <?= ($eszkoz['hibas']) ? "; text-decoration: line-through" : "" ?>' class='kattinthatotr' data-href='./sohoeszkoz/<?=$eszkoz['id']?>'>
                        <td><?=$eszkoz['ipcim']?></td>
                        <td><?=$eszkoz['beepitesinev']?></td>
                        <td><?=$eszkoz['gyarto']?></td>
                        <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['lanportok']?></td>
                        <td><?=$eszkoz['wifi']?></td>
                        <td><?=$eszkoz['sorozatszam']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                        <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                        <td><?=$eszkoz['rack']?></td>
                        <td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td><?php
                        if($csoportir)
                        {
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "sohoeszkoz");
                            ?><td></td>
                            <td></td><?php
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}