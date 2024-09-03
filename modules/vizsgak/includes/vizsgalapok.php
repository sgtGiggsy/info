<?php
// Egyelőre kész
if(!$contextmenujogok['admin'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $vizsgalapok = mySQLConnect("SELECT vizsgak_vizsgalapok.id AS vlapid, azonosito, timestamp, megoldokulcs, felhasznalok.nev AS nev
        FROM vizsgak_vizsgalapok
            LEFT JOIN felhasznalok ON vizsgak_vizsgalapok.letrehozo = felhasznalok.id
        WHERE vizsgaid = $vizsgaid AND aktiv = 1
        ORDER BY timestamp DESC;");

    if(isset($_GET['action']) && $_GET['action'] == "print")
    {
        $javascriptfiles[] = "modules/vizsgak/includes/print.js";

        ?><div class="PrintArea">
            <div class="oldalcim">Megoldókulcsok vizsgalapokhoz</div>
            <table>
                <thead>
                    <tr>
                        <td>Vizsga azonosító&nbsp;</td>
                        <td>Megoldókulcs</td>
                    </tr>
                </thead>
                <tbody><?php
                    foreach($vizsgalapok as $lap)
                    {
                        ?><tr class="trlink">
                            <td><?=$lap['azonosito']?></td>
                            <td><?=$lap['megoldokulcs']?></td>
                        </tr><?php
                    }
                ?></tbody>
            </table>
        </div><?php
    }
    else
    {
        $oszlopok = array(
            array('nev' => 'Azonosító', 'tipus' => 's'),
            array('nev' => 'Létrehozó', 'tipus' => 's'),
            array('nev' => 'Létrehozás ideje', 'tipus' => 's'),
            array('nev' => '', 'tipus' => 's'),
            array('nev' => '', 'tipus' => 's')
        );
        $tablazatnev = "vizsgalapok";

        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/vizsgalap?action=addnew'">Új vizsgalap létrehozása</button>
            <button type="button" onclick="window.open('<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/vizsgalapok&action=print')">Megoldókulcsok nyomtatása</button>
        </div><?php

        ?><div class="oldalcim">Nyomtatható vizsgalapok, papíralapú vizsgázáshoz</div>
        <table id='<?=$tablazatnev?>'>
            <thead>
                <tr>
                    <?php sortTableHeader($oszlopok, $tablazatnev) ?>
                </tr>
            </thead>
            <tbody><?php
                foreach($vizsgalapok as $lap)
                {
                    $kattinthatolink = './vizsgalap/' . $lap['vlapid'];
                    ?><tr class="trlink">
                        <td><a href="<?=$kattinthatolink?>"><?=$lap['azonosito']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$lap['nev']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$lap['timestamp']?></a></td>
                        <td><a style="cursor: pointer;" onclick="window.open('<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/vizsgalap/<?=$lap['vlapid']?>&action=print')"><?=$printicon?></a></td>
                        <td><a style="cursor: pointer;" onclick="confirmSend('Biztos törölni szeretnéd ezt a vizsgalapot?', '<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/vizsgalap?action=delete&lapid=<?=$lap['vlapid']?>')"><?=$deleteicon?></a></td>
                    </tr>
                    <tr class="trlink">
                        <td><a href="<?=$kattinthatolink?>">&nbsp;</a></td>
                        <td colspan="3"><a href="<?=$kattinthatolink?>"><?=$lap['megoldokulcs']?></a></td>
                    </tr><?php
                }
            ?></tbody>
        </table><?php
    }
}