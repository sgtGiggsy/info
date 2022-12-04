<?php
if(!$_SESSION[getenv('SESSION_NAME').'id'])
{
    getPermissionError();
}
else
{
    $sajatolvas = $sajatir = true;
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
        include("./modules/feladatok/db/hibajegydb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        if($_GET['action'] != "stateupdate")
        {
            afterDBRedirect($con, $last_id);
        }
        else
        {
            afterDBRedirect($con, $_POST['feladat']);
        }
    }
    
    // Mivel ehhez a menüponthoz mindenki hozzáfér legalább saját jogosultsággal a legegegyszerűbb
    // itt jogosultságot adni nekik. Olyanokra, akik magasabb jogosultsággal rendelkeznek
    // ez nincs kihatással
    $hibajegy = $magyarazat = $felhasznalo = $rovid = $bovitett = $bejelentesideje = $fajl = $eszkozneve =
    $szakid = $tipus = $epulet = $helyiseg = $allapot = $elsomegtekintes = $ugyintezo = $lezarasideje = null;

    $button = "Hiba bejelentése";
    $oldalcim = "Hiba bejelentése";
    $form = "modules/feladatok/forms/hibajegyform";

    if(isset($_GET['id']))
    {
        // Először kiválasztjuk a megjelenítendő hibajegyek listáját.
        // Plusz jogosultság nélkül mindenki csak a sajátját látja.

        $where = "WHERE feladatok.pubid = $id AND feladattipus = 1";

        if($mindolvas)
        {}
        elseif($csoportolvas)
        {
            $where .= " AND (felhasznalok.alakulat = $alakulat";
        }
        elseif($sajatolvas)
        {
            $where .= " AND (feladatok.felhasznalo = $felhasznaloid";
        }

        $csoportwhere = null;
        if(!$mindolvas)
        {
            // A CsoportWhere űrlapja
            $csopwhereset = array(
                'tipus' => "alakulat",                 // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
                'and' => false,                          // Kerüljön-e AND a parancs elejére
                'alakulatelo' => "felhasznalok",                  // A tábla neve, ahonnan az alakulat neve jön
                'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
                'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
                'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
                'alakulatmegnevezes' => "alakulat"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
            );

            $csoportwhere = "OR " . csoportWhere($csoporttagsagok, $csopwhereset) . ")";
        }

        if(!$csoportir)
        {
            $csoportwhere = ")";
        }

        $hibajegy = mySQLConnect("SELECT feladatok.id AS hibid,
                feladatok.felhasznalo AS felhasznalo,
                alakulatok.nev AS alakulat, alakulatok.id AS alakulatid,
                hatarido, elhalasztva, felhasznalok.nev AS bejelento,
                felhasznalok.id AS bejelentoid, telefon,
                feladatok.rovid AS rovid, bovitett, timestamp AS bejelentesideje,
                eszkozneve, allapot, feladatok.epulet AS epulet, helyiseg,
                feladatok.szakid AS tipus, prioritas, prioritasok.nev AS prioritasnev, 
                epuletek.nev AS epuletnev, epuletek.szam AS epuletszam,
                helyisegnev, helyisegszam, szakok.nev AS erintettszak,
                (SELECT count(id) FROM feladatfajlok WHERE feladat = hibid) AS csatolmanyok
            FROM feladatok
                INNER JOIN felhasznalok ON feladatok.felhasznalo = felhasznalok.id
                LEFT JOIN alakulatok ON felhasznalok.alakulat = alakulatok.id
                LEFT JOIN helyisegek ON feladatok.helyiseg = helyisegek.id
                LEFT JOIN epuletek ON feladatok.epulet = epuletek.id
                LEFT JOIN szakok ON feladatok.szakid = szakok.id
                LEFT JOIN prioritasok ON feladatok.prioritas = prioritasok.id
            $where $csoportwhere;");
        
        // Ha van hibajegy, akkor lekérjük a további hozzá tartozó adatot
        if(mysqli_num_rows($hibajegy) == 1)
        {
            $hibajegy = mysqli_fetch_assoc($hibajegy);

            $felhasznalo = $hibajegy['felhasznalo'];
            $rovid = $hibajegy['rovid'];
            $bovitett = $hibajegy['bovitett'];
            $eszkozneve = $hibajegy['eszkozneve'];
            $szakid = $hibajegy['tipus'];
            $epulet = $hibajegy['epulet'];
            $helyiseg = $hibajegy['helyiseg'];
            $origid = $hibajegy['hibid'];
            $hibajegyalakulat = $hibajegy['alakulatid'];

            $hibajegyallapotok = mySQLConnect("SELECT felhasznalok.nev AS felhasznalo,
                    allapottipusok.folyamat AS esemeny,
                    megjegyzes, timestamp, szerepkor
                FROM feladatallapotok
                    INNER JOIN felhasznalok ON feladatallapotok.felhasznalo = felhasznalok.id
                    INNER JOIN allapottipusok ON feladatallapotok.allapottipus = allapottipusok.id
                    WHERE feladat = $origid
                ORDER BY timestamp DESC");

            $allapottipusok = mySQLConnect("SELECT * FROM allapottipusok");

            $hibajegyfajlok = mySQLConnect("SELECT *
                FROM feladatfajlok
                    INNER JOIN feltoltesek ON feladatfajlok.feltoltes = feltoltesek.id
                WHERE feladat = $origid");
            
            $prioritasok = mySQLConnect("SELECT * FROM prioritasok;");

            $csoportwhere = null;
            if(!$mindolvas)
            {
                // A CsoportWhere űrlapja
                $csopwhereset = array(
                    'tipus' => "alakulat",                 // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
                    'and' => false,                          // Kerüljön-e AND a parancs elejére
                    'alakulatelo' => "csoportjogok",                  // A tábla neve, ahonnan az alakulat neve jön
                    'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
                    'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
                    'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
                    'alakulatmegnevezes' => "alakulat"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
                );

                $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
            }

            if(!$szakid)
            {
                $szakid = "NULL";
            }

            $felelosok = mySQLConnect("SELECT DISTINCT felhasznalok.id AS felhid,
                    felhasznalok.nev AS felhasznalo,
                    (SELECT id FROM feladatfelelosok WHERE felhasznalo = felhid AND feladat = $origid LIMIT 1) AS felelos
                FROM felhasznalok
                    INNER JOIN csoporttagsagok ON csoporttagsagok.felhasznalo = felhasznalok.id
                    INNER JOIN csoportjogok ON csoporttagsagok.csoport = csoportjogok.csoport
                    INNER JOIN csoportok ON csoporttagsagok.csoport = csoportok.id
                    INNER JOIN jogosultsagok ON jogosultsagok.felhasznalo = csoporttagsagok.felhasznalo
                WHERE menupont = 11 AND iras > 1 AND csoportjogok.alakulat = $hibajegyalakulat AND (csoportok.szak = $szakid OR csoportok.szak IS NULL)");

            $kijeloltek = mySQLConnect("SELECT felhasznalok.id AS felhid,
                    felhasznalok.nev AS felhasznalo
                FROM felhasznalok
                    INNER JOIN feladatfelelosok ON felhasznalok.id = feladatfelelosok.felhasznalo
                WHERE feladat = $origid;");
        }
        // Ha nincs hibajegy, vagy valamiért hibából egynél több találat volt,
        // úgy a hibajegy állapotát false-ra állítjuk a hibaüzenet kedvéért.
        else
        {
            $hibajegy = false;
        }

        $button = "Hibajegy szerkesztése";
        $oldalcim = "Hibajegy szerkesztése";
    }

    // Ha a címsor alapján adatmódosítást akart a felhasználó végrehajtani, a form betöltése
    if(isset($_GET['action']) && ($_GET['action'] == "addnew" || $_GET['action'] == "edit"))
    {
        include('./templates/edit.tpl.php');
    }

    // Ha a $hibajegy változó false állapotó, hiba adása, és kilépés
    elseif(!$hibajegy)
    {
        echo "<br><h2>Nincs ilyen sorszámú hibajegy, vagy nincs jogosultsága a megtekintéséhez!</h2>";
    }

    // Ha van megadott id, úgy a hiba adatainak megjelenítése
    elseif($id)
    {
        // Megállapítjuk, hogy a felhasználó írhatja-e felelősként a hibajegyet
        $irhat = false;
        if($mindir)
        {
            $irhat = true;
        }
        elseif($csoportir)
        {
            foreach($csoporttagsagok as $csoport)
            {
                if($csoport['alakulat'] == $hibajegyalakulat || $csoport['alakulat'] == $alakulat)
                {
                    $irhat = true;
                    break;
                }
            }
        }
        
        // A hibajegy felső sora a szerkesztéssel, és help-pel
        ?><div class="dyntripplecol">
            <div class="infobox fullheight">
                <div class="infoboxtitle"><?php

                    // A prioritásválasztó menü megjelenítése a hiba fejlécében az írás jogú felhasználók részére
                    if($irhat)
                    {
                        if(isset($_GET['setpriority']))
                        {
                            $prioritasid = $_GET['setpriority'];
                            mySQLConnect("UPDATE feladatok SET prioritas = $prioritasid WHERE id = $origid");
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
                        if($hibajegy['allapot'] == 0)
                        {
                            $szint = "";
                        }
                        ?><div class="pickpriority <?=$szint?>"><?=($hibajegy['allapot'] == 1) ? "<a onclick=\"showPopup('prioritas')\">" : "" ?><?=($hibajegy['prioritasnev'] && $hibajegy['allapot'] == 1) ? $hibajegy['prioritasnev'] : (($hibajegy['allapot'] == 0) ? "Lezárva" : "Prioritás beállítása" ) ?><?=($hibajegy['allapot'] == 1) ? "</a>" : "" ?>
                            <div id="prioritas"><?php
                                foreach($prioritasok as $prioritas)
                                {
                                    ?><a href="<?=$RootPath?>/hibajegy/<?=$id?>?setpriority=<?=$prioritas['id']?>"><?=$prioritas['nev']?></a><?php
                                }
                            ?></div>
                        </div><?php
                    }

                    ?>A(z) <?=$id?>. sorszámú hibajegy adatai<?php

                    // A mindir joggal rendelkező felhasználók bármely, a csoportir joggal rendelkező
                    // felhasználók a saját maguk által készített hibajegyek adatait szerkeszthetik
                    if($mindir || ($csoportir && $felhasznalo == $felhasznaloid))
                    {
                        ?><a class="help" href="<?=$RootPath?>/hibajegy/<?=$id?>?action=edit" onclick="return confirm('Figyelem!!!\nA hibajegy állapotának módosítása NEM szerkesztéssel történik. A hibajegy szerkesztésére KIZÁRÓLAG akkor van szükség, ha a felhasználó rosszul adott meg valamilyen adatot. (A hiba helye, leírása, az eszköz neve, típusa)\n\nBiztosan szerkeszteni szeretnéd a hibajegyet?')"><img src='<?=$RootPath?>/images/edit.png' alt='Hibajegy szerkesztése' title='Hibajegy szerkesztése'/></a><?php
                    }
                ?></div>
                <div class="infoboxbody fullheight">
                    <div class="infoboxbodytwocol">
                        <div>Bejelentő neve</div>
                        <div><?=$hibajegy['bejelento']?></div>
                        <div>Bejelentő telefonszáma</div>
                        <div><?=$hibajegy['telefon']?></div>
                        <div>Bejelentő alakulata</div>
                        <div><?=$hibajegy['alakulat']?></div>
                        <div>Bejelentés ideje</div>
                        <div><?=$hibajegy['bejelentesideje']?></div>
                        <div>Hibajegy állapota</div>
                        <div><?=($hibajegy['allapot'] == 1) ? "Nyitott" : "Lezárt" ?></div>
                        <div>Határidő</div>
                        <div><?=($hibajegy['hatarido']) ? $hibajegy['hatarido'] : "Nincs beállítva" ?></div><?php
                        if($hibajegy['elhalasztva'])
                        {
                            ?><div>Elhalasztva</div>
                            <div><?=$hibajegy['elhalasztva']?>-ig</div><?php
                        }
                        ?><div>Meghibásodott eszköz/szolgáltatás típusa</div>
                        <div style="text-transform: capitalize"><?=$hibajegy['erintettszak']?></div>
                        <div>Meghibásodott eszköz/szolgáltatás neve</div>
                        <div><?=$hibajegy['eszkozneve']?></div>
                        <div>A meghibásodás helye</div>
                        <div></div>
                        <div>A hiba rövid leírása</div>
                        <div><?=$hibajegy['rovid']?></div>
                        <div>A hiba részletes leírása</div>
                        <div><?=$hibajegy['bovitett']?></div>
                        <div>Kijelölt felelős(ök)</div>
                        <div><?php
                            foreach($kijeloltek as $kijelolt)
                            {
                                ?><p><?=$kijelolt['felhasznalo']?></p><?php
                            }
                        ?></div>
                        <div>A hibajegyhez csatolt kép<?=($hibajegy['csatolmanyok'] > 1) ? "ek" : "" ?></div>
                        <div class="infoboxkepek"><?php
                        $i = 1;
                        foreach($hibajegyfajlok as $fajl)
                        {
                            ?><div class="clickimage">
                                <div id="kep-<?=$i?>">
                                    <a onclick="enlargeImage('kep-<?=$i?>')">
                                        <img src="<?=$RootPath?>/uploads/<?=$fajl['fajl']?>" alt="<?=$i?>. csatolt kép nem elérhető">
                                    </a>
                                </div>
                            </div><?php
                            $i++;
                        }
                        ?></div>
                    </div>
                </div>
            </div>

            <div class="szerkcard">
                <div class="szerkcardtitle"><?=($irhat) ? "Állapotváltozás" : "További információ megadása" ?></div>
                <div class="szerkcardbody">
                    <form action="<?=$RootPath?>/hibajegy&action=stateupdate<?=$kuldooldal?>" method="post" enctype="multipart/form-data" onsubmit="beKuld.disabled = true; return true;">
                        <div class="hibajegyallapotupdate">
                            <input type ="hidden" id="feladat" name="feladat" value=<?=$id?>>

                            <div>
                                <label for="megjegyzes">Megjegyzés:</label><br>
                                <textarea name="megjegyzes" id="megjegyzes" <?=(!$irhat) ? "required" : "" ?>></textarea>
                            </div>

                            <div><?php
                                if($irhat)
                                {
                                    ?><div id="halasztas" style="display: none">
                                        <label for="elhalasztva">Elhalasztás:</label><br>
                                        <input type="date" id="elhalasztva" name="elhalasztva">
                                    </div>

                                    <div id="hatarido" style="display: none;">
                                        <label for="hatarido">Határidő:</label><br>
                                        <input type="date" id="hatarido" name="hatarido">
                                    </div>
                                    
                                    <div id="felelos" style="display: none;">
                                        <div id="felelos-1" style="display: block;">
                                            <label for="felelos">Felelős</label><br>
                                            <select name="felelos[]" id="felelos_sel-1" class="hjegyfelelosok">
                                                <option value=""></option><?php
                                                foreach($felelosok as $felelos)
                                                {
                                                    if(!$felelos['felelos'])
                                                    {
                                                        ?><option value="<?=$felelos['felhid']?>"><?=$felelos['felhasznalo']?></option><?php
                                                    }
                                                }
                                            ?></select>
                                        </div>
                                        <div id="felelos-2" style="display: none"></div>
                                        <div id="felelos-3" style="display: none"></div>
                                        <div id="felelos-4" style="display: none"></div>
                                        <div id="felelos-5" style="display: none"></div>
                                    </div><?php
                                }

                                ?><div id="fajlok" <?=($irhat) ? 'style="display: none;"' : '' ?>>
                                    <label for="fajlok">Fényképek/képernyőképek hozzáadása</label><br>
                                    <input type="file" name="fajlok[]" accept="image/jpeg, image/png, image/bmp" multiple>
                                </div>
                            </div>

                            <div><?php
                                if($irhat || (!$irhat && $hibajegy['allapot'] == 0))
                                {
                                    ?><div>
                                        <label for="allapottipus">Állapot</label><br>
                                        <select name="allapottipus" id="allapottipus"><?php
                                        foreach($allapottipusok as $allapottipus)
                                        {
                                            if($allapottipus['id'] != 0 && (($hibajegy['allapot'] == 1 && $allapottipus['id'] > 20) || ($irhat && $hibajegy['allapot'] == 0 && $allapottipus['id'] == 2) || (!$irhat && $hibajegy['allapot'] == 0 && $allapottipus['id'] == 1)))
                                            {
                                                ?><option value="<?=$allapottipus['id']?>"><?=$allapottipus['nev']?></option><?php
                                            }
                                        }
                                        ?></select>
                                    </div><?php
                                }

                                ?><div>
                                    <input type="submit" name="beKuld" value="<?=($irhat) ? 'Állapot frissítése' : 'Módosítás küldése' ?>">
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><?php

        ////// Állapotváltozások rész
        // Üres ág
        if(mysqli_num_rows($hibajegyallapotok) == 0)
        {
            ?><div class='oldalcim'>A hibajegyet még nem nyitotta meg senki a felelősök részéről</div><?php

            // Ha írás joggal rendelkező felhasználó nyitja meg, akkor a hibajegy állapota "látott"-ra frissül
            if($irhat)
            {
                mySQLConnect("INSERT INTO feladatallapotok (feladat, felhasznalo, allapottipus, szerepkor)
                        VALUES ($origid, $felhasznaloid, '21', '3');");

                $valosnev = $_SESSION[getenv('SESSION_NAME').'nev'];
                $origfelhasznaloid = $hibajegy['bejelentoid'];
                $origalakulat = $hibajegy['alakulatid'];
                hibajegyErtesites("$valosnev frissítette a(z) $id számú hibajegy állapotát", "Megtekintve", $id, $origfelhasznaloid, $origalakulat);
            }
        }

        // Állapotváltozások listázása ág
        else
        {
            ?><div class='oldalcim'>A hibajegy állapotfrissítései</div><?php
            foreach($hibajegyallapotok as $allapot)
            {
                ?><div class="<?=($allapot['szerepkor'] > 2) ? 'felelos' : 'bejelento' ?>">
                    <div class="allapotvaltozas">
                        <div class="allapotvaltozasfej"><?=$allapot['esemeny']?></div>
                        <div class="allapotvaltozasbody"><?=$allapot['megjegyzes']?></div>
                        <div class="allapotvaltozasmeta">👤<?=$allapot['felhasznalo']?> 🕓<?=$allapot['timestamp']?></div>
                    </div>
                </div>
                <?php
            }
        }

        // A hozzáadni kívánt select. Önmagában semmire sincs használva
        ?><div id="selecttoadd" style="display: none;">
            <br><label for="felelos">Felelős</label><br>
            <select name="felelos[]" id="felelosnew" class="hjegyfelelosok">
                <option value=""></option><?php
                foreach($felelosok as $felelos)
                {
                    ?><option value="<?=$felelos['felhid']?>"><?=$felelos['felhasznalo']?></option><?php
                }
            ?></select>
        </div><?php

        // Az oldalhoz tartozó javascript(ek)
        ?><script>
            var szamlalo = 1;

            function addNewFelelos()
            {
                szamlalo++;
                var origselect = document.querySelector('#selecttoadd');
                var clone = origselect.cloneNode(true);
                var elem = document.getElementById('felelos-' + szamlalo);
                elem.style.display = 'block';
                elem.innerHTML += clone.innerHTML;
                var ujselect = document.getElementById('felelosnew');
                ujselect.id = 'felelos_sel-' + szamlalo;

                document.getElementById('felelos_sel-' + szamlalo).onchange = function() {
                    var ujelem = document.getElementById('felelos_sel-' + szamlalo);
                    if(ujelem.value) {
                        addNewFelelos();
                    }
                };
            }

            function nullFelelosok()
            {
                var felelosok = document.getElementsByClassName('hjegyfelelosok');
                l = felelosok.length;
                for(i = 1; i < l; i++) {
                    felelosok[i].value = "";
                }
            }

            document.getElementById('felelos_sel-' + szamlalo).onchange = function() {
                var ujelem = document.getElementById('felelos_sel-' + szamlalo);
                if(ujelem.value) {
                    addNewFelelos();
                }
            };

            document.getElementById('allapottipus').onchange = function() {
                nullFelelosok();
                if(this.value == '26') {
                    document.getElementById('felelos').style.display = 'none';
                    document.getElementById('halasztas').style.display = 'none';
                    document.getElementById('hatarido').style.display = 'none';
                    document.getElementById('fajlok').style.display = 'block';
                } else if(this.value == '27') {
                    document.getElementById('felelos').style.display = 'none';
                    document.getElementById('halasztas').style.display = 'none';
                    document.getElementById('fajlok').style.display = 'none';
                    document.getElementById('hatarido').style.display = 'block';
                } else if (this.value == '28') {
                    document.getElementById('felelos').style.display = 'none';
                    document.getElementById('hatarido').style.display = 'none';
                    document.getElementById('fajlok').style.display = 'none';
                    document.getElementById('halasztas').style.display = 'block';
                } else if (this.value == '29') {
                    document.getElementById('hatarido').style.display = 'none';
                    document.getElementById('fajlok').style.display = 'none';
                    document.getElementById('halasztas').style.display = 'none';
                    document.getElementById('felelos').style.display = 'block';
                } else {
                    document.getElementById('felelos').style.display = 'none';
                    document.getElementById('hatarido').style.display = 'none';
                    document.getElementById('halasztas').style.display = 'none';
                    document.getElementById('fajlok').style.display = 'none';
                }
            };
        </script><?php
    }
}