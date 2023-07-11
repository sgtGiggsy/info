<?php
// Egyelőre kész
if(!$contextmenujogok['admin'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $where = "WHERE vizsgak_kitoltesek.befejezett = 1 AND vizsgak_vizsgakorok.vizsga = $vizsgaid";
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE vizsgak_kitoltesek.befejezett = 1 AND nev LIKE '%$keres%'";
    }
    
    ?><div class="oldalcim">Vizsgák</div><?php
    $kitoltesek = mySQLConnect("SELECT vizsgak_kitoltesek.id as sorszam,
            COUNT(IF(vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id AND vizsgak_valaszlehetosegek.helyes, 1, null)) AS helyes,
            COUNT(IF(vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id, 1, null)) AS ossz,
            felhasznalok.nev as nev
        FROM vizsgak_kitoltesek
            LEFT JOIN vizsgak_kitoltesvalaszok ON vizsgak_kitoltesek.id = vizsgak_kitoltesvalaszok.kitoltes
            LEFT JOIN vizsgak_kerdesek ON vizsgak_kitoltesvalaszok.kerdes = vizsgak_kerdesek.id
            INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_valaszlehetosegek.kerdes = vizsgak_kerdesek.id
            LEFT JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
            LEFT JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
        $where
        GROUP BY vizsgak_kitoltesek.id
        ORDER BY vizsgak_kitoltesek.id DESC;");
    ?>

    <form action="vizsgalista" method="GET">
        <label for="kereses">Vizsgázó keresése</label>
        <input type="text" name="kereses">
        <button>Keres</button>
    </form>

    <table id='vizsgalista'>
        <thead style="font-size: 1.3em; font-weight: bold">
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', 'vizsgalista')">Sorszám</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'vizsgalista')">Vizsgázó</th>
                <th class="tsorth" onclick="sortTable(2, 'i', 'vizsgalista')">Megválaszolt kérdések</th>
                <th class="tsorth" onclick="sortTable(3, 'i', 'vizsgalista')">Helyes válaszok</th>
                <th class="tsorth" onclick="sortTable(4, 'i', 'vizsgalista')">Helyes százalék</th>
            </tr>
        </thead>
        <tbody>
    <?php
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
        
        if($x['helyes'] < $vizsgaadatok['minimumhelyes'])
        {
            ?><tr style="color:red" class='kattinthatotr' data-href='./vizsgareszletezo/<?=$x['sorszam']?>'><?php
        }
        else
        {
            ?><tr style="color:green" class='kattinthatotr' data-href='./vizsgareszletezo/<?=$x['sorszam']?>'><?php
        }
            ?>
                <td><?=$x['sorszam']?></td>
                <td><?=$x['nev']?></td>
                <td><?=$x['ossz']?></td>
                <td><?=$x['helyes']?></td>
                <td><?=$szazalek?></td>
            </tr>
    <?php
    }
    $_SESSION[getenv('SESSION_NAME').'excel'] = $kitoltesek;
    ?>
        </tbody>
    </table>
    <a href="?page=exportexcel">Exportálás Excel fájlba</a>
    <?php
}
?>