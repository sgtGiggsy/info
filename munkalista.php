<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
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
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id;");

    ?><div class="oldalcim">Munkalista</div><?php
    
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
                    <th class="tsorth" onclick="sortTable(0, 'i', 'munkalista')">Sorszam</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'munkalista')">Megrendelő</th>
                    <th class="tsorth" onclick="sortTable(2, 's', 'munkalista')">Telefon</th>
                    <th class="tsorth" onclick="sortTable(3, 's', 'munkalista')">Alakulat</th>
                    <th class="tsorth" onclick="sortTable(4, 's', 'munkalista')">Munkavégzés helye</th>
                    <th class="tsorth" onclick="sortTable(5, 's', 'munkalista')">Igénylés ideje</th>
                    <th class="tsorth" onclick="sortTable(6, 's', 'munkalista')">Végrehajtás ideje</th>
                    <th class="tsorth" onclick="sortTable(7, 's', 'munkalista')">Elvégzett munka</th>
                    <th class="tsorth" onclick="sortTable(8, 's', 'munkalista')">Anyag/eszköz</th>
                    <th class="tsorth" onclick="sortTable(9, 's', 'munkalista')">Munkavégző</th>
                    <th class="tsorth" onclick="sortTable(9, 's', 'munkalista')">Telefon</th>
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
                <td><?=$munka['igenyloalakulat']?></td>
                <td><?=$munka['telephely']?> <?=$munka['epulet']?>.  <?=$munka['eptipus']?> <?=$munka['helyiseg']?>.</td>
                <td nowrap><?=str_replace("-", ".", $munka['igenylesideje'])?></td>
                <td nowrap><?=str_replace("-", ".", $munka['vegrehajtasideje'])?></td>
                <td><?=$munka['leiras']?></td>
                <td><?=$munka['eszkoz']?></td>
                <td><?=$munka['munkavegzo1nev']?></td>
                <td><?=$munka['munkavegzo1telefon']?></td>
                <td><a style="cursor: pointer;" onclick="window.open('<?=$RootPath?>/munkaprint/<?=$munka['id']?>')"><img src='<?=$RootPath?>/images/print.png' alt='Munkalap nyomtatása' title='Munkalap nyomtatása' /></a></td>
                <td><?=($csoportir) ? "<a href='$RootPath/munkaszerkeszt/$munkid'><img src='$RootPath/images/edit.png' alt='Munka szerkesztése' title='Munka szerkesztése'/></a>" : "" ?></td>
            </tr><?php
        }
        ?></tbody>
        </table><?php
    }
    ?></div><?php
}