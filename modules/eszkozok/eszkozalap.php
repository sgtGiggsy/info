<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $contextmenu = array(
        'gyartok' => array('gyujtooldal' => 'gyartoklistaja', 'oldal' => 'gyartoszerkeszt', 'gyujtooldalnev' => 'Gyártók', 'oldalnev' => 'Gyártók'),
        'modellek' => array('gyujtooldal' => 'modelleklistaja', 'oldal' => 'modellszerkeszt', 'gyujtooldalnev' => 'Modellek', 'oldalnev' => 'Modellek'),
        'firmwareek' => array('gyujtooldal' => 'firmwarelista', 'oldal' => 'firmware', 'gyujtooldalnev' => 'Firmwareek', 'oldalnev' => 'Firmware-ek'),
        'switchellenorzo' => array('gyujtooldal' => 'switchellenorzo', 'oldal' => 'switchellenorzo', 'gyujtooldalnev' => 'Switch ellenőrző beállításai', 'oldalnev' => 'Switch ellenőrző beállításai')
    );

    $contextmenujogok['gyartoklistaja'] = $contextmenujogok['modelleklistaja'] = $contextmenujogok['firmwarelista'] = $contextmenujogok['switchellenorzo'] = true;

    $elemid = getElem();
    $betolteni = getAloldal("eszkozok", "gyartoklistaja");

    if($betolteni)
        include($betolteni);
}