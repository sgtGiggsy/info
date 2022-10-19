<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $gepnev = $felhasznalo = null;

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/hkrdb.php");
    }
    
    $button = "Új HKR gép";

    if(isset($_GET['id']))
    {
        $hkrszerk = mySQLConnect("SELECT * FROM hkrgepek WHERE id = $id;");
        $hkrszerk = mysqli_fetch_assoc($hkrszerk);

        $gepnev = $hkrszerk['gepnev'];
        $felhasznalo = $hkrszerk['felhasznalo'];

        $button = "HKR gép szerkesztése";

        ?><div class="oldalcim">HKR gép szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/hkrszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$id?>><?php
    }
    else
    {
        ?><div class="oldalcim">Új HKR gép rögzítése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/hkrszerkeszt?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

        ?><div>
            <label for="gepnev">Gépnév:</label><br>
            <input type="text" accept-charset="utf-8" name="gepnev" id="gepnev" value="<?=$gepnev?>"></input>
        </div>

        <div>
            <?= felhasznaloPicker($felhasznalo, "felhasznalo") ?>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}