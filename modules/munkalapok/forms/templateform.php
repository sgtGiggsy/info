<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/munkatemplateszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'addnew' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if(isset($_GET['id']))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
            }   
            ?><div>
                <label for="szoveg">A template szövege:</label><br>
                <textarea name="szoveg" id="szoveg"><?=$szoveg?></textarea>
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php cancelForm(); ?>
    </div><?php
}