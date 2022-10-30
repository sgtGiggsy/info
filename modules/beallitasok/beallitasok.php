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
        include("./modules/beallitasok/db/beallitasdb.php");
    }
    elseif(count($_POST) > 0)
    {
        echo "<h2>Nincs jogosultsága a beállítások elmentésére!</h2>";
    }

    $button = "Beállítások mentése";
    $beallitassql = mySQLConnect("SELECT * FROM beallitasok");
    $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
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

    <?php include("./modules/beallitasok/menuszerkeszt.php"); ?>

    <form action="<?=$RootPath?>/beallitasok?action=update" method="post">
    <div class="oldalcim"><p onclick="rejtMutat('munkalapok')" style="cursor: pointer">Munkalapok</p></div>
    <div class="contentcenter" id="munkalapok"style='display: none'>

        <?=helyisegPicker($beallitas['defaultmunkahely'], "defaultmunkahely")?>

        <div>
        <label for="defaultugyintezo">Alapértelmezett ügyintéző:</label><br>
            <?=felhasznaloPicker($beallitas['defaultugyintezo'], "defaultugyintezo", false)?>
        </div>
    </div>

    <div class="oldalcim"><p onclick="rejtMutat('switchonline')" style="cursor: pointer">Switchek Online ellenőrzése</p></div>
    <div class="contentcenter" id="switchonline"style='display: none'>
        <div>
            <label for="telephely">Telephely:</label><br>
            <select id="telephely" name="telephely">
                <option value="" selected>Mind</option><?php
                foreach($telephelyek as $x)
                {
                    ?><option value="<?=$x["id"]?>" <?= ($beallitas['telephely'] == $x['id']) ? "selected" : "" ?>><?=$x['telephely']?></option><?php
                }
            ?></select>
        </div>

        <div>
            <label for="onlinefigyeles">Switchek online állapotának mutatása:</label><br>
            <label class="kapcsolo">
                <input type="hidden" name="onlinefigyeles" id="onlinefigyeleshidden" value="">
                <input type="checkbox" name="onlinefigyeles" id="onlinefigyeles" value="1" <?= ($beallitas['onlinefigyeles']) ? "checked" : "" ?>>
                <span class="slider"></span>
            </label>
        </div>
    </div>

    <div class="oldalcim"><p onclick="rejtMutat('levelezes')" style="cursor: pointer">Mail beállítások</p></div>
    <div class="contentcenter" id="levelezes"style='display: none'>
        <div>
            <label for="mailkuld">Automatikus mailküldés:</label><br>
            <label class="kapcsolo">
                <input type="hidden" name="mailkuld" id="mailkuldhidden" value="">
                <input type="checkbox" name="mailkuld" id="mailkuld" value="1" <?= ($beallitas['mailkuld']) ? "checked" : "" ?>>
                <span class="slider"></span>
            </label>
        </div>

        <div>
            <label for="mailserver">Mail szerver:</label><br>
            <input type="text" accept-charset="utf-8" name="mailserver" id="mailserver" value="<?=$beallitas['mailserver']?>"></input>
        </div>

        <div>
            <label for="mailport">Mail port:</label><br>
            <input type="text" accept-charset="utf-8" name="mailport" id="mailport" value="<?=$beallitas['mailport']?>"></input>
        </div>

        <div>
            <label for="mailuser">Mail felhasználó:</label><br>
            <input type="text" accept-charset="utf-8" name="mailuser" id="mailuser" value="<?=$beallitas['mailuser']?>"></input>
        </div>

        <div>
            <label for="mailpassword">Mail jelszó:</label><br>
            <input type="text" accept-charset="utf-8" name="mailpassword" id="mailpassword" value="<?=$beallitas['mailpassword']?>"></input>
        </div>

        <div>
            <label for="mailfrom">Mail küldő címe:</label><br>
            <input type="text" accept-charset="utf-8" name="mailfrom" id="mailfrom" value="<?=$beallitas['mailfrom']?>"></input>
        </div>

        <div>
            <label for="mailto">Mail címzett:</label><br>
            <input type="text" accept-charset="utf-8" name="mailto" id="mailto" value="<?=$beallitas['mailto']?>"></input>
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