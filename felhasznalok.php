<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] <= 10)
{
    echo "<h2>Az oldal kizárólag adminisztrátorok számára érhető el!</h2>";
}
else
{
    if(isset($_POST['oldalankent']))
    {
        $_SESSION['oldalankent'] = $_POST['oldalankent'];
    }
    if(isset($_SESSION['oldalankent']))
    {
        $megjelenit = $_SESSION['oldalankent'];
    }
    else
    {
        $megjelenit = 20;
    }

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

    $where = null;
    if(isset($_GET['kereses']))
    {
        $keres = $_GET['kereses'];
        $where = "WHERE nev LIKE '%$keres%' OR felhasznalonev LIKE '%$keres%' OR osztaly LIKE '%$keres%'";
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
    $where
    LIMIT $start, $megjelenit;");
    ?><div class='oldalcim'>Felhasználók listája</div>
    <div>
    <div>
        <div class="left">
            <form action="felhasznalok" method="GET">
                <label for="kereses">Felhasználó keresése</label>
                <input type="text" name="kereses">
                <button>Keres</button>
            </form>
        </div>
        <div class="right">
            <form action="felhasznalok" method="POST">
                <label for="oldalankent">Oldalanként</label>
                    <select id="oldalankent" name="oldalankent" onchange="this.form.submit()">
                        <option value="10" <?=($megjelenit == 10) ? "selected" : "" ?>>10</option>
                        <option value="20" <?=($megjelenit == 20) ? "selected" : "" ?>>20</option>
                        <option value="50" <?=($megjelenit == 50) ? "selected" : "" ?>>50</option>
                        <option value="100" <?=($megjelenit == 100) ? "selected" : "" ?>>100</option>
                        <option value="200" <?=($megjelenit == 200) ? "selected" : "" ?>>200</option>
                        <option value="500" <?=($megjelenit == 500) ? "selected" : "" ?>>500</option>
                        <option value="1000" <?=($megjelenit == 1000) ? "selected" : "" ?>>1000</option>
                    </select>
            </form>
        </div>
    </div>
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