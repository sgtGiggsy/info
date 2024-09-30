<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

include("../../../includes/config.inc.php");
include("../../../includes/functions.php");
include("../../../Classes/MySQLHandler.class.php");

$returnarr = array();
$response = null;
@$tipusnev = $_GET['tipus'];

if($tipusnev)
{
    if($tipusnev == "mediakonverter" || $tipusnev == "bovitomodul")
    {
        $sql = new MySQLHandler();

        $sql->Query("SELECT * FROM fizikairetegek;");
        $returnarr['fizikairetegek'] = $sql->AsArray();

        $sql->Query("SELECT * FROM csatlakozotipusok;");
        $returnarr['csatlakozok'] = $sql->AsArray();

        $sql->Query("SELECT id, CONCAT(sebesseg, ' Mbit/s') AS nev FROM sebessegek;");
        $returnarr['sebessegek'] = $sql->AsArray();

        $sql->Query("SELECT * FROM atviteliszabvanyok;");
        $returnarr['atviteliszabvanyok'] = $sql->AsArray();
    }
    if(count($returnarr) > 0)
    {
        $response = 200;
    }
}
else
{
    $response = 400;
}

http_response_code($response);

echo json_encode($returnarr);