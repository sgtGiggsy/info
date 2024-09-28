<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/felhasznalo?action=sync" method="post">

            <div>
                <label for="felhasznalonev">Felhasználónév:
                <input type="text" accept-charset="utf-8" name="felhasznalonev" placeholder="Felhasználónév" id="felhasznalonev" required></input></label>
            </div>
            
            <div>
                <label for="jelszo">Jelszó:
                <input type="password" name="jelszo" placeholder="Jelszó" id="jelszo" required></label>
            </div>

            <div class="submit"><input type="submit" value="Szinkronizálás megkezdése"></div>
            <?= cancelForm();?>
        </form>
    </div><?php
}
