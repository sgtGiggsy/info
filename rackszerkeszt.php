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
        include("./db/rackdb.php");
    }

    $racknev = $rackhely = $rackgyarto = $rackunitszam = null;
    $button = "Beépítés";

    $ipcimek = mySQLConnect("SELECT * FROM ipcimek ORDER BY ipcim ASC");

    if(isset($_GET['id']))
    {
        $rackid = $_GET['id'];
        $rack = mySQLConnect("SELECT * FROM rackszekrenyek WHERE id = $rackid;");
        $rack = mysqli_fetch_assoc($rack);

        $racknev = $rack['nev'];
        $rackhely = $rack['helyiseg'];
        $rackgyarto = $rack['gyarto'];
        $rackunitszam = $rack['unitszam'];

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/rackszerkeszt&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$rackid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/rackszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Rack szerkesztése</div>
    <div class="contentcenter">

        <div>
            <label for="nev">Rack neve:</label><br>
            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$racknev?>"></input>
        </div>

        <div>
            <label for="unitszam">Rack unitszáma:</label><br>
            <input type="text" accept-charset="utf-8" name="unitszam" id="unitszam" value="<?=$rackunitszam?>"></input>
        </div>

        <?=helyisegPicker($rackhely)?>

        <?=gyartoPicker($rackgyarto)?>

        <div class="submit"><input type="submit" name="beKuld" value=<?=$button?>></div>
    </form><?php

    if(isset($_GET['id']))
    {
        ?><form action='<?=$RootPath?>/rackek' method="post">
        <div class='submit'><input type='submit' value=Mégsem></div>
        </form><?php
    }
?></div><?php
}