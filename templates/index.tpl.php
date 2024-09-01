<!DOCTYPE HTML>
<html xml:lang="hu" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=0.85">
	<meta name="description" content="<?=$ablakcim?>">
	<link rel="shortcut icon" href="<?=$RootPath?>/favicon.ico"><?php
	if(isset($szemelyes['szinsema']) && $szemelyes['szinsema'])
	{
		?><link rel="stylesheet" href="<?=$RootPath?>/<?=$szemelyes['szinsema']?>.css" type="text/css" id='nightmode'><?php
	}
	?><link rel="stylesheet" href="<?=$RootPath?>/style.css" type="text/css">
	<title><?=$ablakcim . " - " . $currentpage['gyujtocimszoveg']?></title>
</head>

<body>
	<div class="wrapper">
		<!-- Tartalom -->
		<div class="content">
			<div class="left">
				<?php if(isset($_SESSION[getenv('SESSION_NAME').'explorer']) && $_SESSION[getenv('SESSION_NAME').'explorer'])
				{
					?><span style="color: red; font-size: 2em;">Internet Explorer böngészőben nyitotta meg az oldalt, ez komoly működési hibákhoz vezethet!<br><br>
					A felület rendeltetésszerű használatához kérem egy modernebb böngészőben (Microsoft Edge, Google Chrome, Mozilla Firefox) nyissa meg az oldalt!</span><?php
				}
			?></div>
			<div class="right"><a href="<?=$RootPath?>/bugreport?oldal=<?=$current?>">Hiba jelzése</a></div><?php
			include('./includes/contentpage.inc.php')
		?></div>

		<!-- Fejléc -->
		<div class="topmenubase" id="topmenuelement"><?php include('./templates/header.tpl.php'); ?></div><?php

		if(@$contextheader)
		{
			?><!-- Kontextus fejléc -->
			<div class="contextheader" id="contextheader"><img src="<?=$contextheader?>"></div><?php
		}

		?><!-- Menürész -->
		<?php $menuterulet = 1; include('./includes/menu.inc.php'); ?>		

		<!-- Lábléc -->
		<div class="bottom-line"><p><?=($mindir) ? "Adatbázis hívások száma: " . $dbcallcount . " " : "" ?><a href="mailto:kiraly.bela@mil.hu">© Király Béla ftőrm <script>document.write(new Date().getFullYear())</script></a></p><span id="constatus"></span></div>

		<!-- Betöltés során látható területen kívül eső, illetve nem létező tartalmak -->
		<div id="snackbar"></div>
		<div id="newsflash"><div id="newsflashdesc"></div><div id="newsflashtextarea"><div id="newsflashtext"></div></div></div>
		<div id="formkuldatfedes" class="formkuldatfedes"><div class="formkuldmessage">Művelet folyamatban, kérlek várj...<br><div class="loader"></div></div></div>
		<img src="<?=$RootPath?>/images/back.png" alt= "Vissza" id="backtotop" onclick="scrollToTop()"/>
	</div>
</body><?php
	include('./includes/scriptblock.php')
?></html>