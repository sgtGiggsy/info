<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $ipcim = $vlan = $eszkoz = $megjegyzes = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/ipcimdb.php");
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

        ?><div class="oldalcim">IP cím szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/ipszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$id?>><?php
    }
    else
    {
        ?><div class="oldalcim">Új IP cím rögzítése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/ipszerkeszt?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

        ?><div>
            <label for="ipcim">IP cím:</label><br>
            <input type="text" accept-charset="utf-8" name="ipcim" id="ipcim" value="<?=$ipcim?>"></input>
        </div>

        <?= vlanPicker($vlan) ?>

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

        <div>
            <label for="megjegyzes">Megjegyzés:</label><br>
            <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}