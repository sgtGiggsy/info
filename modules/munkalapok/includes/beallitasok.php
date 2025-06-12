<?php
if($contextmenujogok['beallitasok'])
{   
    // Mivel a beallitasdb meghívásával BÁRMELY beállítás megváltoztatható, szűrni kell, hogy csak az a kulcs legyen módosítható, amit előtte explicit megengedtünk
    if(count($_POST) == 2 && isset($_POST['defaultmunkahely']) && isset($_POST['defaultugyintezo']))
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
    $form = "modules/munkalapok/forms/beallitasokform";
    $oldalcim = "Munkalapok alap beállításai"; 

    include('././templates/edit.tpl.php');
}