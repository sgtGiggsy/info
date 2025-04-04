<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
	$portid = $_GET['id'];
	$helyisegid = $thelyid = $thelynev = $epid = $epnev = null;
	if(@$_GET['tipus'] == "lage")
	{

	}
	else
	{
		$portres = new MySQLHandler("SELECT portok.id AS port_id,
					portok.port AS port,
					portok.csatlakozo AS csatlakozo,
					szomszedport_id, portkapcsolat_id, szomszedporttipus,
					epuletek.id AS epid,
					epuletek.nev AS epuletnev,
					epuletek.szam AS epuletszam,
					epulettipusok.tipus AS epulettipus,
					telephelyek.telephely AS telephely,
					telephelyek.id AS thelyid,
					telefonszamok.szam AS telefonszam,
					telefonszamok.cimke AS telefoncimke,

					portepulet.id AS portepid,
					portepulet.nev AS portepuletnev,
					portepulet.szam AS portepuletszam,
					portepulettipus.tipus AS portepulettipus,
					porttelephely.telephely AS porttelephely,
					porttelephely.id AS portthelyid,

					transzportportep.szam AS transzpepszam,
					transzportporteptipus.tipus AS transzpeptipus,

					helyisegek.helyisegszam AS helyisegszam,
					helyisegek.helyisegnev AS helyisegnev,
					helyisegek.id AS helyisegid,
					helyisegek.emelet AS emelet,
					rackszekrenyek.nev AS rack,
					rackszekrenyek.helyiseg AS rackhelyiseg,
					rackhely.helyisegszam AS rackhelyszam,
					rackhely.helyisegnev AS rackhelynev,
					rackepulet.nev AS rackepuletnev,
					eszkozok.sorozatszam AS sorozatszam,
					beepitesek.id AS beepid,
					beepitesek.eszkoz AS eszkozid,
					beepitesek.aktivbeepites AS aktivbeepites,
					beepitesek.nev AS switch,
					ipcimek.ipcim AS switchip,
					IF(vegpontiportok.id, 1,
						IF(transzportportok.id, 2,
							IF(tkozpontportok.id, 3,
								IF(switchportok.id, 4,
									IF(mediakonverterportok.id, 5,
										IF(sohoportok.id, 6, 0)))))) AS porttipus,
					szomszed.port AS szomszport,
					szomszep.nev AS szomszepuletnev,
					szomszhelyiseg.helyisegszam AS szomszhelyisegszam,
					szomszhelyiseg.helyisegnev AS szomszhelyisegnev,
					szomszhelyiseg.id AS szomszhelyisegid,
					szomszrack.nev AS szomszrack,
					szomszrack.helyiseg AS szomszrackhelyiseg,
					szomszrackhely.helyisegszam AS szomszrackhelyszam,
					szomszrackhely.helyisegnev AS szomszrackhelynev,
					szomszrackepulet.nev AS szomszrackepuletnev,
					szomszbeep.id AS szomszbeepid,
					szomszbeep.aktivbeepites AS szomszaktivbeepites,
					szomszbeep.nev AS szomszswitch,
					szomszip.ipcim AS szomszswitchip,
					szomsztelefonszam.szam AS szomsztelefonszam,
					szomsztelefonszam.cimke AS szomsztelefoncimke
				FROM portok
					LEFT JOIN port_kapcsolat_view ON portok.id = port_kapcsolat_view.port_id
					LEFT JOIN transzportportok ON portok.id = transzportportok.port
					LEFT JOIN switchportok ON portok.id = switchportok.port
					LEFT JOIN vegpontiportok ON portok.id = vegpontiportok.port
					LEFT JOIN rackportok ON portok.id = rackportok.port
					LEFT JOIN tkozpontportok ON portok.id = tkozpontportok.port
					LEFT JOIN mediakonverterportok ON portok.id = mediakonverterportok.port
					LEFT JOIN sohoportok ON portok.id = sohoportok.port
					LEFT JOIN telefonszamok ON portok.id = telefonszamok.port
					LEFT JOIN helyisegek ON vegpontiportok.helyiseg = helyisegek.id
					LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
					LEFT JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
					LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
					LEFT JOIN beepitesek besoho ON sohoportok.eszkoz = besoho.eszkoz
					LEFT JOIN beepitesek bemkonverter ON mediakonverterportok.eszkoz = bemkonverter.eszkoz
					LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
					LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
					LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
					LEFT JOIN epuletek portepulet ON vegpontiportok.epulet = portepulet.id
					LEFT JOIN epulettipusok portepulettipus ON portepulet.tipus = portepulettipus.id
					LEFT JOIN epuletek transzportportep ON transzportportok.epulet = transzportportep.id
					LEFT JOIN epulettipusok transzportporteptipus ON transzportportep.tipus = transzportporteptipus.id
					LEFT JOIN telephelyek porttelephely ON portepulet.telephely = porttelephely.id
					LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
					LEFT JOIN helyisegek rackhely ON rackszekrenyek.helyiseg = rackhely.id
					LEFT JOIN epuletek rackepulet ON rackhely.epulet = rackepulet.id
					LEFT JOIN portok szomszed ON szomszed.id = port_kapcsolat_view.szomszedport_id
					LEFT JOIN transzportportok szomsztransz ON szomszed.id = szomsztransz.port
					LEFT JOIN switchportok szomszswp ON szomszed.id = szomszswp.port
					LEFT JOIN vegpontiportok szomszvegp ON szomszed.id = szomszvegp.port
					LEFT JOIN rackportok szomszrackp ON szomszed.id = szomszrackp.port
					LEFT JOIN tkozpontportok szomsztkport ON szomszed.id = szomsztkport.port
					LEFT JOIN mediakonverterportok szomszmediakonvp ON szomszed.id = szomszmediakonvp.port
					LEFT JOIN sohoportok szomszsohop ON szomszed.id = szomszsohop.port
					LEFT JOIN telefonszamok szomsztelefonszam ON szomszed.id = szomsztelefonszam.port
					LEFT JOIN helyisegek szomszhelyiseg ON szomszvegp.helyiseg = szomszhelyiseg.id
					LEFT JOIN eszkozok szomszeszk ON szomszswp.eszkoz = szomszeszk.id
					LEFT JOIN beepitesek szomszbeep ON szomszeszk.id = szomszbeep.eszkoz
					LEFT JOIN ipcimek szomszip ON szomszbeep.ipcim = szomszip.id
					LEFT JOIN epuletek szomszep ON szomszhelyiseg.epulet = szomszep.id
					LEFT JOIN rackszekrenyek szomszrack ON szomszrackp.rack = szomszrack.id
					LEFT JOIN helyisegek szomszrackhely ON szomszrack.helyiseg = szomszrackhely.id
					LEFT JOIN epuletek szomszrackepulet ON szomszrackhely.epulet = szomszrackepulet.id
			WHERE portok.id = ? AND (beepitesek.aktivbeepites = 1 OR beepitesek.aktivbeepites IS NULL)
			ORDER BY szomszedporttipus DESC;", $portid);
		$port = $portres->Fetch();
		//$port2 = $portres->Fetch();

		//print_r($port);
		//echo "<br>";
		//print_r($port2);

		//TODO Lehet, hogy egy több beépítéssel rendelkező switch portja egy korábbi beépítés adatait találja meg.
		//TODO A jelenlegi gyorsfix (beepitesek.aktivbeepites = 1 OR beepitesek.aktivbeepites IS NULL) nem tudom megoldja-e ezt
		//! Nem, nem oldja meg
		if($portres->sorokszama == 0)
		{
			?><div class="breadcumblist"><a>Nem létező port</a></div><?php
		}
		else
		{	
			if($port['helyisegid'] || $port['rackhelyiseg'])
			{
				$helyisegid = $port['helyisegid'];
				if(!$port['helyisegid'])
				{
					$helyisegid = $port['rackhelyiseg'];
				}
				$thelyid = $port['thelyid'];
				$thelynev = $port['telephely'];
				$epid = $port['epid'];
				$epnev = IntRagValaszt($port['epuletszam']) . " " . $port['epulettipus'];
				$helyisegnev = $port['helyisegszam'] . ". helyiség" .  $port['helyisegnev'];
			}
			elseif($port['portthelyid'])
			{
				$thelyid = $port['portthelyid'];
				$thelynev = $port['porttelephely'];
				$epid = $port['portepid'];
				$epnev = $port['portepuletszam'] . ". " . $port['portepulettipus'];
			}
			if($port['transzpepszam'])
			{
				$epnev = IntRagValaszt($port['transzpepszam']) . " " . $port['transzpeptipus'];
			}

			switch($port['porttipus'])
			{
				case 1: $porttipus = "Végponti"; break;
				case 2: $porttipus = "Transzport"; break;
				case 3: $porttipus = "Lage port"; break;
				case 4: $porttipus = "Switchport"; break;
				case 5: $porttipus = "Modemport"; break;
				case 6: $porttipus = "SOHO port"; break;
				default: $porttipus = null;
			}
		
			?><div class="breadcumblist">
				<ol vocab="https://schema.org/" typeof="BreadcrumbList">
					<li property="itemListElement" typeof="ListItem">
						<a property="item" typeof="WebPage"
							href="<?=$RootPath?>/">
						<span property="name">Kecskemét Informatika</span></a>
						<meta property="position" content="1">
					</li>
					<li><b>></b></li><?php
					if($thelyid)
					{
						?>
						<li property="itemListElement" typeof="ListItem">
							<a property="item" typeof="WebPage"
								href="<?=$RootPath?>/epuletek/<?=$thelyid?>">
							<span property="name"><?=$thelynev?></span></a>
							<meta property="position" content="2">
						</li>
						<li><b>></b></li><?php
					}
					if($epid)
					{
						?><li property="itemListElement" typeof="ListItem">
							<a property="item" typeof="WebPage"
								href="<?=$RootPath?>/epulet/<?=$epid?>">
							<span property="name"><?=$epnev?></span></a>
							<meta property="position" content="3">
						</li>
						<li><b>></b></li><?php
					}
					if($helyisegid)
					{
						?><li property="itemListElement" typeof="ListItem">
							<a property="item" typeof="WebPage"
								href="<?=$RootPath?>/helyiseg/<?=$helyisegid?>">
							<span property="name"><?=$helyisegnev?></span></a>
							<meta property="position" content="4">
						</li>
						<li><b>></b></li><?php
					}
					if($port['switch'])
					{
						?><li property="itemListElement" typeof="ListItem">
							<a property="item" typeof="WebPage"
								href="<?=$RootPath?>/aktiveszkoz/<?=$port['eszkozid']?>">
							<span property="name"><?=$port['switch']?></span></a>
							<meta property="position" content="4">
						</li>
						<li><b>></b></li><?php
					}
					?><li property="itemListElement" typeof="ListItem">
						<span property="name"><?=$port['port']?></span>
						<meta property="position" content="4">
					</li>
				</ol>
			</div>
			<div class="oldalcim">A(z) <?=$epnev?> <?=$port['port']?> portjának adatai</div>
			<div class="infobox">
				<div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi csatlakoztatás adatai" : "Csatlakozás adatai" ?></div>
				<div class="infoboxbody">
					<div class="infoboxbodytwocol"><?php
						?><div>Állapot</div>
						<div><?=($port['szomszport'] || $port['telefonszam']) ? "Kirendezve" : "Használaton kívül" ?></div>
						<div>Port típus</div>
						<div><?=$porttipus?></div><?php
						if($port['porttipus'] < 4 && $port['rack'])
						{
							?><div>Központi oldal</div>
							<div><?=$port['rack']?> rack, <?=$port['rackhelyszam']?>. helyiség (<?=$port['rackhelynev']?>)</div><?php
						}
						if($port['switch'])
						{
							?><div>Aktív eszköz</div>
							<div><?=$port['switch']?> (<?=$port['switchip']?>)</div>
							<div>Eszköz sorozatszam</div>
							<div><?=$port['sorozatszam']?></div>
							<div>Csatlakoztatott port</div>
							<div><?=($port['szomszport']) ? $port['szomszport'] : "Üres" ?></div><?php
						}
						if($port['telefonszam'])
						{
							$i = 1;
							foreach($portres->Result() as $telszamok)
							{
								?><div>Telefonszám <?=$i?>.</div>
								<div><?=$telszamok['telefonszam']?></div>
								<div>Telefon cimke <?=$i?>.</div>
								<div><?=$telszamok['telefoncimke']?></div><?php
								$i++;
							}
						}
						if($port['szomszswitch'])
						{
							?><div>Csatlakoztatott eszköz</div>
							<div><?=$port['szomszswitch']?> (<?=$port['szomszswitchip']?>)</div>
							<div>Eszköz portja</div>
							<div><?=$port['szomszport']?></div><?php
						}
						elseif($port['szomszedporttipus'] != 2 && ($port['helyisegszam'] || $port['szomszedport_id']))
						{
							?><div>Végponti oldal</div><?php
							if($port['helyisegszam'])
							{
								?><div><?=$port['helyisegszam']?>. helyiség <?=($port['helyisegnev']) ? "(" . $port['helyisegnev'] . ")" : "" ?></div><?php
							}
							elseif($port['szomszedport_id'])
							{
								?><div><?=IntRagValaszt($port['szomszepuletnev'])?> épület, <?=($port['szomszhelyisegszam']) ? IntRagValaszt($port['szomszhelyisegszam']) . " helyiség" : "" ?></div><?php
							}
						}
						elseif($port['szomszedporttipus'] == 2 && ($port['szomszepuletnev'] || $port['szomszrackepuletnev']))
						{
							?><div>Túlsó épület</div><?php
							if($port['szomszhelyisegszam'])
							{
								?><div><?=$port['szomszhelyisegszam']?>. helyiség <?=($port['szomszhelyisegnev']) ? "(" . $port['szomszhelyisegnev'] . ")" : "" ?></div><?php
							}
							elseif($port['szomszrackepuletnev'])
							{
								?><div><?=IntRagValaszt($port['szomszrackepuletnev'])?> épület, <?=($port['szomszrackhelynev']) ? $port['szomszrackhelynev'] . " helyiség" : "" ?></div><?php
							}
							?><div>Szomszédos port</div>
							<div><?=$port['szomszport']?></div><?php
						}
						$elsoiter = true;
						foreach($portres->Result() as $tovabbicsat)
						{
							if(!$elsoiter && $tovabbicsat['szomszport'])
							{
								?><div>Túlsó oldal</div>
								<div><?=$tovabbicsat['rack']?> rack, <?=$tovabbicsat['rackhelyszam']?>. helyiség (<?=$tovabbicsat['rackhelynev']?>)</div><?php
								if($tovabbicsat['szomszswitch'])
								{
									?><div>Túlsó oldali aktív eszköz</div>
									<div><?=$tovabbicsat['szomszswitch']?> (<?=$tovabbicsat['szomszswitchip']?>)</div><?php
								}
								?><div>Túlsó oldali port</div>
								<div><?=$tovabbicsat['szomszport']?></div><?php
							}
							$elsoiter = false;
						}
					?></div>
				</div>
			</div><?php
		}
	}
}
/*
CREATE VIEW port_kapcsolat_view AS
SELECT port_kapcsolatok.portkapcsolat_id AS portkapcsolat_id,
		portok.id AS port_id,
		IF(portok.id = port_kapcsolatok.port_1, port_kapcsolatok.port_2,port_kapcsolatok.port_1) AS szomszedport_id,	
        IF(vegpontiportok.id, 1,
			IF(transzportportok.id, 2,
				IF(tkozpontportok.id, 3,
					IF(switchportok.id, 4,
						IF(mediakonverterportok.id, 5,
							IF(sohoportok.id, 6, 0)))))) AS szomszedporttipus
		FROM portok
			INNER JOIN port_kapcsolatok ON portok.id = port_kapcsolatok.port_1
            LEFT JOIN transzportportok ON port_kapcsolatok.port_2 = transzportportok.port
			LEFT JOIN switchportok ON port_kapcsolatok.port_2 = switchportok.port
			LEFT JOIN vegpontiportok ON port_kapcsolatok.port_2 = vegpontiportok.port
			LEFT JOIN rackportok ON port_kapcsolatok.port_2 = rackportok.port
			LEFT JOIN tkozpontportok ON port_kapcsolatok.port_2 = tkozpontportok.port
			LEFT JOIN mediakonverterportok ON port_kapcsolatok.port_2 = mediakonverterportok.port
			LEFT JOIN sohoportok ON port_kapcsolatok.port_2 = sohoportok.port
            
UNION ALL

	SELECT port_kapcsolatok.portkapcsolat_id AS portkapcsolat_id,
		portok.id AS port_id,
		IF(portok.id = port_kapcsolatok.port_2, port_kapcsolatok.port_1,port_kapcsolatok.port_2) AS szomszedport_id,	
        IF(vegpontiportok.id, 1,
			IF(transzportportok.id, 2,
				IF(tkozpontportok.id, 3,
					IF(switchportok.id, 4,
						IF(mediakonverterportok.id, 5,
							IF(sohoportok.id, 6, 0)))))) AS szomszedporttipus
		FROM portok
			INNER JOIN port_kapcsolatok ON portok.id = port_kapcsolatok.port_2
            LEFT JOIN transzportportok ON port_kapcsolatok.port_1 = transzportportok.port
			LEFT JOIN switchportok ON port_kapcsolatok.port_1 = switchportok.port
			LEFT JOIN vegpontiportok ON port_kapcsolatok.port_1 = vegpontiportok.port
			LEFT JOIN rackportok ON port_kapcsolatok.port_1 = rackportok.port
			LEFT JOIN tkozpontportok ON port_kapcsolatok.port_1 = tkozpontportok.port
			LEFT JOIN mediakonverterportok ON port_kapcsolatok.port_1 = mediakonverterportok.port
			LEFT JOIN sohoportok ON port_kapcsolatok.port_1 = sohoportok.port;
		*/