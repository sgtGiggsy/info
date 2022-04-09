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

    $button = "Beállítások mentése";
    $beallitassql = mySQLConnect("SELECT * FROM beallitasok");
    $beallitas = array();
    foreach($beallitassql as $x)
    {
        $beallitas[$x['nev']] = $x['ertek'];
    }    

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

    <?php include("./menuszerkeszt.php"); ?>

    <form action="<?=$RootPath?>/beallitasok?action=update" method="post">
    <div class="oldalcim"><p onclick="rejtMutat('munkalapok')" style="cursor: pointer">Munkalapok</p></div>
    <div class="contentcenter" id="munkalapok"style='display: none'>

        <?=helyisegPicker($beallitas['defaultmunkahely'], "defaultmunkahely")?>

        <div>
        <label for="defaultugyintezo">Alapértelmezett ügyintéző:</label><br>
            <?=felhasznaloPicker($beallitas['defaultugyintezo'], "defaultugyintezo", false)?>
        </div>
    </div>

    <div class="oldalcim"><p onclick="rejtMutat('fooldal')" style="cursor: pointer">Főoldal tartalma</p></div>
    <div class="contentcenter" id="fooldal" style='display: none'>
        <div>
            <label for="udvozloszoveg">Üdvözlőszöveg:
            <textarea name="udvozloszoveg" id="udvozloszoveg"><?php if(isset($beallitas['udvozloszoveg'])) { echo $beallitas['udvozloszoveg']; } ?></textarea></label>
        </div>
        <div>
            <label for="udvozloszovegbelepve">Üdvözlőszöveg bejelentkezett felhasználóknak:
            <textarea name="udvozloszovegbelepve" id="udvozloszovegbelepve"><?php if(isset($beallitas['udvozloszovegbelepve'])) { echo $beallitas['udvozloszovegbelepve']; } ?></textarea></label>
        </div>
    </div>
    <div class="submit"><input type="submit" value='<?=$button?>'></div>
    </form>
<?php
if(isset($_GET['menupontok']))
{
    ?><script>window.onload = function()
	{
        document.getElementById('menuk').style.display = "grid";
    }
    </script><?php
}
}