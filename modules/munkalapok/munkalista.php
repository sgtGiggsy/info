<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(isset($_POST['oldalankent']))
    {
        $_SESSION['oldalankent'] = $_POST['oldalankent'];
    }
    if(isset($_SESSION['oldalankent']))
    {
        $megjelenit = $_SESSION['oldalankent'];
    }
    else
    {
        $megjelenit = 20;
    }

    $con = mySQLConnect("SELECT count(*) AS db FROM munkalapok");
    $count = (mysqli_fetch_assoc($con))['db'];

    if(isset($_GET['oldal']))
    {
        $oldal = $_GET['oldal'];
        $start = ($oldal - 1) * $megjelenit;
    }
    else
    {
        $oldal = 1;
        $start = 0;
    }

    if($oldal != 1)
    {
        $previd = $oldal - 1;
    }

    if($oldal * $megjelenit < $count)
    {
        $nextid = $oldal + 1;
    }
    
    $munkak = mySQLConnect("SELECT munkalapok.id AS id, hely, telephelyek.telephely AS telephely, epuletek.szam AS epulet, epulettipusok.tipus AS eptipus, helyisegek.helyisegszam AS helyiseg, igenylo, igenylesideje, vegrehajtasideje, munkavegzo1, munkavegzo2, leiras, eszkoz,
            (SELECT nev FROM felhasznalok WHERE id = igenylo) AS igenylonev,
            (SELECT telefon FROM felhasznalok WHERE id = igenylo) AS igenylotelefon,
            (SELECT rovid FROM felhasznalok INNER JOIN alakulatok ON felhasznalok.alakulat = alakulatok.id WHERE felhasznalok.id = igenylo) AS igenyloalakulat,
            (SELECT nev FROM felhasznalok WHERE id = munkavegzo1) AS munkavegzo1nev,
            (SELECT beosztas FROM felhasznalok WHERE id = munkavegzo1) AS munkavegzo1beosztas,
            (SELECT telefon FROM felhasznalok WHERE id = munkavegzo1) AS munkavegzo1telefon,
            (SELECT nev FROM felhasznalok WHERE id = munkavegzo2) AS munkavegzo2nev,
            (SELECT beosztas FROM felhasznalok WHERE id = munkavegzo2) AS munkavegzo2beosztas,
            (SELECT telefon FROM felhasznalok WHERE id = munkavegzo2) AS munkavegzo2telefon
        FROM munkalapok
            LEFT JOIN helyisegek ON munkalapok.hely = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
        ORDER BY munkalapok.id DESC
        LIMIT $start, $megjelenit;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/munkaszerkeszt'">Új munka</button><?php
    }

    ?><div class="oldalcim">Munkalista
    <div class="right">
        <form method="POST">
            <label for="oldalankent" style="font-size: 14px">Oldalanként</label>
                <select id="oldalankent" name="oldalankent" onchange="this.form.submit()">
                    <option value="10" <?=($megjelenit == 10) ? "selected" : "" ?>>10</option>
                    <option value="20" <?=($megjelenit == 20) ? "selected" : "" ?>>20</option>
                    <option value="50" <?=($megjelenit == 50) ? "selected" : "" ?>>50</option>
                    <option value="100" <?=($megjelenit == 100) ? "selected" : "" ?>>100</option>
                    <option value="200" <?=($megjelenit == 200) ? "selected" : "" ?>>200</option>
                    <option value="500" <?=($megjelenit == 500) ? "selected" : "" ?>>500</option>
                    <option value="1000" <?=($megjelenit == 1000) ? "selected" : "" ?>>1000</option>
                </select>
        </form>
    </div></div><?php

    if(@$previd)
    {
        ?><div class='left'><a href="<?=$RootPath?>/munkalista/oldal/<?=$previd?>">Előző oldal</a></div><?php
    }

    if(@$nextid)
    {
        ?><div class='right'><a href="<?=$RootPath?>/munkalista/oldal/<?=$nextid?>">Következő oldal</a></div><?php
    }
    
    if(isset($_GET['nezet']) && $_GET['nezet'] == "kartya")
    {
        ?><div class="kartyak"><?php
        foreach($munkak as $munka)
        {
            ?><div class="kartya">
                <div>
                    <p><span>Megrendelő: </span><?=$munka['igenylonev']?> <?=$munka['igenylotelefon']?></p>
                    <p><span>Alakulat: </span><?=$munka['igenyloalakulat']?></p>
                </div>
                <div>
                    <p><span>Végrehajtotta: </span><?=$munka['munkavegzo1nev']?> (<?=$munka['munkavegzo1telefon']?>)</p><?php
                    if($munka['munkavegzo2nev'])
                    {
                        ?><p><span>Végrehajtásban részt vett még: </span><?=$munka['munkavegzo1nev']?> (<?=$munka['munkavegzo1telefon']?>)</p><?php
                    }
                ?></div>
                <div>
                    <p><span>Munkavégzés helye: </span><?=$munka['telephely']?> <?=$munka['epulet']?>.  <?=$munka['eptipus']?> <?=$munka['helyiseg']?></p>
                    <p><span>Elvégzett munka: </span><?=$munka['leiras']?></p>
                    <p><span>Anyag / eszköz: </span><?=$munka['eszkoz']?></p>
                </div>
            </div><?php
        }
    }
    else
    {
        ?><div>
        <table id="munkalista">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 'i', 'munkalista')">Azonos.</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'munkalista')">Megrendelő</th>
                    <th class="tsorth" onclick="sortTable(2, 's', 'munkalista')">Telefon</th>
                    <th class="tsorth" onclick="sortTable(3, 's', 'munkalista')">Alakulat</th>
                    <th class="tsorth" onclick="sortTable(4, 's', 'munkalista')">Munkavégzés helye</th>
                    <th class="tsorth" onclick="sortTable(5, 's', 'munkalista')">Igénylés ideje</th>
                    <th class="tsorth" onclick="sortTable(6, 's', 'munkalista')">Végrehajtás ideje</th>
                    <th class="tsorth" onclick="sortTable(7, 's', 'munkalista')">Elvégzett munka</th>
                    <th class="tsorth" onclick="sortTable(8, 's', 'munkalista')">Anyag/eszköz</th>
                    <th class="tsorth" onclick="sortTable(9, 's', 'munkalista')">Munkavégző</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
        <tbody><?php
        $szamoz = 1;
        foreach($munkak as $munka)
        {
            $munkid = $munka['id'];
            ?><tr class='valtottsor-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' style='font-weight: normal'>
                <td><?=$munkid?></td>
                <td><?=$munka['igenylonev']?></td>
                <td><?=$munka['igenylotelefon']?></td>
                <td><?=$munka['igenyloalakulat']?></td>
                <td><?=$munka['telephely']?> <?=$munka['epulet']?>.  <?=$munka['eptipus']?> <?=$munka['helyiseg']?>.</td>
                <td nowrap><?=str_replace("-", ".", $munka['igenylesideje'])?></td>
                <td nowrap><?=str_replace("-", ".", $munka['vegrehajtasideje'])?></td>
                <td><?=$munka['leiras']?></td>
                <td><?=$munka['eszkoz']?></td>
                <td><?=$munka['munkavegzo1nev']?></td>
                <td><a style="cursor: pointer;" onclick="window.open('<?=$RootPath?>/munkaszerkeszt/<?=$munka['id']?>?action=print')"><img src='<?=$RootPath?>/images/print.png' alt='Munkalap nyomtatása' title='Munkalap nyomtatása' /></a></td>
                <td><?=($csoportir) ? "<a href='$RootPath/munkaszerkeszt/$munkid'><img src='$RootPath/images/edit.png' alt='Munka szerkesztése' title='Munka szerkesztése'/></a>" : "" ?></td>
            </tr><?php
            $szamoz++;
        }
        ?></tbody>
        </table><?php
    }
    if(@$previd)
    {
        ?><div class='left'><a href="<?=$RootPath?>/munkalista/oldal/<?=$previd?>">Előző oldal</a></div><?php
    }

    if(@$nextid)
    {
        ?><div class='right'><a href="<?=$RootPath?>/munkalista/oldal/<?=$nextid?>">Következő oldal</a></div><?php
    }
    ?></div><?php
}