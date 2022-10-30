<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = $rovid = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/alap/db/alakulatdb.php");
    }
    
    $button = "Új alakulat";

    if(isset($_GET['id']))
    {
        $alakulatid = $_GET['id'];
        $alakulatszerk = mySQLConnect("SELECT * FROM alakulatok WHERE id = $alakulatid;");
        $alakulatszerk = mysqli_fetch_assoc($alakulatszerk);

        $id = $alakulatszerk['id'];
        $nev = $alakulatszerk['nev'];
        $rovid = $alakulatszerk['rovid'];

        $button = "Alakulat szerkesztése";

        ?><div class="oldalcim">Alakulat szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/alakulatszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$alakulatid?>><?php
    }
    else
    {
        ?><div class="oldalcim">Új alakulat rögzítése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/alakulatszerkeszt?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

        ?><div>
            <label for="nev">Alakulat teljes megnevezése:</label><br>
            <textarea name="nev" id="nev"><?=$nev?></textarea>
            
        </div>

        <div>
            <label for="rovid">Alakulat rövid neve:</label><br>
            <input type="text" accept-charset="utf-8" name="rovid" id="rovid" value="<?=$rovid?>"></input>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}