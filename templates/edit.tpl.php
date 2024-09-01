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
    <div class="szerkcardtitle"><?=$oldalcim?><a class="help" onclick="rejtMutat('magyarazat')"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M478-240q21 0 35.5-14.5T528-290q0-21-14.5-35.5T478-340q-21 0-35.5 14.5T428-290q0 21 14.5 35.5T478-240Zm-36-154h74q0-33 7.5-52t42.5-52q26-26 41-49.5t15-56.5q0-56-41-86t-97-30q-57 0-92.5 30T342-618l66 26q5-18 22.5-39t53.5-21q32 0 48 17.5t16 38.5q0 20-12 37.5T506-526q-44 39-54 59t-10 73Zm38 314q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg></a></div><?php
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

            <div id="magyarazat" style="display: none">
                <h2 style="text-align: center">Súgó</h2>
                <?=$magyarazat?>
            </div>
        </div>
        <?=(isset($csillaggaljelolt)) ? $csillaggaljelolt : "" ?>
    </div>
</div>