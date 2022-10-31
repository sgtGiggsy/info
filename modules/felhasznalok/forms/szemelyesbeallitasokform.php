<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/felhasznalo?beallitasok" method="post">
                <div>
                    <label for="switchstatemail">Switchek online állapotának mutatása:</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="switchstateshow" id="switchstateshowhidden" value="0">
                        <input type="checkbox" name="switchstateshow" id="switchstateshow" value="1" <?= (isset($szemelyes['switchstateshow']) && $szemelyes['switchstateshow'] == 1) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div>
                    <label for="switchstateshow">Automatikus mailküldés:</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="switchstatemail" id="switchstatemailhidden" value="0">
                        <input type="checkbox" name="switchstatemail" id="switchstatemail" value="1" <?= (isset($szemelyes['switchstatemail']) && $szemelyes['switchstatemail'] == 1) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div>
                    <label>Színséma</label><br>
                    <select name="szinsema">
                        <option value="">Alapértelmezett</option>
                        <option value="dark" <?=(isset($szemelyes['szinsema']) && $szemelyes['szinsema'] == "dark") ? "selected" : "" ?>>Sötét</option>
                    </select>
                </div>

                <div class="submit"><input type="submit" value='<?=$button?>'></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}