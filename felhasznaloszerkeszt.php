<div class="oldalcim">Felhasználó adminisztrációja</div>
<div class="contentcenter"><h2>
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
    if(!isset($_GET['id']))
    {
        echo "Nincs kiválasztott felhasználó!";
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
            echo "</h2>";
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