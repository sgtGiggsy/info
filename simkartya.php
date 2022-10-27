<?php
// Elsőként annak ellenőrzése, hogy a felhasználó olvashatja-e,
// majd megvizsgálni, hogy ha olvashatja, de írni szeretné, ahhoz van-e joga
if(!@$mindolvas || (isset($_GET['action']) && !$mindir))
{
    getPermissionError();
}
// Ha van valamilyen módosítási kísérlet, ellenőrizni, hogy van-e rá joga a felhasználónak
elseif(isset($_GET['action']) && $mindir)
{
    $meghiv = true;
    
    // Az eszközszerkesztő oldal includeolása
    include('./includes/eszkozszerkeszt.inc.php');
}