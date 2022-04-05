<div class="topmenuikonok">
<?php
if($_SESSION[getenv('SESSION_NAME').'id'])
{
    
    $usernev = $_SESSION[getenv('SESSION_NAME').'nev'];
    $menuterulet = 2; include('./includes/menu2.inc.php');
    ?><a href="<?=$RootPath?>/felhasznalo">
        <img src= <?=($_SESSION['profilkep']) ? "data:image/jpeg;base64," . base64_encode($_SESSION["profilkep"]) : "$RootPath/images/profil.png " ?> title="<?=$usernev?>" alt="<?=$usernev?>">
    </a><?php
}
else
{
    ?><a href="<?=$RootPath?>/belepes" title="Belépés" alt="Belépés"><img src="<?=$RootPath?>/images/login.png"></a><?php
}
?></div>