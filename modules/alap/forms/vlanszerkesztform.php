<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/vlanszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }
                else
                {
                    ?><div>
                        <label for="nev">VLAN azonosítója:</label><br>
                        <input type="text" accept-charset="utf-8" name="id" id="id"></input>
                    </div><?php
                }
                
                ?><div>
                    <label for="nev">VLAN neve:</label><br>
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                </div>
        
                <div>
                    <label for="leiras">VLAN leírása:</label><br>
                    <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                </div>
        
                <div>
                    <label for="kceh">KCHEH hálózat:</label><br>
                    <select id="kceh" name="kceh">
                        <option value="" <?=(!$kceh) ? "selected" : "" ?>>Nem</option>
                        <option value="1" <?=($kceh == 1) ? "selected" : "" ?>>Igen</option>
                    </select>
                </div>
        
                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                
            </form>
            <?php cancelForm(); ?>
        </div>
    </div><?php
}