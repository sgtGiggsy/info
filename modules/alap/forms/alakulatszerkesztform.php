<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/alakulatszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }   
                ?><div>
                    <label for="nev">Alakulat teljes megnevezése:</label><br>
                    <textarea name="nev" id="nev"><?=$nev?></textarea>
                </div>
    
                <div>
                    <label for="rovid">Alakulat rövid neve:</label><br>
                    <input type="text" accept-charset="utf-8" name="rovid" id="rovid" value="<?=$rovid?>"></input>
                </div>
    
                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form>
            <?php cancelForm(); ?>
        </div>
    </div><?php
}