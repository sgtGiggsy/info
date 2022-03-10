<div class="oldalcim">Felhasználó adminisztrációja</div>
<div class="contentcenter"><h2>
<?php
if(!isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) || $_SESSION[getenv('SESSION_NAME').'jogosultsag'] < 50)
{
	echo "Nincs jogosultságod az oldal megtekintésére!";
}
else
{
// Adatbázis írás funkció    
    if(count($_POST) > 0)
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
            <p>Első belépés ideje: <?=$felhasznalo['elsobelepes']?></p>

            <form action="<?=$RootPath?>/felhasznaloszerkeszt&action=update" method="post">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th style="width: 15%">Csoport olvas</th>
                            <th style="width: 15%">Mind olvas</th>
                            <th style="width: 15%">Saját ír</th>
                            <th style="width: 15%">Mind ír</th>
                        </tr>
                    </thead>
                    <tbody>
                <input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
                foreach($menu as $x)
                {
                    $jogosultsag = array("csoportolvas" => null, "mindolvas" => null, "sajatir" => null, "mindir" => null);
                    foreach($jogosultsagok as $y)
                    {
                        if($y['menupont'] == $x['id'])
                        {
                            $jogosultsag = $y;
                            break;
                        }
                    }
                    ?><tr id="<?=$x['oldal']?>">
                        <td>
                            <label for="<?=$x['oldal']?>"><?=$x['menupont']?></label>
                        </td>
                        <td>
                            <input type="checkbox" value= "1" name="csoportolvas-<?=$x['id']?>" <?=($jogosultsag['csoportolvas']) ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type="checkbox" value= "1" name="mindolvas-<?=$x['id']?>" <?=($jogosultsag['mindolvas']) ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type="checkbox" value= "1" name="sajatir-<?=$x['id']?>" <?=($jogosultsag['sajatir']) ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <input type="checkbox" value= "1" name="mindir-<?=$x['id']?>" <?=($jogosultsag['mindir']) ? 'checked' : '' ?>>
                        </td>
                        <?php
                }
                ?></tbody></table>
                <div class="submit"><input type="submit" value=<?=$button?>></div>
            </form>
            <form action='felhasznalok' method='POST'>
                <div class='submit'><input type='submit' value=Mégsem></div>
            </form><?php
        }
    }
} 
?></div>