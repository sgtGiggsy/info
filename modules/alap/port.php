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
		$portres = new MySQLHandler("SELECT port_id, szomszedport_id, portkapcsolat_id,
					portpar.port AS port,
					portpar.csatlakozo AS csatlakozo,
					epuletek.nev AS epuletnev,
					helyisegek.helyisegszam AS helyisegszam,
					helyisegek.helyisegnev AS helyisegnev,
					helyisegek.id AS helyisegid,
					rackszekrenyek.nev AS rack,
					rackszekrenyek.helyiseg AS rackhelyiseg,
					rackhely.helyisegszam AS rackhelyszam,
					rackhely.helyisegnev AS rackhelynev,
					rackepulet.nev AS rackepuletnev,
					beepitesek.id AS beepid,
					beepitesek.aktivbeepites AS aktivbeepites,
					beepitesek.nev AS switch,
					ipcimek.ipcim AS switchip,
					IF(vegpontiportok.id, 1,
					IF(transzportportok.id, 2,
						IF(switchportok.id, 3,
							IF(tkozpontportok.id, 4, 0)))) AS porttipus,
							szomszed.port AS szomszport,
					szomszep.nev AS szomszepuletnev,
					szomszhelyiseg.helyisegszam AS szomszhelyisegszam,
					szomszhelyiseg.helyisegnev AS szomszhelyisegnev,
					szomszhelyiseg.id AS szomszhelyisegid,
					rackszekrenyek.nev AS szomszrack,
					szomszrack.helyiseg AS szomszrackhelyiseg,
					szomszrackhely.helyisegszam AS szomszrackhelyszam,
					szomszrackhely.helyisegnev AS szomszrackhelynev,
					szomszrackepulet.nev AS szomszrackepuletnev,
					szomszbeep.id AS szomszbeepid,
					szomszbeep.aktivbeepites AS szomszaktivbeepites,
					szomszbeep.nev AS szomszswitch,
					szomszip.ipcim AS szomszswitchip
				FROM
					(SELECT portok.id AS port_id, port, csatlakozo,
							IF(portok.id = port_kapcsolatok.port_1, port_kapcsolatok.port_2, port_kapcsolatok.port_1) AS szomszedport_id,
							portkapcsolat_id
						FROM portok
							LEFT JOIN port_kapcsolatok ON portok.id = port_kapcsolatok.port_1
						WHERE portkapcsolat_id IS NOT NULL
					UNION
						SELECT portok.id AS port_id, port, csatlakozo,
							IF(portok.id = port_kapcsolatok.port_2, port_kapcsolatok.port_1, port_kapcsolatok.port_2) AS szomszedport_id,
							portkapcsolat_id
						FROM portok
							LEFT JOIN port_kapcsolatok ON portok.id = port_kapcsolatok.port_2
						WHERE portkapcsolat_id IS NOT NULL
					) AS portpar
					LEFT JOIN transzportportok ON portpar.port_id = transzportportok.port
					LEFT JOIN switchportok ON portpar.port_id = switchportok.port
					LEFT JOIN vegpontiportok ON portpar.port_id = vegpontiportok.port
					LEFT JOIN rackportok ON portpar.port_id = rackportok.port
					LEFT JOIN tkozpontportok ON portpar.port_id = tkozpontportok.port
					LEFT JOIN helyisegek ON vegpontiportok.helyiseg = helyisegek.id
					LEFT JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
					LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
					LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
					LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
					LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
					LEFT JOIN helyisegek rackhely ON rackszekrenyek.helyiseg = rackhely.id
					LEFT JOIN epuletek rackepulet ON rackhely.epulet = rackepulet.id
					LEFT JOIN portok szomszed ON szomszed.id = portpar.szomszedport_id
					LEFT JOIN transzportportok szomsztransz ON szomszed.id = szomsztransz.port
					LEFT JOIN switchportok szomszswp ON szomszed.id = szomszswp.port
					LEFT JOIN vegpontiportok szomszvegp ON szomszed.id = szomszvegp.port
					LEFT JOIN rackportok szomszrackp ON szomszed.id = szomszrackp.port
					LEFT JOIN tkozpontportok szomsztkport ON szomszed.id = szomsztkport.port
					LEFT JOIN helyisegek szomszhelyiseg ON szomszvegp.helyiseg = szomszhelyiseg.id
					LEFT JOIN eszkozok szomszeszk ON szomszswp.eszkoz = szomszeszk.id
					LEFT JOIN beepitesek szomszbeep ON szomszeszk.id = szomszbeep.eszkoz
					LEFT JOIN ipcimek szomszip ON szomszbeep.ipcim = szomszip.id
					LEFT JOIN epuletek szomszep ON szomszhelyiseg.epulet = szomszep.id
					LEFT JOIN rackszekrenyek szomszrack ON szomszrackp.rack = szomszrack.id
					LEFT JOIN helyisegek szomszrackhely ON szomszrack.helyiseg = szomszrackhely.id
					LEFT JOIN epuletek szomszrackepulet ON szomszrackhely.epulet = szomszrackepulet.id
			WHERE port_id = ?;", $portid);
		$port = $portres->Fetch();
		$port2 = $portres->Fetch();

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
			$helyiseg = new MySQLHandler("SELECT helyisegek.id AS id, helyisegszam, helyisegnev, emelet, epuletek.id AS epid, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev, epulettipusok.tipus AS tipus, telephelyek.telephely AS telephely, telephelyek.id AS thelyid
				FROM helyisegek
					INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
					INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
					INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
				WHERE helyisegek.id = ?;", $helyisegid);
			$helyiseg = $helyiseg->Fetch();

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