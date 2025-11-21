<?php
header('Content-Type: application/json');
$felhasznaloid = @$_SESSION['id'];
$masterpass = @$_SESSION['unlockedmaster'];
$unlockedtime = @$_SESSION['unlockedtime'];
$jelid = @$_GET['jelid'];

if(false && (!$felhasznaloid || !$masterpass || (time() - $unlockedtime) > 300 || !$jelid))
{
    if(!$felhasznaloid)
        $uzenet['eredmeny'] = "Hiba! Nem vagy bejelentkezve!";
    elseif(!$masterpass)
        $uzenet['eredmeny'] = "Hiba! Nem adtad meg a mesterjelszót!";
    elseif(!$jelid)
        $uzenet['eredmeny'] = "Hiba! Nincs kiválasztott jelszó!";
    else
        $uzenet['eredmeny'] = "Hiba! A munkamenet lejárt, add meg újra a mesterjelszót!";

    http_response_code(401);
}
else
{
    $checkauth = new MySQLHandler("SELECT felhasznalo FROM jogosultsagok WHERE felhasznalo = ? AND menupont = 112 AND olvasas > 1", $felhasznaloid);
    if($checkauth->sorokszama < 1)
    {
        $uzenet['eredmeny'] = "Hiba! Nincs jogosultsága a jelszó megtekintésére";
        http_response_code(403);
    }
    else
    {
        $pass = (new MySQLHandler("SELECT pass FROM jelszokezelo_jelszavak WHERE id = ?", $jelid))->Fetch();
        $uzenet['eredmeny'] = decPass($pass['pass']);
        http_response_code(200);
    }
}

echo json_encode($uzenet);