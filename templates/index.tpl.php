<?php
include('./templates/svg.tpl.php');
include('./includes/menu.inc.php');
?><!DOCTYPE HTML>
<html lang="hu" xml:lang="hu" xmlns="http://www.w3.org/1999/xhtml">
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

<body><?php
	if(isset($_SESSION['explorer']) && $_SESSION['explorer'])
	{
		?><span style="color: red; font-size: 2em;" id="iewarning">Internet Explorer böngészőben nyitotta meg az oldalt, de ez a felület <strong>NEM MŰKÖDIK INTERNET EXPLORERREL</strong>!<br><br>
			A felület rendeltetésszerű használatához kérem egy modernebb böngészőben (Microsoft Edge, Google Chrome, Mozilla Firefox) nyissa meg az oldalt!</span><?php
	}
	?><div class="wrapper">
		<!-- Tartalom -->
		<main class="content">
			<div class="left"></div>
			<div class="right"><a href="<?=$RootPath?>/bugreport?oldal=<?=$pagetofind?>">Hiba jelzése</a></div><?php
			include('./includes/contentpage.inc.php');
		?></main>

		<!-- Fejléc -->
		<div class="topmenubase" id="topmenuelement"><?php include('./templates/header.tpl.php'); ?></div><?php

		if(@$contextheader)
		{
			?><!-- Kontextus fejléc -->
			<div class="contextheader" id="contextheader"><img src="<?=$contextheader?>"></div><?php
		}

		?><!-- Menürész -->
		<?php MainMenu(); ?>

		<!-- Lábléc -->
		<div class="bottom-line"><p><a href="mailto:<?=$DEVELOPER_MAIL?>">©<?=$DEVELOPER_NAME?> <?=date("Y")?></a></p><span id="constatus"></span></div>

		<!-- Betöltés során látható területen kívül eső, illetve nem létező tartalmak -->
		<div id="snackbar"></div>
		<div id="newsflash"><div id="newsflashdesc"></div><div id="newsflashtextarea"><div id="newsflashtext"></div></div></div>
		<div id="formkuldatfedes" class="formkuldatfedes"><div class="formkuldmessage">Művelet folyamatban, kérlek várj...<br><div class="loader"></div></div></div>
		<img src="<?=$RootPath?>/images/back.png" alt= "Vissza" id="backtotop" onclick="scrollToTop()"/>
	</div>
</body><?php
	include('./includes/scriptblock.php')
?></html>