<?php
if(@$irhat)
{
    $nyithelp = true;
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/adminszerkeszt" method="POST"><?php
            if(!$id)
            {
                ?><div>
                    <label for="felhasznalo">Felhasználó kiválasztása:</label><br><?php
                    felhasznaloPicker(null, 'felhasznalo');
                ?></div><?php
            }
            else
            {
                ?><input type ="hidden" id="felhasznalo" name="felhasznalo" value=<?=$id?> /><?php
            }

            ?><div class="contentcenter eightypercdivs">

                <div>
                    <label class="customcb rightcheck">
                        <input type="checkbox" name="alapadmin" id="alapadmin" value="1" <?=($id) ? "checked" : "" ?>>
                        <span>Eredmények megtekintése:</span>
                        <span class="customcbjelolo"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Eredmények megtekintése</strong><p>A legalapabb admin jog.
                    Ezt elvéve a felhasználó <b>összes</b> adminisztrációs joga törlődik a vizsgáról.</p>"; ?>

                <div>
                    <label class="customcb rightcheck">
                        <input type="checkbox" name="kerdesek" id="kerdesek" value="1" <?=($kerdesek) ? "checked" : "" ?>>
                        <span>Kérdések szerkesztése:</span>
                        <span class="customcbjelolo"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Kérdések szerkesztése</strong><p>A kérdések hozzáadásához, szerkesztéséhez szükséges jogosultság.</p>"; ?>

                <div>
                    <label class="customcb rightcheck">
                        <input type="checkbox" name="beallitasok" id="beallitasok" value="1" <?=($beallitasok) ? "checked" : "" ?>>
                        <span>Beállítások szerkesztése:</span>
                        <span class="customcbjelolo"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Beállítások szerkesztése</strong><p>A vizsga beállításainak szerkesztéséhez szükséges jogosultság.
                    Enélkül a felhasználó képes lesz kérdéseket szerkeszteni, de új vizsgakört nyitni, a vizsga előzetes kitöltéseit törölni nem.</p>"; ?>

                <div>
                    <label class="customcb rightcheck">
                        <input type="checkbox" name="ujkornyitas" id="ujkornyitas" value="1" <?=($ujkornyitas) ? "checked" : "" ?>>
                        <span>Új vizsgaperiódus nyitása:</span>
                        <span class="customcbjelolo"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Új vizsgaperiódus nyitása</strong><p>Erre a jogosultságra van szükség új vizsgaperiódus nyitásához és az előzetes kitöltések törléséhez.</p>"; ?>

                <div>
                    <label class="customcb rightcheck">
                        <input type="checkbox" name="adminkijeloles" id="adminkijeloles" value="1" <?=($adminkijeloles) ? "checked" : "" ?>>
                        <span>Adminisztrátorok szerkesztése:</span>
                        <span class="customcbjelolo"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Adminisztrátorok szerkesztése</strong><p>A legmagasabb szintű jogosultság.
                    Az ezzel rendelkező felhasználó képes adminisztrátorokat kijelölni, és a meglévő adminisztrátorok jogosultságait módosítani.
                    Csak ezt bejelölve a felhasználó nem kap jogot a beállítások, vagy kérdések szerkesztéséhez, de <b>bármikor</b> képes magának azokhoz
                    jogosultságot adni, így ezzel a jogosultsággal óvatosan bánjunk!</p>"; ?>

            </div>

            <div class="submit"><input type="submit" value="<?=$button?>"></div>
            <?= cancelForm(); ?>
        </form>
    </div><?php
}