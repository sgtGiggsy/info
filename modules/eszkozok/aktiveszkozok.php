<?php

if(!$csoportolvas)
{
	getPermissionError();
}
else
{
    if(!$_SESSION[getenv('SESSION_NAME').'onlinefigyeles'])
    {
        $szuresek = getWhere("(modellek.tipus = 1 OR modellek.tipus = 2)");
        $onfigy = "";
        $onjoin = "";
    }
    else
    {
        $szuresek = getWhere("(modellek.tipus = 1 OR modellek.tipus = 2) AND (aktiveszkoz_allapot.id = (SELECT MAX(ac.id) FROM aktiveszkoz_allapot ac WHERE ac.eszkozid = aktiveszkoz_allapot.eszkozid) OR aktiveszkoz_allapot.id IS NULL)");
        $onfigy = "online, ";
        $onjoin = "LEFT JOIN aktiveszkoz_allapot ON eszkozok.id = aktiveszkoz_allapot.eszkozid ";
    }
    
    $where = $szuresek['where'];

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            aktiveszkozok.web AS web,
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
            $onfigy
            hibas,
            portszam
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            INNER JOIN aktiveszkozok ON aktiveszkozok.eszkoz = eszkozok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            $onjoin
        WHERE $where
        ORDER BY telephely, epuletek.szam + 1, helyisegszam, rack, pozicio, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/aktiveszkoz?action=addnew'">Új aktív eszköz</button><?php
    }

    $tipus = 'eszkozok';
    ?><div class="PrintArea">
        <div class="oldalcim">Aktív eszközök <?=$szuresek['szures']?> <?=keszletFilter($_GET['page'], $szuresek['filter'])?></div>
        <table id="<?=$tipus?>">
            <thead>
                <tr>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="IP cím" title="IP cím"><br></span><span onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Eszköznév" title="Eszköznév"><br></span><span onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Gyártó" title="Gyártó"><br></span><span onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Modell" title="Modell"><br></span><span onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="Portok" title="Portok"><br></span><span onclick="sortTable(4, 'i', '<?=$tipus?>')">Portok</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="Sorozatszám" title="Sorozatszám"><br></span><span onclick="sortTable(5, 's', '<?=$tipus?>')">Sorozatszám</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Eszköztípus" title="Eszköztípus"><br></span><span onclick="sortTable(6, 's', '<?=$tipus?>')">Eszköztípus</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Épület" title="Épület"><br></span><span onclick="sortTable(7, 's', '<?=$tipus?>')">Épület</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f8" onkeyup="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Helyiség" title="Helyiség"><br></span><span onclick="sortTable(8, 's', '<?=$tipus?>')">Helyiség</span></p></th>
                    <th class="tsorth"><p><span class="dontprint"><input type="text" id="f9" onkeyup="filterTable('f9', '<?=$tipus?>', 9)" placeholder="Rack" title="Rack"><br></span><span onclick="sortTable(9, 's', '<?=$tipus?>')">Rack</span></p></th><?php
                    if($csoportir)
                    {
                        ?><th class="tsorth"><p><span class="dontprint"><input type="text" id="f10" onkeyup="filterTable('f10', '<?=$tipus?>', 10)" placeholder="Megjegyzés" title="Megjegyzés"><br></span><span onclick="sortTable(10, 's', '<?=$tipus?>')">Megjegyzés</span></p></th>
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
                        ?><tr class='kattinthatotr<?=($eszkoz['hibas'] == 1) ? " reszhibas" : "" ?><?=($_SESSION[getenv('SESSION_NAME').'onlinefigyeles'] && $eszkoz['online'] == 0 && $eszkoz['online'] != null && $szemelyes['switchstateshow'] == 1) ? " offline" : "" ?>' data-href='./aktiveszkoz/<?=$eszkoz['id']?>'>
                            <td><?=$eszkoz['ipcim']?></td>
                            <td><?=$eszkoz['beepitesinev']?></td>
                            <td><?=$eszkoz['gyarto']?></td>
                            <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                            <td><?=$eszkoz['portszam']?></td>
                            <td><?=$eszkoz['sorozatszam']?></td>
                            <td><?=$eszkoz['tipus']?></td>
                            <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                            <td><?=$eszkoz['rack']?></td>
                            <td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td><?php
                            if($csoportir)
                            {
                                szerkSor($eszkoz['beepid'], $eszkoz['id'], "aktiveszkoz"); ?>
                                <td class="dontprint"><a href="telnet://<?=$eszkoz['ipcim']?>"><img src='<?=$RootPath?>/images/ssh.png' alt='Eszköz adminisztrálása' title='Eszköz adminisztrálása'/></a></td>
                                <td class="dontprint"><?php
                                if($eszkoz['web'])
                                {
                                    ?><a href="" onclick='window.open("http://<?=$eszkoz['ipcim']?>");'><img src='<?=$RootPath?>/images/webmanage.png' alt='Webes adminisztráció' title='Webes adminisztráció'/></a><?php
                                }
                                ?></td><?php
                            }
                        ?></tr><?php
                    }
                }
                foreach($nembeepitett as $eszkoz)
                {
                    ?><tr class='kattinthatotr kiepitett<?=($eszkoz['hibas'] == 2) ? " mukodeskeptelen" : (($eszkoz['hibas'] == 1) ? " reszhibas" : "") ?>' data-href='./aktiveszkoz/<?=$eszkoz['id']?>'>
                        <td><?=$eszkoz['ipcim']?></td>
                        <td><?=$eszkoz['beepitesinev']?></td>
                        <td><?=$eszkoz['gyarto']?></td>
                        <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                        <td><?=$eszkoz['portszam']?></td>
                        <td><?=$eszkoz['sorozatszam']?></td>
                        <td><?=$eszkoz['tipus']?></td>
                        <td><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?></td>
                        <td><?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></td>
                        <td><?=$eszkoz['rack']?></td>
                        <td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td><?php
                        if($csoportir)
                        {
                            szerkSor($eszkoz['beepid'], $eszkoz['id'], "aktiveszkoz");
                            ?><td class="dontprint"></td>
                            <td class="dontprint"></td><?php
                        }
                    ?></tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}