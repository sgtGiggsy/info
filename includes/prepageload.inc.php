<?php

// Betöldendő oldal kiválasztásának elvégzése
$menu = mySQLConnect("SELECT * FROM menupontok ORDER BY sorrend ASC, id DESC");

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
$felhid = $_SESSION[getenv('SESSION_NAME').'id'];
$jogosultsagok = mySQLConnect("SELECT * FROM jogosultsagok WHERE felhasznalo = $felhid");

$menuk = array();

foreach($menu as $menupont)
{
    if(!isset($menuk[$menupont['menuterulet']]))
    {
        $menuk[$menupont['menuterulet']] = array();
    }

    if($menupont['aktiv'] == 3 || ($menupont['aktiv'] == 2 && $_SESSION[getenv('SESSION_NAME').'id']) || ($menupont['aktiv'] == 4 && !$_SESSION[getenv('SESSION_NAME').'id']))
    {
        array_push($menuk[$menupont['menuterulet']], $menupont);
    }
    elseif($menupont['aktiv'] == 1 && $_SESSION[getenv('SESSION_NAME').'id'])
    {
        foreach($jogosultsagok as $jogosultsag)
        {
            if($menupont['id'] == $jogosultsag['menupont'])
            {
                if($jogosultsag['csoportolvas'] == 1)
                {
                    array_push($menuk[$menupont['menuterulet']], $menupont);
                }
            }
        }
    }
}
