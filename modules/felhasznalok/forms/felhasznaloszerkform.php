<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/felhasznalo&action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }   
                ?><div>
                    <label for="felhasznalonev">Felhasználónév:</label><br>
                    <input type="text" accept-charset="utf-8" name="felhasznalonev" id="felhasznalonev" value="<?=$felhasznalonev?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Felhasználónév</strong>
                    <p>A felhasználó AD bejelentkezéshez használt felhasználóneve. Pontosan kell megadni,
                    máskülönben az AD-val történő szinkronizálás során a rendszer nem fogja tudni beazonosítani.</p>"?>

                <div>
                    <label for="nev">Név:</label><br>
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Név</strong><p>A felhasználó megjelenő neve.</p>"?>

                <div>
                    <label for="email">Email:</label><br>
                    <input type="text" accept-charset="utf-8" name="email" id="email" value="<?=$email?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Email</strong><p>A felhasználó e-mail címe.</p>"?>

                <div>
                    <label for="telefon">Telefon:</label><br>
                    <input type="text" accept-charset="utf-8" name="telefon" id="telefon" value="<?=$telefon?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Telefon</strong><p>A felhasználó munkahelyi telefonszáma.</p>"?>

                <?php szervezetPicker($szervezet, "szervezet", true); ?>

                <?php $magyarazat .= "<strong>szervezet</strong><p>A felhasználó szervezete.</p>"?>

                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                <?= cancelForm();?>
            </form>
        </div>
    </div><?php
      
}