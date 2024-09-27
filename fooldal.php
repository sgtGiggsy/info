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
if($_SESSION['id'] == 0)
{
	if($_SESSION['fooldalkijelentkezve'])
	{
		header("Location: ./" . $_SESSION['fooldalkijelentkezve'] . $loginid);
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
		header("Location: ./" . $_SESSION['fooldalbejelentkezve'] . $loginid);
	}
	else
	{
		echo "<div class='oldalcim'>Főoldal</div>";
		echo $_SESSION['udvozloszovegbelepve'];
	}
}