<?php

if(@!$mindir)
{
    echo $sajatolvas . $csoportolvas . $mindolvas . $sajatir . $csoportir . $mindir;
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $nev = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/telefonjogosultsagdb.php");
    }
    
    $button = "Új telefonjog";

    if(isset($_GET['id']))
    {
        $telefonjogid = $_GET['id'];
        $telefonjogszerk = mySQLConnect("SELECT * FROM telefonjogosultsagok WHERE id = $telefonjogid;");
        $telefonjogszerk = mysqli_fetch_assoc($telefonjogszerk);

        $id = $telefonjogszerk['id'];
        $nev = $telefonjogszerk['nev'];

        $button = "Telefonjog szerkesztése";

        ?><div class="oldalcim">Telefonjogosultság szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/telefonjogszerk?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="origid" name="origid" value=<?=$telefonjogid?>><?php
    }
    else
    {
        ?><div class="oldalcim">Új telefonjogosultság rögzítése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/telefonjogszerk?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

        ?><div>
            <label for="id">Telefonjogosultság azonosítója:</label><br>
            <input type="text" accept-charset="utf-8" name="id" id="id" value="<?=$id?>"></input>
        </div>
        
        <div>
            <label for="nev">Telefonjogosultság megnevezése:</label><br>
            <textarea name="nev" id="nev"><?=$nev?></textarea>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}