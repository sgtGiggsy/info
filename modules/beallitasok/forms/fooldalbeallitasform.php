<?php
if(@$irhat)
{
    $PHPvarsToJS[] = array(
            'name' => 'selectors',
            'val' => array('udvozloszoveg', 'udvozloszovegbelepve')
        );
    $javascriptfiles[] = "includes/tinymce/tinymce.min.js";
	$javascriptfiles[] = "includes/js/tinyMCEinitializer.js";
    
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/beallitasok&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
                <?php $magyarazat = "<h2>Főoldal beállításai</h2>
                    <p>Itt adhatjuk meg, hogy a főoldalon egy menüpont jelenjen meg,
                    vagy valami szöveges tartalom attól függően, hogy a felhasználó bejelentkezett, vagy sem.
                    Ha van megadva menüpont, úgy az jelenik meg, nem a beállított szöveg.</p>"; ?>
                <div>
                    <label for="fooldalkijelentkezve">A főoldalon megjelenő menüpont látogatóknak</label><br>
                    <select name="fooldalkijelentkezve">
                        <option value="" selected></option><?php
                        foreach($menu as $x)
                        {
                            ?><option value="<?=$x['id']?>" <?=($x['gyujtooldal'] && $x['gyujtooldal'] == $beallitas['fooldalkijelentkezve']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                        }
                    ?></select>
                </div>
                <?php $magyarazat .= "<strong>A főoldalon megjelenő menüpont látogatóknak</strong><p>A főoldal által betöltött menüpont be nem jelentkezett embereknek.</p>"; ?>
                <div>
                    <label for="fooldalbejelentkezve">A főoldalon megjelenő menüpont felhasználóknak</label><br>
                    <select name="fooldalbejelentkezve">
                        <option value="" selected></option><?php
                        foreach($menu as $x)
                        {
                            ?><option value="<?=$x['id']?>" <?=($x['gyujtooldal'] && $x['gyujtooldal'] == $beallitas['fooldalbejelentkezve']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                        }
                    ?></select>
                </div>
                <?php $magyarazat .= "<strong>A főoldalon megjelenő menüpont felhasználóknak</strong><p>A főoldal által betöltött menüpont bejelentkezett embereknek.</p>"; ?>
                <div>
                    <label for="udvozloszoveg">Üdvözlőszöveg látogatóknak:
                    <textarea name="udvozloszoveg" id="udvozloszoveg"><?php if(isset($beallitas['udvozloszoveg'])) { echo $beallitas['udvozloszoveg']; } ?></textarea></label>
                </div>
                <?php $magyarazat .= "<strong>Üdvözlőszöveg látogatóknak</strong><p>A főoldal szövege be nem jelentkezett embereknek.</p>"; ?>
                <div>
                    <label for="udvozloszovegbelepve">Üdvözlőszöveg felhasználóknak:
                    <textarea name="udvozloszovegbelepve" id="udvozloszovegbelepve"><?php if(isset($beallitas['udvozloszovegbelepve'])) { echo $beallitas['udvozloszovegbelepve']; } ?></textarea></label>
                </div>
                <?php $magyarazat .= "<strong>Üdvözlőszöveg felhasználóknak</strong><p>A főoldal szövege bejelentkezetteknek.</p>"; ?>
            <div class="submit"><input type="submit" name="beKuld" value="<?=$bbutton?>"></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}