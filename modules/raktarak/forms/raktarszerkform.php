<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/raktar<?=(isset($id)) ? '&action=update' : '?action=new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
                }
                ?><div>
                    <label for="nev">Raktár neve:</label><br>
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                </div>
        
                <?=alakulatPicker($alakulat, "alakulat")?>
        
                <?=helyisegPicker($helyiseg, "helyiseg")?>
        
                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form><?php
            cancelForm();
        ?></div>
    </div><?php
}