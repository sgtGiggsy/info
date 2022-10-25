<?php

?><div class="szerkcard">
    <div class="szerkcardtitle"><?=$oldalcim?><a class="help" onclick="rejtMutat('magyarazat')">?</a></div><?php
    if($beuszok)
    {
        ?><div class="szerkcardoptions">
            <div class="szerkcardoptionelement"><span onclick="showSlideIn('1')">Épületportok rackhez kötése</span></div>
        </div><?php
    }
    ?>
    <div class="szerkcardbody">
        <div class="szerkeszt">

            <?php include("./includes/forms/" . $form . ".php") ?>

            <div id="magyarazat">
                <h2 style="text-align: center">Magyarázat</h2>
                <?=$magyarazat?>
            </div>
        </div>
    </div>
</div><?php
if($beuszok)
{
    $i = 1;
    foreach($beuszok as $beuszo)
    {
        ?><div id="slidein-<?=$i?>" onmouseleave="showSlideIn('<?=$i?>')">
            <div class="szerkcard">
                <div class="szerkcardtitle"><?=$beuszo['cimszoveg']?></div>
                <div class="szerkcardbody">
                    <div class="contentcenter">
                        <?php include("./includes/forms/" . $beuszo['formnev'] . ".php") ?>
                    </div>
                </div>
            </div>
        </div><?php
        $i++;
    }
}
