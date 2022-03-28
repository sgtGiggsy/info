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

    $beepid = $beepnev = $beepeszk = $beepip = $beeprack = $beephely = $beeppoz = $beepido = $beepkiep = $admin = $pass = $megjegyzes = null;
    $button = "Beépítés";

    if(isset($_GET['eszkoz']))
    {
        $beepeszk = $_GET['eszkoz'];
    }

    $where = null;
    if(!isset($_GET['id']))
    {
        $where = "WHERE beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL";
    }
    $ipcimek = mySQLConnect("SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim
        FROM ipcimek
            LEFT JOIN beepitesek ON ipcimek.id = beepitesek.ipcim
        $where
        ORDER BY vlan, ipcimek.ipcim;");

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
        $admin = $beepitve['admin'];
        $pass = $beepitve['pass'];
        $megjegyzes = $beepitve['megjegyzes'];

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

    <?=eszkozPicker($beepeszk, ($beepid) ? true : false)?>

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

    <div>
        <label for="admin">Admin user:</label><br>
        <input type="text" accept-charset="utf-8" name="admin" id="admin" value="<?=$admin?>"></input>
    </div>

    <div>
        <label for="pass">Jelszó:</label><br>
        <input type="text" accept-charset="utf-8" name="pass" id="pass" value="<?=$pass?>"></input>
    </div>
    
    <div>
        <label for="megjegyzes">Megjegyzés:</label><br>
        <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
    </div>

    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
    cancelForm();
?></div><?php
}