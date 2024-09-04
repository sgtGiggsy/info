<?php
if(@$irhat)
{
    ?><div class="contentcards"><?php
        $idtocopy = 0;
        foreach($apilista as $api)
        {
            $idtocopy++;
            ?><div class="apicard">
                <div class="menucardtitle">Meglévő API szerkesztése</div>
                <div class="apicardbody">
                    <form action="<?=$RootPath?>/beallitasok/api&action=updateapi" method="post" onsubmit="beKuldSzerk.disabled = true; return true;">
                        <input type ="hidden" id="id-<?=$idtocopy?>" name="id" value="<?=$api['id']?>">
                        <div>
                            <label for="menupont">Menüpont</label><br>
                            <select name="menupont"><?php
                            foreach($menupontok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($api['menupont'] == $x['id']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                            }
                            ?></select>
                        </div>
                        <div>
                            <label for="key">Kulcs</label><br>
                            <div class="buttonininput">
                                <input type="text" name="key" id="key-<?=$idtocopy?>" value="<?=$api['apikey']?>" disabled />
                                <a onclick="copyToClipboard('key-<?=$idtocopy?>')" style="cursor: pointer"><?=$icons['clipboard']?></a>
                            </div>
                        </div>

                        <div>
                            <label for="jogosultsagszint">Jogosultságszint:</label><br>
                            <select name="jogosultsagszint">
                                <option value="1" <?=($api['jogosultsagszint'] == 1) ? "selected" : "" ?>>Csak olvashat</option>
                                <option value="2" <?=($api['jogosultsagszint'] == 2) ? "selected" : "" ?>>Csak írhat</option>
                                <option value="3" <?=($api['jogosultsagszint'] == 3) ? "selected" : "" ?>>Írhat és olvashat</option>
                                <option value="4" <?=($api['jogosultsagszint'] == 4) ? "selected" : "" ?>>Írhat olvashat és módosíthat</option>
                            </select>
                        </div>

                        <div>
                            <label for="aktiv">Aktív:</label><br>
                            <input type="checkbox" name="aktiv" value="1" <?=($api['aktiv']) ? "checked" : "" ?> />
                        </div>
                        <input type="submit" value="Szerkesztés" id="beKuldSzerk"/>
                    </form>
                </div>
            </div><?php
        }
        ?><div class="apicard">
            <div class="menucardtitle">Új API készítése</div>
            <div class="apicardbody">
                <form action="<?=$RootPath?>/beallitasok/api&action=addnew" method="post" onsubmit="beKuldUj.disabled = true; return true;">
                    <div>
                        <label for="menupont">Menüpont</label><br>
                        <select name="menupont"><?php
                        foreach($menupontok as $x)
                        {
                            ?><option value="<?=$x['id']?>"><?=$x['menupont']?></option><?php
                        }
                        ?></select>
                    </div>

                    <div>
                        <label for="jogosultsagszint">Jogosultságszint:</label><br>
                        <select name="jogosultsagszint">
                            <option value="1">Csak olvashat</option>
                            <option value="2">Csak írhat</option>
                            <option value="3">Írhat és olvashat</option>
                            <option value="4">Írhat olvashat és módosíthat</option>
                        </select>
                    </div>

                    <div>
                        <label for="aktiv">Aktív:</label><br>
                        <input type="checkbox"  value="1" name="aktiv" />
                    </div>
                    <input type="submit" value="API hozzáadása" id="beKuldUj"/>
                </form>
            </div>
        </div>
    </div><?php
}