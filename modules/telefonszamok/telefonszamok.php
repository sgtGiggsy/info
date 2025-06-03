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
    if(isset($_GET['rendez']) && $_GET['rendez'] != 'kozpontport' && $_GET['rendez'] != 'cimke' && $_GET['rendez'] != 'szam' && $_GET['rendez'] != 'tipus')
    {
        $rendez = $_GET['rendez'];
        $orderby = "ORDER BY ISNULL($rendez), $rendez ASC";
    }
    elseif(isset($_GET['rendez']) && $_GET['rendez'] == 'tipus')
    {
        $rendez = 'telefonkeszulektipusok.nev';
        $orderby = "ORDER BY ISNULL($rendez), $rendez ASC";
    }

    $telefonszamok = mySQLConnect("SELECT telefonszamok.id AS id,
            telefonszamok.szam AS szam,
            epuletek.szam AS epuletszam,
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
            LEFT JOIN vegpontiportok ON vport.id = vegpontiportok.port
            LEFT JOIN epuletek ON vegpontiportok.epulet = epuletek.id
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
    $oszlopok = array(
        array('nev' => 'Telefonszám', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=szam'),
        array('nev' => 'Cimke', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=cimke'),
        array('nev' => 'Jog', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=jog'),
        array('nev' => 'Végpont', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=faliport'),
        array('nev' => 'Központ', 'tipus' => 'i', 'onclick' => './telefonszamok?rendez=kozpont'),
        array('nev' => 'Lage', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=kozpontport'),
        array('nev' => 'Szám megjegyzés', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=szammegjegyzes'),
        array('nev' => 'Port megjegyzés', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=portmegjegyzes'),
        array('nev' => 'Tipus', 'tipus' => 's', 'onclick' => './telefonszamok?rendez=tipus')
    );

    if($mindir)
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonszam?action=csvimport'">Telefonszámok importálása központból</button><?php
    }
    ?><div class="oldalcim">Telefonszámok</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th><button type="button" id="f0" onclick="filterTable('f0', '<?=$tipus?>', 0)" value='!' placeholder="" title="">!</button></th><?php
                    sortTableHeader($oszlopok, $tipus, true, true, false, false, true, 1); 
                ?><th></th>
            </tr>
        </thead>
        <tbody><?php
        foreach($telefonszamok as $telefonszam)
        {
            $telszamid = $telefonszam['id'];
            $kattinthatolink = $RootPath . "/telefonszam/" . $telszamid;
            ?><tr class="trlink" <?=($telefonszam['manualis']) ? 'style="font-style: italic"' : "" ?>>
                <td><a href="<?=$kattinthatolink?>"><?=($telefonszam['manualis']) ? '!' : "" ?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['szam']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['cimke']?></a></td>
                <td title="<?=$telefonszam['jognev']?>"><a href="<?=$kattinthatolink?>"><?=$telefonszam['jog']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['epuletszam']?><?=($telefonszam['epuletszam']) ? ". épület," : "" ?> <?=$telefonszam['faliport']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['kozpont']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['kozpontport']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['szammegjegyzes']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['portmegjegyzes']?></a></td>
                <td><a href="<?=$kattinthatolink?>"><?=$telefonszam['tipus']?></a></td>
                <td><?=($csoportir) ? "<a href='$RootPath/telefonszam/$telszamid?action=edit'><img src='$RootPath/images/edit.png' alt='Telefonszám szerkesztése' title='Telefonszám szerkesztése'/></a>" : "" ?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
    $enablekeres = true;
}