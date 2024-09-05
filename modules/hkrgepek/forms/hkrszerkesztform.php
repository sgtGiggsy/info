<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/hkrszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if(isset($_GET['id']))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
            }

            ?><div>
                <label for="gepnev">Gépnév:</label><br>
                <input type="text" accept-charset="utf-8" name="gepnev" id="gepnev" value="<?=$gepnev?>"></input>
            </div>
    
            <div>
                <?= felhasznaloPicker($felhasznalo, "felhasznalo") ?>
            </div>
    
            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php cancelForm(); ?>
    </div><?php
}