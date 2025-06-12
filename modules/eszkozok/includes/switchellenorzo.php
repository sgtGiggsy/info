<?php

use LDAP\Result;

if($contextmenujogok['switchellenorzo'] && $mindir)
{
    // Mivel a beallitasdb meghívásával BÁRMELY beállítás megváltoztatható, szűrni kell, hogy csak az a kulcs legyen módosítható, amit előtte explicit megengedtünk
    if(count($_POST) == 2 && isset($_POST['telephely']) && isset($_POST['onlinefigyeles']))
    {
        $irhat = true;
        include("./modules/beallitasok/db/beallitasdb.php");
    }

    $beallitassql = new MySQLHandler("SELECT * FROM beallitasok");
    $telephelyek = new MySQLHandler("SELECT * FROM telephelyek;");
    $telephelyek = $telephelyek->Result();

    $beallitas = array();
    foreach($beallitassql->Result() as $x)
    {
        $beallitas[$x['nev']] = $x['ertek'];
    }

    $button = "Beállítások mentése";
    $irhat = true;
    $form = "modules/eszkozok/forms/switchcheckbeallitasform";
    $oldalcim = "Switch ellenőrző beállítások"; 

    include('././templates/edit.tpl.php');
}