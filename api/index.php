<?php
include("../includes/config.inc.php");
include("../includes/functions.php");
include("../Classes/Ertesites.class.php");
include('../Classes/MailHandler.class.php');

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

interface API
{
	static function GetList($mezo, $ertek) : array;
	static function GetItem($objid);
	static function Post($object, $filter);
	static function Update($object, $filter);
	static function Delete($objid, $filter);
}

$requestbody = file_get_contents('php://input');
$request = json_decode($requestbody);
$method = $_SERVER['REQUEST_METHOD'];
$statuscode = 200;
$RootPath = getenv('APP_ROOT_PATH');
$valasz = array();
$egyedioldal = false;
$apikey = null;

// Az első lépés ellenőrizni, hogy a kérésben szerepel-e az elérni kívánt erőforrás neve.
// Ha nem, a többi lépésnek már nincs is értelme
if(!isset($_GET['list']))
{
	$valasz = array('valasz' => 'Nem adtad meg az elérni kívánt erőforrást az API hívásban!');
	$statuscode = 400;

	// Ezt a kitételt azért tettem be, hogy az API meghívása tesztelhető legyen.
	// Kulcs és paraméter nélkül meghívva az API egy nyugtázó üzenetet ad arról, hogy rendben működik.
	if(!isset(getallheaders()['Authorization']))
	{
		$statuscode = 200;
		$valasz = array('valasz' => 'Az API paraméterek és kulcs nélküli meghívása sikeres volt!');
	}
}
else
{
	// Második lépésként kérjük le az API kulcsot a HTTP fejlcéből. Ha feljebb az $apikey változót true-ra állítjuk,
	// az API debug módban működik, a kéréseket kiszolgálja az API kulcs ellenőrzése nélkül is.
	if(isset(getallheaders()['Authorization']) && !$apikey)
	{
		@$apikey = getallheaders()['Authorization'];
	}
	elseif(!$apikey)
	{
		$valasz = array('valasz' => 'Beazonosítás sikertelen!');
		$statuscode = 401;
	}

	// A következő lépés az API kulcs ellenőrzése az adatbázisban. Ha nem létezik, 401-es hiba dobása
	if($apikey)
	{
		$dbapi = mySQLConnect("SELECT apikey, jogosultsagszint AS jog, api.aktiv AS aktiv, apiurl, oldal, gyujtooldal
				FROM api
					LEFT JOIN menupontok ON api.menupont = menupontok.id
				WHERE apikey = '$apikey'");
		
		if(mysqli_num_rows($dbapi) > 0)
		{
			$dbapi = mysqli_fetch_assoc($dbapi);
			
			// Most az API kulcs szintjének ellenőrzése következik
			// 4 = Teljeskörű
			// 3 = Írhat, olvashat, de nem módosíthat
			// 2 = Írhat, de nem olvashat
			// 1 = Csak olvashat
			// Ha nincs a kérésnek megfelelő szintű jog, 405-ös hiba dobása
			if($dbapi['jog'] == 4
				|| ($dbapi['jog'] == 3 && $method != 'PUT')
				|| ($dbapi['jog'] == 2 && $method != 'GET')
				|| ($dbapi['jog'] == 1 && $method == 'GET')
			)
			{
				// Ezután ellenőrizzük, hogy az API kulcsnak van-e hozzáférése a kívánt erőforráshoz. Ha nem, 403-as hiba dobása
				if(($_GET['list'] != $dbapi['oldal'] && $_GET['list'] != $dbapi['gyujtooldal']) || !$dbapi['aktiv'])
				{
					$valasz = array('valasz' => 'Nincs jogosultságod az erőforrás eléréséhez!');
					$statuscode = 403;
				}
				else
				{
					// Ha az API hívás típusa nem GET és nem érkezett vele kérés, akkor hiba dobása
					if($method != 'GET' && is_array($request) && count($request) == 0)
					{
						$valasz = array('valasz' => 'A kérésben nem érkezett adat, ami alapján a művelet végrehajtható lenne!');
						$statuscode = 400;
					}

					// Ha a meghívni próbált modulhoz nem tartozik API, hiba dobása
					$apioldal = "../{$dbapi['apiurl']}.php";
					if(@!fopen($apioldal, "r"))
					{
						$valasz = array('valasz' => 'A kért modul nem rendelkezik API-val!');
						$statuscode = 400;
					}
				}
				if($_GET['list'] == $dbapi['oldal'])
				{
					$egyedioldal = true;
				}
			}
			else
			{
				$valasz = array('valasz' => 'Nincs jogosultságod a kívánt művelet elvégzéséhez!',
					'Jogosultságszint' => $dbapi['jog']);
				$statuscode = 405;
			}
		}
		else
		{
			$valasz = array('valasz' => 'Beazonosítás sikertelen!');
			$statuscode = 401;
		}	
	}
}

// Ha idáig eljutottunk, akkor a használt API kulcs létezik, a számára a művelet eléréséhez szükséges jogosultság rendben van
// és a modul rendelkezik működő API kapcsolattal. Az apikey változóra azért van szükség, mert eljuthatunk ide 200-as válasszal
// ha csak az API elérhetőségét akartuk egy kulcs nélküli kéréssel ellenőrizni
if($statuscode < 400 && $apikey)
{
	include_once($apioldal);
	$filter = $filterval = null;
	if(isset($_GET['filter']))
	{
		$filter = $_GET['filter'];
	}
	if(isset($_GET['filterval']))
	{
		$filterval = $_GET['filterval'];
	}

	if($method == 'GET')
	{	
		if($egyedioldal)
		{
			$valasz = API_Call::GetItem($filter);
		}
		else
		{
			$valasz = API_Call::GetList($filter, $filterval);
		}

		if(count($valasz) == 0)
		{
			$statuscode = 204;
		}
	}

	elseif($method == 'POST')
	{
		$statuscode = API_Call::Post($request, $filter);
	}

	elseif($method == 'PUT')
	{
		$statuscode = API_Call::Update($request, $filter);
	}

	elseif($method == 'DELETE')
	{
		$statuscode = API_Call::Delete($request, $filter);
	}
}

http_response_code($statuscode);

echo json_encode($valasz);