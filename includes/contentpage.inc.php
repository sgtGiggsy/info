<?php

if(!$_SESSION['id'] && $currentpage['aktiv'] < 3) // Ha nincs bejelentkezett felhasználó, és a menüpont bejelentkezéshez kötött
{
    ?><p><h2>A kért oldal megtekintéséhez kérem jelentkezzen be!</h2></p><?php
    include("./belepes.php");
}
else
{
    include("./{$selectedurl}.php");
}