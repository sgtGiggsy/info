<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(isset($_GET['szures']) && $_GET['szures'] != "keszleten")
    {
        if($_GET['szures'] == "mind")
        {
            $where = "(modellek.tipus = 1 OR modellek.tipus = 2) AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL)";
            $szures = "- Mind";
        }
        elseif ($_GET['szures'] == "leadva")
        {
            $where = "(modellek.tipus = 1 OR modellek.tipus = 2) AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND eszkozok.leadva IS NOT NULL";
            $szures = "- Leadva";
        }
        elseif ($_GET['szures'] == "beepitve")
        {
            $where = "(modellek.tipus = 1 OR modellek.tipus = 2) AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)";
            $szures = "- Beépítve";
        }
        elseif ($_GET['szures'] == "raktaron")
        {
            $where = "(modellek.tipus = 1 OR modellek.tipus = 2) AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND eszkozok.leadva IS NULL AND (beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL OR beepitesek.id IS NULL)";
            $szures = "- Raktáron";
        }
    }
    else
    {
        $where = "(modellek.tipus = 1 OR modellek.tipus = 2) AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND eszkozok.leadva IS NULL";
        $szures = "- Készleten";
    }
    $mindeneszkoz = mySQLConnect("SELECT
            DISTINCT eszkozok.id AS id,
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
            hibas
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
        WHERE $where
        ORDER BY telephely, epuletek.szam + 1, helyisegszam, pozicio, modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $nembeepitett = array();
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/eszkozszerkeszt?tipus=aktiv'">Új aktív eszköz</button><?php
    }

    $tipus = 'eszkozok';
    ?><div class="oldalcim">Aktív eszközök <?=$szures?>
        <div class="right">
                <form action="aktiveszkozok" method="GET">
                    <label for="szures" style="font-size: 14px">Szűrés</label>
                        <select id="szures" name="szures" onchange="this.form.submit()">
                            <option value="keszleten" <?=(isset($_GET['szures'])) ? "" : "selected" ?>>Készleten</option>
                            <option value="beepitve" <?=(isset($_GET['szures']) && $_GET['szures'] == "beepitve") ? "selected" : "" ?>>Beépítve</option>
                            <option value="raktaron" <?=(isset($_GET['szures']) && $_GET['szures'] == "raktaron") ? "selected" : "" ?>>Raktáron</option>
                            <option value="leadva" <?=(isset($_GET['szures']) && $_GET['szures'] == "leadva") ? "selected" : "" ?>>Leadva</option>
                            <option value="mind" <?=(isset($_GET['szures']) && $_GET['szures'] == "mind") ? "selected" : "" ?>>Mind</option>
                        </select>
                </form>
            </div></div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth"><p><input type="text" id="f0" onkeyup="filterTable('f0', '<?=$tipus?>', 0)" placeholder="IP cím" title="IP cím"><br><span onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</span></p></th>
                <th class="tsorth"><p><input type="text" id="f1" onkeyup="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Eszköznév" title="Eszköznév"><br><span onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</span></p></th>
                <th class="tsorth"><p><input type="text" id="f2" onkeyup="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Gyártó" title="Gyártó"><br><span onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</span></p></th>
                <th class="tsorth"><p><input type="text" id="f3" onkeyup="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Modell" title="Modell"><br><span onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</span></p></th>
                <th class="tsorth"><p><input type="text" id="f4" onkeyup="filterTable('f4', '<?=$tipus?>', 4)" placeholder="Sorozatszám" title="Sorozatszám"><br><span onclick="sortTable(4, 's', '<?=$tipus?>')">Sorozatszám</span></p></th>
                <th class="tsorth"><p><input type="text" id="f5" onkeyup="filterTable('f5', '<?=$tipus?>', 5)" placeholder="Eszköztípus" title="Eszköztípus"><br><span onclick="sortTable(5, 's', '<?=$tipus?>')">Eszköztípus</span></p></th>
                <th class="tsorth"><p><input type="text" id="f6" onkeyup="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Épület" title="Épület"><br><span onclick="sortTable(6, 's', '<?=$tipus?>')">Épület</span></p></th>
                <th class="tsorth"><p><input type="text" id="f7" onkeyup="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Helyiség" title="Helyiség"><br><span onclick="sortTable(7, 's', '<?=$tipus?>')">Helyiség</span></p></th>
                <th class="tsorth"><p><input type="text" id="f8" onkeyup="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Rack" title="Rack"><br><span onclick="sortTable(8, 's', '<?=$tipus?>')">Rack</span></p></th>
                <th class="tsorth"><p><input type="text" id="f9" onkeyup="filterTable('f9', '<?=$tipus?>', 9)" placeholder="Megjegyzés" title="Megjegyzés"><br><span onclick="sortTable(9, 's', '<?=$tipus?>')">Megjegyzés</span></p></th>
                <th></th>
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
                            ?><td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td>
                            <td><a href='<?=$RootPath?>/beepites?eszkoz=<?=$eszkid?>'><img src='<?=$RootPath?>/images/newbeep.png' alt='Új beépítés' title='Új beépítés' /></a></td><?php
                            if($eszkoz['beepid'])
                            {
                                ?><td><a href='<?=$RootPath?>/beepites/<?=$eszkoz['beepid']?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a></td><?php
                            }
                            ?><td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td>
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
                ?><tr style='font-weight: normal <?= ($eszkoz['hibas']) ? "; text-decoration: line-through" : "" ?>' class='kattinthatotr' data-href='./aktiveszkoz/<?=$eszkoz['id']?>'>
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
                        ?><td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td>
                        <td><a href='<?=$RootPath?>/beepites?eszkoz=<?=$eszkid?>'><img src='<?=$RootPath?>/images/newbeep.png' alt='Új beépítés' title='Új beépítés' /></a></td><?php
                        if($eszkoz['beepid'])
                        {
                            ?><td><a href='<?=$RootPath?>/beepites/<?=$eszkoz['beepid']?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a></td><?php
                        }
                        else
                        {
                            ?><td></td><?php
                        }
                        ?><td><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td>
                        <td><a href="telnet://<?=$eszkoz['ipcim']?>"><img src='<?=$RootPath?>/images/ssh.png' alt='Eszköz adminisztrálása' title='Eszköz adminisztrálása'/></a></td><?php
                    }
                ?></tr><?php
            }
        ?></tbody>
    </table><?php
}