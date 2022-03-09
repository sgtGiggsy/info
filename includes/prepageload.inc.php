<?php

// Betöldendő oldal kiválasztásának elvégzése
$menu = mySQLConnect("SELECT * FROM menupontok");

if(!(isset($_GET['page'])))
{
    $pagetofind = "fooldal";
}
else
{
    $pagetofind = $_GET['page'];
}

foreach($menu as $x)
{
    if($x['oldal'] == $pagetofind)
    {
        $currentpage = $x;
        break;
    }
}

if(!isset($currentpage))
{
    $currentpage['url'] = $_GET['url'];
    $currentpage['cimszoveg'] = "Oldal";
}

// Felhasználó jogosultságának ellenőrzése
