<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    if((isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) || $_SESSION[getenv('SESSION_NAME').'jogosultsag'] >= 10) && isset($_GET['id']))
    {
        $felhid = $_GET['id'];
        if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] >= 50)
        {
            ?><a href='<?=$RootPath?>/felhasznaloszerkeszt/<?=$felhid?>'>Felhasználó szerkesztése</a><?php
        }
    }
    else
    {
        $felhid = $_SESSION[getenv('SESSION_NAME').'id'];
    }
    
    $query = mySQLConnect("SELECT * FROM felhasznalok WHERE id = $felhid;");
    $felhasznalo = mysqli_fetch_assoc($query);
    ?><div class='oldalcim'>Felhasználói oldal</div>
    <div class='contentcenter'>
    <p>Név: <?=$felhasznalo['nev']?></p>
    <p>Felhasználónév: <?=$felhasznalo['felhasznalonev']?></p>
    <p>Részleg: <?=$felhasznalo['osztaly']?></p>
    <p>Email: <?=$felhasznalo['email']?></p>
    <p>Első belépés ideje: <?=$felhasznalo['elsobelepes']?></p>
    <p>Jogosultság: <?=($felhasznalo['jogosultsag'] < 10) ? "Felhasználó" : "Adminisztrátor";?></p>
    </div><?php

    include('./bejelentkezesek.php');
}
?>