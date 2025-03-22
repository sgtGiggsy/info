<?php

if(!$_SESSION['id'] || !$csoportolvas)
{
    getPermissionError();
}
else
{
    $paramarr = array();
    $javascriptfiles[] = "modules/feladattervezo/includes/feladatterv.js";
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
        include("./modules/feladattervezo/db/feladattervdb.php");

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
    $feladatterv = $untildeadline = $felhasznalo = $rovid = $bovitett = $bejelentesideje = $fajl = $eszkozneve =
    $szakid = $tipus = $epulet = $helyiseg = $allapot = $elsomegtekintes = $ugyintezo = $lezarasideje = null;

    $button = "Feladat mentése";
    $oldalcim = "Feladat tervezése";
    $form = "modules/feladattervezo/forms/feladattervform";
    $javascriptfiles[] = "modules/feladattervezo/includes/feladattervezo.js";

    if(isset($_GET['id']))
    {
        // Először kiválasztjuk a megjelenítendő hibajegyek listáját.
        // Plusz jogosultság nélkül mindenki csak a sajátját látja.

        $where = "WHERE (feladatterv_feladatok.feladat_id = ? OR feladatterv_feladatok.szulo = ?)";
        $paramarr[] = $id;
        $paramarr[] = $id;

        if($mindolvas)
        {}
        elseif($csoportolvas)
        {
            $where .= " AND (felvivo.szervezet = ?)";
            $paramarr[] = $szervezet;
        }

        $csoportwhere = null;
        if(!$mindolvas)
        {
            // A CsoportWhere űrlapja
            $csopwhereset = array(
                'tipus' => "szervezet",                 // A szűrés típusa, null = mindkettő, szervezet = szervezet, telephely = telephely
                'and' => false,                          // Kerüljön-e AND a parancs elejére
                'szervezetelo' => "felvivo",                  // A tábla neve, ahonnan az szervezet neve jön
                'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
                'szervezetnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az szervezetszűréshez
                'telephelynull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
                'szervezetmegnevezes' => "szervezet"    // Az szervezetot tartalmazó mező neve a felhasznált táblában
            );

            $csopwhere = csoportWhere_new($csoporttagsagok, $csopwhereset);
            $csoportwhere = "OR " . $csopwhere[0] . ")";
            $paramarr = array_merge($paramarr, $csopwhere[1]);
        }

        if(!$csoportir)
        {
            $csoportwhere = ")";
        }

        $feladatterv  = new MySQLHandler("SELECT rovid, leiras, prioritas, allapot, szulo, szakid, epulet, felvitte, modositotta,
                    ido_letrehoz, ido_tervezett, ido_tenyleges, ido_hatarido,
                    szakok.nev AS szaknev,
                    feladatterv_feladatok.feladat_id AS feladat_id,
                    felvivo.nev AS felvivo_nev,
                    felvivo.szervezet AS szervezet,
                    modosito.nev AS modosito_nev,
                    epuletek.nev AS epulet_nev,
                    epuletek.szam AS epulet_szam,
                    telephelyek.telephely AS telephely,
                    prioritasok.nev AS prioritasnev,
                    GROUP_CONCAT(DISTINCT felelos.id SEPARATOR ',;,') AS felelosids,
                    GROUP_CONCAT(DISTINCT felelos.nev SEPARATOR ',;,') AS felelosnevek,
                    GROUP_CONCAT(fajl SEPARATOR ',;,') AS fajlok,
                    GROUP_CONCAT(feladatterv_fajlok.felhasznalo_id SEPARATOR ',;,') AS fajlfeltoltoids,
                    GROUP_CONCAT(feltoltesek.timestamp SEPARATOR ',;,') AS feltoltesidok,
                    GROUP_CONCAT(fajlfeltolto.nev SEPARATOR ',;,') AS fajlfeltoltonevek,
                    GROUP_CONCAT(feladatterv_kommentek.szoveg SEPARATOR ',;,') AS kommentek,
                    GROUP_CONCAT(feladatterv_kommentek.timestamp SEPARATOR ',;,') AS kommentidok,
                    GROUP_CONCAT(kommenter.nev SEPARATOR ',;,') AS kommenterek,
                    COUNT(DISTINCT feladatterv_felelosok.felelos_id) AS felelosszam,
                    COUNT(DISTINCT feladatterv_fajlok.feladatfajl_id) AS fajlszam,
                    COUNT(DISTINCT feladatterv_kommentek.komment_id) AS kommentszam
            FROM feladatterv_feladatok
                LEFT JOIN feladatterv_fajlok ON feladatterv_feladatok.feladat_id = feladatterv_fajlok.feladat_id
                LEFT JOIN feladatterv_felelosok ON feladatterv_feladatok.feladat_id = feladatterv_felelosok.feladat_id
                LEFT JOIN feladatterv_kommentek ON feladatterv_feladatok.feladat_id = feladatterv_kommentek.feladat_id
                LEFT JOIN felhasznalok felvivo ON feladatterv_feladatok.felvitte = felvivo.id
                LEFT JOIN felhasznalok modosito ON feladatterv_feladatok.modositotta = modosito.id
                LEFT JOIN felhasznalok felelos ON feladatterv_felelosok.felhasznalo_id = felelos.id
                LEFT JOIN felhasznalok fajlfeltolto ON feladatterv_fajlok.felhasznalo_id = fajlfeltolto.id
                LEFT JOIN felhasznalok kommenter ON feladatterv_kommentek.felhasznalo_id = kommenter.id
                LEFT JOIN epuletek ON feladatterv_feladatok.epulet = epuletek.id
                LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
                LEFT JOIN feltoltesek ON feladatterv_fajlok.feltoltes_id = feltoltesek.id
                LEFT JOIN prioritasok ON feladatterv_feladatok.prioritas = prioritasok.id
                LEFT JOIN szakok ON feladatterv_feladatok.szakid = szakok.id
            $where $csoportwhere
            GROUP BY feladatterv_feladatok.feladat_id
            ORDER BY feladatterv_feladatok.feladat_id ASC, ido_tervezett ASC;", ...$paramarr);
        
        // Ha nincs feladatterv, akkor letiltjuk a hozzáférést
        if($feladatterv->sorokszama == 0)
        {
            $feladatterv = false;
        }
        else
        {
            $feladattervszuloszerv = $feladatterv->Fetch()['szervezet'];
        }
    }

    // Ha a címsor alapján adatmódosítást akart a felhasználó végrehajtani, a form betöltése
    if(isset($_GET['action']) && ($_GET['action'] == "addnew" || $_GET['action'] == "edit"))
    {
        include('./templates/edit.tpl.php');
    }

    // Ha a $feladatterv változó false állapotó, hiba adása, és kilépés
    elseif(!$feladatterv)
    {
        echo "<br><h2>Nincs ilyen sorszámú hibajegy, vagy nincs jogosultsága a megtekintéséhez!</h2>";
    }

    // Ha ide futunk ki, az adott feladatterv megjelenítése következik
    elseif($id)
    {
        // Megállapítjuk, hogy a felhasználó írhatja-e a feladatot
        $irhat = false;
        if($mindir)
        {
            $irhat = true;
        }
        elseif($csoportir)
        {
            foreach($csoporttagsagok as $csoport)
            {
                if($csoport['szervezet'] == $feladattervszuloszerv || $csoport['szervezet'] == $szervezet)
                {
                    $irhat = true;
                    break;
                }
            }
        }
        
        ?><div class="allapotjelentesek"><?php
            $kattinthatolink = null;
            foreach($feladatterv->Result() as $feladatelem)
            {
                $felelosok = concatToAssocArray(array('id', 'nev'), $feladatelem['felelosids'], $feladatelem['felelosnevek']);
                $fajlok = concatToAssocArray(array('fajl', 'felhasznalo_id', 'timestamp', 'nev'), $feladatelem['fajlok'], $feladatelem['fajlfeltoltoids'], $feladatelem['feltoltesidok'], $feladatelem['fajlfeltoltonevek']);
                $kommentek = concatToAssocArray(array('szoveg', 'timestamp', 'nev'), $feladatelem['kommentek'], $feladatelem['kommentidok'], $feladatelem['kommenterek']);
                if($feladatelem['ido_hatarido'])
                {
                    $untildeadline = strtotime($feladatelem['ido_hatarido']) - time();
                    $urgentdeadline = ($untildeadline < 172800) ? true : false;
                }
                $fajlszam = $feladatelem['fajlszam'];
                $kommentszam = $feladatelem['kommentszam'];
                $furtherdeets = ($feladatelem['leiras'] || $fajlszam > 0 || $kommentszam > 0) ? true : false;
                $kepek = array();
                $doksik = array();

                foreach($fajlok as $fajl)
                {
                    if(str_contains_any($fajl['fajl'], array('jpg', 'jpeg', 'png', 'gif')))
                        $kepek[] = $fajl;
                    else
                        $doksik[] = $fajl;
                }
                
                $feladatelem['epulet'];
                $feladatelem['felvitte'];
                $feladatelem['modositotta'];
                $feladatelem['ido_tenyleges'];
                $feladatelem['szervezet'];
                
                switch($feladatelem['prioritas'])
                {
                    case 2 : $urgclass = "fontos"; break;
                    case 3 : $urgclass = "surgos"; break;
                    case 4 : $urgclass = "kritikus"; break;
                    default: $urgclass = "allapotsorszam";
                }

                switch($feladatelem['allapot'])
                {
                    case 0 : $allapot = "Sikertelen"; break;
                    case 1 : $allapot = "Megkezdetlen"; break;
                    case 2 : $allapot = "Folyamatban"; break;
                    case 3 : $allapot = "Befejezve"; break;
                }

                ?><div class="feladatelem <?=($feladatelem['szulo']) ? 'gyermek' : '' ?>"
                    data-surgosseg="<?=$feladatelem['prioritas']?>"
                    data-szakid="<?=$feladatelem['szakid']?>"
                    data-id="<?=$feladatelem['feladat_id']?>"
                    data-szulo="<?=($feladatelem['szulo']) ? $feladatelem['szulo'] : "0" ?>"
                    id="feladat-<?=$feladatelem['feladat_id']?>"
                >
                    <div class="feladatelemdiv">
                        <div class="<?=$urgclass?> allapotelemparent feladatid" title="<?=$feladatelem['prioritasnev']?>">
                            <strong><?=$feladatelem['ido_tervezett']?></strong>
                            <p><small><?=$allapot?></small></p>
                            <p><small><?=$feladatelem['szaknev']?></small></p>
                            <div class="tooldeets"
                                title="Feladat ID: <?=$feladatelem['feladat_id']?>&#013;Létrehozta: <?=$feladatelem['felvivo_nev']?>&#013;Létrehozva: <?=$feladatelem['ido_letrehoz']?>&#013;Modosította: <?=$feladatelem['modosito_nev']?>">
                            </div>
                            <div class="completed">
                                <label class="customcb">
                                <input type="checkbox">
                                    <span class="customcbjelolo"></span>
                                </input>
                            </div>
                        </div>
                        <div class="feladatmain <?=($furtherdeets) ? "feladatnyithato" : "" ?>" <?=($furtherdeets) ? 'onclick="' . 'elemNyit(' . $feladatelem['feladat_id'] . ')"' : '' ?>>
                            <div class="feladatnev">
                                <h2><?=$feladatelem['rovid']?></h2>
                            </div>
                            <div class="feladatleiras" id="leiras-<?=$feladatelem['feladat_id']?>" style="display: none">
                                <?=nl2br($feladatelem['leiras'])?>
                            </div>
                        </div>
                        
                        <div class="basedeets">
                            <div class='felelosok'><?php
                            if($felelosok)
                            {
                                ?><div><h2>Felelősök:</h2></div><?php
                                foreach($felelosok as $felelos)
                                {
                                    ?><div><a href="<?=ROOT_PATH?>/felhasznalo/<?=$felelos['id']?>"><?=$felelos['nev']?></a></div><?php
                                }
                            }
                            ?></div><?php
                            if($feladatelem['telephely'] || $feladatelem['eszkoz'])
                            {
                                ?><div class="felelosok">
                                    <div class="vertflex">
                                        <div><h2>Érintettek:</h2></div>
                                        <p><?=$feladatelem['telephely']?><br>
                                            <?=($feladatelem['epulet_szam']) ? IntRagValaszt($feladatelem['epulet_szam']) . " épület" : "" ?><?=($feladatelem['epulet_szam'] && $feladatelem['epulet_nev']) ? " (" . $feladatelem['epulet_nev'] . ")" : "" ?></div>
                                        </p>
                                </div><?php
                            }
                            ?><div class="felelosok"><?php
                                if($untildeadline) {
                                    //$urgentdeadline = true;
                                    ?><div class="vertflex <?=($urgentdeadline) ? "warning" : "" ?>">
                                        <div><h2><?=($urgentdeadline) ? "KÖZELI HATÁRIDŐ!" : "Határidőig hátravan:" ?></h2></div>
                                        <p><?=secondsToFullFormat($untildeadline, false)?></p>
                                    </div><?php
                                }
                                ?><div class="vertflex tablecardbody">
                                    <div id="fajldb-<?=$feladatelem['feladat_id']?>">Mellékelt fájlok: <?=$fajlszam?></div>
                                    <div id="megjegyzesdb-<?=$feladatelem['feladat_id']?>">Megjegyzések: <?=$kommentszam?></div>
                                </div>
                            </div>
                        </div>
                        <div class="feladatkommentek" id="kommentek-<?=$feladatelem['feladat_id']?>" style="display: none"><?php
                            if($kommentszam > 0)
                            {
                                ?><h2>Megjegyzések</h2>
                                <div class="kommentek"><?php
                                    foreach($kommentek as $komment)
                                    {
                                        ?><div class="allapotvaltozas">
                                            <div class="allapotvaltozasbody"><?=$komment['szoveg']?></div>
                                            <div class="allapotvaltozasmeta">👤<?=$komment['nev']?> 🕓<?=$komment['timestamp']?></div>
                                        </div><?php
                                    }
                                ?></div><?php
                            }
                        ?></div><?php
                        if($fajlszam > 0)
                        {
                            ?><div class="feladatfajlok" id="fajlok-<?=$feladatelem['feladat_id']?>" style="display: none">
                                <div><h2>Feltöltött fájlok</h2></div><?php
                                if(count($kepek) > 0)
                                {
                                    ?><div class="feladatkepek <?=(count($doksik) == 0) ? "fullwidthfajl" : "" ?>"><?php
                                        foreach($kepek as $kep)
                                        {
                                            ?><div>
                                                <img src="<?=ROOT_PATH?>/uploads/<?=$kep['fajl']?>" alt="<?=fajlnevFromPath($kep['fajl'])?>">
                                                <div><small>Feltöltő: <?=$kep['nev']?><br>Feltöltés ideje: <?=$kep['timestamp']?></small></div>
                                            </div><?php
                                        }
                                    ?></div><?php
                                }
                                if(count($doksik) > 0)
                                {
                                    ?><div <?=(count($kepek) == 0) ? "class='fullwidthfajl'" : "" ?>><?php
                                        foreach($doksik as $doksi)
                                        {
                                            ?><div>
                                                <div><a href="<?=ROOT_PATH?>/uploads/<?=$doksi['fajl']?>" target="_blank"><?=fajlnevFromPath($doksi['fajl'])?></a></div>
                                                <div><small>Feltöltő: <?=$doksi['nev']?><br>Feltöltés ideje: <?=$doksi['timestamp']?></small></div>
                                            </div><?php
                                        }
                                    ?></div><?php
                                }
                            ?></div><?php
                        }
                    ?></div>
                </div><?php
            }
        ?></div><?php
    }
}