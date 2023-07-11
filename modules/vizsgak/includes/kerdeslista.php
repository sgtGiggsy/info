<?php
if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $con = mySQLConnect("SELECT id as kerdid, kerdes,
            (SELECT COUNT(id) FROM tesztvalaszok WHERE kerdes = kerdid) AS kerdesszam,
            (SELECT COUNT(tesztvalaszok.id) FROM tesztvalaszok INNER JOIN valaszok ON tesztvalaszok.valasz = valaszok.id WHERE tesztvalaszok.kerdes = kerdid AND valaszok.helyes = 1) helyes
        FROM kerdesek
        ORDER BY id DESC;");
    ?><div class="oldalcim">Kérdések</div>
    <a href='./kerdesszerkeszt'>Új kérdés felvitele</a>
    <table>
        <thead>
            <tr style="font-size: 1.3em; font-weight: bold">
                <th>Azonosító</th>
                <th>Kérdés</th>
                <th>Megválaszolva</th>
                <th>Sikeres %</th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($con as $x)
        {
            $id = $x['kerdid'];
            ?><tr class='kattinthatotr' data-href='./kerdesszerkeszt/<?=$id?>'><?php
                echo "<td>" . $id . "</td>";
                echo "<td>" . $x['kerdes'] . "</td>";
                echo "<td>" . $x['kerdesszam'] . "</td>";
                echo "<td>" . $x['helyes'] . "</td>";
            echo "</tr>"; 
        }
        ?></tbody>
    </table><?php
}