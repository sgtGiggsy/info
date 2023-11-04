<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
//if(!$mindir)
if(@!$mindir)
{
    getPermissionError();
}
else
{
    $alapform = "modules/beallitasok/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
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
    
    if((isset($beallitasfelol) && $beallitasfelol) || (isset($beallitasful) && $beallitasful))
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
        include("./modules/beallitasok/db/menudb.php");

        if(isset($_GET['kuldooldal']))
        {
            redirectToKuldo("szerkesztes");
        }
        else
        {
            header("Location: ./menu?action=edit");
        }
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $magyarazat = "<strong>Menüpont neve</strong><p>Ez jelenik meg a menüben.</p>";
        $magyarazat .= "<strong>Szülő menüpont</strong><p>Akkor kell megadni,
            ha a menüpont egy másik menüpont almenüje.</p>";
        $magyarazat .= "<strong>Megjelenik</strong><p>Itt választható, hogy kinek jelenjen meg a menüpont,
            vagy, hogy megjelenjen-e egyáltalán.</p>";
        $magyarazat .= "<strong>Menüterület</strong><p>A menüterület, ahol a menüpont megjelenik.
            A jelen design mellett a főmenü az 1-es menüterület, a fejléc a 2-es menüterület,
            más menüterület jelenleg nincs.</p>";
        $magyarazat .= "<strong>Sorrend</strong><p>A menüpontok sorrendje fentről lefelé haladva.</p>";
        $magyarazat .= "<strong>Új elem</strong><p>Ha ezt megadjuk, akkor a menüben megjelenik egy + gomb
            a menüponton. Azt a linket kell itt megadni, amivel új elemet lehet felvinni az adatbázisba.
            (Például új aktív eszközt az aktiveszköz?action=addnew cím meghívásával lehet hozzáadni.)</p>";
        $magyarazat .= "<strong>Egyéni/Gyüjtő/Adatbázis oldal</strong>
            <p>A menüpont mindig a <i>gyüjtő</i> oldalt hívja meg,
            az <i>egyéni</i> oldalak a <i>gyüjtő</i> oldalról hívhatóak meg.
            Ha olyan oldalt adnánk hozzá, ami csak <i>egyéni</i> oldalként funkcionál,
            de menüből akarjuk meghívni, akkor azt is <i>gyüjtő</i> oldalként kell hozzáadni.
            Az <i>adatbázis</i> oldal kitöltése csak speciális esetekben szügséges.
            Lényegében, ha nem tudjuk, hogy ki kell-e tölteni, akkor biztosan nem kell.</p>";
        $magyarazat .= "<strong>Fejléc címe</strong>
            <p>A böngészőfülön, és az oldal tartalmi területének tetején megjelenő cím.</p>";
        $magyarazat .= "<strong>Címsorban megjelenő oldal</strong>
            <p>Az oldal rövid linkje. Az itt megadott cím jelenik meg közvetlenül
            a website gyökerének címe után. Például: szervercim/oldal</p>";
        $magyarazat .= "<strong>Tényleges elérési út</strong>
            <p>Az oldal tényleges elérési útja a website gyökeréhez képest.</p>";
        
        $form = $alapform . "menuszerkesztesform";
        $button = "Menük szerkesztése";
        $oldalcim = "Menük szerkesztése";

        $szulo = array();
        foreach($menu as $x)
        {
            $szulo[] = array("id" => $x['id'], "menupont" => $x['menupont']);
        }
        $sortcriteria = 'menupont';
        usort($szulo, function($a, $b)
        {
            return $a['menupont'] > $b['menupont'];
        });

        if(@$_GET['action'] == "addnew")
        {
            $form = $alapform . "menuujform";
            $button = "Menü hozzáadása";
            $oldalcim = "Új menü hozzáadása";
        }
        if(isset($beallitasfelol) && $beallitasfelol)
        {
            if(!$menuszerkmeghivva)
            {
                include('./modules/beallitasok/forms/menuszerkesztesform.php');
                $menuszerkmeghivva = true;
            }
            else
            {
                $button = "Menü hozzáadása";
                $oldalcim = "Új menü hozzáadása";
                include('./modules/beallitasok/forms/menuujform.php');
            }
        }
        else
        {
            include('./templates/edit.tpl.php');
        }
    }

    
}