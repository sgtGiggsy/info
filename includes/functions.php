<?php

function mySQLConnect($querystring = null)
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

function csvToArray($csv)
{
	$bom = pack('CCC', 0xEF, 0xBB, 0xBF);
	$bemenet = file($csv);
	$bomnelkul = str_replace($bom, '', $bemenet); // Arra az esetre, ha a fájl rendelkezne BOM-mal

	$rows = array_map(function($row) { return str_getcsv($row, ';'); }, $bomnelkul);
	$fejlec = array_shift($rows);

	$array = array();

	foreach($rows as $row)
	{
		$array[] = array_combine($fejlec, $row);
	}

	return $array;
}

function trimCimke($cimke)
{
	$totrim = array("\"", "*");

	return str_replace($totrim, "", $cimke);
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
	elseif(str_contains($ldapres, "Légijármű") || str_contains($ldapres, "LéJÜ") || str_contains($ldapres, "MH Lé.Jü.") || str_contains($ldapres, "MH Lé. Jü.")) 
	{
		return 3;
	}
	elseif(str_contains($ldapres, "Légi Műveleti") || str_contains($ldapres, "LMV"))
	{
		return 4;
	}
	elseif(str_contains($ldapres, "Katonai Igazgatási és Központi") || str_contains($ldapres, "KIKNYP"))
	{
		return 5;
	}
	elseif(str_contains($ldapres, "38"))
	{
		return 6;
	}
	elseif(str_contains($ldapres, "Nemzetbiztonsági"))
	{
		return 7;
	}
	else
	{
		return null;
	}
}

function eszkozTipusValaszto($tipusid)
{
	if($tipusid < 6)
	{
		$eszktip = "aktiv";
		$teljesnev =  "aktiveszkoz";
	}
	elseif($tipusid < 11)
	{
		$eszktip = "soho";
		$teljesnev = "sohoeszkoz";
	}
	elseif($tipusid == 11)
	{
		$eszktip = "szamitogep";
		$teljesnev = "szamitogep";
	}
	elseif($tipusid == 12)
	{
		$eszktip = "nyomtato";
		$teljesnev = "nyomtato";
	}
	elseif($tipusid < 20)
	{
		$eszktip = "vegponti";
		$teljesnev = "vegponti";
	}
	elseif($tipusid < 26)
	{
		$eszktip = "mediakonverter";
		$teljesnev = "mediakonverter";
	}
	elseif($tipusid < 31)
	{
		$eszktip = "bovitomodul";
		$teljesnev = "bovitomodul";
	}
	elseif($tipusid < 40)
	{
		$eszktip = "szerver";
		$teljesnev = "szerver";
	}
	elseif($tipusid == 40)
	{
		$eszktip = "telefonkozpont";
		$teljesnev = "telefonkozpont";
	}

	return array("tipus" => $eszktip, "teljes" => $teljesnev);
}

function eszkozPicker($current = false, $beepitett)
{
	$where = null;
	if($current)
	{
		$where = "WHERE eszkozok.id = $current";
	}
	elseif(!$beepitett)
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

	if(!$current)
	{
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
	else
	{
		$eszkoz = mysqli_fetch_assoc($eszkozok);
		?><h2><label><?= $eszkoz['gyarto'] . " " . $eszkoz['modell'] . $eszkoz['varians'] . " (" . $eszkoz['sorozatszam'] . ")" ?></h2></label>
		<input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$current?>><?php
	}
}

function helyisegPicker($current, $selectnev)
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
	<label for="<?=$selectnev?>">Helyiség:</label><br>
	<select id="<?=$selectnev?>" name="<?=$selectnev?>">
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

function felhasznaloPicker($current, $selectnev, $alakulat = null)
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

function alakulatPicker($current, $selectnev = 'alakulat')
{
	$alakulatok = mySQLConnect("SELECT * FROM alakulatok;");

	?><div>
		<label for="<?=$selectnev?>">Alakulat:</label><br>
		<select id="<?=$selectnev?>" name="<?=$selectnev?>">
			<option value="" selected></option><?php
			foreach($alakulatok as $x)
			{
				?><option value="<?=$x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['rovid']?></option><?php
			}
		?></select>
	</div><?php
}

function vlanPicker($current, $selectnev = 'vlan')
{
	$vlanok = mySQLConnect("SELECT * FROM vlanok;");
	
	?><div>
		<label for="<?=$selectnev?>">VLAN:</label><br>
		<select name="<?=$selectnev?>">
			<option value=""></option><?php
			foreach($vlanok as $x)
			{
				?><option value="<?=$x['id']?>" <?=($x['id'] == $current) ? "selected" : "" ?>><?=$x['id'] . " " . $x['nev']?></option><?php
			}
		?></select>
	</div><?php
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

function mysqliNaturalSort($mysqliresult, $sortcriteria)
{
	$returnarr = array();
    foreach($mysqliresult as $sor)
    {
        $element = array();
		foreach($sor as $key => $value)
		{
			$element[$key] = $value;
		}

		//$port = array('portid' => $x['portid'], 'port' => $x['port'], 'hasznalatban' => $x['hasznalatban'], 'tipus' => $x['tipus'], 'szam' => $x['szam']);
        $returnarr[] = $element;
    }

    usort($returnarr, function($a, $b) use ($sortcriteria) {
		if($a[$sortcriteria] == null)
		{
			$a[$sortcriteria] = "zzzzz";
		}

		if($b[$sortcriteria] == null)
		{
			$b[$sortcriteria] = "zzzzz";
		}

        return strnatcmp($a[$sortcriteria], $b[$sortcriteria]); //Case sensitive
        //return strnatcasecmp($a['manager'],$b['manager']); //Case insensitive
    });

	return $returnarr;
}

function getWhere($eszktip = false)
{
	if(isset($_GET['szures']) && $_GET['szures'] != "keszleten")
	{
		$filter = $_GET['szures'];
		if($filter == "mind")
		{
			$where = "$eszktip AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL)";
			$szures = "- Mind";
		}
		elseif ($filter == "leadva")
		{
			$where = "$eszktip AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND eszkozok.leadva IS NOT NULL";
			$szures = "- Leadva";
		}
		elseif ($filter == "beepitve")
		{
			$where = "$eszktip AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)";
			$szures = "- Beépítve";
		}
		elseif ($filter == "raktaron")
		{
			$where = "$eszktip AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND eszkozok.leadva IS NULL AND (beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL OR beepitesek.id IS NULL)";
			$szures = "- Raktáron";
		}
		elseif ($filter == "hibatlan")
		{
			$where = "$eszktip AND eszkozok.hibas IS NULL";
			$szures = "- Hibátlan eszközök";
		}
		elseif ($filter == "reszleges")
		{
			$where = "$eszktip AND eszkozok.hibas = 1";
			$szures = "- Részlegesen működőképes eszközök";
		}
		elseif ($filter == "mukodeskeptelen")
		{
			$where = "$eszktip AND eszkozok.hibas = 2";
			$szures = "- Működésképtelen eszközök";
		}
	}
	elseif($_GET['page'] == "raktar")
	{
		$filter = false;
		$where = "$eszktip AND (beepitesek.id = (SELECT MAX(ic.id) FROM beepitesek ic WHERE ic.eszkoz = beepitesek.eszkoz) OR beepitesek.id IS NULL) AND eszkozok.leadva IS NULL";
		$szures = "- Készleten";
	}
	else
	{
		$filter = false;
		$where = "$eszktip";
		$szures = "- Teljes készlet";
	}
	

	return array('filter' => $filter, 'where' => $where, 'szures' => $szures);
}

function keszletFilter($action, $filter)
{
	?><div class="szuresvalaszto">
		<form action="<?=$action?>" method="GET">
			<label for="szures" style="font-size: 14px">Szűrés</label>
				<select id="szures" name="szures" onchange="this.form.submit()">
					<option value="keszleten" <?=($filter) ? "" : "selected" ?>>Teljes készlet</option>
					<option value="beepitve" <?=($filter == "beepitve") ? "selected" : "" ?>>Beépítve</option>
					<option value="raktaron" <?=($filter == "raktaron") ? "selected" : "" ?>>Raktáron</option>
					<option value="leadva" <?=($filter == "leadva") ? "selected" : "" ?>>Leadva</option>
					<option value="mind" <?=($filter == "mind") ? "selected" : "" ?>>Mind</option>
				</select>
		</form>
	</div><?php
}

function raktarKeszlet($action, $filter)
{
	?><div class="szuresvalaszto">
		<form action="<?=$action?>" method="GET">
			<label for="szures" style="font-size: 14px">Szűrés</label>
				<select id="szures" name="szures" onchange="this.form.submit()">
					<option value="keszleten" <?=($filter) ? "" : "selected" ?>>Készleten</option>
					<option value="hibatlan" <?=($filter == "hibatlan") ? "selected" : "" ?>>Hibátlan</option>
					<option value="reszleges" <?=($filter == "reszleges") ? "selected" : "" ?>>Részlegesen működőképes</option>
					<option value="mukodeskeptelen" <?=($filter == "mukodeskeptelen") ? "selected" : "" ?>>Működésképtelen</option>
				</select>
		</form>
	</div><?php
}

function szerkSor($beepid, $eszkid, $eszktip)
{
	$RootPath = getenv('APP_ROOT_PATH');

	?><td class="dontprint"><?php
	if($beepid)
	{
		?><a href='<?=$RootPath?>/beepites/<?=$beepid?>'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a><?php
	}
	?></td>
	<td class="dontprint"><a href='<?=$RootPath?>/beepites?eszkoz=<?=$eszkid?>&tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/newbeep.png' alt='Új beépítés' title='Új beépítés' /></a></td>
	<td class="dontprint"><a href='<?=$RootPath?>/eszkozszerkeszt/<?=$eszkid?>?tipus=<?=$eszktip?>'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td><?php
}

function modId($muvelet, $tipus, $objid)
{
	$con = mySQLConnect();
	$felhasznalo = $_SESSION[getenv('SESSION_NAME').'id'];
	$string = "INSERT INTO modositasok (felhasznalo, muvelet, $tipus) VALUES ($felhasznalo, $muvelet, $objid)";
	$modositas = mysqli_query($con, $string);

	$last_id = mysqli_insert_id($con);

	return $last_id;
}

function getNotifications()
{
	$felhasznaloid = $_SESSION[getenv('SESSION_NAME').'id'];
	$notifications = array();
	$switchcheck = mySQLConnect("SELECT ertek
		FROM `beallitasok`
		WHERE nev = 'last_switch_check'
			AND ertek < date_sub(now(), INTERVAL 15 MINUTE)
			AND ertek > (SELECT lastseennotif FROM felhasznalok WHERE id = $felhasznaloid)");

	$ertesitesek = mySQLConnect("SELECT ertesitesek.id AS id, cim, szoveg, url, timestamp, latta
		FROM ertesitesek
			INNER JOIN ertesites_megjelenik ON ertesitesek.id = ertesites_megjelenik.ertesites
		WHERE felhasznalo = $felhasznaloid
			AND ertesitesek.id = (SELECT MAX(ic.id) FROM ertesitesek ic WHERE ic.cim = ertesitesek.cim)
			AND ertesitesek.timestamp > date_sub(now(), INTERVAL 7 DAY)
		ORDER BY timestamp DESC");
	
	if(mysqli_num_rows($switchcheck) > 0)
	{
		$switchutolso = mysqli_fetch_assoc($switchcheck)['ertek'];
		$notification = array('cim' => 'Switch ellenőrző leállt', 'szoveg' => 'A switchek állapotát ellenőrző script utolsó futása: ' . $switchutolso, 'url' => null, 'timestamp' => $switchutolso, 'latta' => false);
		$notifications[] = $notification;
	}

	foreach($ertesitesek as $ertesites)
	{
		$latta = false;
		if($ertesites["latta"] > 0)
			$latta = true;

		$tempert = array('id' => $ertesites['id'], 'cim' => $ertesites["cim"], 'szoveg' => $ertesites["szoveg"], 'url' => $ertesites["url"], 'timestamp' => $ertesites["timestamp"], 'latta' => $latta);

		$notifications[] = $tempert;
	}

	return $notifications;
}

function nevToLink($nev)
{
	setlocale(LC_CTYPE, 'hu_HU');
	$charstoremove = array(":", ",", ".", "\"", "'", "(", ")");
	$charstoreplace = array(" ", ".", "_");
	$link = strtolower(str_replace($charstoremove, "", str_replace($charstoreplace, "-", iconv('utf-8', 'ascii//TRANSLIT', $nev))));
	$link = str_replace("--", "-", $link);
	$link = rtrim($link ,"-");
	return $link;
}

function getCimkek($cimkek)
{
	$cimkelista = explode(',', $cimkek);
	return $cimkelista;
}