<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/eszkozalap/gyartoszerkeszt?action=<?=($elemid) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if($elemid)
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$elemid?>><?php
            }

            ?><div>
                <label for="nev">Gyártó neve:</label><br>
                <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>

        </form>
        <?php cancelForm() ?>
    </div><?php
}