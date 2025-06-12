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
        'firmwareek' => array('gyujtooldal' => 'firmwarelista', 'oldal' => 'firmware', 'gyujtooldalnev' => 'Firmwareek', 'oldalnev' => 'Firmware-ek')
    );

    $contextmenujogok['gyartoklistaja'] = $contextmenujogok['modelleklistaja'] = $contextmenujogok['firmwarelista'] = true;

    $elemid = getElem();
    $aloldal = getAloldal("eszkozok");

    if($aloldal)
        include($aloldal);
}