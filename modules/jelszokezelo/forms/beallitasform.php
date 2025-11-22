<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/jelszokezelo/beallitasok?action=masterpass" method="post" onsubmit="beKuld.disabled = true; return true;">
            <div>
                <label for="nev">Régi mester jelszó:</label>
                <input type="password" name="oldpass" placeholder="Eredeti jelszó" id="jelszo" autocomplete="current-password" required>
            </div>

            <div>
                <label for="nev">Új mester jelszó:</label>
                <input type="password" name="newpass" onkeyup="setRequired()" placeholder="Új jelszó" id="newpass" autocomplete="new-password">
            </div>

            <div>
                <label for="nev">Új mester jelszó újra:</label>
                <input type="password" name="newpassrepeat" onkeyup="setRequired()" placeholder="Új jelszó" id="newpassrepeat" autocomplete="new-password">
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>

        </form>
        <?php cancelForm() ?>
    </div><?php
}