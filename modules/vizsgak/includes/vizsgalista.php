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
    $vizsgakorok = new MySQLHandler("SELECT * FROM vizsgak_vizsgakorok WHERE vizsga = ? ORDER BY id DESC", $vizsgaid);
    if(!isset($vizsgakorsorszam))
    {
        $vizsgakorsorszam = $vizsgakorok->Fetch()['sorszam'];
    }
    if(isset($_GET['kereses']))
    {
        $keres = "%" . $_GET['kereses'] . "%";
        $kereses = " AND nev LIKE ?";
        $vizsgaqueryparams[] = $keres;
    }
    if(count($vizsgaqueryparams) == 0)
    {
        $vizsgaqueryparams = null;
    }
    
    $vizsgakorok = $vizsgakorok->Result();
    $where = "WHERE vizsgak_kitoltesek.befejezett = 1 AND $korvizsgaszures $kereses $vizsgaelszures";
    $kitoltesek = new MySQLHandler("SELECT vizsgak_kitoltesek.folyoszam as sorszam,
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
            INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_valaszlehetosegek.kerdes = vizsgak_kerdesek.id
            LEFT JOIN felhasznalok ON vizsgak_kitoltesek.felhasznalo = felhasznalok.id
            LEFT JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
        $where
        GROUP BY vizsgak_kitoltesek.id
        ORDER BY vizsgak_kitoltesek.id DESC;", $vizsgaqueryparams);
    $kitoltesek = $kitoltesek->Result();

    if(isset($_GET['action']) && $_GET['action'] == 'exportexcel')
    {
        include("./modules/vizsgak/includes/exportexcel.php");
    }

    $vizsgalistaurl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgalista";

    $oszlopok = array(
        array('nev' => 'Folyószám', 'tipus' => 's'),
        array('nev' => 'Kitöltés ideje', 'tipus' => 's'),
        array('nev' => 'Vizsgázó', 'tipus' => 's'),
        array('nev' => 'Megválaszolt kérdések', 'tipus' => 'i'),
        array('nev' => 'Helyes válaszok', 'tipus' => 'i'),
        array('nev' => 'Eredmény', 'tipus' => 'i')
    );

    $tablazatnev = "vizsgalista";

    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='<?=$vizsgalistaurl?>?action=exportexcel&vizsgakor=<?=$vizsgakorsorszam?>'">Exportálás Excel fájlba</button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim">Vizsgák
            <div class="szuresvalaszto">
                <form action="<?=$vizsgalistaurl?>" method="GET">
                    <label for="vizsgakor" style="font-size: 14px">Vizsgaperiódus kiválasztása</label>
                    <select id="vizsgakor" name="vizsgakor" onchange="this.form.submit()"><?php
                        foreach($vizsgakorok as $x)
                        {
                            ?><option value="<?=$x['sorszam']?>" <?=($vizsgakorsorszam == $x['sorszam']) ? "selected" : "" ?>><?=$x['kezdet']?> - <?=$x['veg']?></option><?php
                        }
                    ?></select>
                </form>
            </div>
        </div><?php
        
        ?><table id='<?=$tablazatnev?>'>
            <thead>
                <tr>
                    <?php sortTableHeader($oszlopok, $tablazatnev) ?>
                </tr>
            </thead>
            <tbody><?php
                foreach($kitoltesek as $x)
                {
                    if($x['helyes'] == 0)
                    {
                        $szazalek = 0;
                    }
                    else
                    {
                        $szazalek = round($x['helyes']/$x['ossz']*100, 2);
                    }
                    
                    $kattinthatolink = './vizsgareszletezo/' . $x['id'];
                    ?><tr class="trlink<?=($x['helyes'] < $vizsgaadatok['minimumhelyes']) ? ' hibastipp' : ' helyes' ?>">
                        <td><a href="<?=$kattinthatolink?>"><?=$x['sorszam']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$x['kitoltesideje']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$x['nev']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$x['ossz']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$x['helyes']?></a></td>
                        <td><a href="<?=$kattinthatolink?>"><?=$szazalek?></a></td>
                    </tr><?php
                }
            ?></tbody>
        </table>
    </div><?php
}