<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/munkalapok/templateszerkeszt?action=<?=(isset($_GET['param'])) ? 'update' : 'addnew' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if(isset($_GET['param']))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$_GET['param']?>><?php
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