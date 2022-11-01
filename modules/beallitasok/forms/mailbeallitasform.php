<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/beallitasok&action=update<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">

                <?php $magyarazat = "<h2>Mail beállítások</h2>
                    <p>Az oldal, illetve az oldalt használó scriptek, programok mail beállításai.
                    Ezt beállítva nyílik lehetőség arra, hogy az oldal
                    mailt küldjön a különböző folyamatokról.</p>"; ?>

                <div>
                    <label for="mailkuld">Automatikus mailküldés:</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="mailkuld" id="mailkuldhidden" value="">
                        <input type="checkbox" name="mailkuld" id="mailkuld" value="1" <?= ($beallitas['mailkuld']) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Automatikus mailküldés</strong>
                    <p>Ez kapcsolja ki-be a mailküldést. Kikapcsolt állapotban az oldal <b>semmiről</b> nem
                    küld emailt.</p>"; ?>

                <div>
                    <label for="mailserver">Mail szerver:</label><br>
                    <input type="text" accept-charset="utf-8" name="mailserver" id="mailserver" value="<?=$beallitas['mailserver']?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Mail szerver</strong><p>A levelezőszerver teljes címe
                    a <b>tartománynévvel együtt</b>!</p>"; ?>

                <div>
                    <label for="mailport">Mail port:</label><br>
                    <input type="text" accept-charset="utf-8" name="mailport" id="mailport" value="<?=$beallitas['mailport']?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Mail port</strong><p>A levelezőszerver portja.</p>"; ?>

                <div>
                    <label for="mailuser">Mail felhasználó:</label><br>
                    <input type="text" accept-charset="utf-8" name="mailuser" id="mailuser" value="<?=$beallitas['mailuser']?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Mail felhasználó</strong>
                    <p>A levél küldéséhez használt felhasználó felhasználóneve.</p>"; ?>

                <div>
                    <label for="mailpassword">Mail jelszó:</label><br>
                    <input type="text" accept-charset="utf-8" name="mailpassword" id="mailpassword" value="<?=$beallitas['mailpassword']?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Mail jelszó</strong>
                    <p>A levélküldéshez használt felhasználó jelszava.
                    <b>A jelszó egyszerű szövegként kerül eltárolásra, ezért ne használjunk
                    tényleges jogosultságokkal rendelkező felhasználót a levélküldéshez!</b></p>"; ?>

                <div>
                    <label for="mailfrom">Mail küldő címe:</label><br>
                    <input type="text" accept-charset="utf-8" name="mailfrom" id="mailfrom" value="<?=$beallitas['mailfrom']?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Mail küldő címe</strong>
                    <p>A mailküldő felhasználó emailcíme.</p>"; ?>

                <div>
                    <label for="mailto">Mail címzett:</label><br>
                    <input type="text" accept-charset="utf-8" name="mailto" id="mailto" value="<?=$beallitas['mailto']?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Mail címzett</strong>
                    <p>A kimenő levelek alapértelmezett címzettje. A legtöbb folyamat és program
                    saját címzettlistát használ, így ez a beállítás többnyire figyelmen kívül hagyásra kerül.</p>"; ?>
            
            <div class="submit"><input type="submit" name="beKuld" value="<?=$bbutton?>"></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}