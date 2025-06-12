<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/eszkozalap/firmware?action=<?=($elemid) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if($elemid)
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$elemid?>><?php
            }   
            ?><div>
                <label for="eszkoztipus">Modell:</label><br>
                <select id="eszkoztipus" name="eszkoztipus" required>
                    <option value="" selected></option><?php
                    foreach($eszkoztipuslista as $x)
                    {
                        ?><option value="<?=$x["id"]?>" <?= ($eszkoztipus == $x['id']) ? "selected" : "" ?>><?=$x['gyarto']?> <?=$x['modell']?></option><?php
                    }
                ?></select>
            </div>

            <?php $magyarazat .= "<strong>Modell</strong><p>Azt választhatjuk ki, hogy milyen modell használja a megadott firmware-t.</p>"; ?>

            <div>
                <label for="nev">Firmware teljes megnevezése:</label><br>
                <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
            </div>

            <?php $magyarazat .= "<strong>Firmware teljes megnevezése</strong><p>Ide a teljes verziónevet kell megadni, pl.: c2950-i6k2l2q4-mz.121-22.EA14</p>"; ?>

            <div>
                <label for="kiadasideje">Kiadás dátuma</label><br>
                <input type="date" id="kiadasideje" name="kiadasideje" value="<?=$kiadasideje?>">
            </div>

            <?php $magyarazat .= "<strong>Kiadás dátuma</strong><p>Itt azt adhatjuk meg, hogy mikor jelent meg az adott firmware.</p>"; ?>

            <div>
                <label class="customcb">
                    Végső verzió
                    <input type="checkbox"
                            name="vegsoverzio"
                            id="vegsoverzio"
                            value="1"
                            <?=($vegsoverzio) ? "checked" : "" ?>><?php
                            /* if(count($valaszlehetosegek) > 0)
                            {
                                ?><input type ="hidden" id="vid-<?=$i?>" name="vid[<?=$i?>]" value=<?=$valaszlehetosegek[$i-1]['valaszid']?> /><?php
                            }*/
                    ?><span class="customcbjelolo"></span>
                </label>
            </div>

            <?php $magyarazat .= "<strong>Végső verzió</strong><p>Figyelem, ezt <b>NEM</b> a firmware legfrissebb verziójánál kell kiválasztani, hanem akkor, ha ennél frissebb firmware-t már <b>NEM IS FOGNAK KIADNI</b> az ezt használó eszközökhöz.</p>"; ?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
        <?php cancelForm(); ?>
    </div><?php
}