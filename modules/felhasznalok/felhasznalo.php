<?php
// Ha nincs bejelentkezett felhasználó,
// vagy van, de írási művelettel próbálkozik írási jog nélkül
if((isset($_GET['id']) && $_GET['id'] != @$felhasznaloid && !$csoportolvas) || (!$mindir && isset($_GET['action'])))
{
    getPermissionError();
}
elseif($mindir && isset($_GET['action']))
{
    $magyarazat = null;
    $form = "modules/felhasznalok/forms/";

    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
    if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete" || $_GET['action'] == "sync" || $_GET['action'] == "syncou" || ($_GET['action'] == "permissions" && count($_POST) > 0))
    {
        $irhat = true;
        $dbir = true;
        if($_GET['action'] == "new")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "uj";
        }
        elseif($_GET['action'] == "update" || $_GET['action'] == "permissions")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "szerkesztes";
        }
        elseif($_GET['action'] == "sync" || $_GET['action'] == "syncou")
        {
            // Ez jelzi a visszajelző funkciónak, hogy milyen üzenetet kell kiírnia
            $dbop = "szinkronizalas";
        }
    }

    // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
    if($_GET['action'] == "addnew" || $_GET['action'] == "edit" || $_GET['action'] == "sync" || $_GET['action'] == "syncou" || $_GET['action'] == "permissions")
    {
        $irhat = true;
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt
    if(!$irhat && !$dbir)
    {
        getPermissionError();
    }
    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/felhasznalok/db/felhasznalodb.php");
        // A kiinduló oldalra visszairányító függvény meghívása.
        redirectToKuldo($dbop);
    }
    // Új felhasználó manuális hozzáadása, vagy meglévő szerkesztése
    elseif($_GET['action'] == "addnew" || $_GET['action'] == "edit")
    {
        $felhasznalonev = $nev = $email = $telefon = $szervezet = null;
        $button = "Új felhasználó";
        $oldalcim = "Új felhasználó hozzáadása";
        $form .= "felhasznaloszerkform";
        if(isset($_GET['id']))
        {
            $id = $_GET["id"];
            $query = mySQLConnect("SELECT * FROM felhasznalok WHERE id = $id");
            $felhasznalo = mysqli_fetch_assoc($query);
            if(mysqli_num_rows($query) == 0)
            {
                echo "Nincs ilyen felhasználói azonosító!";
            }
            else
            {
                $felhasznalonev = $felhasznalo['felhasznalonev'];
                $nev = $felhasznalo['nev'];
                $email = $felhasznalo['email'];
                $telefon = $felhasznalo['telefon'];
                $szervezet = $felhasznalo['szervezet'];

                $button = "Felhasználó szerkesztése";
                $oldalcim = "Felhasználó szerkesztése";
                
                $magyarazat .= "<strong style='color: var(--warning)'>Fontos!!!</strong>
                    <p> A felhasználó szerkesztése során megváltoztatott adatok azonnal felülíródnak,
                    amint bejelentkezik az oldalra, vagy manuális szinkronizálás történik az AD-val.</p>";
            }
        }
        else
        {
            $magyarazat .= "<strong>Megjegyzés</strong>
                <p>Az itt megadott adatok mind felülíródnak, amint a felhasználó belép az oldalra,
                vagy manuális szinkronizálás történik az AD-val.</p>";
        }

        include('././templates/edit.tpl.php');
        
    }
    // Felhasználó jogosultságainak szerkesztése
    elseif($_GET['action'] == "permissions")
    {
        $oldalcim = "Felhasználó jogosultságainak módosítása";
        $button = "Jogosultságok módosítása";
        $form .= "jogosultsagform";

        $menu = mySQLConnect("SELECT * FROM menupontok ORDER BY menupont ASC");
        $jogosultsagok = mySQLConnect("SELECT * FROM jogosultsagok WHERE felhasznalo = $id");

        include('././templates/edit.tpl.php');
    }
    // Adatok AD-val történő szinkronizálása
    elseif($_GET['action'] == "sync")
    {
        $magyarazat = "<strong>Adatok szinkronizálása AD-vel</strong>
            <p>A szinkronizáláshoz kapcsolódni kell az Active Directory-hoz.
            Kérlek adj meg egy érvényes Active Directory felhasználónevet és jelszót a folyamat futtatásához.
            A megadott felhasználónévnek nem szüksége adminisztrátori jogokkal rendelkeznie.<br>
            <span style='color: var(--warning)'>A folyamat a felhasználók számától függően akár több percig
            is eltarthat, és ezalatt a rendszer semmilyen visszajelzést nem ad az állapotról!</span></p>";
        
        $oldalcim = "Felhasználók szinkronizálása az Active Directory-val";
        $form .= "felhasznalosyncform";

        include('././templates/edit.tpl.php');
    }

    elseif($_GET['action'] == "syncou")
    {
        $magyarazat = "<strong>Kiválasztott OU szinkronizálása</strong>
            <p>A szinkronizáláshoz kapcsolódni kell az Active Directory-hoz.
            Kérlek adj meg egy érvényes Active Directory felhasználónevet, jelszót, valamint a szinkronizálni kívánt OU-t a folyamat futtatásához.
            A megadott felhasználónévnek nem szüksége adminisztrátori jogokkal rendelkeznie.<br>
            <span style='color: var(--warning)'>A folyamat a felhasználók számától függően akár több percig
            is eltarthat, és ezalatt a rendszer semmilyen visszajelzést nem ad az állapotról!</span></p>";
        
        $oldalcim = "Kiválasztott OU szinkronizálása";
        $form .= "ousyncform";

        include('././templates/edit.tpl.php');
    }
}
// A személyes beállítások meghívása
elseif(isset($_SESSION[getenv('SESSION_NAME').'id']) && isset($_GET['beallitasok']))
{
    $switchstate = false;
    $magyarazat = null;
    $irhat = true;
    $id = $_SESSION[getenv('SESSION_NAME').'id'];
    $form = "modules/felhasznalok/forms/szemelyesbeallitasokform";
    $button = "Beállítások szerkesztése";
    $oldalcim = "Személyes beállítások szerkesztése";
    $jogosultsagok = mySQLConnect("SELECT * FROM jogosultsagok WHERE felhasznalo = $id AND menupont = 5 AND olvasas > 1");
    if(mysqli_num_rows($jogosultsagok) > 0)
    {
        $switchstate = true;
    }

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/felhasznalok/db/szemelyesdb.php");
        header("Location: ./felhasznalo?beallitasok");
    }

    include('././templates/edit.tpl.php');
}
else
{
    if(isset($_GET['id']))
    {
        $felhid = $_GET['id'];
    }
    else
    {
        $felhid = $felhasznaloid;
    }

    $csoportwhere = null;
    if(!$mindolvas && $felhid != $_SESSION[getenv('SESSION_NAME').'id'])
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => "szervezet",                        // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'szervezetelo' => "felhasznalok",                  // A tábla neve, ahonnan az szervezet neve jön
            'telephelyelo' => null,           // A tábla neve, ahonnan a telephely neve jön
            'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
    $query = mySQLConnect("SELECT felhasznalok.id as felhid, felhasznalok.nev AS nev, felhasznalonev, email, elsobelepes, osztaly, telefon, beosztas, profilkep, szervezetek.nev AS szervezet
            FROM felhasznalok
                LEFT JOIN szervezetek ON felhasznalok.szervezet = szervezetek.id
            WHERE felhasznalok.id = $felhid $csoportwhere;");
    $felhasznalo = mysqli_fetch_assoc($query);

    $jogok = mySQLConnect("SELECT menupontok.menupont AS menupont, iras, olvasas
            FROM jogosultsagok
                INNER JOIN menupontok ON jogosultsagok.menupont = menupontok.id
            WHERE felhasznalo = $felhid AND olvasas IS NOT NULL AND olvasas != 0
            ORDER BY menupontok.menupont ASC;");

    if(mysqli_num_rows($query) != 1)
    {
        getPermissionError();
    }
    else
    {
        ?><div class="dyntripplecol">
            <div class="infobox">
                <div class="infoboxtitle">Felhasználó adatai<?php
                    if($mindir)
                    {
                        ?><a class="help" href="<?=$RootPath?>/felhasznalo/<?=$felhid?>?action=edit"><img src='<?=$RootPath?>/images/edit.png' alt='Felhasználó módosítása' title='Felhasználó módosítása'/></a><?php
                    }
                ?></div>
                <div class="infoboxbody">
                    <div class="infoboxbodytwocol">
                        <div><?php
                            if($felhasznalo['profilkep'])
                            {
                                ?><img src="data:image/jpeg;base64,<?=base64_encode($felhasznalo['profilkep'])?>" /><?php
                            }
                        ?></div>
                        <div></div>
                        <div>Név:</div>
                        <div><?=$felhasznalo['nev']?></div>
                        <div>Felhasználónév:</div>
                        <div><?=$felhasznalo['felhasznalonev']?></div>
                        <div>Szervezet:</div>
                        <div><?=$felhasznalo['szervezet']?></div>
                        <div>Részleg:</div>
                        <div><?=$felhasznalo['osztaly']?></div>
                        <div>Beosztás:</div>
                        <div><?=$felhasznalo['beosztas']?></div>
                        <div>Telefon:</div>
                        <div><?=$felhasznalo['telefon']?></div>
                        <div>Email:</div>
                        <div><?=$felhasznalo['email']?></div>
                        <div>Első belépés ideje:</div>
                        <div><?=$felhasznalo['elsobelepes']?></div>
                    </div>
                </div>
            </div>

            <div class="infobox">
                <div class="infoboxtitle">Jogosultságok<?php
                    if($mindir)
                    {
                        ?><a class="help" href="<?=$RootPath?>/felhasznalo/<?=$felhid?>?action=permissions"><img src='<?=$RootPath?>/images/edit.png' alt='Jogosultságok módosítása' title='Jogosultságok módosítása'/></a><?php
                    }
                ?></div>
                <div class="infoboxbody">
                    <table>
                        <thead>
                            <tr>
                                <th>Menüpont</th>
                                <th>Olvasási jog</th>
                                <th>Írási jog</th>
                            </tr>
                        </thead>
                        <tbody><?php
                            foreach($jogok as $x)
                            {
                                ?><tr>
                                    <td><?=$x['menupont']?></td>
                                    <td><?=($x['olvasas'] == 3) ? "Mind" : (($x['olvasas'] == 2) ? "Csoport" : (($x['olvasas'] == 1) ? "Saját" : "Semmi")) ?></td>
                                    <td><?=($x['iras'] == 3) ? "Mind" : (($x['iras'] == 2) ? "Csoport" : (($x['iras'] == 1) ? "Saját" : "Semmi")) ?></td>
                                </tr><?php
                            }
                        ?></tbody>
                    </table>
                    <div class="infoboxbodythreecol">
                        <?php
                        
                    ?></div>
                </div>
            </div>

            <div class="infobox">
                <div class="infoboxtitle">Bejelentkezések</div>
                <div class="infoboxbody"><?php
                    include('bejelentkezesek.php');
                ?></div>
            </div>
        </div><?php
    }
}
?>