<?php

header('Content-Type: application/json');
$beoid = 0;
if(isset($_GET['beoid']))
    $beoid = $_GET['beoid'];

$beobasics = mySQLConnect("SELECT belsoszam, belsoszam2, fax, kozcelu, kozcelufax, sorrend FROM telefonkonyvbeosztasok WHERE id = $beoid");
$beobasics = mysqliToArray($beobasics);
$beobasics[0]['belsoszam'] = substr($beobasics[0]['belsoszam'], 6, 4);
$beobasics[0]['belsoszam2'] = substr($beobasics[0]['belsoszam2'], 6, 4);
$beobasics[0]['fax'] = substr($beobasics[0]['fax'], 6, 4);
$beobasics[0]['kozcelu'] = substr($beobasics[0]['kozcelu'], 8, 6);
$beobasics[0]['kozcelufax'] = substr($beobasics[0]['kozcelufax'], 8, 6);

echo json_encode($beobasics);