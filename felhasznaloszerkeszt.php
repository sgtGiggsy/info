<div class="oldalcim">Felhasználó adminisztrációja</div>
<div class="contentcenter">
<?php
if(!($mindir || ($sajatir && $_GET['id'] == $_SESSION[getenv('SESSION_NAME').'id'])))
{
	echo "Nincs jogosultságod az oldal megtekintésére!";
}
else
{
// Adatbázis írás funkció
    if(count($_POST) > 0 && $sajatir)
    {
        $irhat = true;
        include("./db/felhasznalodb.php");
    }

    $felhasznalonev = $nev = $email = $telefon = $alakulat = null;
    if(!isset($_GET['id']))
    {
        $button = "Új felhasználó";
        ?><form action="<?=$RootPath?>/felhasznaloszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true;">
        
            <div style="margin: 0 20px 0 0">
                <label for="felhasznalonev">Felhasználónév:</label><br>
                <input type="text" accept-charset="utf-8" name="felhasznalonev" id="felhasznalonev" value="<?=$felhasznalonev?>"></input>
            </div>

            <div style="margin: 0 20px 0 0">
                <label for="nev">Név:</label><br>
                <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
            </div>

            <div style="margin: 0 20px 0 0">
                <label for="email">Email:</label><br>
                <input type="text" accept-charset="utf-8" name="email" id="email" value="<?=$email?>"></input>
            </div>

            <div style="margin: 0 20px 0 0">
                <label for="telefon">Telefon:</label><br>
                <input type="text" accept-charset="utf-8" name="telefon" id="telefon" value="<?=$telefon?>"></input>
            </div>

            <?php alakulatPicker($alakulat, "alakulat"); ?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form><?php
        cancelForm();
    }
    else
    {
        $id = $_GET["id"];
        $query = mySQLConnect("SELECT * FROM felhasznalok WHERE id = $id");
        if(mysqli_num_rows($query) == 0)
        {
            echo "Nincs ilyen felhasználói azonosító!";
        }
        else
        {
            $felhasznalo = mysqli_fetch_assoc($query);
            $button = "Szerkesztés"; ?>
            <p>Név: <?=$felhasznalo['nev']?></p>
            <p>Felhasználónév: <?=$felhasznalo['felhasznalonev']?></p>
            <p>Email: <?=$felhasznalo['email']?></p>
            <p>Első belépés ideje: <?=$felhasznalo['elsobelepes']?></p><?php

            if($mindir)
            {
                include("jogosultsagok.php");
            }
            ?><form action='felhasznalok' method='POST'>
                <div class='submit'><input type='submit' value=Mégsem></div>
            </form><?php
        }
    }
} 
?></div>