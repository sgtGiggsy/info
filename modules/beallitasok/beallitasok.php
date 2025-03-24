<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
if(@!$mindir)
{
    getPermissionError();
}
else
{
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = $beallitasful = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = true;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
        {
            $dbir = true;
        }
    }

    if(isset($_GET['id']))
    {
        $beallitasful = $_GET['id'];
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    if($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/beallitasok/db/beallitasdb.php");

        header("Location: ./beallitasok?sikeres=szerkesztes");
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    if($irhat || $beallitasful)
    {
        $con = mySQLConnect();
        print_r(mysqli_stat($con));
        //echo "<br>";
        //print_r(mysqli_get_connection_stats($con));

        $sugo = array();
        $magyarazat = null;
        $mindir = true;
        $bbutton = "Beállítások mentése";
        $oldalcim = "Főoldal beállítások";

        $beallitassql = mySQLConnect("SELECT * FROM beallitasok");
        $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
        $beallitas = array();
        foreach($beallitassql as $x)
        {
            $beallitas[$x['nev']] = $x['ertek'];
        }

        $beallitasfelol = true;
        $beallitasfulek = array();
        $beallitasfulek[] = array('cimszoveg' => 'Főoldal beállítások', 'formnev' => 'modules/beallitasok/forms/fooldalbeallitasform', 'getnev' => 'fooldal');
        $beallitasfulek[] = array('cimszoveg' => 'Menü beállításai', 'formnev' => 'modules/beallitasok/menu', 'getnev' => 'menu');
        $beallitasfulek[] = array('cimszoveg' => 'Új menüpont', 'formnev' => 'modules/beallitasok/menu', 'getnev' => 'ujmenu');
        $beallitasfulek[] = array('cimszoveg' => 'API beállítások', 'formnev' => 'modules/beallitasok/api', 'getnev' => 'api');
        $beallitasfulek[] = array('cimszoveg' => 'Mail beállítások', 'formnev' => 'modules/beallitasok/forms/mailbeallitasform', 'getnev' => 'mailkuldes');
        $beallitasfulek[] = array('cimszoveg' => 'Munkalap beállítások', 'formnev' => 'modules/beallitasok/forms/munkalapbeallitasform', 'getnev' => 'munkalapok');
        $beallitasfulek[] = array('cimszoveg' => 'Switch ellenőrző beállítások', 'formnev' => 'modules/beallitasok/forms/switchcheckbeallitasform', 'getnev' => 'switchcheckbeallitasa');

        // A beállítások fő oldala
        ?><div class="szerkcard">
            <div class="szerkcardtitle"><span id="beallitastitle"><?=$oldalcim?></span><a class="help" onclick="rejtMutat('magyarazat')">?</a></div><?php
                $i = 1;
                ?><div class="szerkcardoptions"><?php
                    foreach($beallitasfulek as $ful)
                    {
                        ?><div class="szerkcardoptionelement" id="szerkcard-<?=$i?>" <?=($i == 1) ? "style='background-color: var(--infoboxtitle)'" : "" ?>><span onclick="changeTitle('beallitastitle', '<?=$ful['cimszoveg']?>'); showOnlyOne('beallitas-', '<?=$i?>'); showOnlyOne('sugo-', '<?=$i?>')"><?=$ful['cimszoveg']?></span></div><?php
                        if($beallitasful == $ful['getnev'])
                        {
                            $kinyit = $i;
                            $fulszoveg = $ful['cimszoveg'];
                        }
                        $i++;
                    }
                ?></div>
            <div class="szerkcardbody">
                <div class="szerkeszt">
                    <div><?php
                        // A menüszerkesztés az első meghívás során a meglévő menüket szerkeszti,
                        // a következő meghívásnál új menüpontot ad hozzá. Ez a változó adja meg,
                        // hogy megvolt-e már hívva az oldal.
                        $menuszerkmeghivva = false;
                        $j = 1; // A menüszerkesztési oldal megnöveli az i értékét, így más változó kell
                        foreach($beallitasfulek as $szerkform)
                        {
                            ?><div id="beallitas-<?=$j?>" <?=($j > 1) ? 'style="display: none"' : 'style="display: block"' ?>><?php
                                include("./" . $szerkform['formnev'] . ".php");
                                $sugo[] = $magyarazat;
                            ?></div><?php
                            $j++;
                        }
                    ?></div><div id="magyarazat">
                        <h2 style="text-align: center">Súgó</h2><?php
                        $s = 1;
                        foreach($beallitasfulek as $ful)
                        {
                            ?><div id="sugo-<?=$s?>" <?=($s > 1) ? 'style="display: none"' : 'style="display: block"' ?>>
                                <?php print_r($sugo[$s-1]);?>
                            </div><?php
                            $s++;
                        }
                    ?></div>
                </div>
            </div>
        </div><?php
        
        if(isset($kinyit))
        {
            $PHPvarsToJS[] = array(
                'name' => 'fulszoveg',
                'val' => $fulszoveg
            );
            $PHPvarsToJS[] = array(
                'name' => 'kinyit',
                'val' => $kinyit
            );
        }
    }
}