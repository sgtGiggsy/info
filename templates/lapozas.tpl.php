<?php
function PrevNext($firstid, $previd, $nextid, $lastid, $RootPath, $oldalnev, $keres, $icons)
{
    ?><div class="prevnext">
        <div><?php
            if($firstid)
            {
                ?><button type="button" onclick="location.href='<?=$RootPath?>/<?=$oldalnev?><?=$keres?>'"><?=$icons['elsooldal']?></button><?php
            }
            if($previd)
            {
                ?><button type="button" onclick="location.href='<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$previd?><?=$keres?>'"><?=$icons['elozooldal']?></button><?php
            }
        ?></div>
        <div><?php

        if($nextid)
        {
            ?><button type="button" onclick="location.href='<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$nextid?><?=$keres?>'"><?=$icons['kovetkezooldal']?></button><?php
        }
        if($lastid)
        {
            ?><button type="button" onclick="location.href='<?=$RootPath?>/<?=$oldalnev?>/oldal/<?=$lastid?><?=$keres?>'"><?=$icons['utolsooldal']?></button><?php
        }
        ?></div>
    </div><?php
}

if(isset($countquery))
{
    $c = new MySQLHandler($countquery, ...$cqueryparams);
    $count = $c->Fetch()['db'];
}
else
{
    $count = mySQLConnect("SELECT count(*) AS db FROM $adattabla $where")->fetch_assoc()['db'];
}
$firstid = $previd = $nextid = $lastid = null;

if(isset($_POST['oldalankent']))
{
    $_SESSION[$pagetofind . '-oldalankent'] = $_POST['oldalankent'];
}
if(isset($_SESSION[$pagetofind . '-oldalankent']))
{
    $megjelenit = $_SESSION[$pagetofind . '-oldalankent'];
}
else
{
    $megjelenit = 20;
}

if(isset($_GET['subpage']) && $_GET['subpage'] == 'oldal' && isset($_GET['param']) && is_numeric($_GET['param']))
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
    $firstid = 1;
}

if($oldal * $megjelenit < $count)
{
    $nextid = $oldal + 1;
    $lastid = $count / $megjelenit;
    if(!is_int($lastid))
        $lastid = floor($lastid) + 1;
}

?><div class='oldalcim'><?=$oldalcim?>
    <div class="szuresvalaszto">
        <form action="<?=$pagetofind?>" method="POST">
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
<div class="contentcenter"><?php
    PrevNext($firstid, $previd, $nextid, $lastid, $RootPath, $oldalnev, $keres, $icons);
    include("./" . $table . ".php");
    PrevNext($firstid, $previd, $nextid, $lastid, $RootPath, $oldalnev, $keres, $icons);
?></div>