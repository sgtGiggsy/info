<?php
// Egyelőre kész
if(!$contextmenujogok['admin'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $kereses = null;
    $enablekeres = true;
    $vizsgakorok = mySQLConnect("SELECT * FROM vizsgak_vizsgakorok WHERE vizsga = $vizsgaid ORDER BY id DESC");
    if(!isset($vizsgakorsorszam))
    {
        $vizsgakorsorszam = mysqli_fetch_assoc($vizsgakorok)['sorszam'];
    }
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $kereses = " AND nev LIKE '%$keres%'";
    }

    $where = "WHERE vizsgak_kitoltesek.befejezett = 1 AND $korvizsgaszures $kereses";
    $kitoltesek = mySQLConnect("SELECT vizsgak_kitoltesek.id as sorszam,
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
            INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_valaszlehetosegek.kerdes = vizsgak_kerdesek.id
            LEFT JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
            LEFT JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
        $where
        GROUP BY vizsgak_kitoltesek.id
        ORDER BY vizsgak_kitoltesek.id DESC;");

    if(isset($_GET['action']) && $_GET['action'] == 'exportexcel')
    {
        include("./modules/vizsgak/includes/exportexcel.php");
    }

    $vizsgalistaurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgalista";

    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$vizsgalistaurl?>?action=exportexcel&vizsgakor=<?=$vizsgakorsorszam?>'">Exportálás Excel fájlba</button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim">Vizsgák
            <div class="szuresvalaszto">
                <form action="<?=$vizsgalistaurl?>" method="GET">
                    <label for="vizsgakor" style="font-size: 14px">Vizsgakör kiválasztása</label>
                    <select id="vizsgakor" name="vizsgakor" onchange="this.form.submit()"><?php
                        foreach($vizsgakorok as $x)
                        {
                            ?><option value="<?=$x['sorszam']?>" <?=($vizsgakorsorszam == $x['sorszam']) ? "selected" : "" ?>><?=$x['kezdet']?> - <?=$x['veg']?></option><?php
                        }
                    ?></select>
                </form>
            </div>
        </div><?php
        
        ?><table id='vizsgalista'>
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