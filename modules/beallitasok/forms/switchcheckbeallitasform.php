<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/beallitasok&action=update<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
                
                <?php $magyarazat = "<h2>Switchellenőrző beállítása</h2>
                    <p>Itt találhatóak a switchek állapotának vizsgálatát végző program beállításai.</p>"; ?>

                <div>
                    <label for="telephely">Telephely:</label><br>
                    <select id="telephely" name="telephely">
                        <option value="" selected>Mind</option><?php
                        foreach($telephelyek as $x)
                        {
                            ?><option value="<?=$x["id"]?>" <?= ($beallitas['telephely'] == $x['id']) ? "selected" : "" ?>><?=$x['telephely']?></option><?php
                        }
                    ?></select>
                </div>

                <?php $magyarazat .= "<strong>Telephely</strong>
                    <p>A telephely, aminek aktív eszközeit a program ellenőrzi.</p>"; ?>

                <div>
                    <label for="onlinefigyeles">Switchek online állapotának mutatása:</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="onlinefigyeles" id="onlinefigyeleshidden" value="">
                        <input type="checkbox" name="onlinefigyeles" id="onlinefigyeles" value="1" <?= ($beallitas['onlinefigyeles']) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>Switchek online állapotának mutatása</strong>
                    <p>Ezt bekapcsolva jelenik meg az oldalon akárhol a switchek online állapota.</p>"; ?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$bbutton?>"></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}