<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/szervezetszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if(isset($_GET['id']))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
            }   
            ?><div>
                <label for="nev">Szervezet teljes megnevezése</label><br>
                <textarea name="nev" id="nev"><?=$nev?></textarea>
            </div>

            <?php $magyarazat .= "<strong>Szervezet teljes megnevezése</strong><p>Ide a szervezet teljes nevét kell megadni, vesszőre, betűre pontosan.</p>"; ?>

            <div>
                <label for="rovid">Szervezet rövid neve</label><br>
                <input type="text" accept-charset="utf-8" name="rovid" id="rovid" value="<?=$rovid?>"></input>
            </div>

            <?php $magyarazat .= "<strong>Szervezet rövid neve</strong><p>Ide a szervezet <b>hivatalos</b> rövid neve kerül (ha van neki).</p>"; ?>

            <div>
                <label for="statusz">A szervezet jogállása</label><br>
                <select name="statusz" id="statusz">
                    <option value="" selected></option>
                    <option value="1" <?=($statusz == "1") ? "selected" : "" ?>>Alakulat</option>
                    <option value="2" <?=($statusz == "2") ? "selected" : "" ?>>HM tulajdon</option>
                    <option value="3" <?=($statusz == "3") ? "selected" : "" ?>>Civil beszállító</option>
                </select>
            </div>

            <?php $magyarazat .= "<strong>A szervezet jogállása</strong><p>Itt kell kiválasztani az adott szervezet jogállását.</p>"; ?>
            
            <div>
                <label for="ldapstring">AD-ból vett név töredék</label><br>
                <input type="text" accept-charset="utf-8" name="ldapstring" id="ldapstring" value="<?=$ldapstring?>"></input>
            </div>

            <?php $magyarazat .= "<strong>AD-ból vett név töredék</strong><p>Elnézést a hülye megnevezés miatt, értelmesebb nem jutott az eszembe.
                            Mivel az AD és ez a felület nincs semmilyen formában összekötve egymással, ezért a rendszernek valahogy
                            muszáj beazonosítania, hogy a bejelentkező felhasználó melyik szervezetnek a dolgozója.
                            Erre csak úgy van mód, hogy ha az AD-ban megadott \"cégnév\" mező tartalmát a rendszer összeveti a saját adatbázisával,
                            és az alapján kiválasztja, hogy a bejelentkező felhasználó melyik szervezetnek a dolgozója.
                            Mivel a cégnév mező nem mindenkinél egységesen van megadva az AD-ban, ezért itt több lehetőséget is megadhatunk,
                            ami alapján a rendszer beazonosíthatja a szervezetet. Fontos, hogy <i>egyedi</i> szövegeket adjunk meg itt,
                            ami egyértelműen beazonosítja a szervezetet. Szövegrészleteket válasszuk el pontosvesszőkkel.</p>"; ?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php cancelForm(); ?>
    </div><?php
}