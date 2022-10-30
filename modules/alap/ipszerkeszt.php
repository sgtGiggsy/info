<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $ipcim = $vlan = $eszkoz = $megjegyzes = $magyarazat = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/ipcimdb.php");
    }
    
    $button = "Új IP cím";

    $eszkozok = mySQLConnect("SELECT * FROM eszkozok");

    if(isset($_GET['id']))
    {
        $ipid = $_GET['id'];
        $ipszerk = mySQLConnect("SELECT * FROM ipcimek WHERE id = $ipid;");
        $ipszerk = mysqli_fetch_assoc($ipszerk);

        $id = $ipszerk['id'];
        $ipcim = $ipszerk['ipcim'];
        $vlan = $ipszerk['vlan'];
        $eszkoz = $ipszerk['eszkoz'];
        $megjegyzes = $ipszerk['megjegyzes'];

        $button = "IP cím szerkesztése";

        ?><div class="oldalcim">IP cím szerkesztése<a class="help" onclick="rejtMutat('magyarazat')">?</a></div>
        <div class="szerkeszt">
            <div class="contentcenter">
                <div>
                    <form action="<?=$RootPath?>/ipszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
                    <input type ="hidden" id="id" name="id" value=<?=$id?>><?php
    }
    else
    {
        ?><div class="oldalcim">Új IP cím rögzítése<a class="help" onclick="rejtMutat('magyarazat')">?</a></div>
        <div class="szerkeszt">
            <div class="contentcenter">
                <div>
                    <form action="<?=$RootPath?>/ipszerkeszt?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

        ?><div>
            <label for="ipcim">IP cím:</label><br>
            <input type="text" accept-charset="utf-8" name="ipcim" id="ipcim" value="<?=$ipcim?>"></input>
        </div>

        <?php $magyarazat .= "<strong>IP cím</strong><p>A szerkeszteni/létrehozni kívánt IP cím.</p>"; ?>

        <?= vlanPicker($vlan) ?>

        <?php $magyarazat .= "<strong>VLAN</strong><p>A vlan amelybe az adott IP cím tartozik.</p>"; ?>

        <datalist id="eszkozok"><?php
            foreach($eszkozok as $x)
            {
                ?><option value="<?=$x['id']?>"><?=$x['sorozatszam']?></option><?php
            }
        ?></datalist>

        <div>
            <label for="eszkoz">Eszköz:</label><br>
            <input type="text" accept-charset="utf-8" name="eszkoz" id="eszkoz" value="<?=$eszkoz?>" list="eszkozok" />
        </div>

        <?php $magyarazat .= "<strong>Eszköz</strong><p><b>Kizárólag</b> akkor kell megadni, ha az olyan végponti eszközhöz van kiadva az IP,
            ami a nyilvántartás egyetlen egyéb pontjában sem tud megjelenni (pl.: VTC eszköz, számítógép).</p>"; ?>

        <div>
            <label for="megjegyzes">Megjegyzés:</label><br>
            <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
        </div>

        <?php $magyarazat .= "<strong>Megjegyzés</strong><p>Az IP címhez tartozó megjegyzés. Leginkább csak akkor lehet rá szükség,
            ha az Eszköz menüpontban megadtunk valamit.</p>"; ?>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div>
    </div>
    <div id="magyarazat">
        <h2 style="text-align: center">Magyarázat</h2>
        <?=$magyarazat?>
    </div>
    </div><?php
}