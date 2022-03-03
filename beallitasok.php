<script type ="text/javascript">
    tinymce.init({
        selector: '#udvozloszoveg',
        plugins : 'advlist autolink link image lists charmap print preview code'
    });

    tinymce.init({
        selector: '#udvozloszovegbelepve',
        plugins : 'advlist autolink link image lists charmap print preview code'
    })

    tinymce.init({
        selector: '#lablecinfo',
        plugins : 'advlist autolink link image lists charmap print preview code'
    })
</script>

<div class="oldalcim">Beállítások</div>
<div class="contentcenter">
<?php
if(!isset($_SESSION[getenv('SESSION_NAME').'id']))
{
    echo "<h2>Nincs bejelentkezett felhasználó!</h2>" ?>
    <head><meta http-equiv="refresh" content="1; URL='?page=fooldal'" /></head><?php
}
else
{
    if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
    {
        echo "Nincs jogosultságod az oldal megtekintésére!";
    }
    else
    {
        if(isset($_GET['vizsgareset']))
        {
            mySQLConnect("DELETE FROM `tesztvalaszok`;");
            mySQLConnect("DELETE FROM `kitoltesek`;");
            mySQLConnect("ALTER TABLE `kitoltesek` AUTO_INCREMENT = 1;");
            mySQLConnect("ALTER TABLE `tesztvalaszok` AUTO_INCREMENT = 1");
            header("Location: $RootPath/beallitasok");
        }
        $beallitassql = mySQLConnect("SELECT * FROM beallitasok");
        $beallitas = array();
        foreach($beallitassql as $x)
        {
            $beallitas[$x['nev']] = $x['ertek'];
        }
        $button = "Szerkesztés"; ?> 
        <form action="?page=beallitasdb" method="post">

        <div>
            <label for="vizsgahossz">A vizsga kérdésszáma:
            <input type="text" accept-charset="utf-8" name="vizsgahossz" id="vizsgahossz" value="<?php if(isset($beallitas['vizsgahossz'])) { echo $beallitas['vizsgahossz']; } ?>"></input></label>
        </div>
        <div>
            <label for="minimumhelyes">Minimális helyes válaszok száma:
            <input type="text" accept-charset="utf-8" name="minimumhelyes" id="minimumhelyes" value="<?php if(isset($beallitas['minimumhelyes'])) { echo $beallitas['minimumhelyes']; } ?>"></input></label>
        </div>
        <div>
            <label for="vizsgaido">A vizsgára adott idő:
            <input type="text" accept-charset="utf-8" name="vizsgaido" id="vizsgaido" value="<?php if(isset($beallitas['vizsgaido'])) { echo $beallitas['vizsgaido']; } ?>"></input></label>
        </div>
        <div>
            <label for="ismetelheto">A vizsga ismételhető:</label>
            <select id="ismetelheto" name="ismetelheto">
                <option value="1" <?php if(isset($beallitas['ismetelheto']) && $beallitas['ismetelheto'] == 1) { ?> selected <?php } ?>>Igen</option>
                <option value="0" <?php if(isset($beallitas['ismetelheto']) && $beallitas['ismetelheto'] == 0) { ?> selected <?php } ?>>Nem</option>
            </select>
        </div>
        <div>
            <label for="maxismetles">Újrapróbálkozások maximális száma:
            <input type="text" accept-charset="utf-8" name="maxismetles" id="maxismetles" value="<?php if(isset($beallitas['maxismetles'])) { echo $beallitas['maxismetles']; } ?>"></input></label>
        </div>
        <div>
            <label for="udvozloszoveg">Üdvözlőszöveg:
            <textarea name="udvozloszoveg" id="udvozloszoveg"><?php if(isset($beallitas['udvozloszoveg'])) { echo $beallitas['udvozloszoveg']; } ?></textarea></label>
        </div>
        <div>
            <label for="udvozloszovegbelepve">Üdvözlőszöveg bejelentkezett felhasználóknak:
            <textarea name="udvozloszovegbelepve" id="udvozloszovegbelepve"><?php if(isset($beallitas['udvozloszovegbelepve'])) { echo $beallitas['udvozloszovegbelepve']; } ?></textarea></label>
        </div>
        <div>
            <label for="lablecinfo">Infó rész a láblécben:
            <textarea name="lablecinfo" id="lablecinfo"><?php if(isset($beallitas['lablecinfo'])) { echo $beallitas['lablecinfo']; } ?></textarea></label>
        </div>
        <div class="submit"><input type="submit" value=<?=$button?>></div>
        </form>
        <?php
        if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 50 && isset($_GET['torlomod']))
        {
            ?><a href="?vizsgareset" style="color: red" onclick="return confirm('Biztos vagy benne, hogy törölni akarod az ÖSSZES VISZGÁT?')">Korábbi vizsgák törlése</a><?php
        }
        elseif($_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 50 && !isset($_GET['torlomod']))
        {
            ?><a href="?torlomod" style="color: red" onclick="return confirm('Biztos vagy benne, hogy bekapcsolod a törlő módot?')">Törlőmód bekapcsolása</a><?php
        }
    }
}
?>
</div>