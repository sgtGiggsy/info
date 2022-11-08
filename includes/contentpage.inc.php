<?php

if(!$_SESSION[getenv('SESSION_NAME').'id'] && $currentpage['aktiv'] < 3) // Ha nincs bejelentkezett felhasználó, és a menüpont bejelentkezéshez kötött
{
    ?><p><h2>A kért oldal megtekintéséhez kérem jelentkezzen be!</h2></p><?php
    include("./belepes.php");
}
else
{
    if($currentpage['oldal'] == "fooldal")
    {
        include("./{$currentpage['url']}.php");
    }
    elseif($_GET['page'] == $currentpage['oldal'])
    {
        include("./{$currentpage['url']}.php");
    }
    elseif($_GET['page'] == $currentpage['gyujtooldal'])
    {
        include("./{$currentpage['gyujtourl']}.php");
    }
    elseif($_GET['page'] == $currentpage['dboldal'])
    {
        include("./{$currentpage['dburl']}.php");
    }
    elseif($_GET['page'] == $currentpage['szerkoldal'])
    {
        include("./{$currentpage['szerkoldal']}.php");
    }
}
?>