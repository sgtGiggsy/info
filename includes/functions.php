<?php

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
		//echo $querystring;
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

function timeStampToDate($timestamp)
{
	if($timestamp)
	{
		return date('Y M j.', strtotime($timestamp));
	}
	else
	{
		return null;
	}
}

function timeStampToDateTimeLocal($timestamp)
{
    if($timestamp)
	{
		return str_replace(" ", "T", $timestamp);
	}
	else
	{
		return null;
	}
	
}

function dateTimeLocalToTimeStamp($datetimelocal)
{
	if($datetimelocal)
	{
		return str_replace("T", " ", $datetimelocal);
	}
	else
	{
		return null;
	}
}

function alakulatValaszto($ldapres)
{
	if(str_contains($ldapres, "59"))
	{
		return 1;
	}
	elseif(str_contains($ldapres, "43"))
	{
		return 2;
	}
	elseif(str_contains($ldapres, "Légijármű") || str_contains($ldapres, "LéJÜ"))
	{
		return 3;
	}
	elseif(str_contains($ldapres, "Légi Műveleti"))
	{
		return 4;
	}
	elseif(str_contains($ldapres, "Katonai Igazgatási és Központi") || str_contains($ldapres, "KIKNYP"))
	{
		return 5;
	}
	else
	{
		return null;
	}
}

function eszkozTipusValaszto($tipusid)
{
	if($tipusid < 11)
	{
		$eszktip = "aktiv";
	}
	elseif($tipusid == 11)
	{
		$eszktip = "szamitogep";
	}
	elseif($tipusid == 12)
	{
		$eszktip = "nyomtato";
	}
	elseif($tipusid < 21)
	{
		$eszktip = "vegponti";
	}
	elseif($tipusid < 31)
	{
		$eszktip = "konverter";
	}
	elseif($tipusid < 41)
	{
		$eszktip = "szerver";
	}

	return $eszktip;
}

function eszkozPicker($current, $beepitett)
{
	$where = null;
	if(!$beepitett)
	{
		$where = "WHERE beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL";
	}
	$eszkozok = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus
        FROM
            eszkozok
				INNER JOIN modellek ON eszkozok.modell = modellek.id
				INNER JOIN gyartok ON modellek.gyarto = gyartok.id
				INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
				LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
		$where
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

	?><div>
		<label for="eszkoz">Eszköz:</label><br>
		<select id="eszkoz" name="eszkoz" required>
			<option value=""></option><?php
			foreach($eszkozok as $x)
			{
				?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?= $x['gyarto'] . " " . $x['modell'] . $x['varians'] . " (" . $x['sorozatszam'] . ")" ?></option><?php
			}
		?></select>
	</div><?php
}

function helyisegPicker($current)
{
	$helyisegek = mySQLConnect("SELECT
            helyisegek.id AS id,
            szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            epulet AS epuletid,
            epuletek.nev AS epuletnev
        FROM
            helyisegek LEFT JOIN
                epuletek ON helyisegek.epulet = epuletek.id
        ORDER BY epuletszam + 0, helyisegszam;");

	?><div>
	<label for="helyiseg">Helyiség:</label><br>
	<select id="helyiseg" name="helyiseg">
		<option value="" selected></option><?php
		foreach($helyisegek as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?= $x['epuletszam'] . ". épület" . ", " . $x['helyisegszam'] . " (" . $x['helyisegnev'] . ")" ?></option><?php
		}
	?></select>
	</div><?php
}

function rackPicker($current)
{
	$rackek = mySQLConnect("SELECT
            rackszekrenyek.id AS id,
            szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            epuletek.nev AS epuletnev,
            rackszekrenyek.nev AS rack
        FROM
            rackszekrenyek LEFT JOIN
                helyisegek ON rackszekrenyek.helyiseg = helyisegek.id LEFT JOIN
                epuletek ON helyisegek.epulet = epuletek.id
        ORDER BY epuletszam, helyisegszam, rackszekrenyek.nev;");

	?><div>
	<label for="rack">Rackszekrény:</label><br>
	<select id="rack" name="rack">
		<option value="" selected></option><?php
		foreach($rackek as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?= $x['rack'] . " rack, " . $x['helyisegszam'] . " (" . $x['helyisegnev'] . ") " . $x['epuletszam'] . ". épület" ?></option><?php
		}
	?></select>
	</div><?php
}

function gyartoPicker($current)
{
	$gyartok = mySQLConnect("SELECT * FROM gyartok ORDER BY nev");

	?><div>
	<label for="gyarto">Gyártó:</label><br>
	<select id="gyarto" name="gyarto">
		<option value="" selected></option><?php
		foreach($gyartok as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
		}
	?></select>
	</div><?php
}

function priorityPicker($current)
{
	$priority = mySQLConnect("SELECT * FROM prioritasok ORDER BY id DESC");

	?><div>
	<label for="prioritas">Prioritás:</label><br>
	<select id="prioritas" name="prioritas">
		<option value="" selected></option><?php
		foreach($priority as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
		}
	?></select>
	</div><?php
}

function bugTypePicker($current)
{
	$bugtype = mySQLConnect("SELECT * FROM bugtipusok ORDER BY nev");

	?><div>
	<label for="tipus">A hiba fajtája:</label><br>
	<select id="tipus" name="tipus">
		<option value="" selected></option><?php
		foreach($bugtype as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
		}
	?></select>
	</div><?php
}

function felhasznaloPicker($current, $selectnev, $alakulat)
{
	$where = null;
	if($alakulat)
	{
		$where = "WHERE alakulat = $alakulat";
	}
	$felhasznalok = mySQLConnect("SELECT id, nev FROM felhasznalok $where ORDER BY nev ASC");

	?><select id="<?=$selectnev?>" name="<?=$selectnev?>">
		<option value="" selected></option><?php
		foreach($felhasznalok as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
		}
	?></select><?php
}

function cancelForm()
{
	$RootPath = getenv('APP_ROOT_PATH');
	if(isset($_SERVER['HTTP_REFERER']))
	{
		$backtosender = $_SERVER['HTTP_REFERER'];
	}
	else
	{
		$backtosender = $RootPath;
	}
	
	?><form action='<?=$backtosender?>' method="post">
        <div class='submit'><input type='submit' value='Mégsem'></div>
    </form><?php
}