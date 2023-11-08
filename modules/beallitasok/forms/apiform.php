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
                                <a onclick="copyToClipboard('key-<?=$idtocopy?>')" style="cursor: pointer"><svg height="20px" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 442 512.08"><path d="M100.46 40.14h43.25C146.47 17.51 164.73 0 186.89 0c22.02 0 40.19 17.27 43.13 39.67l43.89.47a4.78 4.78 0 0 1 4.79 4.8v52.81c0 2.65-2.14 4.79-4.79 4.79h-173.4c-2.61 0-4.8-2.14-4.8-4.79V44.94c-.04-2.67 2.1-4.8 4.75-4.8zM225.93 425.8c-5.56 0-10.07-4.51-10.07-10.07 0-5.56 4.51-10.07 10.07-10.07H366.3c5.56 0 10.07 4.51 10.07 10.07 0 5.56-4.51 10.07-10.07 10.07H225.93zm0-66.12c-5.56 0-10.07-4.51-10.07-10.07 0-5.56 4.51-10.07 10.07-10.07H366.3c5.56 0 10.07 4.51 10.07 10.07 0 5.56-4.51 10.07-10.07 10.07H225.93zm0-66.12c-5.56 0-10.07-4.51-10.07-10.07 0-5.56 4.51-10.07 10.07-10.07H366.3c5.56 0 10.07 4.51 10.07 10.07 0 5.56-4.51 10.07-10.07 10.07H225.93zm-41.06-106.42h222.49c9.5 0 18.15 3.91 24.42 10.17 6.31 6.28 10.22 14.96 10.22 24.47v255.66c0 9.5-3.91 18.18-10.19 24.45-6.28 6.28-14.95 10.19-24.45 10.19H184.87c-9.5 0-18.18-3.9-24.46-10.18l-.59-.64c-5.92-6.21-9.59-14.62-9.59-23.82V221.78c0-9.53 3.9-18.2 10.17-24.47s14.94-10.17 24.47-10.17zm222.49 20.14H184.87c-3.98 0-7.61 1.64-10.23 4.26-2.63 2.63-4.27 6.26-4.27 10.24v255.66c0 3.79 1.48 7.26 3.87 9.85l.4.37c2.64 2.64 6.27 4.28 10.23 4.28h222.49c3.95 0 7.58-1.65 10.21-4.28 2.64-2.64 4.29-6.26 4.29-10.22V221.78c0-3.96-1.65-7.59-4.28-10.23a14.372 14.372 0 0 0-10.22-4.27zm-298.61 246.3c5.56 0 10.07 4.51 10.07 10.07 0 5.56-4.51 10.07-10.07 10.07H40.16c-10.95 0-21.02-4.54-28.33-11.85C4.55 454.63 0 444.6 0 433.56V102.88c0-11.05 4.51-21.1 11.78-28.37l.63-.57c7.22-6.93 17.02-11.21 27.75-11.21H72.6v20.15H40.16c-5.3 0-10.14 2.08-13.73 5.45l-.4.43c-3.63 3.62-5.89 8.63-5.89 14.12v330.68c0 5.46 2.28 10.45 5.91 14.08 3.63 3.68 8.65 5.94 14.11 5.94h68.59zM301.8 62.73h32.43c11.03 0 21.04 4.54 28.31 11.8 7.31 7.31 11.86 17.38 11.86 28.35v33.61c0 5.56-4.51 10.07-10.07 10.07-5.56 0-10.07-4.51-10.07-10.07v-33.61c0-5.47-2.26-10.48-5.89-14.1-3.63-3.63-8.64-5.9-14.14-5.9H301.8V62.73zM186.39 20.54c12.53 0 22.68 10.15 22.68 22.68 0 12.52-10.15 22.68-22.68 22.68-12.52 0-22.68-10.16-22.68-22.68 0-12.53 10.16-22.68 22.68-22.68z"/></svg></a>
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