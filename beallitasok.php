<?php
if(!$sajatolvas)
{
    echo "<h2>Nincs jogosultsága az oldal megtekintésére!</h2>";
}
else
{
    if(count($_POST) > 0 && @$mindir)
    {
        $irhat = true;
        include("./db/beallitasdb.php");
    }
    elseif(count($_POST) > 0)
    {
        echo "<h2>Nincs jogosultsága a beállítások elmentésére!</h2>";
    }

    include("./menuszerkeszt.php");

    ?><script type ="text/javascript">
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
    <div class="contentcenter"><?php
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
    <form action="<?=$RootPath?>/beallitasok?action=update" method="post">

    <?=helyisegPicker($beallitas['defaultmunkahely'], "defaultmunkahely")?>

    <div>
	<label for="defaultugyintezo">Alapértelmezett ügyintéző a munkalapokon:</label><br>
        <?=felhasznaloPicker($beallitas['defaultugyintezo'], "defaultugyintezo", false)?>
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
    ?></div><?php
}