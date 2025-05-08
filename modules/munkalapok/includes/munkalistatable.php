<?php
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
    ?></div><?php
}
else
{
    ?><table id="munkalista">
        <thead>
            <tr>
                <th class="tsorth" onclick="tableQuickSort(0, 'i', 'munkalista')">Azonos.</th>
                <th class="tsorth" onclick="tableQuickSort(1, 's', 'munkalista')">Megrendelő</th>
                <th class="tsorth" onclick="tableQuickSort(2, 's', 'munkalista')">Telefon</th>
                <th class="tsorth" onclick="tableQuickSort(3, 's', 'munkalista')">Szervezet</th>
                <th class="tsorth" onclick="tableQuickSort(4, 's', 'munkalista')">Munkavégzés helye</th>
                <th class="tsorth" onclick="tableQuickSort(5, 's', 'munkalista')">Igénylés ideje</th>
                <th class="tsorth" onclick="tableQuickSort(6, 's', 'munkalista')">Végrehajtás ideje</th>
                <th class="tsorth" onclick="tableQuickSort(7, 's', 'munkalista')">Elvégzett munka</th>
                <th class="tsorth" onclick="tableQuickSort(8, 's', 'munkalista')">Anyag/eszköz</th>
                <th class="tsorth" onclick="tableQuickSort(9, 's', 'munkalista')">Munkavégző</th>
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