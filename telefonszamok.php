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
        $where = "WHERE szam LIKE '%$keres%' OR cimke LIKE '%$keres%'";
    }

    $telefonszamok = mySQLConnect("SELECT telefonszamok.id AS id, szam, cimke, port, telefonszamok.jog AS jog, telefonjogosultsagok.nev AS jognev, tkozpontport, megjegyzes, telefonvonaltipusok.nev AS tipus
        FROM telefonszamok
            LEFT JOIN telefonvonaltipusok ON telefonszamok.tipus = telefonvonaltipusok.id
            LEFT JOIN telefonjogosultsagok ON telefonszamok.jog = telefonjogosultsagok.id
        $where
        ORDER BY megjegyzes;");

    $tipus = "telefonszamok";
    ?><div class="oldalcim">Telefonszámok</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', '<?=$tipus?>?>')">Telefonszám</th>
                <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>?>')">Cimke</th>
                <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>?>')">Jog</th>
                <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>?>')">Végponti port</th>
                <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>?>')">Megjegyzés</th>
                <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>?>')">Tipus</th>
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
                <td><?=$telefonszam['port']?></td>
                <td><?=$telefonszam['megjegyzes']?></td>
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