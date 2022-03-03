<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] <= 10)
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    $megjelenit = 20;

    $con = mySQLConnect("SELECT count(*) AS db FROM felhasznalok");
    $count = (mysqli_fetch_assoc($con))['db'];

    if(isset($_GET['oldal']))
    {
        $oldal = $_GET['oldal'];
        $start = ($oldal - 1) * $megjelenit;
    }
    else
    {
        $oldal = 1;
        $start = 0;
    }

    if($oldal != 1)
    {
        $previd = $oldal - 1;
    }

    if($oldal * $megjelenit < $count)
    {
        $nextid = $oldal + 1;
    }

    $lista = mySQLConnect("SELECT id as felhasznaloid, nev, felhasznalonev, jogosultsag, email, elsobelepes, osztaly,
    (SELECT COUNT(IF(tesztvalaszok.valasz = valaszok.id AND valaszok.helyes, 1, null)) as jovalasz
         FROM tesztvalaszok
             INNER JOIN valaszok ON tesztvalaszok.valasz = valaszok.id
             INNER JOIN kitoltesek ON tesztvalaszok.kitoltes = kitoltesek.id
         WHERE kitoltesek.felhasznalo = felhasznaloid
        GROUP BY kitoltesek.id
        ORDER BY jovalasz DESC
        LIMIT 1) AS legjobb,
        (SELECT COUNT(*) FROM kitoltesek WHERE felhasznalo = felhasznaloid) AS kitoltesszam
    FROM felhasznalok
    LIMIT $start, $megjelenit;");
    ?><div class='oldalcim'>Felhasználók listája</div>
    <div>
    <table>
        <thead style="font-size: 1.3em; font-weight: bold">
            <tr>
                <td>ID</td>
                <td>Felhasználó</td>
                <td>Usernév</td>
                <td>Emailcím</td>
                <td>Részleg</td>
                <td>Jogosultság</td>
                <td>Első bejelentkezés</td>
                <td>Legjobb eredmény</td>
                <td>Kitöltések</td>
            </tr>
        </thead>
        <tbody><?php
        foreach($lista as $x)
        {
            if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] < 50)
            {
                echo "<tr>";
            }
            else
            {
                ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/felhasznalo/<?=$x['felhasznaloid']?>'><?php
            }
                ?>
                <td><?=$x['felhasznaloid']?></td>
                <td><?=$x['nev']?></td>
                <td><?=$x['felhasznalonev']?></td>
                <td><?=$x['email']?></td>
                <td><?=$x['osztaly']?></td>
                <td><?=($x['jogosultsag'] < 10) ? "Felhasználó" : "Adminisztrátor";?></td>
                <td><?=$x['elsobelepes']?></td>
                <td><?=$x['legjobb']?></td>
                <td><?=$x['kitoltesszam']?></td>
            </tr><?php
        }
        ?></tbody>
        </table><?php
        if(@$previd)
        {
            ?><div class='left'><a href="<?=$RootPath?>/felhasznalok/oldal/<?=$previd?>">Előző oldal</a></div><?php
        }

        if(@$nextid)
        {
            ?><div class='right'><a href="<?=$RootPath?>/felhasznalok/oldal/<?=$nextid?>">Következő oldal</a></div><?php
        }
    ?></div><?php
}
?>