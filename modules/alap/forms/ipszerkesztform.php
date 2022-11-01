<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/ipszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }   
                ?>

                <div>
                    <label for="ipcim">IP cím:</label><br>
                    <input type="text" accept-charset="utf-8" name="ipcim" id="ipcim" value="<?=$ipcim?>"></input>
                </div>

                <?php $magyarazat .= "<strong>IP cím</strong><p>A szerkeszteni/létrehozni kívánt IP cím.</p>"; ?>

                <?= vlanPicker($vlan) ?>

                <?php $magyarazat .= "<strong>VLAN</strong><p>A vlan amelybe az adott IP cím tartozik.</p>"; ?>

                <datalist id="eszkozok"><?php
                    foreach($eszkozok as $x)
                    {
                        ?><option value="<?=$x['id']?>"><?=$x['sorozatszam']?></option><?php
                    }
                ?></datalist>

                <div>
                    <label for="eszkoz">Eszköz:</label><br>
                    <input type="text" accept-charset="utf-8" name="eszkoz" id="eszkoz" value="<?=$eszkoz?>" list="eszkozok" />
                </div>

                <?php $magyarazat .= "<strong>Eszköz</strong><p><b>Kizárólag</b> akkor kell megadni, ha az olyan végponti eszközhöz van kiadva az IP,
                    ami a nyilvántartás egyetlen egyéb pontjában sem tud megjelenni (pl.: VTC eszköz, számítógép).</p>"; ?>

                <div>
                    <label for="megjegyzes">Megjegyzés:</label><br>
                    <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Megjegyzés</strong><p>Az IP címhez tartozó megjegyzés. Leginkább csak akkor lehet rá szükség,
                    ha az Eszköz menüpontban megadtunk valamit.</p>"; ?>

                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form>
            <?php cancelForm(); ?>
        </div>
    </div><?php
}