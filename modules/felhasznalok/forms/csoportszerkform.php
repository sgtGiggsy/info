<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/csoport?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }
   
                ?><div>
                    <label for="nev">Csoport neve:</label><br>
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                </div>

                <div>
                    <label for="leiras">A csoport leírása:</label><br>
                    <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                </div>

                <div>
                    <label for="szak">A csoport szakterülete:</label><br>
                    <select name="szak" id="szak">
                        <option value="" selected></option>
                        <option value="1" <?=($szak == 1) ? "selected" : "" ?>>Híradó</option>
                        <option value="2" <?=($szak == 2) ? "selected" : "" ?>>Informatika</option>
                    </select>
                </div>

                <div class="submit"><input type="submit" value='<?=$button?>'></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}