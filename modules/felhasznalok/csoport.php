<?php
// Ha nincs bejelentkezett felhasználó,
// vagy van, de írási művelettel próbálkozik írási jog nélkül
if((isset($_GET['id']) && $_GET['id'] != @$felhasznaloid && !$mindolvas) || (!$mindir && isset($_GET['action'])))
{
    getPermissionError();
}
elseif($mindir && isset($_GET['action']))
{
    $magyarazat = null;
    $alapform = "modules/felhasznalok/forms/";

    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
    if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete" || $_GET['action'] == "removemember" || $_GET['action'] == 'removeresponsibility')
    {
        $irhat = true;
        $dbir = true;
        if($_GET['action'] == "new")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "uj";
        }
        elseif($_GET['action'] == "update")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "szerkesztes";
        }
        elseif($_GET['action'] == "removemember" || $_GET['action'] == 'removeresponsibility')
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "eltavolitas";
        }
    }

    // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
    if($_GET['action'] == "addnew" || $_GET['action'] == "edit" || $_GET['action'] == "addmember" || $_GET['action'] == "addresponsibility")
    {
        $irhat = true;
    }

    if(($_GET['action'] == "addmember" || $_GET['action'] == "addresponsibility") && isset($_POST) && count($_POST) > 0)
    {
        $dbir = true;
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        getPermissionError();
    }
    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && (count($_POST) > 0 || $_GET['action'] == 'removemember' || $_GET['action'] == 'removeresponsibility'))
    {
        include("./modules/felhasznalok/db/csoportdb.php");

        // A kiinduló oldalra visszairányító függvény meghívása.
        header("Location: $RootPath/csoportok");
    }
    // Új felhasználó manuális hozzáadása, vagy meglévő szerkesztése
    elseif($_GET['action'] == "addnew" || $_GET['action'] == "edit" || $_GET['action'] == "addmember" || $_GET['action'] == "addresponsibility")
    {
        $nev = $szak = $leiras = null;
        $button = "Új csoport";
        $oldalcim = "Új csoport hozzáadása";
        $form = $alapform . "csoportszerkform";
        if(isset($_GET['id']))
        {
            $id = $_GET["id"];
            $query = mySQLConnect("SELECT * FROM csoportok WHERE id = $id");
            $csoport = mysqli_fetch_assoc($query);
            if(mysqli_num_rows($query) == 0)
            {
                echo "Nincs ilyen csoport azonosító!";
            }
            else
            {
                $nev = $csoport['nev'];
                $szak = $csoport['szak'];
                $leiras = $csoport['leiras'];

                $button = "Csoport szerkesztése";
                $oldalcim = "Csoport szerkesztése";
            }
        }
        else
        {
        }

        if($_GET['action'] == "addmember")
        {
            $button = "Csoporthoz ad";
            $oldalcim = "Felhasználó <i>$nev</i> csoporthoz adása";
            $form = $alapform . "csoporthozadform";
        }

        if($_GET['action'] == "addresponsibility")
        {
            $magyarazat .= "<h3>Felelősségi kör hozzáadása</h3><p>Egy felelősségi kör <b>kizárólag</b>
                alakulatra, vagy telephelyre fonatkozhat, <b>soha</b> nem egyszerre mindkettőre.
                Ha egy csoportnak alakulatra és telephelyre is jogosultságot szeretnénk adni,
                úgy azt két külön folyamatban kell megtennünk.</p>";
            $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
            $button = "Felelősség hozzáadása";
            $oldalcim = "Felelősségi kör hozzáadása a(z) <i>$nev</i> csoporthoz";
            $form = $alapform . "csoportfelelosegikorform";
        }

        include('././templates/edit.tpl.php');
    }
}
else
{
    header("Location: $RootPath/csoportok");
}
?>