<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $leiras = $kceh = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/vlandb.php");
    }
    
    $button = "Új VLAN";

    if(isset($_GET['id']))
    {
        $vlanid = $_GET['id'];
        $vlanszerk = mySQLConnect("SELECT * FROM vlanok WHERE id = $vlanid;");
        $vlanszerk = mysqli_fetch_assoc($vlanszerk);

        $id = $vlanszerk['id'];
        $nev = $vlanszerk['nev'];
        $leiras = $vlanszerk['leiras'];
        $kceh = $vlanszerk['kceh'];

        $button = "VLAN szerkesztése";

        ?><div class="oldalcim">VLAN szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/vlanszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }
    else
    {
        ?><div class="oldalcim">Új VLAN rögzítése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/vlanszerkeszt?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

        ?><div>
            <label for="id">VLAN azonosító:</label><br>
            <input type="text" accept-charset="utf-8" name="id" id="id" value="<?=$id?>"></input>
        </div>

        <div>
            <label for="nev">VLAN neve:</label><br>
            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
        </div>

        <div>
            <label for="leiras">VLAN leírása:</label><br>
            <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
        </div>

        <div>
            <label for="kceh">KCHEH hálózat:</label><br>
            <select id="kceh" name="kceh">
                <option value="" <?=(!$kceh) ? "selected" : "" ?>>Nem</option>
                <option value="1" <?=($kceh == 1) ? "selected" : "" ?>>Igen</option>
            </select>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}