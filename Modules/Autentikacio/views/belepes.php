<?php
if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1>Bejelentkezés</h1>
    <form action="<?= ROOT_PATH ?>/belepes" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div>
            <label for="usernev">Felhasználónév</label>
            <input type="text" 
                accept-charset="utf-8" 
                name="usernev"
                placeholder="Felhasználónév"
                id="usernev"
                autocomplete="username"
                required></input>
        </div>
        <div>
            <label for="jelszo">Jelszó</label>
            <input type="password"
                name="jelszo"
                placeholder="Jelszó"
                id="jelszo"
                autocomplete="current-password"
                required>
        </div>
        <div class="submit"><input type="submit" value="Bejelentkezés"></div>
    </form>
    <p>Nincs még fiókod?</p>
    <p><a href="<?= ROOT_PATH . "/regisztracio" ?>">Regisztrálj!</a></p>
    <p>Elfelejtetted a jelszavad?</p>
    <p><a href="<?= ROOT_PATH . "/elfelejtettjelszo" ?>">Kérj jelszóemlékeztetőt!</a></p>
</div>