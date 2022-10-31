<?php
if(@$irhat)
{
    $i = 1;
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/menu&action=update<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                foreach($menu as $menupont)
                {
                    ?><div id="mid-<?=$menupont['id']?>" class="menucard<?=($menupont['szulo']) ? "-child" : "" ?>">
                        <div class="menucardtitle"><?=$menupont['menupont']?><a class="help" id="nyil-<?=$menupont['id']?>" onclick="rejtMutat('menupontid-<?=$menupont['id']?>');upDownConversion('nyil-<?=$menupont['id']?>')">˅</a></div>
                        <div class="menucardbody" id="menupontid-<?=$menupont['id']?>" style="display: none">
                            <input type ="hidden" id="id-<?=$i?>" name="id-<?=$i?>" value="<?=$menupont['id']?>">
                            <div>
                                <div><strong>Alapbeállítások</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Menüpont neve</label>
                                        <input type="text" style="width:15ch" name="menupont-<?=$i?>" value="<?=$menupont['menupont']?>">
                                    </div>
                                    <div>
                                        <label>Szülő menüpont</label>
                                        <select name="szulo-<?=$i?>">
                                            <option value="">Nincs</option><?php
                                            foreach($szulo as $x)
                                            {
                                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $menupont['szulo']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                                            }
                                        ?></select>
                                    </div>
                                    <div>
                                        <label>Megjelenik</label>
                                        <select name="aktiv-<?=$i?>">
                                            <option value="" <?=(!$menupont['aktiv']) ? "selected" : "" ?>>Senkinek</option>
                                            <option value="1" <?=($menupont['aktiv'] == "1") ? "selected" : "" ?>>Jogosultaknak</option>
                                            <option value="2" <?=($menupont['aktiv'] == "2") ? "selected" : "" ?>>Bejelentkezetteknek</option>
                                            <option value="3" <?=($menupont['aktiv'] == "3") ? "selected" : "" ?>>Mindenkinek</option>
                                            <option value="4" <?=($menupont['aktiv'] == "4") ? "selected" : "" ?>>Csak látogatóknak</option>
                                        </select>
                                    </div>    
                                    <div>
                                        <label>Menüterület</label>
                                        <input type="text" style="width:6ch" name="menuterulet-<?=$i?>" value="<?=$menupont['menuterulet']?>">
                                    </div>
                                    <div>
                                        <label>Sorrend</label>
                                        <input type="text" style="width:6ch" name="sorrend-<?=$i?>" value="<?=$menupont['sorrend']?>">
                                    </div>
                                    <div>
                                        <label>Új elem</label>
                                        <input type="text" name="szerkoldal-<?=$i?>" value="<?=$menupont['szerkoldal']?>"  placeholder="Pl.: menupont?action=addnew">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>Egyéni oldal</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Fejléc címe</label>
                                        <input type="text" name="cimszoveg-<?=$i?>" value="<?=$menupont['cimszoveg']?>">
                                    </div>
                                    <div>
                                        <label>Címsorban megjelenő oldal</label>
                                        <input type="text" name="oldal-<?=$i?>" value="<?=$menupont['oldal']?>">
                                    </div>
                                    <div>
                                        <label>Tényleges elérési út</label>
                                        <input type="text" style="width:40ch" name="url-<?=$i?>" value="<?=$menupont['url']?>">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>Gyüjtő oldal</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Fejléc címe</label>
                                        <input type="text" name="gyujtocimszoveg-<?=$i?>" value="<?=$menupont['gyujtocimszoveg']?>">
                                    </div>
                                    <div>
                                        <label>Címsorban megjelenő oldal</label>
                                        <input type="text" name="gyujtooldal-<?=$i?>" value="<?=$menupont['gyujtooldal']?>">
                                    </div>
                                    <div>
                                        <label>Tényleges elérési út</label> 
                                        <input type="text" style="width:40ch" name="gyujtourl-<?=$i?>" value="<?=$menupont['gyujtourl']?>">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div><strong>Adatbázis oldal</strong></div>
                                <div class="menuszerksor">
                                    <div>
                                        <label>Címsorban megjelenő oldal</label>    
                                        <input type="text" name="dboldal-<?=$i?>" value="<?=$menupont['dboldal']?>">
                                    </div>
                                    <div>
                                        <label>Tényleges elérési út</label>
                                        <input type="text" style="width:40ch" name="dburl-<?=$i?>" value="<?=$menupont['dburl']?>" placeholder="">
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