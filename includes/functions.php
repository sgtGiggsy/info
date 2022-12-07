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

function epuletPicker($current)
{
	$epuletek = mySQLConnect("SELECT epuletek.id AS id,
			szam AS epuletszam,
			epuletek.nev AS epuletnev,
			epulettipusok.tipus AS tipus
        FROM epuletek
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
		ORDER BY epuletek.szam;");

	?><div>
	<label for="epulet">Épület:</label><br>
	<select id="epulet" name="epulet">
		<option value="" selected></option><?php
		foreach($epuletek as $x)
		{
			?><option value="<?php echo $x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?= $x['epuletszam'] . ". " . $x['tipus'] . ($x['epuletnev']) ? " (" . $x['epuletnev'] . ")" : "" ?></option><?php
		}
	?></select>
	</div><?php
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
        FROM helyisegek
			LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
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

function alakulatPicker($current, $selectnev = 'alakulat', $hosszu = false)
{
	$alakulatok = mySQLConnect("SELECT * FROM alakulatok;");

	?><div>
		<label for="<?=$selectnev?>">Alakulat:</label><br>
		<select id="<?=$selectnev?>" name="<?=$selectnev?>">
			<option value="" selected></option><?php
			foreach($alakulatok as $x)
			{
				?><option value="<?=$x["id"] ?>" <?= ($current == $x['id']) ? "selected" : "" ?>><?=(!$hosszu) ? $x['rovid'] : $x['nev']?></option><?php
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
	
	?><div class="submit">
		<button type="button" onclick="location.href='<?=$backtosender?>'">Mégsem</button>
   </div><?php
}

function mysqliNaturalSort($mysqliresult, $sortcriteria)
{
	$returnarr = mysqliToArray($mysqliresult);

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
		elseif(!$_GET['page'] != "raktar")
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
	$RootPath = getenv('APP_ROOT_PATH');

	?><td class="dontprint"><?php
	if($beepid)
	{
		?><a href='<?=$RootPath?>/<?=$eszktip?>/<?=$eszkid?>?beepites=<?=$beepid?>&action=edit'><img src='<?=$RootPath?>/images/beepites.png' alt='Beépítés szerkesztése' title='Beépítés szerkesztése' /></a><?php
	}
	?></td>
	<td class="dontprint"><a href='<?=$RootPath?>/<?=$eszktip?>/<?=$eszkid?>?beepites&action=addnew'><img src='<?=$RootPath?>/images/newbeep.png' alt='Új beépítés' title='Új beépítés' /></a></td>
	<td class="dontprint"><a href='<?=$RootPath?>/<?=$eszktip?>/<?=$eszkid?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Eszköz szerkesztése' title='Eszköz szerkesztése'/></a></td><?php
}

function modId($muvelet, $tipus, $objid)
{
	$con = mySQLConnect();
	$felhasznalo = $_SESSION[getenv('SESSION_NAME').'id'];
	$string = "INSERT INTO modositasok (felhasznalo, muvelet, $tipus) VALUES ($felhasznalo, $muvelet, $objid)";
	mysqli_query($con, $string);

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
	$RootPath = getenv('APP_ROOT_PATH');
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
	$RootPath = getenv('APP_ROOT_PATH');
	$targeturl = $RootPath . "/" . $_GET['kuldooldal'] . ((isset($_GET['kuldooldalid'])) ? "/" . $_GET['kuldooldalid'] : "");

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
	$RootPath = getenv('APP_ROOT_PATH');
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
					?><a class="<?=($port['hasznalatban'] || $port['szam']) ? "foglalt" : "ures" ?>" href='<?=$RootPath?>/port/<?=$port['portid']?>'>
						<div class="vegpont">
							<div><?=$port['port']?></div>
							<div>
								<?=($port['vlan']) ? "<div>" . $port['vlan'] . "</div>" : "" ?>
								<?=($port['szam']) ? "<div>" . $port['szam'] . "</div>" : "" ?><?php
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
	
	$RootPath = getenv('APP_ROOT_PATH');

	$where = "";
	if($tipus == 'epulet')
	{
		$where = "transzportportok.epulet = $id";
	}
	elseif($tipus == 'rack')
	{
		$where = "rackportok.rack = $id";
	}
	elseif($tipus == 'helyiseg')
	{
		$where = "rackszekrenyek.helyiseg = $id";
	}

	$portok = mySQLConnect("SELECT DISTINCT portok.id AS portid, portok.port AS port,
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
                hurok.port AS huroktuloldal
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
            ORDER BY portok.port;");
	
	if(mysqli_num_rows($portok) > 0)
	{
		?><div class="transzportlist"><?php
			$elozoport = null;
			$huroktulportok = mysqliToArray($portok);
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
				
				?><a class="<?=($port['hasznalatban'] || $elozoport || $port['huroktuloldal']) ? "foglalt" : "ures" ?>" href='<?=$RootPath?>/port/<?=$port['portid']?>'>
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

							if(($port['beepitesnev'] || ($port['huroktuloldal'] && $port['szomszednev'])) && ($port['fizikaireteg'] == 2 || $port['fizikaireteg'] == 3))
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

function hibajegyErtesites($ertesites, $szoveg, $hibajegyid, $felhasznalo, $alakulat, $szak = null)
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
    			WHERE menupont = 11 AND iras > 1 AND csoportjogok.alakulat = $alakulat $szak
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

function csoportWhere($csoporttagsagok, $csopwhereset)
{
	$alakulatok = array();
    $telephelyek = array();

	foreach($csoporttagsagok as $csoportjog)
    {
        if($csoportjog['alakulat'] && !in_array($csoportjog['alakulat'], $alakulatok))
		{
			$alakulatok[] = $csoportjog['alakulat'];
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

	if($csopwhereset['alakulatelo'])
	{
		$csopwhereset['alakulatelo'] = $csopwhereset['alakulatelo'] . ".";
	}

	if($csopwhereset['telephelyelo'])
	{
		$csopwhereset['telephelyelo'] = $csopwhereset['telephelyelo'] . ".";
	}

	$alakulatdb = count($alakulatok);
	$telepehelydb = count($telephelyek);

	$wherealak = "";
	$wheretelep = "";
	
	for($i = 0; $i < $alakulatdb; $i++)
	{
		$wherealak .= $csopwhereset['alakulatelo'] . $csopwhereset['alakulatmegnevezes'] . " = " . $alakulatok[$i];

		if($i != $alakulatdb - 1)
		{
			$wherealak .= " OR ";
		}
	}

	if($csopwhereset['alakulatnull'])
	{
		if($alakulatdb > 0)
		{
			$wherealak .= " OR ";
		}
		$wherealak .= $csopwhereset['alakulatelo'] . $csopwhereset['alakulatmegnevezes'] . " IS NULL";
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
		if($telepehelydb == 0 && $alakulatdb == 0)
		{
			$where .= $csopwhereset['telephelyelo'] . "telephely = 999 AND " . $csopwhereset['alakulatelo'] . $csopwhereset['alakulatmegnevezes'] . " = 999";
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
	elseif($csopwhereset['tipus'] == "alakulat")
	{
		if($alakulatdb == 0)
		{
			$where .= $csopwhereset['alakulatelo'] . $csopwhereset['alakulatmegnevezes'] . " = 999";
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

function mysqliToArray($mysqlires)
{
	$returnarr = array();
    foreach($mysqlires as $sor)
    {
        $element = array();
		foreach($sor as $key => $value)
		{
			$element[$key] = $value;
		}

		//$port = array('portid' => $x['portid'], 'port' => $x['port'], 'hasznalatban' => $x['hasznalatban'], 'tipus' => $x['tipus'], 'szam' => $x['szam']);
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
	foreach($_POST as $key => $value)
    {
        if ($value == "NULL" || $value == "")
        {
            $_POST[$key] = NULL;
        }
		elseif(!$ishtml)
        {
            $_POST[$key] = quickXSSfilter($value);
        }
    }
}