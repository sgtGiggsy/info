<?php
include("../includes/config.inc.php");
include("../includes/functions.php");

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$requestbody = file_get_contents('php://input');
$request = json_decode($requestbody);
$method = $_SERVER['REQUEST_METHOD'];
$statuscode = 200;
$apikey = null;
$valasz = array();


// Az első lépés ellenőrizni, hogy a kérésben szerepel-e az elérni kívánt erőforrás neve.
// Ha nem, a többi lépésnek már nincs is értelme
if(!isset($_GET['list']))
{
	$valasz = array('hibaoka' => 'Nem adtad meg az elérni kívánt erőforrást az API hívásban!');
	$statuscode = 400;
}
else
{
	// Második lépésként kérjük le az API kulcsot a HTTP fejlcéből. Ha a feljebb az $apikey változót true-ra állítjuk,
	// az API debug módban működik, a kéréseket kiszolgálja az API kulcs ellenőrzése nélkül is.
	if(isset(getallheaders()['Authorization']) && !$apikey)
	{
		@$apikey = getallheaders()['Authorization'];
	}
	elseif(!$apikey)
	{
		$valasz = array('hibaoka' => 'Beazonosítás sikertelen!');
		$statuscode = 401;
	}

	// A következő lépés az API kulcs ellenőrzése az adatbázisban. Ha nem létezik, 401-es hiba dobása
	if($apikey)
	{
		// Ezután ellenőrizzük, hogy az API kulcsnak van-e hozzáférése a kívánt erőforráshoz. Ha nem, 403-as hiba dobása
		if(1 < 2)
		{

		}
		else
		{
			$valasz = array('hibaoka' => 'Nincs jogosultságod az erőforrás eléréséhez!');
			$statuscode = 403;
		}
	}
	else
	{
		$valasz = array('hibaoka' => 'Beazonosítás sikertelen!');
		$statuscode = 401;
	}	
}

if(($method == 'POST' || $method == 'PUT' || $method == 'DELETE') && count($request) == 0)
{
	$valasz = array('hibaoka' => 'A kérésben nem érkezett adat, ami alapján a művelet végrehajtható lenne!');
	$statuscode = 400;
}

if($statuscode < 400)
{
	if ($method == 'GET')
	{
		if($valasz)
		{
			$replybody = json_encode($valasz);
		}
		if(count($valasz) == 0)
		{
			$statuscode = 204;
		}
	}

	elseif ($method == 'POST')
	{
		// Átadni a modulnak, ami kezeli
		

		if($valasz == "201")
		{
			$statuscode = 201;
		}
	}

	elseif ($method == 'PUT')
	{
		if($valasz == "201")
		{
			$statuscode = 201;
		}
	}

	// Egyelőre törlést nem tervezek megvalósítani, ezért ez automatikus letiltás
	elseif ($method == 'DELETE')
	{
		$statuscode = 405;
	}
}

http_response_code($statuscode);

echo json_encode($valasz);