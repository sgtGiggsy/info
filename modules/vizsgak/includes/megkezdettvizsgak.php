<?php
// Egyelőre kész
if(!$contextmenujogok['admin'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $kitoltesek = mySQLConnect("SELECT vizsgak_kitoltesek.folyoszam as sorszam,
            vizsgak_kitoltesek.id as id,
            ROUND(SUM(
                IF((vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id AND vizsgak_valaszlehetosegek.helyes)
                    OR (vizsgak_kitoltesvalaszok.valasz2 = vizsgak_valaszlehetosegek.id AND vizsgak_valaszlehetosegek.helyes)
                    OR (vizsgak_kitoltesvalaszok.valasz3 = vizsgak_valaszlehetosegek.id AND vizsgak_valaszlehetosegek.helyes), helyes, null)), 2) AS helyes,
            COUNT(IF(vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id, 1, null)) AS ossz,
            felhasznalok.nev as nev,
            felhasznalonev,
            kitoltesideje,
            osztaly
        FROM vizsgak_kitoltesek
            LEFT JOIN vizsgak_kitoltesvalaszok ON vizsgak_kitoltesek.id = vizsgak_kitoltesvalaszok.kitoltes
            LEFT JOIN vizsgak_kerdesek ON vizsgak_kitoltesvalaszok.kerdes = vizsgak_kerdesek.id
            LEFT JOIN vizsgak_valaszlehetosegek ON vizsgak_valaszlehetosegek.kerdes = vizsgak_kerdesek.id
            LEFT JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
            LEFT JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
        WHERE vizsgak_kitoltesek.befejezett IS NULL AND vizsgak_vizsgakorok.vizsga = $vizsgaid AND vizsgak_vizsgakorok.sorszam = (SELECT MAX(sorszam) FROM vizsgak_vizsgakorok WHERE vizsga = $vizsgaid) $vizsgaelszures
        GROUP BY vizsgak_kitoltesek.id
        ORDER BY vizsgak_kitoltesek.id DESC;");

    if(isset($_GET['action']) && $_GET['action'] == 'exportexcel')
    {
        include("./modules/vizsgak/includes/exportexcel.php");
    }

    $vizsgalistaurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/megkezdettvizsgak";

    $oszlopok = array(
        array('nev' => 'Folyószám', 'tipus' => 's'),
        array('nev' => 'Vizsgázó', 'tipus' => 's'),
        array('nev' => 'Megválaszolt kérdések', 'tipus' => 'i'),
        array('nev' => 'Helyes válaszok', 'tipus' => 'i'),
        array('nev' => 'Eredmény', 'tipus' => 'i')
    );

    $tablazatnev = "vizsgalista";

    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$vizsgalistaurl?>?action=exportexcel'">Exportálás Excel fájlba</button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim">Vizsgák</div>
        <table id='<?=$tablazatnev?>'>
            <thead>
                <tr>
                    <?php sortTableHeader($oszlopok, $tablazatnev) ?>
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
                    
                    ?><tr style="<?=($x['helyes'] < $vizsgaadatok['minimumhelyes']) ? 'color:red' : 'color:green' ?>" class='kattinthatotr' data-href='./vizsgareszletezo/<?=$x['id']?>'>
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