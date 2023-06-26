<?php
if(isset($beuszok) && $beuszok)
{
    $i = 1;
    foreach($beuszok as $beuszo)
    {
        ?><div id="slidein-<?=$i?>">
            <div class="szerkcard">
                <div class="szerkcardtitle"><?=$beuszo['cimszoveg']?><a class="help" onclick="showSlideIn('<?=$i?>')">X</a></div>
                <div class="szerkcardbody">
                        <?php include("./" . $beuszo['formnev'] . ".php") ?>
                </div>
            </div>
        </div><?php
        $i++;
    }
}
?><div class="szerkcard">
    <div class="szerkcardtitle"><?=$oldalcim?><a class="help" onclick="rejtMutat('magyarazat')">?</a></div><?php
    if(isset($beuszok) && $beuszok)
    {
        $i = 1;
        ?><div class="szerkcardoptions"><?php
            foreach($beuszok as $beuszo)
            {
                ?><div class="szerkcardoptionelement"><span onclick="showSlideIn('<?=$i?>')"><?=$beuszo['cimszoveg']?></span></div><?php
                $i++;
            }
        ?></div><?php
    }
    ?>
    <div class="szerkcardbody">
        <div class="szerkeszt">

            <?php include("./" . $form . ".php") ?>

            <div id="magyarazat">
                <h2 style="text-align: center">Súgó</h2>
                <?=$magyarazat?>
            </div>
        </div>
        <?=(isset($csillaggaljelolt)) ? $csillaggaljelolt : "" ?>
    </div>
</div>