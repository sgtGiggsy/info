<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = null;
    if(isset($_GET['id']))
    {
        $modelltipid = $_GET['id'];
    }

    if(isset($_GET['csvimport']))
    {

    }

    $where = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE szam LIKE '%$keres%' OR cimke LIKE '%$keres%' OR portok.port LIKE '%$keres%' OR telefonkeszulektipusok.nev LIKE '%$keres%' OR telefonkozpontok.nev LIKE '%$keres%'";
    }

    $orderby = "ORDER BY szam";
    if(isset($_GET['rendez']) && $_GET['rendez'] != 'kozpontport' && $_GET['rendez'] != 'cimke')
    {
        $rendez = $_GET['rendez'];
        $orderby = "ORDER BY ISNULL($rendez), $rendez ASC";
    }

    $telefonszamok = mySQLConnect("SELECT telefonszamok.id AS id,
            szam,
            cimke,
            vport.port AS faliport,
            telefonszamok.jog AS jog,
            telefonszamok.manualis AS manualis,
            telefonjogosultsagok.nev AS jognev,
            tkozpontport AS kozpontportid,
            portok.port AS kozpontport,
            telefonszamok.megjegyzes AS szammegjegyzes,
            tkozpontportok.megjegyzes AS portmegjegyzes,
            telefonkeszulektipusok.nev AS tipus,
            telefonkozpontok.nev AS kozpont
        FROM telefonszamok
            LEFT JOIN telefonkeszulektipusok ON telefonszamok.tipus = telefonkeszulektipusok.id
            LEFT JOIN telefonjogosultsagok ON telefonszamok.jog = telefonjogosultsagok.id
            LEFT JOIN portok ON telefonszamok.tkozpontport = portok.id
            LEFT JOIN portok vport ON telefonszamok.port = vport.id
            LEFT JOIN tkozpontportok ON tkozpontportok.port = portok.id
            LEFT JOIN eszkozok ON tkozpontportok.eszkoz = eszkozok.id
            LEFT JOIN telefonkozpontok ON telefonkozpontok.eszkoz = eszkozok.id
        $where
        $orderby;");

    if(isset($_GET['rendez']) && $_GET['rendez'] == 'kozpontport')
    {
        $telefonszamok = mysqliNaturalSort($telefonszamok, 'kozpontport');
    }

    if(isset($_GET['rendez']) && $_GET['rendez'] == 'cimke')
    {
        $telefonszamok = mysqliNaturalSort($telefonszamok, 'cimke');
    }

    $tipus = "telefonszamok";

    if($mindir)
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonszam?action=csvimport'">Telefonszámok importálása központból</button><?php
    }
    ?><div class="oldalcim">Telefonszámok</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th><button type="button" id="f0" onclick="filterTable('f0', '<?=$tipus?>', 0)" value='!' placeholder="" title="">!</button></th>
                <th class='tsorth'><p><input type="text" id="f1" onchange="filterTable('f1', '<?=$tipus?>', 1)" placeholder="Telefonszám" title="Telefonszám"><br><span onclick="location.href='./telefonszamok?rendez=szam'">Telefonszám</th>
                <th class='tsorth'><p><input type="text" id="f2" onchange="filterTable('f2', '<?=$tipus?>', 2)" placeholder="Cimke" title="Cimke"><br><span onclick="location.href='./telefonszamok?rendez=cimke'">Cimke</th>
                <th class='tsorth'><p><input type="text" id="f3" onchange="filterTable('f3', '<?=$tipus?>', 3)" placeholder="Jog" title="Jog"><br><span onclick="location.href='./telefonszamok?rendez=jog'">Jog</th>
                <th class='tsorth'><p><input type="text" id="f4" onchange="filterTable('f4', '<?=$tipus?>', 4)" placeholder="Végpont" title="Végpont"><br><span onclick="location.href='./telefonszamok?rendez=faliport'">Végpont</th>
                <th class='tsorth'><p><input type="text" id="f5" onchange="filterTable('f5', '<?=$tipus?>', 5)" placeholder="Központ" title="Központ"><br><span onclick="location.href='./telefonszamok?rendez=kozpont'">Központ</th>
                <th class='tsorth'><p><input type="text" id="f6" onchange="filterTable('f6', '<?=$tipus?>', 6)" placeholder="Lage" title="Lage"><br><span onclick="location.href='./telefonszamok?rendez=kozpontport'">Lage</th>
                <th class='tsorth'><p><input type="text" id="f7" onchange="filterTable('f7', '<?=$tipus?>', 7)" placeholder="Szám megjegyzés" title="Szám megjegyzés"><br><span onclick="location.href='./telefonszamok?rendez=szammegjegyzes'">Szám megjegyzés</th>
                <th class='tsorth'><p><input type="text" id="f8" onchange="filterTable('f8', '<?=$tipus?>', 8)" placeholder="Port megjegyzés" title="Port megjegyzés"><br><span onclick="location.href='./telefonszamok?rendez=portmegjegyzes'">Port megjegyzés</th>
                <th class='tsorth'><p><input type="text" id="f9" onchange="filterTable('f9', '<?=$tipus?>', 9)" placeholder="Tipus" title="Tipus"><br><span onclick="location.href='./telefonszamok?rendez=tipus'">Tipus</th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php
        foreach($telefonszamok as $telefonszam)
        {
            $telszamid = $telefonszam['id'];
            ?><tr class='kattinthatotr' <?=($telefonszam['manualis']) ? 'style="font-style: italic"' : "" ?> data-href='./telefonszam/<?=$telefonszam['id']?>'>
                <td><?=($telefonszam['manualis']) ? '!' : "" ?></td>
                <td><?=$telefonszam['szam']?></td>
                <td><?=$telefonszam['cimke']?></td>
                <td title="<?=$telefonszam['jognev']?>"><?=$telefonszam['jog']?></td>
                <td><?=$telefonszam['faliport']?></td>
                <td><?=$telefonszam['kozpont']?></td>
                <td><?=$telefonszam['kozpontport']?></td>
                <td><?=$telefonszam['szammegjegyzes']?></td>
                <td><?=$telefonszam['portmegjegyzes']?></td>
                <td><?=$telefonszam['tipus']?></td>
                <td><?=($csoportir) ? "<a href='$RootPath/telefonszam/$telszamid?action=edit'><img src='$RootPath/images/edit.png' alt='Telefonszám szerkesztése' title='Telefonszám szerkesztése'/></a>" : "" ?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
    $enablekeres = true;
}