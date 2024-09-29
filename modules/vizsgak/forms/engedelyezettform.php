<?php
if(@$irhat)
{
    $javascriptfiles[] = "modules/vizsgak/includes/engedelyezszur.js";
    $nyithelp = true;
    ?><div class="contentcenter">
        <div class="szuresoptions basepadding">
            <div><h2>Lista szűrése</h2></div>
            <div class="kereses" id="kereses">
                <div class="felkormezo">
                    <input type="text" name="listaszur" id="listaszur" placeholder="Szűrés névre, vagy felhasználónévre" aria-label="Keresés" onkeyup="listaSzur()" />
                    <button class="felkorbutton" class="searchicon"><?=$icons['search']?></button>
                </div>
            </div>
        </div>
        <div>
            <form action="<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/engedelyezettszerkeszt" method="POST">
                <input type="hidden" name="vizsgaid" value="<?=$vizsgaid?>" />
                <div class="felhasznalomultiselect" id="szurendolista"><?php
                    foreach($felhasznalolist as $vizsgazhat)
                    {
                        ?><div class="twocolgrid3-1 gridleft" nev="<?=$vizsgazhat['nev']?>" username="<?=$vizsgazhat['usernev']?>">
                            <div><label for="engedelyezett-<?=$vizsgazhat['id']?>"><?=$vizsgazhat['nev']?></label></div>
                            <div>
                                <label class="customcb">
                                    <input type="checkbox" name="engedelyezett[]" id="engedelyezett-<?=$vizsgazhat['id']?>" value="<?=$vizsgazhat['id']?>" <?=($vizsgazhat['engedelyezve'] && $vizsgazhat['vizsga'] == $vizsgaid) ? "checked" : "" ?>>
                                    <span class="customcbjelolo"></span>
                                </label>
                            </div>
                            </div><?php
                    }
                ?></div>

                <?php $magyarazat .= "<strong>Az oldal használata</strong><p>A vizsgához engedélyezni kívánt felhasználó neve mellé tenni kell egy pipát.<br>
                        A szűrés mező használatával gyorsan megtalálható a hozzáadni kívánt személy. Egyszerre több felhasználó is hozzáadható a listhához,
                        nem szükséges minden kiválasztás után elmenteni a változásokat.<br>
                        A <i>szűrés</i> csak elrejti a felhasználókat, a korábban kiválasztott felhasználókról nem veszi le a kijelölést.<br>
                        Egy felhasználó eltávolításához a vizsgára engedélyezett emberek közül elég csak kivenni a neve mellől a pipát.<br>
                        A módosítások csak az oldal alján található <b>Jogosultságok módosítása</b> gombra kattintást követően kerülnek mentésre.</p>"; ?>

                <div class="submit"><input type="submit" value="<?=$button?>"></div>
                <?= cancelForm(); ?>
            </form>
        </div>
    </div><?php
}