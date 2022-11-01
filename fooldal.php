<?php 

/*
foreach($_SESSION as $key => $value)
{
	echo "$key = $value";
} 
*/

//
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
	if($_SESSION[getenv('SESSION_NAME').'fooldalkijelentkezve'])
	{
		header("Location: ./" . $_SESSION[getenv('SESSION_NAME').'fooldalkijelentkezve']);
	}
	else
	{
		echo "<div class='oldalcim'>Főoldal</div>";
		echo $_SESSION[getenv('SESSION_NAME').'udvozloszoveg'];
	}
}
else
{
	if($_SESSION[getenv('SESSION_NAME').'fooldalbejelentkezve'])
	{
		header("Location: ./" . $_SESSION[getenv('SESSION_NAME').'fooldalbejelentkezve']);
	}
	else
	{
		echo "<div class='oldalcim'>Főoldal</div>";
		echo $_SESSION[getenv('SESSION_NAME').'udvozloszovegbelepve'];
	}
}