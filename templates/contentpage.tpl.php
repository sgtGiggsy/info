<?php
if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] == 0)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{
    ?><div class='oldalcim'>Ide jön a cím</div>
    <div class='contentcenter'><?php

    echo "Ide jön a tartalom";
    
    ?></div><?php
}
?>