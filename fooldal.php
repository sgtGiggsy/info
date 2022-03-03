<?php 

/*foreach($_SESSION as $key => $value)
{
	echo "$key = $value";
} */

//echo "<div class='oldalcim'>Főoldal</div>";
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
	$udvszovegSQL = mySQLConnect("SELECT * FROM beallitasok WHERE nev = 'udvozloszoveg'");
}
else
{
	$udvszovegSQL = mySQLConnect("SELECT * FROM beallitasok WHERE nev = 'udvozloszovegbelepve'");
}
$udvszoveg = mysqli_fetch_assoc($udvszovegSQL);
echo $udvszoveg['ertek'] ?>
