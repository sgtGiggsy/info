<?php

include("../modules/telefonkonyv/includes/functions.php");

header('Content-Type: Content-Type: text/html; charset=utf-8');

$felhid = $_GET['felhid'];
$beoid = $_GET['beoid'];

$csoport = mySQLConnect("SELECT csoport FROM telefonkonyvbeosztasok WHERE id = $beoid");
$csoport = mysqli_fetch_assoc($csoport)['csoport'];

$tkonyvwheresettings = array(
    'and' => true,
    'where' => false,
    'mezonev' => true,
    'felhasznalo' => $felhid,
    'modkorszur' => null,
    'modid' => null
);

$globaltelefonkonyvadmin = telefonKonyvAdminCheck($felhid);
$wherecsoport = getTkonyvszerkesztoWhere($globaltelefonkonyvadmin, $tkonyvwheresettings);

$csoportok = mySQLConnect("SELECT * FROM telefonkonyvcsoportok WHERE id > 1 AND torolve IS NULL $wherecsoport;");

?><option value=""></option><?php
    foreach($csoportok as $x)
    {
        ?><option value="<?=$x['id']?>" <?=($x['id'] == $csoport) ? "selected" : "" ?>><?=$x['nev']?></option><?php
    }
?>