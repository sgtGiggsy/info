<?php
$count = mySQLConnect("SELECT count(*) AS db FROM $adattabla")->fetch_assoc()['db'];

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

if(isset($_GET['subpage']) && $_GET['subpage'] == 'oldal' && isset($_GET['param']) && verifyWholeNum($_GET['param']))
{
    $oldal = $_GET['param'];
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

?><div class='oldalcim'><?=$oldalcim?>
    <div class="szuresvalaszto">
        <form action="tevekenysegek" method="POST">
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
<div class="contentcenter">
    <div class="prevnext">
        <div><?php
            if(@$previd)
            {
                ?><a href="<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$previd?><?=$keres?>">Előző oldal</a><?php
            }
        ?></div>
        <div><?php

        if(@$nextid)
        {
            ?><a href="<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$nextid?><?=$keres?>">Következő oldal</a><?php
        }
        ?></div>
    </div><?php

    include("./" . $table . ".php");

    ?><div class="prevnext">
        <div><?php
            if(@$previd)
            {
                ?><a href="<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$previd?><?=$keres?>">Előző oldal</a><?php
            }
        ?></div>
        <div><?php

        if(@$nextid)
        {
            ?><a href="<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$nextid?><?=$keres?>">Következő oldal</a><?php
        }
        ?></div>
    </div>
</div>