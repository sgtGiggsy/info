<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $contextmenu = array(
        'lista' => array('gyujtooldal' => '', 'oldal' => 'lista', 'gyujtooldalnev' => 'Lista', 'oldalnev' => 'Lista'),
        'jogosultsagok' => array('gyujtooldal' => 'jogosultsagok', 'oldal' => 'telefonszamok/jogosultsagszerkeszt', 'gyujtooldalnev' => 'Jogosultságok', 'oldalnev' => 'Jogosultság')
    );
    $contextmenujogok['lista'] = $contextmenujogok['jogosultsagok'] = true;

    $elemid = getElem();
    $betolteni = getAloldal("telefonszamok");

    if($betolteni)
        include($betolteni);
}