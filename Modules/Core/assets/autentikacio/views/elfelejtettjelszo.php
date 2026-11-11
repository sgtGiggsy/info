<?php
if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1>Elfelejtett jelszó</h1>
    <form action="<?= ROOT_PATH ?>/elfelejtettjelszo" method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div>
            <label for="email">E-mail cím</label>
            <input type="email" 
                name="email"
                placeholder="email@cim.hu"
                id="email"
                autocomplete="email"
                required></input>
        </div>
        <div class="submit"><input type="submit" value="Új jelszó igénylése"></div>
    </form>
</div>