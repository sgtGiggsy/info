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

    if($menupont['oldal'] == $pagetofind)
    {
        foreach($jogosultsagok as $jogosultsag)
        {
            if($menupont['id'] == $jogosultsag['menupont'])
            {
                ($jogosultsag['sajatolvas']) ? $sajatolvas = true : $sajatolvas = false;
                ($jogosultsag['csoportolvas']) ? $csoportolvas = true : $csoportolvas = false;
                ($jogosultsag['mindolvas']) ? $mindolvas = true : $mindolvas = false;
                ($jogosultsag['sajatir']) ? $sajatir = true : $sajatir = false;
                ($jogosultsag['csoportir']) ? $csoportir = true : $csoportir = false;
                ($jogosultsag['mindir']) ? $mindir = true : $mindir = false;
                break;
            }
        }
        $currentpage = $menupont;
    }

    if($menupont['aktiv'] == 3 || ($menupont['aktiv'] == 2 && $_SESSION[getenv('SESSION_NAME').'id']) || ($menupont['aktiv'] == 4 && !$_SESSION[getenv('SESSION_NAME').'id']))
    {
        $sajatolvas = true;
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
                    break;
                }
            }
        }
    }
}

if(!isset($currentpage))
{
    $currentpage['url'] = $_GET['page'];
    $currentpage['cimszoveg'] = "Oldal";
}

//echo "$sajatolvas $csoportolvas $mindolvas $sajatir $csoportir $mindir";