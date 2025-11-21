<?php

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
    $unlockedtime = time() - @$_SESSION['unlockedtime'];
    if(!isset($_SESSION['unlockedmaster']) || $unlockedtime > 300)
    {
        $_SESSION['unlockedmaster'] = null;
        $_SESSION['unlockedtime'] = null;
    }

    $contextmenu = array(
        'jelszavak' => array('gyujtooldal' => 'jelszavak', 'oldal' => 'jelszo', 'gyujtooldalnev' => 'Jelszavak', 'oldalnev' => 'Jelszó'),
        'beallitasok' => array('gyujtooldal' => 'beallitasok', 'oldal' => 'beallitasok', 'gyujtooldalnev' => 'Beállítások', 'oldalnev' => 'Beállítások'),
        'countd' => array('gyujtooldal' => '', 'oldal' => '', 'gyujtooldalnev' => '', 'oldalnev' => '')
    );

    $contextmenujogok['jelszavak'] = $contextmenujogok['beallitasok'] = $contextmenujogok['countd'] = true;

    $elemid = getElem();
    $betolteni = getAloldal("jelszokezelo", "jelszavak");

    if($betolteni)
        include($betolteni);

    $javascriptfiles[] = "modules/jelszokezelo/includes/jelszokezelo.js";
    $PHPvarsToJS['unlockedtime'] = $unlockedtime;
}