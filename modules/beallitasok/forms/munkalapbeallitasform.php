<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/beallitasok&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">

                <?php $magyarazat = "<h2>Munkalapok beállítása</h2><p></p>"; ?>

                <?=helyisegPicker($beallitas['defaultmunkahely'], "defaultmunkahely")?>

                <?php $magyarazat .= "<strong>Helyiség</strong>
                    <p>Munkalapok készítésekor alapértelmezettként használt helyiség.</p>"; ?>

                <div>
                    <label for="defaultugyintezo">Alapértelmezett ügyintéző:</label><br>
                    <?=felhasznaloPicker($beallitas['defaultugyintezo'], "defaultugyintezo", null)?>
                </div>

                <?php $magyarazat .= "<strong>Alapértelmezett ügyintéző</strong>
                    <p>Munkalapok készítésekor alapértelmezettként kiválasztott ügyintéző.</p>"; ?>
            
            <div class="submit"><input type="submit" name="beKuld" value="<?=$bbutton?>"></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}