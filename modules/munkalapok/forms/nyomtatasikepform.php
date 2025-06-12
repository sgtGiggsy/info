<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="" method="post" onsubmit="beKuld.disabled = true; return true;">
            <div>
                <label for="munkalapfejlec">Munkalap fejléce:</label><br>
                <textarea class="wideta" name="munkalapfejlec" id="munkalapfejlec"><?=$beallitas["munkalapfejlec"]?></textarea>
            </div>

            <?php $magyarazat = "" ?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php cancelForm() ?>
    </div><?php
}