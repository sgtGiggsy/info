<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
	$portid = $_GET['id'];
	if(@$_GET['tipus'] == "lage")
	{

	}
	else
	{
		$portres = mySQLConnect("SELECT portok.id AS portid,
				portok.port AS port,
				epuletek.nev AS epuletnev,
				helyisegszam,
				helyisegnev,
				helyisegek.id AS helyisegid,
				rackszekrenyek.nev AS rack,
				rackszekrenyek.helyiseg AS rackhelyiseg,
				(SELECT helyisegnev FROM helyisegek WHERE id = rackhelyiseg) as rackhelynev,
				(SELECT helyisegszam FROM helyisegek WHERE id = rackhelyiseg) as rackhelyszam,
				(SELECT port FROM portok WHERE csatlakozas = portid ORDER BY id ASC LIMIT 1) AS szomszedport,
				(SELECT id FROM portok WHERE csatlakozas = portid LIMIT 1) AS szomszedportid,
				(SELECT beepitesek.nev FROM switchportok
						INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
						INNER JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
					WHERE switchportok.port = szomszedportid) AS switch,
				(SELECT ipcimek.ipcim FROM switchportok
						INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
						INNER JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
						INNER JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
					WHERE switchportok.port = szomszedportid) AS switchip,
				(SELECT port FROM portok WHERE csatlakozas = portid ORDER BY id DESC LIMIT 1) AS szomszedport2,
				(SELECT id FROM portok WHERE csatlakozas = portid ORDER BY id DESC LIMIT 1) AS szomszedportid2,
				(SELECT beepitesek.nev FROM switchportok
						INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
						INNER JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
					WHERE switchportok.port = szomszedportid2) AS switch2,
				(SELECT ipcimek.ipcim FROM switchportok
						INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
						INNER JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
						INNER JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
					WHERE switchportok.port = szomszedportid2) AS switchip2
			FROM portok
				LEFT JOIN switchportok ON portok.id = switchportok.port
				LEFT JOIN vegpontiportok ON portok.id = vegpontiportok.port
				LEFT JOIN rackportok ON portok.id = rackportok.port
				LEFT JOIN tkozpontportok ON portok.id = tkozpontportok.port
				LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
				LEFT JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
				LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
				LEFT JOIN helyisegek ON vegpontiportok.helyiseg = helyisegek.id
				LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
			WHERE portok.id = $portid;");
		$port = mysqli_fetch_assoc($portres);
		$port2 = mysqli_fetch_assoc($portres);

		//print_r($port);
		//echo "<br>";
		//print_r($port2);

		if($port['helyisegid'])
		{
			$helyisegid = $port['helyisegid'];
		}
		else
		{
			$helyisegid = $port['rackhelyiseg'];
		}
		
		if($helyisegid)
		{
			$helyiseg = mySQLConnect("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
				FROM helyisegek
					INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
					INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
					INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
				WHERE helyisegek.id = $helyisegid;");
			$helyiseg = mysqli_fetch_assoc($helyiseg);

			?><div class="breadcumblist">
				<ol vocab="https://schema.org/" typeof="BreadcrumbList">
					<li property="itemListElement" typeof="ListItem">
						<a property="item" typeof="WebPage"
							href="<?=$RootPath?>/">
						<span property="name">Kecskemét Informatika</span></a>
						<meta property="position" content="1">
					</li>
					<li><b>></b></li>
					<li property="itemListElement" typeof="ListItem">
						<a property="item" typeof="WebPage"
							href="<?=$RootPath?>/epuletek/<?=$helyiseg['thelyid']?>">
						<span property="name"><?=$helyiseg['telephely']?></span></a>
						<meta property="position" content="2">
					</li>
					<li><b>></b></li>
					<li property="itemListElement" typeof="ListItem">
						<a property="item" typeof="WebPage"
							href="<?=$RootPath?>/epulet/<?=$helyiseg['epid']?>">
						<span property="name"><?=$helyiseg['epuletszam']?>. <?=$helyiseg['tipus']?></span></a>
						<meta property="position" content="3">
					</li>
					<li><b>></b></li>
					<li property="itemListElement" typeof="ListItem">
						<a property="item" typeof="WebPage"
							href="<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>">
						<span property="name"><?=$helyiseg['helyisegszam']?>. helyiség (<?=$helyiseg['helyisegnev']?>)</span></a>
						<meta property="position" content="4">
					</li>
					<li><b>></b></li>
					<li property="itemListElement" typeof="ListItem">
						<span property="name"><?=$port['port']?></span>
						<meta property="position" content="4">
					</li>
				</ol>
			</div><?php
		}
		else
		{
			?><div class="breadcumblist"><a>A port helyiséghez, vagy rackszekrényhez kötése még nem történt meg</a></div><?php
		}


		?><div class="oldalcim">A(z) <?=$port['epuletnev']?> <?=$port['port']?> portjának adatai</div>
		<div class="infobox">
			<div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi csatlakoztatás adatai" : "Csatlakozás adatai" ?></div>
			<div class="infoboxbody">
				<div class="infoboxbodytwocol"><?php
					?><div>Állapot</div>
					<div><?=($port['szomszedport']) ? "Kirendezve" : "Használaton kívül" ?></div>
					<div>Központi oldal</div>
					<div><?=$port['rack']?> rack, <?=$port['rackhelyszam']?>. helyiség (<?=$port['rackhelynev']?>)</div><?php
					if($port['szomszedport'])
					{
						?><div>Aktív eszköz</div>
						<div><?=$port['switch']?> (<?=$port['switchip']?>)</div>
						<div>Eszköz portja</div>
						<div><?=$port['szomszedport']?></div><?php
					}
					if(isset($port2['rack']) && $port2['rack'])
					{
						?><div>Túlsó oldal</div>
						<div><?=$port2['rack']?> rack, <?=$port2['rackhelyszam']?>. helyiség (<?=$port2['rackhelynev']?>)</div>
						<div>Túlsó oldali aktív eszköz</div>
						<div><?=$port2['switch2']?> (<?=$port2['switchip2']?>)</div>
						<div>Túlsó oldali aktív eszköz portja</div>
						<div><?=$port2['szomszedport2']?></div><?php
					}
					else
					{
						?><div>Végponti oldal</div>
						<div><?=$port['helyisegszam']?>. helyiség <?=($port['helyisegnev']) ? "(" . $port['helyisegnev'] . ")" : "" ?></div><?php
					}
				?></div>
			</div>
		</div><?php
	}
}