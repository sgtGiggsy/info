<div id="masterpass-dialog" style="display:none">
    <form action="<?=$RootPath?>/jelszokezelo" method="POST" autocomplete="off">
        <input type="text" name="fake-user" style="display:none">
        <input type="password" name="fake-pass" style="display:none">
        <div>
            <label for="masterpass">Mester jelszó megadása:</label>
            <input type="password" name="masterpass" placeholder="Mesterjelszó" id="masterpass" autocomplete="new-password" />
        </div>
        <div class="submit"><input type="submit" name="beKuld" value="Beküld"></div>
    </form>
</div>