<?php
if(!$contextmenujogok['kerdeslista'])
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $kerdeseklistaja = mySQLConnect("SELECT id as kerdid, kerdes,
            (SELECT COUNT(id) FROM vizsgak_kitoltesvalaszok WHERE kerdes = kerdid) AS kerdesszam,
            (SELECT COUNT(vizsgak_kitoltesvalaszok.id) FROM vizsgak_kitoltesvalaszok INNER JOIN vizsgak_valaszlehetosegek ON vizsgak_kitoltesvalaszok.valasz = vizsgak_valaszlehetosegek.id WHERE vizsgak_kitoltesvalaszok.kerdes = kerdid AND vizsgak_valaszlehetosegek.helyes = 1) AS helyes
        FROM vizsgak_kerdesek
        WHERE vizsga = $vizsgaid
        ORDER BY id DESC;");

    ?><div class="oldalcim">Kérdések</div>
    <a href='./kerdesszerkeszt'>Új kérdés felvitele</a>
    <table>
        <thead>
            <tr style="font-size: 1.3em; font-weight: bold">
                <th>Azonosító</th>
                <th>Kérdés</th>
                <th>Megválaszolva</th>
                <th>Helyes</th>
                <th>%</th>
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
                <td><?=$id?></td>
                <td><?=$x['kerdes']?></td>
                <td><?=$x['kerdesszam']?></td>
                <td><?=$x['helyes']?></td>
                <td><?=$helyesszazalek?></td>
              </tr><?php
        }
        ?></tbody>
    </table><?php
}