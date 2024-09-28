<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/telefonjogszerk?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if(isset($_GET['id']))
            {
                ?><input type ="hidden" id="origid" name="id" value=<?=$_GET['id']?>><?php
            }

            ?><div>
                <label for="nev">Telefonjogosultság megnevezése:</label><br>
                <textarea name="nev" id="nev"><?=$nev?></textarea>
            </div>
    
            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php cancelForm(); ?>
    </div><?php
}