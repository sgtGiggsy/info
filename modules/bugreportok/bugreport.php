<?php

if(!$_SESSION['id'])
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $button = "Bug jelentése";

    $id	= $cim = $leiras = $lezarva	= $oldal = $tipus = $prioritas = null;

    if(isset($_GET['oldal']))
    {
        $oldal = $_GET['oldal'];
    }
    
    $button = "Hiba bejelentése";
    $oldalcim = "Hiba bejelentése";
    $form = "modules/bugreportok/forms/bugreportform";
    $javascriptfiles[] = "modules/feladatok/includes/hibajegy.js";

    include('././templates/edit.tpl.php');
}