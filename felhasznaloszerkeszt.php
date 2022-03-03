<div class="oldalcim">Felhasználó adminisztrációja</div>
<div class="contentcenter"><h2>
<?php
if(!isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) || $_SESSION[getenv('SESSION_NAME').'jogosultsag'] < 50)
{
	echo "Nincs jogosultságod az oldal megtekintésére!";
}
else
{
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
            <p>Első belépés ideje: <?=$felhasznalo['elsobelepes']?></p>

            <form action="?page=felhasznalodb&action=update" method="post">
                <input type ="hidden" id="id" name="id" value=<?=$id?> />
                <div>
                    <label for="jogosultsag">Jogosultság szint
                    <input type="text" accept-charset="utf-8" name="jogosultsag" id="jogosultsag" value="<?=$felhasznalo['jogosultsag']?>"></input></label>
                    <br><small>Jelen oldalon a számnak nincs nagy jelentősége. 10 alatt mindenki felhasználó, afelett mindenki admin. 51-től lesz valaki kvázi szuperadmin akinek joga van a felhasználók admin státuszának megváltoztatására is.</small>
                </div>
                <div class="submit"><input type="submit" value=<?=$button?>></div>
            </form>
            <form action='?page=felhasznalok' method='post'>
                <div class='submit'><input type='submit' value=Mégsem></div>
            </form><?php
        }
    }
} 
?></div>