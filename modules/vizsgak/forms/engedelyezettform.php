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
                    <button class="felkorbutton" class="searchicon">
                        <svg enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24" focusable="false">
                            <path d="m20.87 20.17-5.59-5.59C16.35 13.35 17 11.75 17 10c0-3.87-3.13-7-7-7s-7 3.13-7 7 3.13 7 7 7c1.75 0 3.35-.65 4.58-1.71l5.59 5.59.7-.71zM10 16c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div>
            <form action="<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/engedelyezettszerkeszt" method="POST">
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