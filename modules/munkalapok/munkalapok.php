<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $contextmenu = array(
        'lista' => array('gyujtooldal' => 'lista', 'oldal' => 'munkaszerkeszt', 'gyujtooldalnev' => 'Lista', 'oldalnev' => 'Lista'),
        'templateek' => array('gyujtooldal' => 'templateek', 'oldal' => 'munkalapok/templateszerkeszt', 'gyujtooldalnev' => 'Templateek', 'oldalnev' => 'Template'),
        'nyomtatasikep' => array('gyujtooldal' => 'nyomtatasikep', 'oldal' => 'nyomtatasikep', 'gyujtooldalnev' => 'Nyomtatási kép', 'oldalnev' => 'Nyomtatási kép'),
        'beallitasok' => array('gyujtooldal' => 'beallitasok', 'oldal' => 'beallitasok', 'gyujtooldalnev' => 'Beállítások', 'oldalnev' => 'Beállítások')
    );

    $contextmenujogok['lista'] = $contextmenujogok['templateek'] = $contextmenujogok['nyomtatasikep'] = $contextmenujogok['beallitasok'] = true;

    $elemid = getElem();
    $betolteni = getAloldal("munkalapok");

    if($betolteni)
        include($betolteni);
}