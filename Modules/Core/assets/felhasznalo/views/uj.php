<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1>Új felhasználó felvétele</h1>
    <form action="<?=ROOT_PATH?>/felhasznalo/uj" method="post" enctype="multipart/form-data">
        <p>Active Directory felhasználó felvitelénél elég csak a felhasználónevet megadni, mert az első bejelentkezése
            során minden adatot az AD-ból vesz a rendszer.
            A jelszó megadása csak akkor szükséges, ha a felhasználó nem kapcsolódik az Active Directory-hoz.
        </p>
        <?= $this->form->Render() ?>
        <div class="jelszoblokk">
            <div>
                <label for="jelszo">Jelszó</label>
                <input type="password"
                    name="jelszo"
                    placeholder="Jelszó"
                    id="jelszo"
                    autocomplete="new-password">
            </div>
            <div>
                <label for="jelszoismetles">Jelszó megerősítés</label>
                <input type="password"
                    name="jelszoismetles"
                    placeholder="Jelszó megerősítés"
                    id="jelszoismetles"
                    autocomplete="new-password">
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
        <div>
            <label for="profilkep">Profilkép</label>
            <input type="file" name="profilkep" id="profilkep" accept="image/jpeg, image/png, image/bmp, image/webp">
        </div>
        <div class="submit"><input type="submit" value="Módosítások mentése"></div>
    </form>
</div>