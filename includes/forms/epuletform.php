<?php
if(@$irhat)
{
    ?><div class="contentcenter">
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

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form><?php
        cancelForm();
    ?></div><?php
}