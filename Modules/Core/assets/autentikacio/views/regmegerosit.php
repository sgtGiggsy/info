<?php
if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent"><?php
    if($this->megerosit) {
        ?><h1>Sikeres megerősítés!</h1>
        <p>Most már bejelentkezhetsz a regisztráció során megadott felhasználóneveddel és jelszavaddal.</p><?php
    }
    else {
        ?><h1>Megerősítés sikertelen!</h1>
        <p>Az e-mail fiókod megerősítése nem sikerült. Lehetséges, hogy nem a teljes linket másoltad ki, vagy ez a megerősítő link már lejárt.</p><?php
    }
?></div>