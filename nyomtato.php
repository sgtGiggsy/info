<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
	$portid = $_GET['id'];
	$port = mySQLConnect("SELECT portok.id AS portid, portok.port AS port, epuletek.nev AS epuletnev, beepitesek.nev AS eszkoz, helyisegszam, helyisegnev, rackszekrenyek.nev AS rack, rackszekrenyek.helyiseg AS rackhelyiseg,
			(SELECT helyisegnev FROM helyisegek WHERE id = rackhelyiseg) as rackhelynev,
			(SELECT helyisegszam FROM helyisegek WHERE id = rackhelyiseg) as rackhelyszam,
			(SELECT csatlakozas FROM portok WHERE csatlakozas = portid LIMIT 1) AS szomszedport
		FROM portok
			LEFT JOIN switchportok ON portok.id = switchportok.port
			LEFT JOIN vegpontiportok ON portok.id = vegpontiportok.port
			LEFT JOIN rackportok ON portok.id = rackportok.port
			LEFT JOIN rackszekrenyek ON rackportok.rack = rackszekrenyek.id
			LEFT JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
			LEFT JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
			LEFT JOIN helyisegek ON vegpontiportok.helyiseg = helyisegek.id
			LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
		WHERE portok.id = $portid;");
	$port = mysqli_fetch_assoc($port);

	?><div class="oldalcim">A(z) <?=$port['epuletnev']?> <?=$port['port']?> portjának adatai</div>
	<div class="infobox"><?php
		?><div>Állapot</div>
		<div><?=($port['szomszedport']) ? "Kirendezve" : "Használaton kívül" ?></div>
		<div>Központi oldal</div>
		<div><?=$port['rack']?> rack, <?=$port['rackhelyszam']?>. helyiség (<?=$port['rackhelynev']?>)</div>
		<div><?=($port['szomszedport']) ? "Aktív eszköz" : "" ?></div>
		<div><?=($port['szomszedport']) ? $port['eszkoz'] : "" ?></div>
		<div>Végponti oldal</div>
		<div><?=$port['helyisegszam']?>. helyiség <?=($port['helyisegnev']) ? "(" . $port['helyisegnev'] . ")" : "" ?></div>
	</div><?php
}