<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/felhasznalo?beallitasok" method="post"><?php
                if($switchstate)
                {
                    ?><div>
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
                    </div><?php
                }

                ?><div>
                    <label>Színséma</label><br>
                    <div class="kapcsolo toggle <?=(isset($szemelyes['szinsema']) && $szemelyes['szinsema'] == "dark") ? "on" : "off" ?>" onclick="this.classList.toggle(`off`);this.classList.toggle(`on`);switchNightMode();">
                        <input type="hidden" name="szinsema" id="szinsemahidden" value="">
                        <input type="checkbox" name="szinsema" id="szinsema" value="dark" <?=(isset($szemelyes['szinsema']) && $szemelyes['szinsema'] == "dark") ? "checked" : "" ?>>
                    </div>
                </div>

                

                <div class="submit"><input type="submit" value='<?=$button?>'></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div>
    
    <script>
        function switchNightMode()
        {
            var nightmode = document.getElementById('szinsema').checked;
            if(nightmode)
            {
                document.getElementById('szinsema').checked = false;
            }
            else
            {
                document.getElementById('szinsema').checked = true;
            }
        }
    </script>
    
    <?php
}