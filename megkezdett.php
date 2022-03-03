<?php
if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    ?><div class="oldalcim">Vizsgák</div><?php
    $kitoltesek = mySQLConnect("SELECT kitoltesek.id as sorszam, COUNT(IF(tesztvalaszok.valasz = valaszok.id AND valaszok.helyes, 1, null)) AS helyes, COUNT(IF(tesztvalaszok.valasz = valaszok.id, 1, null)) AS ossz, felhasznalok.nev as nev
    FROM kitoltesek
        LEFT JOIN tesztvalaszok ON kitoltesek.id = tesztvalaszok.kitoltes
        LEFT JOIN kerdesek ON tesztvalaszok.kerdes = kerdesek.id
        LEFT JOIN valaszok ON valaszok.kerdes = kerdesek.id
        LEFT JOIN felhasznalok ON kitoltesek.felhasznalo = felhasznalok.id
    WHERE kitoltesek.befejezett IS NULL
    GROUP BY kitoltesek.id
    ORDER BY kitoltesek.id DESC;");
    ?>

    <table>
        <thead style="font-size: 1.3em; font-weight: bold">
            <tr>
                <td>Sorszám</td>
                <td>Vizsgázó</td>
                <td>Megválaszolt kérdések</td>
                <td>Helyes válaszok</td>
                <td>Helyes százalék</td>
                <td>Eredmény</td>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach($kitoltesek as $x)
    {
        if( $x['helyes'] == 0)
        {
            $szazalek = 0;
            $eredmeny = 0;
        }
        else
        {
            $szazalek = round($x['helyes']/$x['ossz']*100, 2);
            $eredmeny = round($x['helyes']/$_SESSION[getenv('SESSION_NAME').'vizsgahossz']*100, 2);
        }
        
        if($x['helyes'] < $_SESSION[getenv('SESSION_NAME').'minimumhelyes'])
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
                <td><?=$eredmeny?></td>
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