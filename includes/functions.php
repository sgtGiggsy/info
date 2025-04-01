<?php

function mySQLConnect($querystring = null)
{
	## MySQL connect ##
	include('config.inc.php');

	@$GLOBALS['dbcallcount']++;
	
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
		$result = null;
		if(isset($GLOBALS["querylist"]) && $GLOBALS["querylist"])
			$GLOBALS["querylist"][] = $querystring;
		try
		{
			$result = mysqli_query($con, $querystring);
			if(isset($_SESSION['id']) && $_SESSION['id'] == 1 && !$result)
			{
				echo $querystring;
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage() . "<br>";
			if(isset($_SESSION['id']) && $_SESSION['id'] == 1)
				echo $querystring;
		}
		$con->close();
		return $result;
	}
	else
	{
		return $con;
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

function parseUserAgent()
{
	$uastring = $_SERVER['HTTP_USER_AGENT'];
	$arch = null;
	$browser = explode(" ", $uastring);
	$os = explode("; ", (explode(" (", $uastring))[1]);
	if((str_contains($uastring, "like Gecko") && !str_contains($uastring, "like Gecko)")) || str_contains($uastring, "MSIE"))
	{
		$bongeszo = "Internet Explorer";
		$_SESSION['explorer'] = true;
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
	$gepnev = explode(".", gethostbyaddr($_SERVER['REMOTE_ADDR']))[0];
	$gepadat = parseUserAgent();
	$bejelentkezesek = new MySQLHandler('INSERT INTO bejelentkezesek (felhasznalo, ipcim, bongeszo, bongeszoverzio, oprendszer, oprendszerverzio, oprendszerarch, gepnev) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
		$felhasznalo, $_SERVER['REMOTE_ADDR'], $gepadat['bongeszo'], $gepadat['bongeszover'], $gepadat['oprendszer'], $gepadat['oprendszerver'], $gepadat['architektura'], $gepnev);

	return $bejelentkezesek->last_insert_id;
}

function logActivity($felhasznalo, $params)
{
	@$almenu = $_GET['subpage'];
	@$menupont = $_GET['page'];
	@$elemid = $_GET['id'];

	if($felhasznalo == 0)
	{
		$felhasznalo = null;
	}

	if(isset($_GET['id']) && is_string($_GET['id']) && !isset($_GET['subpage']))
	{
		@$almenu = $_GET['id'];
		$elemid = null;
	}

	if(isset($_GET['subpage']) && isset($_GET['param']) && is_numeric($_GET['param']))
	{
		@$elemid = $_GET['param'];
		unset($params['param']);
	}

	if(count($params) > 0)
	{
		$params = json_encode($params);
	}
	else
	{
		$params = null;
	}
	
	$con = mySQLConnect(false);
	$tevekenysegek = new MySQLHandler('INSERT INTO felhasznalotevekenysegek (ipcim, felhasznalo, menupont, almenu, elemid, params) VALUES (?, ?, ?, ?, ?, ?)',
		$_SERVER['REMOTE_ADDR'], $felhasznalo, $menupont, $almenu, $elemid, $params);

	return $tevekenysegek->last_insert_id;
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

function thisDate()
{
	return date('Y-m-d');
}

function timeStampForSQL($timestamp = null)
{
	
	return date('Y-m-d H:i:s', $timestamp);
}

function szervezetValaszto($ldapres)
{
	if($ldapres)
	{
		$szervezet = new MySQLHandler("SELECT szervezet FROM `szervezetldap` WHERE ? LIKE CONCAT('%', szervezetldap.needle, '%');", $ldapres);
		@$szervezet = $szervezet->Fetch()['szervezet'];
	}
	else
	{
		$szervezet = null;
	}

	return $szervezet;
}

function sortTableHeader($oszlopok, $tablazatnev, $filterinput = false, $sortinput = true, $szuloszur = true, $sortbyurl = false, $onchange = false, $oszlopszam = 0)
{
	foreach($oszlopok as $oszlop)
	{
		if($oszlop['nev'])
		{
			?><th class="tsorth"><?php
				if($filterinput) {
					?><span class="dontprint">
					<input
						size="1"
						type="search"
						id="f<?=$oszlopszam?>"
						<?=($onchange) ? 'onchange' : 'onkeyup' ?>="filterTable('f<?=$oszlopszam?>', '<?=$tablazatnev?>', <?=$oszlopszam?>, <?=($szuloszur) ? 'true' : 'false' ?>)"
						placeholder="<?=$oszlop['nev']?>"
						title="<?=$oszlop['nev']?>">
					<br></span><?php
				}
				if($sortinput)
				{
					if(!$sortbyurl)
					{
						?><span onclick="sortTable(<?=$oszlopszam?>, '<?=$oszlop['tipus']?>', '<?=$tablazatnev?>')"><?=$oszlop['nev']?></span><?php
					}
					else
					{
						?><a href="<?=$oszlop['onclick']?>"><?=$oszlop['nev']?></a><?php
					}
				}
				else
				{
					?><span style="cursor: auto; width: 100%; display: block;"><?=$oszlop['nev']?></span><?php
				}
			?></th><?php
		}
		else
		{
			?><th style="width:2ch"></th><?php
		}
		$oszlopszam++;
	}

	return $oszlopszam;
}

function eszkozTipusValaszto($tipusid)
{
	if($tipusid < 6)
	{
		$eszktip = "aktiveszkoz";
	}
	elseif($tipusid < 11)
	{
		$eszktip = "sohoeszkoz";
	}
	elseif($tipusid == 11)
	{
		$eszktip = "szamitogep";
	}
	elseif($tipusid == 12)
	{
		$eszktip = "nyomtato";
	}
	elseif($tipusid < 20)
	{
		$eszktip = "vegponti";
	}
	elseif($tipusid < 26)
	{
		$eszktip = "mediakonverter";
	}
	elseif($tipusid < 31)
	{
		$eszktip = "bovitomodul";
	}
	elseif($tipusid < 40)
	{
		$eszktip = "szerver";
	}
	elseif($tipusid == 40)
	{
		$eszktip = "telefonkozpont";
	}

	return $eszktip;
}

function eszkozPicker($current = null, $beepitett)
{
	$where = null;
	if($current)
	{
		$where = "WHERE eszkozok.id = ?";
	}
	elseif(!$beepitett)
	{
		$where = "WHERE beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL";
	}
	$eszkozok = new MySQLHandler("SELECT
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
		ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;", $current);

	if(!$current)
	{
		$eszkozok = $eszkozok->Result();
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
		$eszkoz = $eszkozok->Fetch();
		?><h2><label><?= $eszkoz['gyarto'] . " " . $eszkoz['modell'] . $eszkoz['varians'] . " (" . $eszkoz['sorozatszam'] . ")" ?></h2></label>
		<input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$current?>><?php
	}
}

function epuletPicker($current, $js = false)
{
	$epuletek = new MySQLHandler("SELECT telephelyek.telephely AS telephelynev,
			epuletek.id AS id,
			szam AS epuletszam,
			epuletek.nev AS epuletnev,
			epulettipusok.tipus AS tipus
        FROM epuletek
			INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
		ORDER BY epuletek.telephely, epuletek.szam + 0;");
	$epuletek = $epuletek->Result();
	$elozotelephely = "";

	?><div>
	<label for="epulet">Épület:</label><br>
	<select id="epulet" name="epulet" <?=($js) ? 'onchange="epValaszt()"' : ""?>>
		<option value="" selected></option><?php
		foreach($epuletek as $x)
		{
			if($elozotelephely != $x['telephelynev'])
			{
				if($elozotelephely != "")
				{
					?></optgroup><?php
				}

				?><optgroup label="<?=$x['telephelynev']?>"><?php
				$elozotelephely = $x['telephelynev'];
			}
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=IntRagValaszt($x['epuletszam'])?> <?=$x['tipus']?> <?=($x['epuletnev']) ? " (" . $x['epuletnev'] . ")" : "" ?></option><?php
		}
	?></select>
	</div><?php
}

function helyisegPicker($current, $selectnev, $hideall = false)
{
	$helyisegek = new MySQLHandler("SELECT
            helyisegek.id AS id,
            szam AS epuletszam,
            helyisegszam,
            helyisegnev,
            epulet AS epuletid,
            epuletek.nev AS epuletnev
        FROM helyisegek
			LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
        ORDER BY epuletszam + 0, helyisegszam;");
	$helyisegek = $helyisegek->Result();
	$elozoepulet = 0;

	?><div>
	<label for="<?=$selectnev?>">Helyiség:</label><br>
	<select id="<?=$selectnev?>" name="<?=$selectnev?>">
		<option value="" selected></option><?php
		foreach($helyisegek as $x)
		{
			if($elozoepulet != $x['epuletszam'])
			{
				if($elozoepulet != 0)
				{
					?></optgroup><?php
				}

				?><optgroup label="<?=IntRagValaszt($x['epuletszam'])?> épület" class="optgrp" id="<?=$x['epuletid']?>-epulet" <?=($hideall) ? 'style="display: none"' : "" ?>><?php
				$elozoepulet = $x['epuletszam'];
			}
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['helyisegszam'] . " (" . $x['helyisegnev'] . ")" ?></option><?php
		}
	?></select>
	</div><?php
}

function rackPicker($current)
{
	$rackek = new MySQLHandler("SELECT
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
	$rackek = $rackek->Result();

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
	$gyartok = new MySQLHandler("SELECT * FROM gyartok ORDER BY nev");
	$gyartok = $gyartok->Result();

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
	$priority = new MySQLHandler("SELECT * FROM prioritasok ORDER BY id DESC");
	$priority = $priority->Result();

	?><div>
	<label for="prioritas">Prioritás:</label><br>
	<select name="prioritas">
		<option value="" selected></option><?php
		foreach($priority as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ((!$current && $x['id'] == 3) || $current == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
		}
	?></select>
	</div><?php
}

function bugTypePicker($current)
{
	$bugtype = new MySQLHandler("SELECT * FROM bugtipusok ORDER BY nev");
	$bugtype = $bugtype->Result();

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

function felhasznaloPicker($current, $selectnev, $szervezet = null)
{
	$where = "WHERE aktiv = 1";
	if($szervezet)
	{
		$where .= " AND szervezet = ?";
	}
	$felhasznalok = new MySQLHandler("SELECT id, nev FROM felhasznalok $where ORDER BY nev ASC;", $szervezet);
	$felhasznalok = $felhasznalok->Result();

	?><select id="<?=$selectnev?>" name="<?=$selectnev?>">
		<option value="" selected></option><?php
		foreach($felhasznalok as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
		}
	?></select><?php
}

function szervezetPicker($current, $selectnev = 'szervezet', $hosszu = false)
{
	$szervezetek = new MySQLHandler("SELECT * FROM szervezetek;");
	$szervezetek = $szervezetek->Result();

	?><div>
		<label for="<?=$selectnev?>">szervezet:</label><br>
		<select id="<?=$selectnev?>" name="<?=$selectnev?>">
			<option value="" selected></option><?php
			foreach($szervezetek as $x)
			{
				?><option value="<?=$x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=(!$hosszu) ? $x['rovid'] : $x['nev']?></option><?php
			}
		?></select>
	</div><?php
}

function vlanPicker($current, $selectnev = 'vlan')
{
	$vlanok = new MySQLHandler("SELECT * FROM vlanok;");
	$vlanok = $vlanok->Result();
	
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
	?><div class="submit">
		<button type="button" onclick="location.href='<?=$GLOBALS['backtosender']?>'">Mégsem</button>
   </div><?php
}

function mysqliNaturalSort($mysqliresult, $sortcriteria)
{
	$returnarr = mysqliToArray($mysqliresult);
	return arrayNaturalSort($returnarr, $sortcriteria);
}

function arrayNaturalSort($returnarr, $sortcriteria)
{
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
	if($eszktip)
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
		elseif(@!$_GET['page'] != "raktar")
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
	}
	else
	{
		$filter = @$_GET['szures'];
		if ($filter == "raktarban")
		{
			$where = "$eszktip AND eszkozok.hibas = 2";
			$szures = "- Raktárban";
		}
		elseif ($filter == "kiadva")
		{
			$where = "$eszktip AND eszkozok.hibas = 2";
			$szures = "- Kiadva felhasználóknak";
		}
		else
		{
			$where = "$eszktip AND eszkozok.hibas = 2";
			$szures = "- Minden SIM kártya";
		}
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
	include('.\templates\svg.tpl.php');

	?><td class="dontprint"><?php
	if($beepid)
	{
		?><a href='<?=ROOT_PATH?>/<?=$eszktip?>/<?=$eszkid?>?beepites=<?=$beepid?>&action=edit'><?=$icons['deploy']?></a><?php
	}
	?></td>
	<td class="dontprint"><a href='<?=ROOT_PATH?>/<?=$eszktip?>/<?=$eszkid?>?beepites&action=addnew'><?=$icons['deploynew']?></a></td>
	<td class="dontprint"><a href='<?=ROOT_PATH?>/<?=$eszktip?>/<?=$eszkid?>?action=edit'><?=$icons['edit']?></a></td><?php
}

function modId($muvelet, $tipus, $objid)
{
	$modositas = new MySQLHandler("INSERT INTO modositasok (felhasznalo, muvelet, $tipus) VALUES (?, ?, ?)",
		$_SESSION['id'], $muvelet, $objid);

	return $modositas->last_insert_id;
}

function getNotifications()
{
	$felhasznaloid = $_SESSION['id'];
	$notifications = array();
	$switchcheck = mySQLConnect("SELECT ertek
		FROM `beallitasok`
		WHERE nev = 'last_switch_check'
			AND ertek < date_sub(now(), INTERVAL 15 MINUTE)
			AND ertek > date_sub((SELECT lastseennotif FROM felhasznalok WHERE id = $felhasznaloid), INTERVAL 15 MINUTE)");

	if(mysqli_num_rows($switchcheck) > 0)
	{
		$switchutolso = mysqli_fetch_assoc($switchcheck)['ertek'];
		$cim = 'Switch ellenőrző leállt';
		$szoveg = 'A switchek állapotát ellenőrző script utolsó futása: ' . $switchutolso;
		$ertesitette = mySQLConnect("SELECT id FROM ertesitesek WHERE timestamp = '$switchutolso' AND cim = 'Switch ellenőrző leállt'");
		if(mysqli_num_rows($ertesitette) == 0)
		{
			mySQLConnect("INSERT INTO ertesitesek (cim, szoveg, timestamp, url, tipus) VALUES ('$cim', '$szoveg', '$switchutolso', 'aktiveszkozok', '1')");
		}
	}

	$ertesitesek = mySQLConnect("SELECT ertesitesek.id AS id, cim, szoveg, url, timestamp, latta
		FROM ertesitesek
			INNER JOIN ertesites_megjelenik ON ertesitesek.id = ertesites_megjelenik.ertesites
		WHERE felhasznalo = $felhasznaloid
			AND ertesitesek.id = (SELECT MAX(ic.id) FROM ertesitesek ic WHERE ic.cim = ertesitesek.cim)
			AND ertesitesek.timestamp > date_sub(now(), INTERVAL 7 DAY)
		ORDER BY latta ASC, timestamp DESC");

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

function afterDBRedirect($con, $last_id = null)
{
	$RootPath = ROOT_PATH;
	$oldal = $_GET['page'];
	// Ha új elemet vittünk fel, az átirányításhoz lekérjük az utolsó adatbázisművelet id-ját
    if(!isset($_POST['id']))
    {
        if($last_id)
		{
			$id = $last_id;
		}
		else
		{
			$id = mysqli_insert_id($con);
		}
		$action = "uj";
    }
    // Ha szerkesztettük az eszközt, a szerkesztett eszközhöz való visszairányítás
    else
    {
        $id = $_POST['id'];
        $action = "szerkesztes";
    }

    // Ha nem volt hibaüzenet az adatbázisírás során, a felhasználó átirányítása, és eredményről való visszajelzés
    if(mysqli_errno($con) == 0)
    {
        header("Location: $RootPath/$oldal/$id?sikeres=$action");
    }
}

function getPermissionError()
{
    http_response_code(403);
	?><h1>403</h1>
	<strong>A kért művelet nem engedélyezett!</strong><?php
}

function redirectToKuldo($sikeres = null)
{
	$target = $GLOBALS['backtosender'];
	$targeturl = explode("?", $target)[0];

	if($sikeres == "uj")
	{
		$targeturl .= "&sikeres=uj";
	}
	elseif($sikeres == "szerkesztes")
	{
		$targeturl .= "&sikeres=szerkesztes";
	}

	header("Location: $targeturl");
}

function redirectToGyujto($gyujtonev)
{
	$eredmeny = null;
	if($_GET['action'] == "new")
	{
		$eredmeny = "?sikeres=uj";
	}
	elseif($_GET['action'] == "update")
	{
		$eredmeny = "?sikeres=szerkesztes";
	}
	header("Location: ./" . $gyujtonev . $eredmeny);
}

function vegpontLista($portok)
{
	$portok = mysqliNaturalSort($portok, "port");
	if(count($portok) > 0)
	{
		?><div class="vegpontlist"><?php
			$elozoport = null;
			$elozovlan = false;
			foreach($portok as $port)
			{
				// Ha nem a ciklus első körében vagyunk, és egy új port adatait írjuk ki, az előző port divjeinek és hivatkozásának lezárása
				if($elozoport && $elozoport != $port['portid'])
				{
					$elozovlan = false;
					?></div></div></a><?php
				}

				// Ha egy új port adatait írjuk ki, új div nyitása
				if($elozoport != $port['portid'])
				{
					if($port['vlan'] && $port['szam']) // Mivel egy szálon informatikai végpont mellett nem mehet más informatika, ha van telefon, csak az első VLAN kíírása (a többi ennek duplikáltja lenne)
					{
						$elozovlan = true;
					}
					?><a class="<?=($port['hasznalatban'] || $port['szam'] || $port['athurkolas']) ? "foglalt" : "ures" ?>" href='<?=ROOT_PATH?>/port/<?=$port['portid']?>'>
						<div class="vegpont">
							<div><?=$port['port']?></div>
							<div>
								<?=($port['vlan']) ? "<div>" . $port['vlan'] . "</div>" : "" ?>
								<?=($port['szam']) ? "<div>" . $port['szam'] . "</div>" : "" ?>
								<?=($port['athurkolas']) ? "<div>" . $port['athurkolas'] . "</div>" : "" ?><?php
				}

				// Ha egy már megjelenített port további kapcsolatait írjuk ki, csak a további adatok kiírása, új div nyitása nélkül
				else
				{
					?><?=($port['szam']) ? "<div>" . $port['szam'] . "</div>" : "" ?>
					<?=($port['vlan'] && !$elozovlan) ? "<div>" . $port['vlan'] . "</div>" : "" ?><?php
				}

				// A jelenlegi port azonosítása a ciklus következő iterációja részére
				$elozoport = $port['portid'];
			}

			// A legutolsó port div-jeinek lezárása. FONTOS!!! Az utolsó iterációt követően MINDEN ESETBEN NYITVA MARAD KÉT DIV ÉS EGY HIVATKOZÁS.
			?></div></div></a>
		</div><?php
	}
}

function transzportPortLista($id, $tipus = 'epulet', $xlsexport = false)
{
	// JAVÍTANI VALÓ!!!!
	// Mivel a rendszer nem tudja kezelni azt, hogy egy switchport két portra csatlakozzon, így jelenleg egy trükkel lett megoldva a megjelenítés, ami valószínűleg 99%-ban működik.
	// Az eszközön csak az optikai pár ELSŐ felét kell kiválasztani, a megjelenítésnél a következő szál automatikusan foglaltnak minősül.
	// Ebből adódóan a jelen megjelenítés nem működik, ha nem egymás melletti optikai szálakra van csatlakoztatva az eszköz.
	// Másfajta átvitel (réz, mikró) esetén ilyen nincs, ott a rendszer egy portot egy porthoz csatlakozónak vesz

	$where = "";
	if($tipus == 'epulet')
	{
		$where = "transzportportok.epulet = ?";
	}
	elseif($tipus == 'rack')
	{
		$where = "rackportok.rack = ?";
	}
	elseif($tipus == 'helyiseg')
	{
		$where = "rackszekrenyek.helyiseg = ?";
	}

	$portok = new MySQLHandler("SELECT DISTINCT portok.id AS portid, portok.port AS port,
                eszkozok.id AS hasznalatban,
                tultransz.port AS tulport,
                epuletek.szam AS epuletszam,
                epuletek.nev AS epuletnev,
                beepitesek.nev AS beepitesnev,
                netdevlocal.port AS helyieszkport,
                remotebeep.nev AS szomszednev,
                netdevremote.port AS szomszedeszkport,
                transzportportok.fizikaireteg AS fizikaireteg,
                portok.athurkolas AS athurkolas,
                hurok.port AS huroktuloldal,
				portok.csatlakozo AS csatlakozo
            FROM portok
                INNER JOIN transzportportok ON transzportportok.port = portok.id
                LEFT JOIN transzportportok tuloldal ON portok.csatlakozas = tuloldal.port
                LEFT JOIN epuletek ON tuloldal.epulet = epuletek.id
				LEFT JOIN epuletek helyi ON transzportportok.epulet = epuletek.id
				LEFT JOIN rackportok ON rackportok.port = portok.id
				LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
				LEFT JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN portok tultransz ON tuloldal.port = tultransz.id
                LEFT JOIN portok netdevlocal ON portok.id = netdevlocal.csatlakozas
                LEFT JOIN portok netdevremote ON portok.csatlakozas = netdevremote.csatlakozas
				LEFT JOIN portok hurok ON portok.athurkolas = hurok.id
                LEFT JOIN switchportok ON switchportok.port = netdevlocal.id
                LEFT JOIN sohoportok ON sohoportok.port = netdevlocal.id
                LEFT JOIN mediakonverterportok ON mediakonverterportok.port = netdevlocal.id
                LEFT JOIN eszkozok ON switchportok.eszkoz = eszkozok.id OR mediakonverterportok.eszkoz = eszkozok.id OR sohoportok.eszkoz = eszkozok.id
                LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                LEFT JOIN switchportok remoteswport ON remoteswport.port = netdevremote.id
                LEFT JOIN sohoportok remotesohoport ON remotesohoport.port = netdevremote.id
                LEFT JOIN mediakonverterportok remotemkport ON remotemkport.port = netdevremote.id
                LEFT JOIN eszkozok remoteeszk ON remoteswport.eszkoz = remoteeszk.id OR remotemkport.eszkoz = remoteeszk.id OR remotesohoport.eszkoz = remoteeszk.id
                LEFT JOIN beepitesek remotebeep ON remoteeszk.id = remotebeep.eszkoz
            WHERE $where AND beepitesek.kiepitesideje IS NULL AND remotebeep.kiepitesideje IS NULL
            GROUP BY portok.id
            ORDER BY portok.port;", $id);
	
	if($portok->sorokszama > 0)
	{
		$elozoport = null;
		$huroktulportok = $portok->AsArray();
		$portok = $portok->Result();

		?><div class="transzportlist"><?php
			foreach($portok as $port)
			{
				switch($port['fizikaireteg'])
				{
					case 1 : $retegtip = "rez"; break;
					case 2 : $retegtip = "single"; break;
					case 3 : $retegtip = "multi"; break;
					case 4 : $retegtip = "mikro"; break;
					default: $retegtip = "rez";
				}
				
				?><a class="<?=($port['hasznalatban'] || $elozoport || $port['huroktuloldal']) ? "foglalt" : "ures" ?>" href='<?=ROOT_PATH?>/port/<?=$port['portid']?>'>
					<div class="infoboxtitle"><div class="fizikairetegtip <?=$retegtip?>"></div><?=$port['port']?><?=($port['tulport']) ? "<span class='center' style='width: unset'>-</span><span class='right'>" . $port['tulport'] . "</span>" : "" ?></div>
					<div class="transzport">
						<div><h4><?=($port['tulport']) ? $port['epuletszam'] . ". épület felé" : "" ?></h4></div><?php
						if(!$elozoport)
						{
							if($port['huroktuloldal'])
							{
								?><div><?=($port['huroktuloldal']) ? "<strong>Áthurkolva: </strong>". $port['huroktuloldal'] : "" ?></div>
								<div><?=($port['szomszednev']) ? "<strong>Ide érkező eszköz: </strong>". $port['szomszednev'] . " - " . $port['szomszedeszkport'] : "" ?></div><?php
								foreach($huroktulportok as $tulport)
								{
									?><div><?=($tulport['szomszednev'] && $tulport['athurkolas'] == $port['portid']) ? "<strong>Hurok túlfelére érkező eszköz: </strong>". $tulport['szomszednev'] . " - " . $tulport['szomszedeszkport'] : "" ?></div><?php
								}
							}
							else
							{
								?><div><?=($port['beepitesnev']) ? "<strong>Helyi eszköz: </strong>". $port['beepitesnev'] . " - " . $port['helyieszkport'] : "" ?></div>
								<div><?=($port['szomszednev']) ? "<strong>Távoli eszköz: </strong>". $port['szomszednev'] . " - " . $port['szomszedeszkport'] : "" ?></div><?php
							}

							// Ha ST csatlakozó, és van rajta eszköz, akkor a következő portot is foglaltnak vesszük
							if($port['csatlakozo'] == 3 && $port['beepitesnev'] && ($port['fizikaireteg'] == 2 || $port['fizikaireteg'] == 3))
							{
								$elozoport = $port;
							}
						}
						else
						{
							if($elozoport['huroktuloldal'])
							{
								?><div><?=($elozoport['huroktuloldal']) ? "<strong>Áthurkolva: </strong>". $elozoport['huroktuloldal'] : "" ?></div>
								<div><?=($elozoport['szomszednev']) ? "<strong>Ide érkező eszköz: </strong>". $elozoport['szomszednev'] . " - " . $elozoport['szomszedeszkport'] : "" ?></div><?php
								foreach($huroktulportok as $tulport)
								{
									?><div><?=($tulport['athurkolas'] == $elozoport['portid']) ? "<strong>Hurok túlfelére érkező eszköz: </strong>". $tulport['szomszednev'] . " - " . $tulport['szomszedeszkport'] : "" ?></div><?php
								}
							}
							else
							{
								?><div><?=($elozoport['beepitesnev']) ? "<strong>Helyi eszköz: </strong>". $elozoport['beepitesnev'] . " - " . $elozoport['helyieszkport'] : "" ?></div>
								<div><?=($elozoport['szomszednev']) ? "<strong>Távoli eszköz: </strong>". $elozoport['szomszednev'] . " - " . $elozoport['szomszedeszkport'] : "" ?></div><?php
							}
							$elozoport = null;
						}
					?></div>
				</a><?php
			}
		?></div><?php
	}
}

function hibajegyErtesites($ertesites, $szoveg, $hibajegyid, $felhasznalo, $szervezet, $szak = null)
{
	$url = "hibajegy/$hibajegyid";
	$tipus = "11";
	$con = mySQLConnect(false);
	$stmt = $con->prepare('INSERT INTO ertesitesek (cim, szoveg, url, tipus) VALUES (?, ?, ?, ?)');
	$stmt->bind_param('ssss', $ertesites, $szoveg, $url, $tipus);
	$stmt->execute();

	$ertesitesid = mysqli_insert_id($con);

	if($szak)
	{
		$szak = "AND (csoportok.szak = $szak OR csoportok.szak IS NULL)";
	}

	mySQLConnect("INSERT INTO ertesites_megjelenik(felhasznalo, ertesites)
			SELECT DISTINCT csoporttagsagok.felhasznalo AS felhasznalo, '$ertesitesid'
				FROM csoportok
                	INNER JOIN csoporttagsagok ON csoportok.id = csoporttagsagok.csoport
					INNER JOIN csoportjogok ON csoporttagsagok.csoport = csoportjogok.csoport
					INNER JOIN jogosultsagok ON jogosultsagok.felhasznalo = csoporttagsagok.felhasznalo
    			WHERE menupont = 11 AND iras > 1 AND csoportjogok.szervezet = $szervezet $szak
			UNION
			SELECT id AS felhasznalo, '$ertesitesid' FROM felhasznalok WHERE id = $felhasznalo;");
}

function quickXSSfilter($string)
{
	$string = str_replace("<", "&lt;", $string);
	$string = str_replace(">", "&gt;", $string);
	$string = str_replace("{", "&#123;", $string);
	$string = str_replace("}", "&#125;", $string);
	$string = str_replace("$", "&#36;", $string);
	$string = str_replace("(", "&#40;", $string);
	$string = str_replace(")", "&#41;", $string);
	return $string;
}

function revertXSSfilter($string)
{
	$string = str_replace("&lt;", "<", $string);
	$string = str_replace("&gt;", ">", $string);
	$string = str_replace("&#123;", "{", $string);
	$string = str_replace("&#125;", "}", $string);
	$string = str_replace("&#36;", "$", $string);
	$string = str_replace("&#40;", "(", $string);
	$string = str_replace("&#41;", ")", $string);
	return $string;
}

function csoportWhere($csoporttagsagok, $csopwhereset)
{
	$szervezetek = array();
    $telephelyek = array();

	foreach($csoporttagsagok as $csoportjog)
    {
        if($csoportjog['szervezet'] && !in_array($csoportjog['szervezet'], $szervezetek))
		{
			$szervezetek[] = $csoportjog['szervezet'];
		}
		elseif($csoportjog['telephely'] && !in_array($csoportjog['telephely'], $telephelyek))
		{
			$telephelyek[] = $csoportjog['telephely'];
		}
    }

	$where = "(";

	if($csopwhereset['and'])
	{
		$where = "AND (";
	}

	if($csopwhereset['szervezetelo'])
	{
		$csopwhereset['szervezetelo'] = $csopwhereset['szervezetelo'] . ".";
	}

	if($csopwhereset['telephelyelo'])
	{
		$csopwhereset['telephelyelo'] = $csopwhereset['telephelyelo'] . ".";
	}

	$szervezetdb = count($szervezetek);
	$telepehelydb = count($telephelyek);

	$wherealak = "";
	$wheretelep = "";
	
	for($i = 0; $i < $szervezetdb; $i++)
	{
		$wherealak .= $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " = " . $szervezetek[$i];

		if($i != $szervezetdb - 1)
		{
			$wherealak .= " OR ";
		}
	}

	if($csopwhereset['szervezetnull'])
	{
		if($szervezetdb > 0)
		{
			$wherealak .= " OR ";
		}
		$wherealak .= $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " IS NULL";
	}

	for($i = 0; $i < $telepehelydb; $i++)
	{
		$wheretelep .= $csopwhereset['telephelyelo'] . "telephely = " . $telephelyek[$i];

		if($i != $telepehelydb - 1)
		{
			$wheretelep .= " OR ";
		}
	}

	if($csopwhereset['telephelynull'])
	{
		if($telepehelydb > 0)
		{
			$wheretelep .= " OR ";
		}
		$wheretelep .= $csopwhereset['telephelyelo'] . "telephely IS NULL";
	}

	if(!$csopwhereset['tipus'])
	{
		if($telepehelydb == 0 && $szervezetdb == 0)
		{
			$where .= $csopwhereset['telephelyelo'] . "telephely = 999 AND " . $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " = 999";
		}
		else
		{		
			$where .= $wherealak;
			if($wherealak)
			{
				$where .= " OR ";
			}
			$where .= $wheretelep;
		}
	}
	elseif($csopwhereset['tipus'] == "szervezet")
	{
		if($szervezetdb == 0)
		{
			$where .= $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " = 999";
		}
		else
		{
			$where .= $wherealak;
		}
	}
	elseif($csopwhereset['tipus'] == "telephely")
	{
		if($telepehelydb == 0)
		{
			$where .= $csopwhereset['telephelyelo'] . "telephely = 999";
		}
		else
		{
			$where .= $wheretelep;
		}
	}

	$where .= ") ";

	return $where;
}

function csoportWhere_new($csoporttagsagok, $csopwhereset)
{
	$szervezetek = array();
    $telephelyek = array();
	$paramarr = array();

	foreach($csoporttagsagok as $csoportjog)
    {
        if($csoportjog['szervezet'] && !in_array($csoportjog['szervezet'], $szervezetek))
		{
			$szervezetek[] = $csoportjog['szervezet'];
		}
		elseif($csoportjog['telephely'] && !in_array($csoportjog['telephely'], $telephelyek))
		{
			$telephelyek[] = $csoportjog['telephely'];
		}
    }

	$where = "(";

	if($csopwhereset['and'])
	{
		$where = "AND (";
	}

	if($csopwhereset['szervezetelo'])
	{
		$csopwhereset['szervezetelo'] = $csopwhereset['szervezetelo'] . ".";
	}

	if($csopwhereset['telephelyelo'])
	{
		$csopwhereset['telephelyelo'] = $csopwhereset['telephelyelo'] . ".";
	}

	$szervezetdb = count($szervezetek);
	$telepehelydb = count($telephelyek);

	$wherealak = "";
	$wheretelep = "";
	
	for($i = 0; $i < $szervezetdb; $i++)
	{
		if($i == 0)
		{
			$wherealak .= $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " IN (";
		}
		
		$wherealak .= "?";
		$paramarr[] = $szervezetek[$i];

		if($i != $szervezetdb - 1)
		{
			$wherealak .= ", ";
		}

		if($i == $szervezetdb - 1)
		{
			$wherealak .= ")";
		}
	}

	if($csopwhereset['szervezetnull'])
	{
		if($szervezetdb > 0)
		{
			$wherealak .= " OR ";
		}
		$wherealak .= $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " IS NULL";
	}

	for($i = 0; $i < $telepehelydb; $i++)
	{
		if($i == 0)
		{
			$wheretelep .= $csopwhereset['telephelyelo'] . "telephely IN (";
		}

		$wheretelep .= "?";
		//$paramarr[] = $telephelyek[$i];

		if($i != $telepehelydb - 1)
		{
			$wheretelep .= ", ";
		}
		if($i == $telepehelydb - 1)
		{
			$wheretelep .= ")";
		}
	}

	if($csopwhereset['telephelynull'])
	{
		if($telepehelydb > 0)
		{
			$wheretelep .= " OR ";
		}
		$wheretelep .= $csopwhereset['telephelyelo'] . "telephely IS NULL";
	}

	if(!$csopwhereset['tipus'])
	{
		if($telepehelydb == 0 && $szervezetdb == 0)
		{
			$where .= $csopwhereset['telephelyelo'] . "telephely = 999 AND " . $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " = 999";
		}
		else
		{		
			$where .= $wherealak;
			if($wherealak)
			{
				$where .= " OR ";
			}
			$where .= $wheretelep;
		}
	}
	elseif($csopwhereset['tipus'] == "szervezet")
	{
		if($szervezetdb == 0)
		{
			$where .= $csopwhereset['szervezetelo'] . $csopwhereset['szervezetmegnevezes'] . " = 999";
		}
		else
		{
			$where .= $wherealak;
		}
	}
	elseif($csopwhereset['tipus'] == "telephely")
	{
		if($telepehelydb == 0)
		{
			$where .= $csopwhereset['telephelyelo'] . "telephely = 999";
		}
		else
		{
			$where .= $wheretelep;
		}
	}

	$where .= ") ";

	return array($where, $paramarr);
}

function mysqliToArray($mysqlires, $ondimensional = false)
{
	$returnarr = array();
    foreach($mysqlires as $sor)
    {
		$element = array();
		foreach($sor as $key => $value)
		{
			if($ondimensional)
			{
				$element = $value;
				break;
			}
			$element[$key] = $value;
		}
        $returnarr[] = $element;
    }
	return $returnarr;
}

function atHurkolas($helyiportid, $hurok, $con, $jelenportmod)
{
	$null = null;

	// Kezdetként annak ellenőrzése, hogy a jelenlegi port szerepel-e hurokként valahol,
	// illetve, hogy a jelen portra van-e rakva hurok
	$huroktul = mySQLConnect("SELECT id FROM portok WHERE athurkolas = $helyiportid");
	$huroktul = mysqli_fetch_assoc($huroktul);
	if($huroktul)
	{	
		$huroktul = $huroktul['id'];
	}

	$jelenhurok = mySQLConnect("SELECT athurkolas, modid FROM portok WHERE id = $helyiportid");
	$jelenhurokat = mysqli_fetch_assoc($jelenhurok);
	if($jelenhurokat)
	{
		$jelenhurok = $jelenhurokat['athurkolas'];
	}

	if(!$jelenportmod && ($hurok != $huroktul || $hurok != $jelenhurok))
	{
		mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
			SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
			FROM portok
			WHERE id = $helyiportid");

			// A tényleges módosítás folyamata
			$modif_id = modId("2", "port", $helyiportid);
	}
	else
	{
		$modif_id = $jelenhurokat['modid'];
	}

	// Ha a hurokként küldött port nem egyezik azzal, ahol a jelen port hurokként szerepel, úgy modosít
	if($hurok != $huroktul)
	{
		if($huroktul)
		{
			mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
				SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
				FROM portok
				WHERE id = $huroktul");
		}

		if($hurok)
		{
			mySQLConnect("INSERT INTO portok_history (portid, port, csatlakozo, csatlakozas, athurkolas, modid)
				SELECT id, port, csatlakozo, csatlakozas, athurkolas, modid
				FROM portok
				WHERE id = $hurok");
		}
		
		// Először azon port hurkának nullázása, ahol a jelen port hurokként szerepel
		$stmt = $con->prepare('UPDATE portok SET athurkolas=?, modid=? WHERE id=?');
		$stmt->bind_param('ssi', $null, $modif_id, $huroktul);
		$stmt->execute();

		// Majd a jelen port hurokként beállítása a hurokként küldött portra, ha az nem null értékű
		if($hurok)
		{
			$stmt = $con->prepare('UPDATE portok SET athurkolas=?, modid=? WHERE id=?');
			$stmt->bind_param('ssi', $helyiportid, $modif_id, $hurok);
			$stmt->execute();
		}
	}

	// Ha a jelenlegi porton más van beállítva huroknak, mint amit a form küldött, módosítás
	if($hurok != $jelenhurok)
	{
		$stmt = $con->prepare('UPDATE portok SET athurkolas=?, modid=? WHERE id=?');
		$stmt->bind_param('ssi', $hurok, $modif_id, $helyiportid);
		$stmt->execute();
	}
}

function purifyPost($ishtml = false)
{
	$activitylogid = $GLOBALS['activitylogid'];
	foreach($_POST as $key => $value)
    {
        if(is_array($value))
		{
			foreach($value as $key2 => $value2)
			{
				if(is_array($value2))
				{
					foreach($value2 as $key3 => $value3)
					{
						if ($value3 == "NULL" || $value3 == "")
						{
							$_POST[$key][$key2][$key3] = NULL;
						}
						elseif(!$ishtml)
						{
							$_POST[$key][$key2][$key3] = quickXSSfilter($value3);
						}
					}
				}
				else
				{
					if ($value2 == "NULL" || $value2 == "")
					{
						$_POST[$key][$key2] = NULL;
					}
					elseif(!$ishtml)
					{
						$_POST[$key][$key2] = quickXSSfilter($value2);
					}
				}
			}
		}
		else
		{
			if ($value == "NULL" || $value == "")
			{
				//echo "null";
				$_POST[$key] = NULL;
			}
			elseif(!$ishtml)
			{
				$_POST[$key] = quickXSSfilter($value);
			}
		}
	}
}

function purifyArray($array)
{
	foreach($array as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $array[$key] = NULL;
        }
		else
        {
            $array[$key] = quickXSSfilter($value);
        }
    }

	return $array;
}

function showHelyiseg($szam, $nev = null)
{
	($szam) ? $szam . ". helyiség" : "";
	($szam && $nev) ? " - " : "";
	echo $nev;
}

function showEpulet($szam, $tipus = null)
{
	($szam) ? $szam : "";
	($szam && $tipus) ? ". " : "";
	echo $tipus;
}

function showBreadcumb($eszkoz, $lastlink = false)
{
	$i = 1;

	?><div class="breadcumblist">
		<ol vocab="https://schema.org/" typeof="BreadcrumbList">
			<li property="itemListElement" typeof="ListItem">
				<a property="item" typeof="WebPage"
					href="<?=ROOT_PATH?>/">
				<span property="name">Kecskemét Informatika</span></a>
				<meta property="position" content="<?=$i++?>">
			</li><?php
			if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
            {
				?><?=($eszkoz['thelyid']) ? "<li><b>></b></li>" : "" ?>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" typeof="WebPage"
						href="<?=ROOT_PATH?>/epuletek/<?=$eszkoz['thelyid']?>">
					<span property="name"><?=$eszkoz['telephely']?></span></a>
					<meta property="position" content="<?=$i++?>">
				</li>
				<?=($eszkoz['epuletid']) ? "<li><b>></b></li>" : "" ?>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" typeof="WebPage"
						href="<?=ROOT_PATH?>/epulet/<?=$eszkoz['epuletid']?>">
					<span property="name"><?=showEpulet($eszkoz['epuletszam'], $eszkoz['epulettipus'])?></span></a>
					<meta property="position" content="<?=$i++?>">
				</li>
				<?=($eszkoz['helyisegid']) ? "<li><b>></b></li>" : "" ?>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" typeof="WebPage"
						href="<?=ROOT_PATH?>/helyiseg/<?=$eszkoz['helyisegid']?>">
					<span property="name"><?=showHelyiseg($eszkoz['helyisegszam'], $eszkoz['helyisegnev'])?></span></a>
					<meta property="position" content="<?=$i++?>">
				</li>
				<?php if(isset($eszkoz['rackid']))
				{
					?><li><b>></b></li>
					<li property="itemListElement" typeof="ListItem">
						<a property="item" typeof="WebPage"
							href="<?=ROOT_PATH?>/rack/<?=$eszkoz['rackid']?>">
						<span property="name"><?=$eszkoz['rack']?></span></a>
						<meta property="position" content="<?=$i++?>">
					</li><?php
				}

				?><?=($eszkoz['beepitesinev'] || $eszkoz['ipcim']) ? "<li><b>></b></li>" : "hh" ?>
				<li property="itemListElement" typeof="ListItem">
					<span property="name"><?=($eszkoz['beepitesinev']) ? $eszkoz['beepitesinev'] : "" ?> <?=(isset($eszkoz['ipcim']) && $eszkoz['ipcim']) ? "(" . $eszkoz['ipcim'] . ")" : "" ?></span>
					<meta property="position" content="<?=$i++?>">
				</li><?php
			}
			else
			{
				?><li><b>></b></li>
				<li property="itemListElement" typeof="ListItem">
					<a property="item" typeof="WebPage"
						href="<?=ROOT_PATH?>/aktiveszkozok">
					<span property="name">Aktív eszközök</span></a>
					<meta property="position" content="<?=$i++?>">
				</li>
				<li><b>></b></li>
				<li property="itemListElement" typeof="ListItem">
					<span property="name"><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?><?=$eszkoz['varians']?> (<?=$eszkoz['sorozatszam']?>)</span>
					<meta property="position" content="<?=$i++?>">
				</li><?php
			}
		?></ol>
	</div><?php
}

function verifyWholeNum($szam)
{
	if(is_int($szam))
	{
		return true;
	}
	elseif(ctype_digit($szam))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function generateSimpleExcel($fejlec, $sorok)
{
	$oszlopnevek = array();
	$oszlopmezok = array();

	foreach($fejlec as $x)
	{
		$oszlopnevek[] = revertXSSfilter($x['nev']);
		$oszlopmezok[] = $x['adatmezo'];
	}
	$exportdata = array($oszlopnevek);

	foreach($sorok as $sor)
	{
		$adatok = array();
		foreach($oszlopmezok as $mezonev)
		{
			foreach($sor as $key => $value)
			{
				if($key == $mezonev)
				{
					$adatok[] = revertXSSfilter($value);
					break;
				}
			}
		}
		$exportdata[] = $adatok;
	}

/* DEBUG CÉLOKRA, KIÍRJA A GENERÁLT TÁBLÁZATOT
	?><table><?php
	foreach($exportdata as $sor)
	{
		?><tr><?php
		foreach($sor as $mezo)
		{
			?><td><?=$mezo?></td><?php
		}
		?></tr><?php
	}

	?></table>
	<?php */

	return $exportdata;
}

function exportExcel($data, $fajlnev)
{
	include_once('./classes/xlsxwriter.class.php');

	$file = "./uploads/$fajlnev.xlsx";
    $writer = new XLSXWriter();
    $writer->writeSheet($data);
	$writer->
    $writer->writeToFile($file);

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/xlsx');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        readfile($file);
        exit;
    }
}

function arrayMultiDimension($array)
{
   rsort($array);
   return isset($array[0]) && is_array($array[0]);
}

function fajlFeltoltes($fajlok, $filetypes, $mediatype, $gyokermappa, $egyedimappa)
{
	$con = mySQLConnect(false);
	$feltoltesimappa = "$gyokermappa/$egyedimappa/";
	$feltoltottfajlok = array();
    $uploadids = array();
	$single = false;

	if(arrayMultiDimension($fajlok))
	{
		$db = count($fajlok['name']);
	}
	else
	{
		$single = true;
		$db = 1;
	}

	for($i = 0; $i < $db; $i++)
	{
		if($single)
		{
			$fajlok['name'] = array($fajlok['name']);
			$fajlok['type'] = array($fajlok['type']);
			$fajlok['tmp_name'] = array($fajlok['tmp_name']);
		}

		if (!in_array($fajlok['type'][$i], $mediatype))
		{
			$uzenet = "A fájl típusa nem megengedett: " . $fajlok['name'][$i];
		}
		else
		{
			if(!file_exists($feltoltesimappa))
			{
				mkdir($feltoltesimappa, 0777, true);
			}

			$fajlnev = strtolower(str_replace(".", time() . ".", $fajlok['name'][$i]));
			$finalfile = $feltoltesimappa . $fajlnev;
			if(file_exists($finalfile))
			{
				$uzenet = "A feltölteni kívánt fájl már létezik: " . $fajlnev;
			}
			else
			{
				move_uploaded_file($fajlok['tmp_name'][$i], $finalfile);
				$uzenet = 'A fájl feltöltése sikeresen megtörtént: ' . $fajlnev;
				$feltoltottfajlok[] = "$egyedimappa/" . "$fajlnev";
			}
		}
	}

	if(count($feltoltottfajlok) > 0)
	{
		foreach($feltoltottfajlok as $fajl)
		{
			$stmt = $con->prepare('INSERT INTO feltoltesek (fajl) VALUES (?)');
			$stmt->bind_param('s', $fajl);
			$stmt->execute();

			$fajlid = mysqli_insert_id($con);
			$uploadids[] = $fajlid;
		}
	}

	echo $uzenet;

	return $uploadids;
}

function roundUp99($value)
{
	$tizedes = $value - floor($value);
	if($tizedes > 0.95)
	{
		return ceil($value);
	}
	else
	{
		return $value;
	}
}

function secondsToFullFormat($seconds, $showseconds = true)
{
	if($seconds < 0)
		$seconds = $seconds * - 1;
	$nap = $ev = null;
	$masodperc = str_pad(($seconds % 60), 2, "0", STR_PAD_LEFT);
	$perc = str_pad(($seconds / 60 % 60), 2, "0", STR_PAD_LEFT);
	$ora = str_pad((floor($seconds / 3600 % 24)), 2, "0", STR_PAD_LEFT);
	$ora = "$ora óra, ";
	$napok = floor($seconds / 3600 / 24);
	$evek = floor($napok / 365);
	$napok = $napok - ($evek * 365);

	if($napok > 0)
	{
		$nap = "$napok nap, ";
	}

	if($evek > 0)
	{
		$ev = "$evek év, ";
	}

	if($showseconds)
	{
		$masodperc = ", $masodperc másodperc";
	}
	else
	{
		$masodperc = "";
	}

	return $ev . $nap . $ora . "$perc perc$masodperc";
}

function ConvertToDistinguishedName($OrganizationalUnit)
{
	if(str_contains($OrganizationalUnit, 'DC='))
	{
		$forditott = $OrganizationalUnit;
	}
	else
	{
		$kimenet = explode("/", $OrganizationalUnit);
		$db = count($kimenet) - 1;
		$forditott = "";
		$dcnev = "";
		for ($i = $db; $i > -1; $i--) #Loop starts from the last section of the string array to put them to the front
		{
			if ($i != 0) #Do the conversion until we get to the DC part
			{
				$forditott .= 'OU="' . $kimenet[$i] . '",';
			}
			else #Here's where we turn DC name into DistinguishedName format too
			{
				$dcnevold = $kimenet[$i];
				$dcnevtemp = explode(".", $dcnevold);
				for ($j = 0; $j < count($dcnevtemp); $j++)
				{
					$dcnev .= 'DC="' . $dcnevtemp[$j] . '",';
				}
				$forditott .= trim($dcnev, ',');
			}
		}
	}

    return $forditott; #OU name in DistinguishedName form
}

function FormatSQL($sql)
{
	$sql = str_replace("INNER", "<br>&nbsp&nbsp\n&nbsp&nbspINNER", $sql);
	$sql = str_replace("LEFT", "<br>&nbsp&nbsp\n&nbsp&nbspLEFT", $sql);
	$sql = str_replace("FROM", "<br>&nbsp&nbspFROM", $sql);
	$sql = str_replace("WHERE", "<br>&nbsp&nbspWHERE", $sql);
	$sql = str_replace("ORDER", "<br>&nbsp&nbspORDER", $sql);
	$sql = str_replace("GROUP", "<br>&nbsp&nbspGROUP", $sql);
	$sql = str_replace("(SELECT", "<br>&nbsp&nbsp(SELECT", $sql);
	$sql = str_replace("UNION", "<br>UNION<br>", $sql);
	return $sql;
}

function IntRagValaszt($szam)
{
    if($szam == 0)
    {
        return $szam . "-s";
    }
	elseif(!is_numeric($szam))
	{
		return $szam;
	}
    else
    {
        switch($szam % 10)
        {
            case 1: case 2: case 4: case 7: case 9:
                return $szam . "-es";
                break;
            case 3: case 8:
                return $szam . "-as";
                break;
            case 5:
                return $szam . "-ös";
                break;
            case 6:
                return $szam . "-os";
                break;
            case 0:
                switch($szam / 10)
                {
                    case 1: case 4: case 5: case 7: case 9:
                        return $szam . "-es";
                        break;
                    case 2: case 3: case 6: case 8: default:
                        return $szam . "-as";
                        break;
                }
        }
    }
}

function concatToAssocArray($fields, ...$concats)
{
	if(count($fields) != count($concats))
	{
		echo "A mezőnevek és értékmezők száma nem egyezik!";
		return false;
	}

	if(!$concats[0])
	{
		return array();
	}

	$duplicatedarray = array();
	$assoc = array();

	$mezoindex = 0;

	foreach($concats as $concat)
	{
		$temparr = explode(",;,", $concat);

		$elemszam = count($temparr);

		for($i = 0; $i < $elemszam; $i++)
		{
			$duplicatedarray[$i][$fields[$mezoindex]] = $temparr[$i];
		}
		$mezoindex++;
	}

	foreach($duplicatedarray as $duplicate)
	{
		if(!in_array($duplicate, $assoc))
		{
			$assoc[] = $duplicate;
		}
	}

	return $assoc;
}

function str_contains_any($haystack, $needles): bool
{
	return array_reduce($needles, fn($a, $n) => $a || str_contains($haystack, $n), false);
}

function fajlnevFromPath($path)
{
	$ut = explode("/", $path);
	return end($ut);
}

function multiSelectDropdown($elements, array $selected, string $selectnev, string $label, $selectid = null)
{
	//TODO: Ha a szülőelemen van overflow:hidden, úgy a legördülő menü nem tud "megszökni" a szülőelem határain kívülre

	?><div>
		<label><?=$label?></label>
		<div class="msdropdownparent">
			<div onclick="dropdownMutat('<?=$selectid?>')"><input type="text" readonly value="<?=$label?> listája"></input></div>
			<div <?=($selectid) ? "id=" . $selectid : "" ?> class="msdropdown" onmouseleave="dropdownRejt('<?=$selectid?>')"><?php
			foreach($elements as $element)
			{
				?><label class="customcb">
					<input type="checkbox" name="<?=$selectnev?>[]" value="<?=$element['id']?>" <?=(in_array($element['id'], $selected)) ? "checked" : ""?>>
						<span class="msddlabel"><?=$element['nev']?></span>
					<span class="customcbjelolo"></span>
					</input>
				</label><?php
			}
			?></div>
		</div>
   </div><?php
}

function isVerifiedToWrite($querystring, $needle, $haystack, $params = null)
{
	$verify = new mySQLHandler();
	$verify->KeepAlive();
	
	if(!MINDIR)
	{
		if(CSOPORTIR)
		{
			if(isset($_POST['id']) && isset($_GET['id']))
			{
				$f_id = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];
			
				$verify->Query($querystring, $f_id, ...$params);
				if($verify->Fetch()[$haystack] == $needle)
				{
					$irhat = true;
				}
				else
				{
					$irhat = false;
				}
			}
			else
			{
				$irhat = true;
			}
		}
		else
		{
			$irhat = false;
		}
	}
	else
	{
		$irhat = true;
	}

	return $irhat;
}