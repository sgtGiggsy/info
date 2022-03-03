<div><?php 
if(!isset($kerdesid))
{
    echo "<h2>HIBA!!!</h2><br>Kérdés nem került kiválasztásra";
}
else
{
    $kivalasztva = 0;
    if(isset($_GET['kerdes']))
    {
        $valaszid = $_GET['kerdes'];
        $felhasznalo = $_SESSION[getenv('SESSION_NAME').'id'];
        $szerkeszteni = mySQLConnect("SELECT * FROM tesztvalaszok INNER JOIN kitoltesek ON tesztvalaszok.kitoltes = kitoltesek.id WHERE tesztvalaszok.id = $valaszid AND felhasznalo = $felhasznalo AND befejezett IS NULL");
        if(mysqli_num_rows($szerkeszteni) != 1)
        {
            header("Location: $RootPath/vizsga");
        }
        else
        {
            $data = mysqli_fetch_assoc($szerkeszteni);
            $_SESSION[getenv('SESSION_NAME').'valaszszerkeszt'] = $valaszid;
            $kivalasztva = $data['valasz'];
            $kerdesid = $data['kerdes'];
        }
    }
    $kerdesadat = mySQLConnect("SELECT kerdesek.id as kerdesid, kerdesek.kerdes as kerdes, valaszszoveg, valaszok.id as valaszid
    FROM kerdesek LEFT JOIN valaszok ON kerdesek.id = valaszok.kerdes
    WHERE kerdesek.id = $kerdesid");
    $kerdes = mysqli_fetch_assoc($kerdesadat);

    if(mysqli_num_rows($kerdesadat) == 0)
    {
        echo $kerdesid;
        echo "<h2>HIBA!!!</h2><br>Nincs ilyen sorszámú kérdés az adatbázisban!";
    }
    else
    {
        if($utolso)
        {
            $button = "Vizsga befejezése, kiértékelés";
            $_SESSION[getenv('SESSION_NAME').'lezar'] = true;
        }
        elseif(isset($_GET['kerdes']))
        {
            $button = "Válasz szerkesztése";
            $_SESSION[getenv('SESSION_NAME').'lezar'] = false;
        }
        else
        {
            $button = "Beküld";
            $_SESSION[getenv('SESSION_NAME').'lezar'] = false;
        }
        if(isset($_GET['kerdes']))
        {
            ?><form action="?page=valaszdb&action=update" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        }
        else
        {
            ?><form action="?page=valaszdb&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        }
        ?><h3><?=$kerdes['kerdes']?></h3>
        <?php foreach($kerdesadat as $x)
        { ?>
            <p><input type="radio" id="valasz<?=$x['valaszid']?>" name="valasz" value="<?=$x['valaszid']?>" <?= ($kivalasztva == $x['valaszid']) ? "checked" : "" ?> required >
            <label for="valasz<?=$x['valaszid']?>"><?=$x['valaszszoveg']?></label></p><?php
        } ?>
        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php
    }
}
?></div>