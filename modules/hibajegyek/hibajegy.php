<?php
if(!$_SESSION[getenv('SESSION_NAME').'id'])
{
    getPermissionError();
}
else
{
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "stateupdate")
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
        {
            $irhat = true;
        }
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    if($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/hibajegyek/db/hibajegydb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        if($_GET['action'] != "stateupdate")
        {
            afterDBRedirect($con, $last_id);
        }
        else
        {
            afterDBRedirect($con, $_POST['hibajegy']);
        }
    }
    
    // Mivel ehhez a menüponthoz mindenki hozzáfér legalább saját jogosultsággal a legegegyszerűbb
    // itt jogosultságot adni nekik. Olyanokra, akik magasabb jogosultsággal rendelkeznek
    // ez nincs kihatással
    $sajatolvas = $sajatir = true;
    $hibajegy = $magyarazat = $felhasznalo = $rovid = $bovitett = $bejelentesideje = $fajl = $eszkozneve =
    $tipus = $epulet = $helyiseg = $allapot = $elsomegtekintes = $ugyintezo = $lezarasideje = null;

    $button = "Hiba bejelentése";
    $oldalcim = "Hiba bejelentése";
    $form = "modules/hibajegyek/forms/hibajegyform";

    if(isset($_GET['id']))
    {
        // Először kiválasztjuk a megjelenítendő hibajegyek listáját.
        // Plusz jogosultság nélkül mindenki csak a sajátját látja.

        $origid = hashId($id);
        $where = "WHERE hibajegyek.id = $origid";

        if($mindolvas)
        {}
        elseif($csoportolvas)
        {
            $where .= " AND felhasznalok.alakulat = $alakulat";
        }
        elseif($sajatolvas)
        {
            $where .= " AND hibajegyek.felhasznalo = $userid";
        }

        $hibajegy = mySQLConnect("SELECT hibajegyek.id AS hibid,
                hibajegyek.felhasznalo AS felhasznalo,
                alakulatok.nev AS alakulat,
                felhasznalok.nev AS bejelento,
                hibajegyek.rovid AS rovid, bovitett, bejelentesideje,
                eszkozneve, allapot, hibajegyek.epulet AS epulet, helyiseg,
                hibajegyek.tipus AS tipus, prioritas, prioritasok.nev AS prioritasnev, 
                epuletek.nev AS epuletnev, epuletek.szam AS epuletszam,
                helyisegnev, helyisegszam, szakok.nev AS erintettszak,
                (SELECT count(id) FROM hibajegyfajlok WHERE hibajegy = hibid) AS csatolmanyok
            FROM hibajegyek
                INNER JOIN felhasznalok ON hibajegyek.felhasznalo = felhasznalok.id
                LEFT JOIN alakulatok ON felhasznalok.alakulat = alakulatok.id
                LEFT JOIN helyisegek ON hibajegyek.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON hibajegyek.epulet = epuletek.id
                LEFT JOIN szakok ON hibajegyek.tipus = szakok.id
                LEFT JOIN prioritasok ON hibajegyek.prioritas = prioritasok.id
            $where;");
        
        if(mysqli_num_rows($hibajegy) == 1)
        {
            $hibajegy = mysqli_fetch_assoc($hibajegy);

            $felhasznalo = $hibajegy['felhasznalo'];
            $rovid = $hibajegy['rovid'];
            $bovitett = $hibajegy['bovitett'];
            $eszkozneve = $hibajegy['eszkozneve'];
            $tipus = $hibajegy['tipus'];
            $epulet = $hibajegy['epulet'];
            $helyiseg = $hibajegy['helyiseg'];

            $hibajegyallapotok = mySQLConnect("SELECT felhasznalok.nev AS felhasznalo,
                    hibajegyvaltozastipusok.nev AS esemeny,
                    megjegyzes, timestamp
                FROM hibajegyallapotok
                    INNER JOIN felhasznalok ON hibajegyallapotok.felhasznalo = felhasznalok.id
                    INNER JOIN hibajegyvaltozastipusok ON hibajegyallapotok.valtozastipus = hibajegyvaltozastipusok.id
                    WHERE hibajegy = $origid
                ORDER BY timestamp DESC");

            $valtozastipusok = mySQLConnect("SELECT * FROM hibajegyvaltozastipusok");

            $hibajegyfajlok = mySQLConnect("SELECT * FROM hibajegyfajlok WHERE hibajegy = $origid");
        }
        else
        {
            $hibajegy = false;
        }

        $button = "Hibajegy szerkesztése";
        $oldalcim = "Hibajegy szerkesztése";
    }

    if(isset($_GET['action']) && ($_GET['action'] == "addnew" || $_GET['action'] == "edit"))
    {
        include('./templates/edit.tpl.php');
    }

    elseif(!$hibajegy)
    {
        echo "<h2>Nincs ilyen sorszámú hibajegy, vagy nincs jogosultsága a megtekintéséhez!</h2>";
    }

    elseif($id)
    {
        ?><div class="infobox fullheight">
            <div class="infoboxtitle"><?php
                if($mindir)
                {
                    if(isset($_GET['setpriority']))
                    {
                        $hibajegyid = hashId($id);
                        $prioritasid = $_GET['setpriority'];
                        mySQLConnect("UPDATE hibajegyek SET prioritas = $prioritasid WHERE id = $hibajegyid");
                        header("Location: $RootPath/hibajegy/$id");
                    }
                    else
                    {
                        $prioritasid = $hibajegy['prioritas'];
                    }

                    switch($prioritasid)
                    {
                        case 1: $szint = "alacsony-font"; break;
                        case 2: $szint = "halaszthato-font"; break;
                        case 3: $szint = "fontos-font"; break;
                        case 4: $szint = "surgos-font"; break;
                        case 5: $szint = "kritikus-font"; break;
                        default: $szint = "";
                    }
                    ?><div class="pickpriority <?=$szint?>"><a onclick="showPopup('prioritas')"><?=($hibajegy['prioritasnev']) ? $hibajegy['prioritasnev'] : "Prioritás beállítása" ?></a>
                        <div id="prioritas">
                            <a href="<?=$RootPath?>/hibajegy/<?=$id?>?setpriority=1">Alacsony</a>
                            <a href="<?=$RootPath?>/hibajegy/<?=$id?>?setpriority=2">Halasztható</a>
                            <a href="<?=$RootPath?>/hibajegy/<?=$id?>?setpriority=3">Fontos</a>
                            <a href="<?=$RootPath?>/hibajegy/<?=$id?>?setpriority=4">Sürgős</a>
                            <a href="<?=$RootPath?>/hibajegy/<?=$id?>?setpriority=5">Kritikus</a>
                        </div>
                    </div>
                    
                    <?php
                }
                ?>

                A(z) <?=$id?>. sorszámú hibajegy adatai<?php
                if($mindir)
                {
                    ?><a class="help" href="<?=$RootPath?>/hibajegy/<?=$id?>?action=edit"><img src='<?=$RootPath?>/images/edit.png' alt='Hibajegy szerkesztése' title='Hibajegy szerkesztése'/></a><?php
                }
            ?></div>
            <div class="infoboxbody fullheight">
                <div class="infoboxbodytwocol">
                    <div>Bejelentő neve</div>
                    <div><?=$hibajegy['bejelento']?></div>
                    <div>Bejelentő alakulata</div>
                    <div><?=$hibajegy['alakulat']?></div>
                    <div>Bejelentés ideje</div>
                    <div><?=$hibajegy['bejelentesideje']?></div>
                    <div>Hibajegy állapota</div>
                    <div><?=($hibajegy['allapot'] == 1) ? "Nyitott" : "Lezárt" ?></div>
                    <div>Meghibásodott eszköz/szolgáltatás típusa</div>
                    <div style="text-transform: capitalize"><?=$hibajegy['erintettszak']?></div>
                    <div>Meghibásodott eszköz/szolgáltatás neve</div>
                    <div><?=$hibajegy['eszkozneve']?></div>
                    <div>A meghibásodás helye</div>
                    <div></div>
                    <div>A hiba rövid leírása</div>
                    <div><?=$hibajegy['rovid']?></div>
                    <div>A hiba részletes leírása</div>
                    <div><?=$hibajegy['bovitett']?></div>
                    <div>A hibajegyhez csatolt kép<?=($hibajegy['csatolmanyok'] > 1) ? "ek" : "" ?></div>
                    <div class="infoboxkepek"><?php
                    $i = 1;
                    foreach($hibajegyfajlok as $fajl)
                    {
                        ?><div class="clickimage">
                            <div id="kep-<?=$i?>">
                                <a onclick="enlargeImage('kep-<?=$i?>')">
                                    <img src="<?=$RootPath?>/uploads/<?=$_SESSION[getenv('SESSION_NAME').'hibajegymappa']?>/<?=$fajl['fajl']?>">
                                </a>
                            </div>
                        </div><?php
                        $i++;
                    }
                    ?></div>
                </div>
            </div>
        </div><?php

        if($hibajegy['allapot'] == 1 || $mindir)
        {
            ?><form action="<?=$RootPath?>/hibajegy&action=stateupdate<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
                <div class="hibajegyallapotupdate">
        
                    <input type ="hidden" id="hibajegy" name="hibajegy" value=<?=$id?>><?php

                    if($mindir)
                    {
                        ?><div>
                            <label for="valtozastipus">Állapot</label><br>
                            <select name="valtozastipus" id="valtozastipus"><?php
                            foreach($valtozastipusok as $valtozastipus)
                            {
                                if($valtozastipus['id'] != 0 && (($hibajegy['allapot'] == 1 && $valtozastipus['id'] != 6)|| ($hibajegy['allapot'] == 0 && $valtozastipus['id'] == 6)))
                                {
                                    ?><option value="<?=$valtozastipus['id']?>"><?=$valtozastipus['nev']?></option><?php
                                }
                            }
                            ?></select>
                        </div><?php
                    }

                    ?><div>
                        <label for="megjegyzes">Megjegyzés:</label><br>
                        <textarea name="megjegyzes" id="megjegyzes"></textarea>
                    </div>

                    <div><input type="submit" name="beKuld" value="Állapot frissítése"></div>
                </div>
            </form><?php
        }

        if(mysqli_num_rows($hibajegyallapotok) == 0)
        {
            ?><div class='oldalcim'>A hibajegyet még nem nyitotta meg senki a felelősök részéről</div><?php
            if($mindir)
            {
                mySQLConnect("INSERT INTO hibajegyallapotok (hibajegy, felhasznalo, valtozastipus, szerepkor)
                        VALUES ($origid, $felhasznaloid, '1', '3');");
            }
        }
        else
        {
            ?><div class='oldalcim'>A hibajegy állapotfrissítései</div><?php
            foreach($hibajegyallapotok as $x)
            {
                print_r($x);
                echo "<br>";
            }
        }
    }
}