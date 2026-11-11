<?php
if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1>Regisztráció</h1>
    <form action="<?= ROOT_PATH ?>/regisztracio" method="post">
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
            <label for="email">E-mail cím</label>
            <input type="email" 
                name="email"
                placeholder="email@cim.hu"
                id="email"
                autocomplete="email"
                required>
        </div>
        <div class="jelszoblokk">
            <div>
                <label for="jelszo">Jelszó</label>
                <input type="password"
                    name="jelszo"
                    placeholder="Jelszó"
                    id="jelszo"
                    autocomplete="new-password"
                    required>
            </div>
            <div>
                <label for="jelszoismetles">Jelszó megerősítés</label>
                <input type="password"
                    name="jelszoismetles"
                    placeholder="Jelszó megerősítés"
                    id="jelszoismetles"
                    autocomplete="new-password"
                    required>
            </div>
            <div>
                <ul id="jelszoszabalyok">
                    <li id="rule-length">Legalább 12 karakter</li>
                    <li id="rule-lower">Kisbetű</li>
                    <li id="rule-upper">Nagybetű</li>
                    <li id="rule-number">Szám</li>
                    <li id="rule-match">Jelszavak egyeznek</li>
                </ul>
                <div id="jelszoerobox">
                    <div id="jelszocsik"></div>
                    <div id="jelszoero">Jelszó erősség: -</div>
                </div>
            </div>
        </div>
        <div class="customcbwrapper top">
            <label class="customcb">
                <input type="checkbox"
                name="beleegyezes"
                id="beleegyezes"
                value="1"
                required>
                <span class="customcbjelolo"></span>
            </label>
            <label for="beleegyezes">
                <strong>Hozzájáruló nyilatkozat</strong>
                <p>A „Kalóriafaló” weboldalon történő regisztrációval kijelentem, hogy:</p>
                <ul>
                    <li>az általam megadott adatok a valóságnak megfelelnek,</li>
                    <li>a szolgáltatást saját felelősségemre veszem igénybe,</li>
                    <li>elfogadom az oldal működésével, funkcióival és esetleges korlátaival kapcsolatos feltételeket,</li>
                    <li>hozzájárulok ahhoz, hogy a megadott adataimat a szolgáltatás működtetése, a felhasználói élmény biztosítása, valamint szükség esetén kapcsolattartás céljából kezeljék,</li>
                    <li>kijelentem, hogy az adatkezelési tájékoztatót megismertem és elfogadom.</li>
                </ul>
                <span>A jelölőnégyzet bejelölésével kifejezetten hozzájárulok a fentiekhez.</span>
            </label>
        </div>
        <div class="submit"><input type="submit" value="Regisztráció"></div>
    </form>
</div>