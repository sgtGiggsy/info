<?php

if(@!$sajatolvas)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/beepitesdb.php");
    }

    $beepid = $beepnev = $beepeszk = $beepip = $beeprack = $beephely = $beeppoz = $beepido = $beepkiep = null;
    $button = "Beépítés";

    $ipcimek = mySQLConnect("SELECT * FROM ipcimek ORDER BY ipcim ASC");

    if(isset($_GET['id']))
    {
        $beepid = $_GET['id'];
        $beepitve = mySQLConnect("SELECT * FROM beepitesek WHERE id = $beepid;");
        $beepitve = mysqli_fetch_assoc($beepitve);

        $beepnev = $beepitve['nev'];
        $beepeszk = $beepitve['eszkoz'];
        $beepip = $beepitve['ipcim'];
        $beeprack = $beepitve['rack'];
        $beephely = $beepitve['helyiseg'];
        $beeppoz = $beepitve['pozicio'];
        $beepido = $beepitve['beepitesideje'];
        $beepkiep = $beepitve['kiepitesideje'];

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/beepites&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$beepid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/beepites&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Eszköz beépítése</div>
    <div class="contentcenter">

    <div>
        <label for="nev">Beépítési név:</label><br>
        <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$beepnev?>"></input>
    </div>
    
    <div>
        <label for="ipcim">IP cím:</label><br>
        <select id="ipcim" name="ipcim">
            <option value="" selected></option><?php
            foreach($ipcimek as $x)
            {
                ?><option value="<?php echo $x["id"] ?>" <?= ($beepip == $x['id']) ? "selected" : "" ?>><?=$x['ipcim']?></option><?php
            }
        ?></select>
    </div>

    <?=eszkozPicker($beepeszk)?>

    <?=helyisegPicker($beephely)?>

    <?=rackPicker($beeprack)?>

    <div>
        <label for="pozicio">Pozíció:</label><br>
        <input type="text" id="pozicio" name="pozicio" value="<?=$beeppoz?>">
    </div>

    <div>
        <label for="beepitesideje">Beépítés ideje</label><br>
        <input type="datetime-local" id="beepitesideje" name="beepitesideje" value="<?=timeStampToDateTimeLocal($beepido)?>">
    </div>

    <div>
        <label for="kiepitesideje">Kiépítés ideje</label><br>
        <input type="datetime-local" id="kiepitesideje" name="kiepitesideje" value="<?=timeStampToDateTimeLocal($beepkiep)?>">
    </div>

    <div class="submit"><input type="submit" name="beKuld" value=<?=$button?>></div>
    </form><?php

    if(isset($_GET['id']))
    {
        ?><form action = '<?=$RootPath?>/beepites'>
        <div class='submit'><input type='submit' value=Mégsem></div>
        </form><?php
    }
?></div><?php
}