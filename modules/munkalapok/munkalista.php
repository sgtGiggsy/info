<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $where = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE felhasznalok.nev LIKE '%$keres%' OR szervezetek.rovid LIKE '%$keres%' OR mv1.nev LIKE '%$keres%' OR mv2.nev LIKE '%$keres%' OR leiras LIKE '%$keres%' OR eszkoz LIKE '%$keres%'";
    }
    
    $csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "szervezet",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => false,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => "felhasznalok",                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => null,           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
        if(!$where)
        {
            $where = "WHERE ";
        }
        else
        {
            $csoportwhere = "AND $csoportwhere";
        }
    }
    
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

    if(isset($_GET['subpage']) && $_GET['subpage'] == 'oldal' && isset($_GET['param']) && verifyWholeNum($_GET['param']))
    {
        $oldal = $_GET['param'];
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
    
    $munkak = mySQLConnect("SELECT munkalapok.id AS id, hely, telephelyek.telephely AS telephely,
            epuletek.szam AS epulet, epulettipusok.tipus AS eptipus, helyisegek.helyisegszam AS helyiseg,
            igenylonev, igenylotelefon, igenylesideje, vegrehajtasideje, leiras, eszkoz,
            szervezetek.rovid AS igenyloszervezet,
            munkavegzo1nev,
            munkavegzo1telefon,
            munkavegzo1beosztas,
            munkavegzo2nev,
            munkavegzo2telefon,
            munkavegzo2beosztas,
            IF(vegrehajtasideje > date_sub(now(), INTERVAL 31 DAY), 1, 0) AS modenged
        FROM munkalapok
            LEFT JOIN helyisegek ON munkalapok.hely = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN felhasznalok ON munkalapok.igenylo = felhasznalok.id
            LEFT JOIN szervezetek ON felhasznalok.szervezet = szervezetek.id
        $where $csoportwhere
        ORDER BY munkalapok.id DESC
        LIMIT $start, $megjelenit;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/munkaszerkeszt'">Új munka</button><?php
    }

    ?><div class="oldalcim">Munkalista
    <div class="szuresvalaszto">
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
                    <p><span>Szervezet: </span><?=$munka['igenyloszervezet']?></p>
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
                    <th class="tsorth" onclick="sortTable(3, 's', 'munkalista')">Szervezet</th>
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
        foreach($munkak as $munka)
        {
            $munkid = $munka['id'];
            ?><tr>
                <td><?=$munkid?></td>
                <td><?=$munka['igenylonev']?></td>
                <td><?=$munka['igenylotelefon']?></td>
                <td><?=$munka['igenyloszervezet']?></td>
                <td><?=$munka['telephely']?> <?=$munka['epulet']?>.  <?=$munka['eptipus']?> <?=$munka['helyiseg']?>.</td>
                <td nowrap><?=str_replace("-", ".", $munka['igenylesideje'])?></td>
                <td nowrap><?=str_replace("-", ".", $munka['vegrehajtasideje'])?></td>
                <td><?=$munka['leiras']?></td>
                <td><?=$munka['eszkoz']?></td>
                <td><?=$munka['munkavegzo1nev']?></td>
                <td><a style="cursor: pointer;" onclick="window.open('<?=$RootPath?>/munkaszerkeszt/<?=$munka['id']?>?action=print')"><img src='<?=$RootPath?>/images/print.png' alt='Munkalap nyomtatása' title='Munkalap nyomtatása' /></a></td>
                <td><?=($csoportir && $munka['modenged']) ? "<a href='$RootPath/munkaszerkeszt/$munkid'><img src='$RootPath/images/edit.png' alt='Munka szerkesztése' title='Munka szerkesztése'/></a>" : "" ?></td>
            </tr><?php
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
    $enablekeres = true;
}