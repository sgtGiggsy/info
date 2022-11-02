<?php
if(!$csoportolvas)
{
    getPermissionError();
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
        $where = "WHERE felhasznalok.nev LIKE '%$keres%' OR felhasznalonev LIKE '%$keres%' OR osztaly LIKE '%$keres%'";
    }

    $lista = mySQLConnect("SELECT felhasznalok.id as felhasznaloid, felhasznalok.nev AS nev, felhasznalonev, email, elsobelepes, osztaly, telefon, beosztas, alakulatok.nev AS alakulat
        FROM felhasznalok
            LEFT JOIN alakulatok ON felhasznalok.alakulat = alakulatok.id
        $where
        LIMIT $start, $megjelenit;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/felhasznalo?action=addnew'">Új felhasználó</button><?php
    }
    ?><div class='oldalcim'>Felhasználók listája
        <div class="right">
            <form action="felhasznalok" method="POST">
                <label for="oldalankent" style="font-size: 14px">Oldalanként</label>
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
    <div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Felhasználó</th>
                <th>Usernév</th>
                <th>Emailcím</th>
                <th>Telefon</th>
                <th>Alakulat</th>
                <th>Részleg</th>
                <th>Beosztás</th>
                <th>Első bejelentkezés</th>
            </tr>
        </thead>
        <tbody><?php
        foreach($lista as $x)
        {
            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/felhasznalo/<?=$x['felhasznaloid']?>'>
                <td><?=$x['felhasznaloid']?></td>
                <td><?=$x['nev']?></td>
                <td><?=$x['felhasznalonev']?></td>
                <td><?=$x['email']?></td>
                <td><?=$x['telefon']?></td>
                <td><?=$x['alakulat']?></td>
                <td><?=$x['osztaly']?></td>
                <td><?=$x['beosztas']?></td>
                <td><?=$x['elsobelepes']?></td>
            </tr><?php
        }
        ?></tbody>
        </table>
        <?php
        if(@$previd)
        {
            ?><div class='left'><a href="<?=$RootPath?>/felhasznalok/oldal/<?=$previd?>">Előző oldal</a></div><?php
        }

        if(@$nextid)
        {
            ?><div class='right'><a href="<?=$RootPath?>/felhasznalok/oldal/<?=$nextid?>">Következő oldal</a></div><?php
        }
    ?></div><?php
    $enablekeres = true;
}
?>