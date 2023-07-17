<?php
// Egyelőre kész
if(!$contextmenujogok['admin'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $kitoltesek = mySQLConnect("SELECT vizsgak_kitoltesek.id as sorszam,
            COUNT(IF(vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id AND vizsgak_valaszlehetosegek.helyes, 1, null)) AS helyes,
            COUNT(IF(vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id, 1, null)) AS ossz,
            felhasznalok.nev as nev
        FROM vizsgak_kitoltesek
            LEFT JOIN vizsgak_kitoltesvalaszok ON vizsgak_kitoltesek.id = vizsgak_kitoltesvalaszok.kitoltes
            LEFT JOIN vizsgak_kerdesek ON vizsgak_kitoltesvalaszok.kerdes = vizsgak_kerdesek.id
            LEFT JOIN vizsgak_valaszlehetosegek ON vizsgak_valaszlehetosegek.kerdes = vizsgak_kerdesek.id
            LEFT JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
            LEFT JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
        WHERE vizsgak_kitoltesek.befejezett IS NULL AND vizsgak_vizsgakorok.vizsga = $vizsgaid AND vizsgak_vizsgakorok.sorszam = (SELECT MAX(sorszam) FROM vizsgak_vizsgakorok WHERE vizsga = $vizsgaid)
        GROUP BY vizsgak_kitoltesek.id
        ORDER BY vizsgak_kitoltesek.id DESC;");

    $vizsgalistaurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/megkezdettvizsgak";

    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$vizsgalistaurl?>?action=exportexcel'">Exportálás Excel fájlba</button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim">Vizsgák</div>
        <table id='vizsgalista'>
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 'i', 'vizsgalista')">Sorszám</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'vizsgalista')">Vizsgázó</th>
                    <th class="tsorth" onclick="sortTable(2, 'i', 'vizsgalista')">Megválaszolt kérdések</th>
                    <th class="tsorth" onclick="sortTable(3, 'i', 'vizsgalista')">Helyes válaszok</th>
                    <th class="tsorth" onclick="sortTable(4, 'i', 'vizsgalista')">Helyes százalék</th>
                </tr>
            </thead>
            <tbody><?php
                foreach($kitoltesek as $x)
                {
                    if( $x['helyes'] == 0)
                    {
                        $szazalek = 0;
                    }
                    else
                    {
                        $szazalek = round($x['helyes']/$x['ossz']*100, 2);
                    }
                    
                    ?><tr style="<?=($x['helyes'] < $vizsgaadatok['minimumhelyes']) ? 'color:red' : 'color:green' ?>" class='kattinthatotr' data-href='./vizsgareszletezo/<?=$x['sorszam']?>'>
                        <td><?=$x['sorszam']?></td>
                        <td><?=$x['nev']?></td>
                        <td><?=$x['ossz']?></td>
                        <td><?=$x['helyes']?></td>
                        <td><?=$szazalek?></td>
                    </tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}