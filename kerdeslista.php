<?php
if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $con = mySQLConnect("SELECT id, kerdes FROM kerdesek ORDER BY id DESC");
    ?><div class="oldalcim">Kérdések</div>
    <a href='./kerdesszerkeszt'>Új kérdés felvitele</a>
    <table>
        <thead>
            <tr style="font-size: 1.3em; font-weight: bold">
                <td>Azonosító</td>
                <td>Kérdés</td>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($con as $x)
        {
            $id = $x['id'];
            ?><tr class='kattinthatotr' data-href='./kerdesszerkeszt/<?=$id?>'><?php
                echo "<td>" . $id . "</td>";
                echo "<td>" . $x['kerdes'] . "</td>";
            echo "</tr>"; 
        }
        ?></tbody>
    </table><?php
}