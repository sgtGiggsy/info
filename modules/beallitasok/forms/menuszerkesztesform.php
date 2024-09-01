<?php
if(@$irhat)
{
    $i = 0;
    $javascriptfiles[] = "modules/beallitasok/includes/formactions.js";
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/menu&action=update<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                foreach($menu as $menupont)
                {
                    ?><div id="mid-<?=$menupont['id']?>" class="menucard<?=($menupont['szulo']) ? "-child" : "" ?>">
                        <div class="menucardtitle"><?=$menupont['menupont']?><a class="help" id="nyil-<?=$menupont['id']?>" onclick="rejtMutat('menupontid-<?=$menupont['id']?>');upDownConversion('nyil-<?=$menupont['id']?>')"><strong>⮟</strong></a></div>
                        <div class="menucardbody" id="menupontid-<?=$menupont['id']?>" style="display: none">
                            <input type ="hidden" id="id-<?=$i?>" name="menupont[<?=$i?>][id]" value="<?=$menupont['id']?>">
                            <div>
                                <div><strong>Alapbeállítások</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Menüpont neve</label>
                                        <input type="text" style="width:15ch"  value="<?=$menupont['menupont']?>" onkeyup="fieldEnable('<?=$i?>');" id="menupont-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Szülő menüpont</label>
                                        <select onchange="fieldEnable('<?=$i?>');" id="szulo-<?=$i?>">
                                            <option value="">Nincs</option><?php
                                            foreach($szulo as $x)
                                            {
                                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $menupont['szulo']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                                            }
                                        ?></select>
                                    </div>
                                    <div>
                                        <label>Megjelenik</label>
                                        <select onchange="fieldEnable('<?=$i?>');" id="aktiv-<?=$i?>">
                                            <option value="" <?=(!$menupont['aktiv']) ? "selected" : "" ?>>Senkinek</option>
                                            <option value="1" <?=($menupont['aktiv'] == "1") ? "selected" : "" ?>>Jogosultaknak</option>
                                            <option value="2" <?=($menupont['aktiv'] == "2") ? "selected" : "" ?>>Bejelentkezetteknek</option>
                                            <option value="3" <?=($menupont['aktiv'] == "3") ? "selected" : "" ?>>Mindenkinek</option>
                                            <option value="4" <?=($menupont['aktiv'] == "4") ? "selected" : "" ?>>Csak látogatóknak</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Menüterület</label>
                                        <input type="text" style="width:6ch" value="<?=$menupont['menuterulet']?>" onkeyup="fieldEnable('<?=$i?>');" id="menuterulet-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Sorrend</label>
                                        <input type="text" style="width:6ch" value="<?=$menupont['sorrend']?>" onkeyup="fieldEnable('<?=$i?>');" id="sorrend-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Új elem</label>
                                        <input type="text" value="<?=$menupont['szerkoldal']?>"  placeholder="Pl.: menupont?action=addnew" onkeyup="fieldEnable('<?=$i?>');" id="szerkoldal-<?=$i?>">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>Egyéni oldal</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Fejléc címe</label>
                                        <input type="text" value="<?=$menupont['cimszoveg']?>" onkeyup="fieldEnable('<?=$i?>');" id="cimszoveg-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Címsorban megjelenő oldal</label>
                                        <input type="text" value="<?=$menupont['oldal']?>" onkeyup="fieldEnable('<?=$i?>');" id="oldal-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Tényleges elérési út</label>
                                        <input type="text" style="width:40ch" value="<?=$menupont['url']?>" onkeyup="fieldEnable('<?=$i?>');" id="url-<?=$i?>">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>Gyüjtő oldal</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Fejléc címe</label>
                                        <input type="text" value="<?=$menupont['gyujtocimszoveg']?>" onkeyup="fieldEnable('<?=$i?>');" id="gyujtocimszoveg-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Címsorban megjelenő oldal</label>
                                        <input type="text" value="<?=$menupont['gyujtooldal']?>" onkeyup="fieldEnable('<?=$i?>');" id="gyujtooldal-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Tényleges elérési út</label> 
                                        <input type="text" style="width:40ch" value="<?=$menupont['gyujtourl']?>" onkeyup="fieldEnable('<?=$i?>');" id="gyujtourl-<?=$i?>">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>Adatbázis oldal</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Címsorban megjelenő oldal</label>    
                                        <input type="text" value="<?=$menupont['dboldal']?>" onkeyup="fieldEnable('<?=$i?>');" id="dboldal-<?=$i?>">
                                    </div>
                                    <div>
                                        <label>Tényleges elérési út</label>
                                        <input type="text" style="width:40ch" value="<?=$menupont['dburl']?>" placeholder="" onkeyup="fieldEnable('<?=$i?>');" id="dburl-<?=$i?>">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>API</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>API elérési útja</label>    
                                        <input type="text" style="width:60ch" value="<?=$menupont['apiurl']?>" onkeyup="fieldEnable('<?=$i?>');" id="apiurl-<?=$i?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><?php
                    $i++;
                }
                ?><div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}