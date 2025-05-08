<?php
header('Content-Type: application/json');
$felhasznaloid = @$_SESSION['id'];
if($felhasznaloid = 1)
{
    $ertesitesek = Ertesites::GetErtesitesek(14);
    http_response_code(200);
    echo json_encode($ertesitesek);
}
else
{
    http_response_code(403);
    echo "Nincs jogosultsága az adat megjelenítésére!";
}