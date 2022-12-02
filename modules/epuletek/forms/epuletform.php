<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/epulet&action=<?=(isset($id)) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($id))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
                }
                ?><div>
                    <label for="telephely">Telephely:</label><br>
                    <select id="telephely" name="telephely">
                        <option value="" selected></option><?php
                        foreach($telephelyek as $x)
                        {
                            ?><option value="<?=$x["id"]?>" <?= ($telephely == $x['id']) ? "selected" : "" ?>><?=$x['telephely']?></option><?php
                        }
                    ?></select>
                </div>

                <?php $magyarazat .= "<strong>Telephely</strong><p>A telephely, ahol az épület található.</p>"; ?>

                <div>
                    <label for="szam">Épület rajzszáma:</label><br>
                    <input type="text" accept-charset="utf-8" name="szam" id="szam" value="<?=$szam?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Épület rajzszáma</strong><p>Az épület telephely alaprajz alapján kiosztott száma.</p>"; ?>

                <div>
                    <label for="nev">Épület neve:</label><br>
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Épület neve</strong><p>Az épület általánosan használt (nem hivatalos) neve.</p>"; ?>

                <div>
                    <label for="tipus">Épülettipus:</label><br>
                    <select id="tipus" name="tipus">
                        <option value="" selected></option><?php
                        foreach($epulettipusok as $x)
                        {
                            ?><option value="<?=$x["id"]?>" <?= ($tipus == $x['id']) ? "selected" : "" ?>><?=$x['tipus']?></option><?php
                        }
                    ?></select>
                </div>
                
                <?php $magyarazat .= "<strong>Épülettípus</strong><p>Az épület típusa.</p>"; ?>

                <div>
                    <label for="megjegyzes">Megjegyzés:</label><br>
                    <textarea accept-charset="utf-8" name="megjegyzes" id="megjegyzes"><?=$megjegyzes?></textarea>
                    <?php $magyarazat .= "<strong>Megjegyzés</strong><p>Az épülethez kapcsolódó esetleges magyarázat.</p>"; ?>
                </div><?php

                if(isset($id))
                {
                    ?><div>
                        <label for="naprakesz">A nyilvántartás naprakész:</label><br>
                        <input type="hidden" name="naprakesz" id="naprakeszhidden" value="0"></input>
                        <input type="checkbox" accept-charset="utf-8" name="naprakesz" id="naprakesz" value="1" <?= ($naprakesz) ? "checked" : "" ?>></input>
                    </div><?php

                    $magyarazat .= "<strong>A nyilvántartás naprakész</strong><p>Adatok felvitelénél lehet hasznos. Akkor kell bepipálni,
                        ha legjobb tudomásunk szerint a nyilvántartás naprakész.</p>";
                }

                ?><div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form><?php
            cancelForm();
        ?></div>
    </div><?php
}