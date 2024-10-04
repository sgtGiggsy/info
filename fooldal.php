<?php
$fooldal = $menusql->AsArray('id');

if($_SESSION['id'] == 0)
{
	if($_SESSION['fooldalkijelentkezve'])
	{
		include("./{$fooldal[$_SESSION['fooldalkijelentkezve']]['gyujtourl']}.php");

	}
	else
	{
		echo "<div class='oldalcim'>Főoldal</div>";
		echo $_SESSION['udvozloszoveg'];
	}
}
else
{
	if($_SESSION['fooldalbejelentkezve'])
	{
		include("./{$fooldal[$_SESSION['fooldalbejelentkezve']]['gyujtourl']}.php");
	}
	else
	{
		echo "<div class='oldalcim'>Főoldal</div>";
		echo $_SESSION['udvozloszovegbelepve'];
	}
}