<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/menu&action=new" method="post" onsubmit="beKuld.disabled = true; return true;">
                <div class="menucard">
                    <div class="menucardtitle"><?=$menupont['menupont']?></div>
                    <div class="menucardbody">
                        <div>
                            <div><strong>Alapbeállítások</strong></div>
                            <div class="menuszerksor">
                                <div>
                                    <label>Menüpont neve</label>
                                    <input type="text" style="width:15ch" name="menupont">
                                </div>
                                <div>
                                    <label>Szülő menüpont</label>
                                    <select name="szulo">
                                        <option value="">Nincs</option><?php
                                        foreach($szulo as $x)
                                        {
                                            ?><option value="<?=$x['id']?>"><?=$x['menupont']?></option><?php
                                        }
                                    ?></select>
                                </div>
                                <div>
                                    <label>Megjelenik</label>
                                    <select name="aktiv">
                                        <option value="">Senkinek</option>
                                        <option value="1">Jogosultaknak</option>
                                        <option value="2">Bejelentkezetteknek</option>
                                        <option value="3">Mindenkinek</option>
                                        <option value="4">Csak látogatóknak</option>
                                    </select>
                                </div>    
                                <div>
                                    <label>Menüterület</label>
                                    <input type="text" style="width:6ch" name="menuterulet">
                                </div>
                                <div>
                                    <label>Sorrend</label>
                                    <input type="text" style="width:6ch" name="sorrend">
                                </div>
                                <div>
                                    <label>Új elem</label>
                                    <input type="text" name="szerkoldal" placeholder="Pl.: menupont?action=addnew">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div><strong>Egyéni oldal</strong></div>
                            <div class="menuszerksor">
                                <div>
                                    <label>Fejléc címe</label>
                                    <input type="text" name="cimszoveg">
                                </div>
                                <div>
                                    <label>Címsorban megjelenő oldal</label>
                                    <input type="text" name="oldal">
                                </div>
                                <div>
                                    <label>Tényleges elérési út</label>
                                    <input type="text" style="width:40ch" name="url">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div><strong>Gyüjtő oldal</strong></div>
                            <div class="menuszerksor">
                                <div>
                                    <label>Fejléc címe</label>
                                    <input type="text" name="gyujtocimszoveg">
                                </div>
                                <div>
                                    <label>Címsorban megjelenő oldal</label>
                                    <input type="text" name="gyujtooldal">
                                </div>
                                <div>
                                    <label>Tényleges elérési út</label> 
                                    <input type="text" style="width:40ch" name="gyujtourl">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div><strong>Adatbázis oldal</strong></div>
                            <div class="menuszerksor">
                                <div>
                                    <label>Címsorban megjelenő oldal</label>    
                                    <input type="text" name="dboldal">
                                </div>
                                <div>
                                    <label>Tényleges elérési út</label>
                                    <input type="text" style="width:40ch" name="dburl">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div><strong>API</strong></div>
                            <div class="menuszerksor">
                                <div>
                                    <label>API elérési útja</label>    
                                    <input type="text" style="width:60ch" name="apiurl">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}