<?php 

/*
foreach($_SESSION as $key => $value)
{
	echo "$key = $value";
} 
*/

//
if($loginid)
{
	$loginid = "?loginid=$loginid";
}
if($_SESSION[getenv('SESSION_NAME').'id'] == 0)
{
	if($_SESSION[getenv('SESSION_NAME').'fooldalkijelentkezve'])
	{
		header("Location: ./" . $_SESSION[getenv('SESSION_NAME').'fooldalkijelentkezve'] . $loginid);
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
		header("Location: ./" . $_SESSION[getenv('SESSION_NAME').'fooldalbejelentkezve'] . $loginid);
	}
	else
	{
		echo "<div class='oldalcim'>Főoldal</div>";
		echo $_SESSION[getenv('SESSION_NAME').'udvozloszovegbelepve'];
	}
}