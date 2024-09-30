<?php
if(!$contextmenujogok['kerdeslista'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $kerdeseklistaja = new MySQLHandler("SELECT id as kerdid, kerdes,
            (SELECT COUNT(id) FROM vizsgak_kitoltesvalaszok WHERE kerdes = kerdid) AS kerdesszam,
            (SELECT COUNT(vizsgak_kitoltesvalaszok.id) FROM vizsgak_kitoltesvalaszok INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id WHERE vizsgak_kitoltesvalaszok.kerdes = kerdid AND vizsgak_valaszlehetosegek.helyes IS NOT NULL) AS helyes
        FROM vizsgak_kerdesek
        WHERE vizsga = ?
        ORDER BY id DESC;", $vizsgaid);
    $kerdessorszam = $kerdeseklistaja->sorokszama;
    $kerdeseklistaja = $kerdeseklistaja->Result();

    $oszlopok = array(
        array('nev' => 'Sorszam', 'tipus' => 'i'),
        array('nev' => 'Kérdés', 'tipus' => 's'),
        array('nev' => 'Megválaszolva', 'tipus' => 'i'),
        array('nev' => 'Helyes', 'tipus' => 'i'),
        array('nev' => '%', 'tipus' => 'i')
    );

    $tablazatnev = "kerdeslista";

    ?><div class="szerkgombsor">
        <button type="button" onclick="location.href='./kerdesszerkeszt'">Új kérdés felvitele</button>
    </div>
    <div class="PrintArea">
        <div class="oldalcim">Kérdések</div>
        <table id="<?=$tablazatnev?>">
            <thead>
                <tr>
                    <?php sortTableHeader($oszlopok, $tablazatnev) ?>
                </tr>
            </thead>
            <tbody>
        <?php
            foreach ($kerdeseklistaja as $x)
            {
                $id = $x['kerdid'];
                $helyesszazalek = 0;
                if($x['helyes'] > 0 && $x['kerdesszam'] > 0)
                {
                    $helyesszazalek = round($x['helyes']/$x['kerdesszam']*100, 2);
                }
                ?><tr class='kattinthatotr' data-href='./kerdesszerkeszt/<?=$id?>'>
                    <td><?=$kerdessorszam?></td>
                    <td><?=$x['kerdes']?></td>
                    <td><?=$x['kerdesszam']?></td>
                    <td><?=$x['helyes']?></td>
                    <td><?=$helyesszazalek?></td>
                </tr><?php
                $kerdessorszam--;
            }
            ?></tbody>
        </table>
    </div><?php
}