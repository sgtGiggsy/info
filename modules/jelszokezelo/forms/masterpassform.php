<div id="masterpass-dialog" style="display:none">
    <span class="closeol" onclick="enterMasterPass()">&times;</span>
    <form action="<?=$RootPath?>/jelszokezelo" method="POST" autocomplete="off">
        <div>
            <label for="masterpass">Mester jelszó megadása:</label>
            <input type="password" name="masterpass" placeholder="Mesterjelszó" id="masterpass" autocomplete="one-time-code" />
        </div>
        <div class="submit"><input type="submit" name="beKuld" value="Beküld"></div>
    </form>
</div>