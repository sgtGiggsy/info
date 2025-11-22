<?php

###############################################################
#
# Leírás:               Egyszerű jelszókezelő. Egy mesterjelszóval titkosítva tárolja a jelszavakat.
#
# Speciális igény:      Engedélyezett sodium a php.ini-ben és az Apache bin mappájába be kell másolni a libsodium.dll-t.
#                       A libsodium.dll NEM egyezik meg a php ext mappájában található php_sodium.dll-lel!
#
# Külső függőségek:     SweetAlert2
#
# Fejlesztés:           Mesterjelszavak és jelszó pool-ok szétválogatása.
#                       Jelenleg csak egy mesterjelszó van, de megoldható lenne,
#                       hogy csoportonként eltérő legyen.
#
###############################################################

if(!@$csoportolvas)
{
	getPermissionError();
}
elseif(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')
{
    echo "<h2>!!! FIGYELMEZTETÉS !!!</h2><p>Biztonsági okokból a jelszókezelő modul csak biztonságos kapcsolaton keresztül (https) érhető el.</p><p>Ha használni szeretnéd a modult, kérlek a HTTPS-es verzióval töltsd be!</p>";
}
else
{
    $contextmenu = array(
        'jelszavak' => array('gyujtooldal' => 'jelszavak', 'oldal' => 'jelszo', 'gyujtooldalnev' => 'Jelszavak', 'oldalnev' => 'Jelszó'),
        'beallitasok' => array('gyujtooldal' => 'beallitasok', 'oldal' => 'beallitasok', 'gyujtooldalnev' => 'Beállítások', 'oldalnev' => 'Beállítások'),
        'countd' => array('gyujtooldal' => '', 'oldal' => '', 'gyujtooldalnev' => 'Feloldás', 'oldalnev' => 'enterMasterPass()')
    );

    $contextmenujogok['jelszavak'] = $contextmenujogok['beallitasok'] = $contextmenujogok['countd'] = true;

    if(!isset($_SESSION['masterbadpasscount']))
        $_SESSION['masterbadpasscount'] = 0;

    $popup = array(
        "type" => null,
        "message" => null
    );

    if(isset($_POST['masterpass']) && $_SESSION['masterbadpasscount'] < 3)
    {
        $masterpasscheck = (new MySQLHandler("SELECT masterpass FROM jelszokezelo_beallitasok WHERE id = ?;", 1))->Fetch();
        if(!password_verify($_POST['masterpass'], $masterpasscheck['masterpass']))
        {
            $masterpasscheck = new MySQLHandler("INSERT INTO jelszokezelo_log (felhasznalo) VALUES (?);", $felhasznaloid);
            $_SESSION['masterbadpasscount']++;
            $_SESSION['unlockedmaster'] = null;
            $popup['type'] = "warning";
            $popup['message'] = "Hibásan megadott mesterjelszó!";
        }
        else
        {
            $_SESSION['unlockedmaster'] = $_POST['masterpass'];
            $_SESSION['unlockedtime'] = time();
            $_SESSION['masterbadpasscount'] = 0;
            header("Location: $RootPath/jelszokezelo");
        }
    }

    $unlockedtime = time() - @$_SESSION['unlockedtime'];

    // Flood védelem.
    // 1. Ha három rossz jelszó volt, és öt percen belül próbálkozott utoljára, VAGY három rossz jelszó volt, és még nem lett timer indítva, reset öt perc
    // 2. Ha három rossz jelszó volt, és már túl van öt percen az utolsó oldalbetöltés, új próbálkozás engedése
    // 3. Default zárolás. Ha ez az első betöltés VAGY lejárt az öt perc, tiltás
    if(($_SESSION['masterbadpasscount'] > 2 && $unlockedtime < 300) || ($_SESSION['masterbadpasscount'] > 2 && @!$_SESSION['unlockedtime']))
    {
        $popup['type'] = "error";
        $popup['message'] = "Háromszor próbáltad hibásan megadni a mesterjelszót! Öt percre zárolva vagy!";
        $_SESSION['unlockedtime'] = time();
        $unlockedtime = time() - @$_SESSION['unlockedtime'];
    }
    elseif($_SESSION['masterbadpasscount'] > 2 && $unlockedtime > 300)
    {
        $_SESSION['masterbadpasscount'] = 0;
    }
    elseif(!isset($_SESSION['unlockedmaster']) || $unlockedtime > 300)
    {
        $_SESSION['unlockedmaster'] = null;
        $_SESSION['unlockedtime'] = null;
    }

    $elemid = getElem();
    $betolteni = getAloldal("jelszokezelo", "jelszavak");

    if($betolteni)
        include($betolteni);

    $javascriptfiles[] = "external/sweetalert/sweetalert2.all.min.js";
    $javascriptfiles[] = "modules/jelszokezelo/includes/jelszokezelo.js";
    $PHPvarsToJS['unlockedtime'] = $unlockedtime;
    $PHPvarsToJS['unlocked'] = ($_SESSION['unlockedmaster']) ? true : false;
    $PHPvarsToJS['popup'] = json_encode($popup);

    include("./modules/jelszokezelo/forms/masterpassform.php");
}