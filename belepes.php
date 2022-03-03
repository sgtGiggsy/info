<div class="oldalcim">Bejelentkezés</div>
<div class="contentcenter">
<form action="<?=$RootPath?>/index.php" method="post">
    <table>
        <tr>
            <td><label for="felhasznalonev">Felhasználónév:</label></td>
            <td><input type="text" accept-charset="utf-8" name="felhasznalonev" placeholder="Felhasználónév" id="felhasznalonev" required></input></td>
        </tr>
        <tr>
            <td><label for="jelszo">Jelszó:</label></td>
            <td><input type="password" name="jelszo" placeholder="Jelszó" id="jelszo" required></td>
        </tr>
    </table>
    <div class="submit"><input type="submit" value="Bejelentkezés"></div>
</form>
</div>