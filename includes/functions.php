<?php

function getAdminState()
{
	if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
	$jog = 0;
	if (isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']))
	{
		$jog = $_SESSION[getenv('SESSION_NAME').'jogosultsag'];
	}
	if ($jog > 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getFoadminState()
{
	if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
	$jog = 0;
	if (isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']))
	{
		$jog = $_SESSION[getenv('SESSION_NAME').'jogosultsag'];
	}
	if ($jog > 50)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function ifUser()
{
	if (isset($_SESSION[getenv('SESSION_NAME').'id']))
	{
		$con = mySQLConnect(false);
		if ($stmt = $con->prepare('SELECT id FROM felhasznalok WHERE id = ?'))
		{
			$stmt->bind_param('s', $_SESSION[getenv('SESSION_NAME').'id']);
			$stmt->execute();
			$stmt->store_result();
		
			if ($stmt->num_rows == 0)
			{
				session_destroy();
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function mySQLConnect($querystring)
{
	## MySQL connect ##
	include('config.inc.php');
	
	@$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

	if($con)
	{
		mysqli_set_charset($con, "UTF8");
	}
	
	if(mysqli_connect_errno()) {
		die ('Nem lehet csatlakozni a MySQL kiszolgálóhoz: ' . mysqli_connect_error());
	}

	if($querystring && $con)
	{
		$result = mysqli_query($con, $querystring);
		$con->close();
		return $result;
	}
	else
	{
		return $con;
	}
}

function showMenu($menuitems, $admin)
{
	$RootPath = getenv('APP_ROOT_PATH');
	$jog = 0;
	if (isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']))
	{
		$jog = $_SESSION[getenv('SESSION_NAME').'jogosultsag'];
	}

	?>
	<nav class="greedy">
		<ul class="links">
			<?php 
			$current = null;
			if (!(isset($_GET['page'])))
			{
				$current = "fooldal";
			}
			else
			{
				$current = $_GET['page'];
			}

			foreach ($menuitems as $x => $menuitem)
			{
				if($menuitem['aktiv'] && $menuitem['jog'] <= $_SESSION[getenv('SESSION_NAME').'jogosultsag'] /*&& $menuitem['parent'] == 0*/)
				{ ?>
					<li <?= (($menuitem['url'] == $current) ? ' class="nav-active"' : '') ?>>  
						<a href="<?= (($menuitem['url'] == '/') ? $RootPath : $RootPath."/".$menuitem['url']) ?>"><?=$menuitem['cimke']?></a>	
					</li> <?php
				}
			} ?>
		</ul>
		<button aria-label="További oldalak"><img src="<?=$RootPath?>/images/hamburger.png" alt="További oldalak"></button>
		<ul class='hidden-links hidden'></ul>
	</nav><?php
}

function currentPage($currentpage)
{
	$pagename = null;
	foreach ($currentpage as $x => $oldal)
	{
		if (!(isset( $_GET['page'])))
		{
			$pagename = "fooldal";
			break;
		}
		if ($x == $_GET['page'])
		{
			$pagename = $oldal['fajl'];
			break;
		}
	}
	$title = $oldal['szoveg'];

	try
	{
		$page = @fopen("./{$pagename}.php", "r");
		if (!$page) {
			$page = @fopen("../{$pagename}.php", "r");
			if(!$page)
			{
				throw new Exception();
			}
		}
	}
	catch(Exception $e)
	{
		$pagename = "404";
		$title = "Oldal nem található!";
		?><title>EIBSZ Vizsga - <?= $title  ?></title><?php
	}

	return $pagename;
}

function countVisitor($pagename)
{
	$oldal = $pagename;
	$ipcim = $_SERVER['REMOTE_ADDR'];
	$usernev = null;
	$referrer = null;
	if(isset($_SERVER['HTTP_REFERER']))
	{
		$referrer = $_SERVER['HTTP_REFERER'];
		if(stristr($referrer, "exatlonhunstats.nhely.hu"))
		{
			$referrer = null;
		}
	}
	if(isset($_SESSION[getenv('SESSION_NAME').'id']))
	{
		$usernev = $_SESSION[getenv('SESSION_NAME').'id'];
	}

	$datum = date("Y-m-d");
	$con = mySQLConnect(false);
	if ($stmt = $con->prepare('INSERT INTO latogatasok (oldal, felhasznalo, ipcim, datum, referrer) VALUES (?, ?, ?, ?, ?)'))
    {
        $stmt->bind_param('sssss', $oldal, $usernev, $ipcim, $datum, $referrer);
		$stmt->execute();
	}
	else
	{
		echo "";
	}
}

function getSettings()
{
	$beallitas = mySQLConnect("SELECT * FROM beallitasok");
	foreach($beallitas as $x)
	{
		$nev = $x['nev'];
		$ertek = trim($x['ertek']);
		$_SESSION[getenv('SESSION_NAME')."$nev"] = $ertek;
	}
	if($_SESSION[getenv('SESSION_NAME').'ismetelheto'] == 0)
	{
		$_SESSION[getenv('SESSION_NAME').'ismetelheto'] = false;
	}
	else
	{
		$_SESSION[getenv('SESSION_NAME').'ismetelheto'] = true;
	}
}

function checkLDAPConnection($host)
{
	include('config.inc.php');

	if(@fsockopen("$host", $LDAP_PORT, $errno, $errstr, 30))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function parseUserAgent($uastring)
{
	$browser = explode(" ", $uastring);
	$os = explode("; ", (explode(" (", $uastring))[1]);
	if((str_contains($uastring, "like Gecko") && !str_contains($uastring, "like Gecko)")) || str_contains($uastring, "MSIE"))
	{
		$bongeszo = "Internet Explorer";
	}
	foreach($browser as $x)
	{
		if(str_contains($x, "rv:"))
		{
			$bongeszover = substr(str_replace("rv:", "", $x), 0, -1);
		}
		if(str_contains($x, "Firefox"))
		{
			$bongeszo = "Firefox";
			$bongeszover = str_replace("Firefox/", "", $x);
		}
		elseif(str_contains($x, "Edg"))
		{
			$bongeszo = "Edge";
			$bongeszover = str_replace("Edg/", "", $x);
		}
		elseif(str_contains($x, "Chrome"))
		{
			$bongeszo = "Chrome";
			$bongeszover = str_replace("Chrome/", "", $x);
		}
	}
	foreach($os as $x)
	{
		if(str_contains($x, "Windows"))
		{
			$winver = explode(" NT ", $x);
			$opsystem = $winver[0];
			switch($winver[1])
			{
				case "5.0": $opver = "2000"; break;
				case "5.1": ; case "5.2": $opver = "XP"; break;
				case "6.0": $opver = "Vista"; break;
				case "6.1": $opver = "7"; break;
				case "6.2": $opver = "8"; break;
				case "6.3": $opver = "8.1"; break;
				case "10.0": $opver = "10"; break;
				default: $opver = "?";
			}
		}
		elseif(str_contains($x, "Linux"))
		{
			$opsystem = "Linux";
		}

		$arch = null;
		if(str_contains($x, "64"))
		{
			$arch = "64bit";
		}
		elseif(!(@$arch == "64bit"))
		{
			$arch = "32bit";
		}
	}

	$userdesktop = array(
		'oprendszer' => $opsystem,
		'oprendszerver' => $opver,
		'architektura' => $arch,
		'bongeszo' => $bongeszo,
		'bongeszover' => $bongeszover
	);

	return $userdesktop;
}

function logLogin($felhasznalo)
{
	$gepadat = parseUserAgent($_SERVER['HTTP_USER_AGENT']);
	$con = mySQLConnect(false);
	if ($stmt = $con->prepare('INSERT INTO bejelentkezesek (felhasznalo, ipcim, bongeszo, bongeszoverzio, oprendszer, oprendszerverzio, oprendszerarch) VALUES (?, ?, ?, ?, ?, ?, ?)'))
    {
        $stmt->bind_param('sssssss', $felhasznalo, $_SERVER['REMOTE_ADDR'], $gepadat['bongeszo'], $gepadat['bongeszover'], $gepadat['oprendszer'], $gepadat['oprendszerver'], $gepadat['architektura']);
		$stmt->execute();
	}
}