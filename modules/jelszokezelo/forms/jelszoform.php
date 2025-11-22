<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/jelszokezelo/jelszo?action=<?=($elemid) ? "update" : "addnew" ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if($elemid)
                ?><input type ="hidden" id="id" name="id" value=<?=$elemid?>><?php
            ?><div>
                <label for="uname">Felhasználónév:</label>
                <input type="text" name="uname" placeholder="Felhasználónév" id="uname" value="<?=$uname?>" autocomplete="one-time-code" />
            </div>

            <div>
                <label for="pass">Jelszó:</label>
                <input type="password" name="pass" placeholder="Jelszó" id="pass"  autocomplete="one-time-code" required />
            </div>

            <div>
                <label for="leiras">Leírás:</label>
                <textarea name="leiras" placeholder="Leírás" id="leiras" required><?=$leiras?></textarea>
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>

        </form>
        <?php cancelForm() ?>
    </div><?php
}