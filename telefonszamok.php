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

    $where = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE szam LIKE '%$keres%' OR cimke LIKE '%$keres%' OR portok.port LIKE '%$keres%'";
    }

    $telefonszamok = mySQLConnect("SELECT telefonszamok.id AS id,
            szam,
            cimke,
            telefonszamok.port AS faliportid,
            (SELECT port FROM portok WHERE id = faliportid) AS faliport,
            telefonszamok.jog AS jog,
            telefonjogosultsagok.nev AS jognev,
            tkozpontport AS kozpontportid,
            portok.port AS kozpontport,
            telefonszamok.megjegyzes AS szammegjegyzes,
            tkozpontportok.megjegyzes AS portmegjegyzes,
            telefonvonaltipusok.nev AS tipus,
            telefonkozpontok.nev AS kozpont
        FROM telefonszamok
            LEFT JOIN telefonvonaltipusok ON telefonszamok.tipus = telefonvonaltipusok.id
            LEFT JOIN telefonjogosultsagok ON telefonszamok.jog = telefonjogosultsagok.id
            LEFT JOIN portok ON telefonszamok.tkozpontport = portok.id
            LEFT JOIN tkozpontportok ON tkozpontportok.port = portok.id
            LEFT JOIN eszkozok ON tkozpontportok.eszkoz = eszkozok.id
            LEFT JOIN telefonkozpontok ON telefonkozpontok.eszkoz = eszkozok.id
        $where
        ORDER BY szam;");

    $tipus = "telefonszamok";
    ?><div class="oldalcim">Telefonszámok</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', '<?=$tipus?>')">Telefonszám</th>
                <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>')">Cimke</th>
                <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>')">Jog</th>
                <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>')">Végponti port</th>
                <th class="tsorth" onclick="sortTable(4, 's', '<?=$tipus?>')">Központ</th>
                <th class="tsorth" onclick="sortTable(5, 's', '<?=$tipus?>')">Központ port</th>
                <th class="tsorth" onclick="sortTable(6, 's', '<?=$tipus?>')">Szám megjegyzés</th>
                <th class="tsorth" onclick="sortTable(7, 's', '<?=$tipus?>')">Port megjegyzés</th>
                <th class="tsorth" onclick="sortTable(8, 's', '<?=$tipus?>')">Tipus</th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php
        foreach($telefonszamok as $telefonszam)
        {
            ?><tr>
                <td><?=$telefonszam['szam']?></td>
                <td><?=$telefonszam['cimke']?></td>
                <td title="<?=$telefonszam['jognev']?>"><?=$telefonszam['jog']?></td>
                <td><?=$telefonszam['faliport']?></td>
                <td><?=$telefonszam['kozpont']?></td>
                <td><?=$telefonszam['kozpontport']?></td>
                <td><?=$telefonszam['szammegjegyzes']?></td>
                <td><?=$telefonszam['portmegjegyzes']?></td>
                <td><?=$telefonszam['tipus']?></td>
            </tr><?php
        }
        ?></tbody>
        </table>
        
        <script>
            window.addEventListener("load", function () {
                document.getElementById('kereses').style.visibility = "visible";
            });
        </script><?php
}