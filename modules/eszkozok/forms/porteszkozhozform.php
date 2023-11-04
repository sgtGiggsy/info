<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/portdb?action=generate&tipus=<?=($eszkoztipus == "aktiveszkoz") ? "switch" : "soho" ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
            <input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$id?>>
            
            <div>
                <label for="accportpre">Access port előtag<small> (pl Fa0/, vagy, ha nincs: - )</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="accportpre" id="accportpre"></input>
            </div>

            <div>
                <label for="kezdoacc">Kezdő access port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="kezdoacc" id="kezdoacc"></input>
            </div>

            <div>
                <label for="zaroacc">Záró access port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="zaroacc" id="zaroacc"></input>
            </div>

            <div>
                <label for="accportsebesseg">Access portok sebessége:</label><br>
                <select id="accportsebesseg" name="accportsebesseg">
                    <option value="" selected></option><?php
                    foreach($sebessegek as $x)
                    {
                        ?><option value="<?=$x["id"]?>"><?=$x['sebesseg']?> Mbit</option><?php
                    }
                ?></select>
            </div>

            <div>
                <label for="uplportpre">Uplink port előtag<small> (pl Gi0/, vagy, ha nincs: - )</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="uplportpre" id="uplportpre"></input>
            </div>

            <div>
                <label for="kezdoupl">Kezdő uplink port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="kezdoupl" id="kezdoupl"></input>
            </div>

            <div>
                <label for="zaroupl">Záró uplink port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="zaroupl" id="zaroupl"></input>
            </div>

            <div>
                <label for="uplportsebesseg">Uplink portok sebessége:</label><br>
                <select id="uplportsebesseg" name="uplportsebesseg">
                    <option value="" selected></option><?php
                    foreach($sebessegek as $x)
                    {
                        ?><option value="<?=$x["id"]?>"><?=$x['sebesseg']?> Mbit</option><?php
                    }
                ?></select>
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="Portok generálása"></div>
        </form>
    </div><?php
}