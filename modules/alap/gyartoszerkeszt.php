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
        include("./modules/alap/db/gyartodb.php");
    }

    $nev = null;
    $button = "Új gyártó";

    if(isset($_GET['id']))
    {
        $gyartoid = $_GET['id'];
        $gyarto = mySQLConnect("SELECT * FROM gyartok WHERE id = $gyartoid;");
        $gyarto = mysqli_fetch_assoc($gyarto);

        $nev = $gyarto['nev'];

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/gyartoszerkeszt&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$gyartoid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/gyartoszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Gyártó szerkesztése</div>
    <div class="contentcenter">

        <div>
            <label for="nev">Modellnév:</label><br>
            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php

    if(isset($_GET['id']))
    {
        ?><form action='<?=$RootPath?>/gyartoklistaja' method="post">
        <div class='submit'><input type='submit' value='Mégsem'></div>
        </form><?php
    }
    ?></div><?php
}