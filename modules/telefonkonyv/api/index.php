<?php
@$felhasznaloid = $_SESSION['id'];
if($felhasznaloid)
{
    include("../modules/telefonkonyv/includes/functions.php");
    $globaltelefonkonyvadmin = telefonKonyvAdminCheck($felhasznaloid);
    include("../modules/telefonkonyv/api/" . $_GET['tipus'] . ".php");
}
else
{
    http_response_code(403);
    echo "Nincs jogosultsága az adat megjelenítésére!";
}