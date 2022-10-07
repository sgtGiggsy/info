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
        include("./db/raktardb.php");
    }

    $nev = $alakulat = $helyiseg = null;
    $button = "Beépítés";

    if(isset($_GET['id']))
    {
        $raktar = mySQLConnect("SELECT * FROM raktarak WHERE id = $id;");
        $raktar = mysqli_fetch_assoc($raktar);

        $nev = $raktar['nev'];
        $helyiseg = $raktar['helyiseg'];
        $alakulat = $raktar['alakulat'];

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/raktarszerkeszt&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$id?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/raktarszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Raktár szerkesztése</div>
    <div class="contentcenter">

        <div>
            <label for="nev">Raktár neve:</label><br>
            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
        </div>

        <?=alakulatPicker($alakulat, "alakulat")?>

        <?=helyisegPicker($helyiseg, "helyiseg")?>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
    cancelForm();
?></div><?php
}