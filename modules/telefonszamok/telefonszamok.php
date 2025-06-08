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
    $aloldal = getAloldal("telefonszamok");

    if($aloldal)
        include($aloldal);
}