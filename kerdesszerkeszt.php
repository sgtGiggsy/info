<div class="oldalcim">Kérdés adminisztrációja</div>
<div class="contentcenter">
<?php
if(!(isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 10))
{
	echo "Nincs jogosultságod az oldal megtekintésére!";
}
else
{
    $button = "Hozzáadás";
    if(isset($_GET['id']))
    {
        $id = $_GET["id"];
        $kerdesadat = mySQLConnect("SELECT kerdesek.id as kerdesid, kerdesek.kerdes as kerdes, valaszszoveg, valaszok.id as valaszid, valaszok.helyes as helyes, letrehozo, letrehozasideje, modosito, modositasideje, (SELECT felhasznalok.nev FROM felhasznalok WHERE letrehozo = felhasznalok.id) AS letrehozonev, (SELECT felhasznalok.nev FROM felhasznalok WHERE modosito = felhasznalok.id) AS modositonev
        FROM kerdesek INNER JOIN valaszok ON kerdesek.id = valaszok.kerdes
        INNER JOIN felhasznalok ON letrehozo = felhasznalok.id
        WHERE kerdesek.id = $id");
        $kerdes = mysqli_fetch_assoc($kerdesadat);
        $button = "Szerkesztés"; ?> 
        <form action="?page=kerdesdb&action=update" method="post">
        <div><label class="szerklabel">Kérdés: <?=$id?></label></div>
        <div style='padding-top: 0px'><small>Létrehozó: <?=$kerdes['letrehozonev']?> (<?=$kerdes['letrehozasideje']?>)</small><br></div>
        <div style='padding-top: 0px'><small>Utoljára módosította: <?=$kerdes['modositonev']?> (<?=$kerdes['modositasideje']?>)</small><br></div>
        <input type ="hidden" id="id" name="id" value=<?=$id?> />
        <?php
    }
    else
    {
        ?><form action="?page=kerdesdb&action=new" method="post"><?php
    } ?>
    <div>
        <label for="kerdes">Kérdés szöveg:<br></label>
        <textarea name="kerdes" id="kerdes"><?php if(isset($kerdes['kerdes'])) { echo $kerdes['kerdes']; } ?></textarea>
    </div>
    <?php
    if(isset($_GET['id']))
    {
        $i = 1;
        foreach($kerdesadat as $x)
        {
            ?>
            <div>
            <label for="valasz<?=$i?>">Válasz <?=$i?>:<br></label>
                <textarea name="valasz<?=$i?>" id="valasz<?=$i?>"><?=$x['valaszszoveg']?></textarea><input type="radio" name="helyes" id="helyes<?=$i?>" value="<?=$x['valaszid']?>" <?php if($x["helyes"] == 0) { } else { ?> checked <?php } ?>>
                <input type ="hidden" id="vid<?=$i?>" name="vid<?=$i?>" value=<?=$x['valaszid']?> />
            </div>
            <?php
            $i++;
        }
    }
    else
    {
    ?>
        <div>
            <label for="valasz1">Válasz 1:<br></label>
            <textarea name="valasz1" id="valasz1"></textarea><input type="radio" name="helyes" value="1">
        </div>
        <div>
            <label for="valasz2">Válasz 2:<br></label>
            <textarea name="valasz2" id="valasz2"></textarea><input type="radio" name="helyes" value="2">
        </div>
        <div>
            <label for="valasz3">Válasz 3:<br></label>
            <textarea name="valasz3" id="valasz3"></textarea><input type="radio" name="helyes" value="3">
        </div>
        <div>
            <label for="valasz4">Válasz 4:<br></label>
            <textarea name="valasz4" id="valasz4"></textarea><input type="radio" name="helyes" value="4">
        </div>
    <?php
    } ?>
    <div class="submit"><input type="submit" value=<?=$button?>></div>
    </form>
    <form action='?page=kerdeslista' method='post'>
        <div class='submit'><input type='submit' value=Mégsem></div>
    </form>
    <?php
}
?></div>