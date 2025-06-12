<?php
if($contextmenujogok['nyomtatasikep'])
{
    // Mivel a beallitasdb meghívásával BÁRMELY beállítás megváltoztatható, szűrni kell, hogy csak az a kulcs legyen módosítható, amit előtte explicit megengedtünk
    if(count($_POST) == 1 && isset($_POST['munkalapfejlec']))
    {
        $irhat = true;
        include("./modules/beallitasok/db/beallitasdb.php");
    }

    $beallitassql = new MySQLHandler("SELECT * FROM beallitasok");
    $beallitas = array();
    foreach($beallitassql->Result() as $x)
    {
        $beallitas[$x['nev']] = $x['ertek'];
    }

    $button = "Beállítások mentése";
    $irhat = true;
    $form = "modules/munkalapok/forms/nyomtatasikepform";
    $oldalcim = "Munkalap nyomtatási képének adatai"; 

    include('././templates/edit.tpl.php');
}