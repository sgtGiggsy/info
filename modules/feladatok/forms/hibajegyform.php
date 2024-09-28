<?php
if(@$irhat)
{
    ?><form action="<?=$RootPath?>/hibajegy&action=<?=(isset($id)) ? 'update' : 'new' ?>" method="post" enctype="multipart/form-data" onsubmit="beKuld.disabled = true; return true;">
        <div class="doublecolumn">
            <div><?php
                if(isset($id))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
                }

                if($mindir)
                {
                    ?><div>
                        <label for="felhasznalo">A hiba bejelentője:</label><br><?php
                        felhasznaloPicker($felhasznalo, "felhasznalo");
                    ?></div><?php
                    $magyarazat .= "<strong>A hiba bejelentője</strong>
                    <p>A hibát bejelentő felhasználó neve. Kizárólag végrehajtó állomány számára jelenik meg,
                    a felhasználók csak a saját nevükben tudnak hibát bejelenteni.</p>";
                }

                ?><div>
                    <label for="rovid">A hiba rövid leírása (maximum 80 karakter):</label><br>
                    <textarea name="rovid" id="rovid" required><?=$rovid?></textarea>
                    <p id="szamlalo"></p>
                </div>

                <?php $magyarazat .= "<strong>A hiba rövid leírása</strong>
                    <p>Az észlelt hiba leírása mindössze pár szóban. <b>Kötelező megadni!</b></p>"; ?>
                
                <div>
                    <label for="bovitett">A hiba bővebb leírása:</label><br>
                    <textarea name="bovitett" id="bovitett"><?=$bovitett?></textarea>
                </div>

                <?php $magyarazat .= "<strong>A hiba bővebb leírása</strong>
                    <p>Az észlelt hiba bővebb kifejtése. Pontosan mi az, ami nem az elvártak szerint műküdik,
                    mit próbált tenni, mikor a hibát tapasztalta, milyen hibaüzenetet írt ki a rendszer, stb.</p>"; ?>

            </div>
            <div>

                <div>
                    <label for="eszkozneve">A meghibásodott eszköz neve:</label><br>
                    <input type="text" accept-charset="utf-8" name="eszkozneve" id="eszkozneve" value="<?=$eszkozneve?>"></input>
                </div>

                <?php $magyarazat .= "<strong>A meghibásodott eszköz neve</strong>
                    <p>Amennyiben egy számítógéppel van gond, úgy elég a gép nevét megadni.
                    Nyomtató esetén a márka és pontos típus megnevezés szükséges. Telefon
                    <i>Számítógép nevének kiderítése: jobb egérgombbal kattint a <b>Start menün</b>,
                    <b>Rendszer</b> utána <b>Eszköz megnevezése</b></i></p>"; ?>

                <div>
                    <label for="szakid">A meghibásodott eszköz típusa:</label><br>
                    <select name="szakid" id="szakid">
                        <option value="" selected></option>
                        <option value="1" <?=($szakid == 1) ? "selected" : "" ?>>Híradó</option>
                        <option value="2" <?=($szakid == 2) ? "selected" : "" ?>>Informatika</option>
                    </select>
                </div>

                <?php $magyarazat .= "<strong>A meghibásodott eszköz típusa</strong>
                    <p>Amennyiben telefonkészülékkel, telefonszámmal van probléma, <b>híradó</b>,
                    ha számítógéppel, nyomtatóval, hálózati eszközzel, <b>informatika</b>.</p>"; ?>

                <?php epuletPicker($epulet) ?>

                <?php $magyarazat .= "<strong>Épület</strong><p>A meghibásodás helye.
                    Nem kötlező megadni, de segíthet a hiba elhárításában.</p>"; ?>

                <?php helyisegPicker($helyiseg, "helyiseg") ?>

                <?php $magyarazat .= "<strong>Helyiség</strong><p>A meghibásodás pontos helye.
                    Nem kötlező megadni, de segíthet a hiba elhárításában.</p>"; ?>

                <div>
                    <label for="fajlok">Fényképek/képernyőképek a hibáról</label><br>
                    <input type="file" name="fajlok[]" accept="image/jpeg, image/png, image/bmp" multiple>
                </div>

                <?php $magyarazat .= "<strong>Fénykép/képernyőkép a hibáról</strong>
                    <p>Amennyiben van lehetőség fényképet, vagy képernyőképet készíteni a hibáról,
                    az elkészült képet itt lehet csatolni a hibajelentéshez. Nem kötelező megadni,
                    de segítheti a szakembereket a hiba megoldásában.</p>"; ?>
            </div>
        </div>
        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div><?php
        cancelForm();
    ?></form><?php

    if(!$csoportir)
    {
        $nyithelp = true;
    }
}