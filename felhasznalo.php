<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    if(isset($_GET['id']))
    {
        $felhid = $_GET['id'];
    }
    else
    {
        $felhid = $_SESSION[getenv('SESSION_NAME').'id'];
    }
    
    $query = mySQLConnect("SELECT felhasznalok.id as felhid, felhasznalok.nev AS nev, felhasznalonev, jogosultsag, email, elsobelepes, osztaly, telefon, beosztas, profilkep, alakulatok.nev AS alakulat
            FROM felhasznalok
                LEFT JOIN alakulatok ON felhasznalok.alakulat = alakulatok.id
            WHERE felhasznalok.id = $felhid;");
    $felhasznalo = mysqli_fetch_assoc($query);

    ?><?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/felhasznaloszerkeszt/$felhid'\">Felhasználó szerkesztése</button>" : "" ?>
    <div class='oldalcim'>Felhasználói oldal</div>
    <div class='contentcenter'>
        <p><img src="data:image/jpeg;base64,<?=base64_encode($felhasznalo['profilkep'])?>" /></p>
        <p>Név: <?=$felhasznalo['nev']?></p>
        <p>Felhasználónév: <?=$felhasznalo['felhasznalonev']?></p>
        <p>Alakulat: <?=$felhasznalo['alakulat']?></p>
        <p>Részleg: <?=$felhasznalo['osztaly']?></p>
        <p>Beosztás: <?=$felhasznalo['beosztas']?></p>
        <p>Telefon: <?=$felhasznalo['telefon']?></p>
        <p>Email: <?=$felhasznalo['email']?></p>
        <p>Első belépés ideje: <?=$felhasznalo['elsobelepes']?></p>
    </div><?php

    include('./bejelentkezesek.php');
}
?>