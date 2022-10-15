<?php
if(!$felhasznaloid)
{
    echo "<h2>Nincs jogosultsága az oldal megtekintésére!</h2>";
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/szemelyesdb.php");
    }

    $button = "Beállítások mentése";

    ?><div class='oldalcim'>Személyes beállítások</div>
    <div class="contentcenter" id="munkalapok">
        <form action="<?=$RootPath?>/szemelyes" method="post">
            <div>
                <small>A switchek listájában</small><br>
                <label for="switchstatemail">Switchek online állapotának mutatása:</label><br>
                <label class="kapcsolo">
                    <input type="hidden" name="switchstateshow" id="switchstateshowhidden" value="0">
                    <input type="checkbox" name="switchstateshow" id="switchstateshow" value="1" <?= (isset($szemelyes['switchstateshow']) && $szemelyes['switchstateshow'] == 1) ? "checked" : "" ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <div>
                <small>A switchek online állapotáról</small><br>
                <label for="switchstateshow">Automatikus mailküldés:</label><br>
                <label class="kapcsolo">
                    <input type="hidden" name="switchstatemail" id="switchstatemailhidden" value="0">
                    <input type="checkbox" name="switchstatemail" id="switchstatemail" value="1" <?= (isset($szemelyes['switchstatemail']) && $szemelyes['switchstatemail'] == 1) ? "checked" : "" ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="submit"><input type="submit" value='<?=$button?>'></div>
        </form>
    </div><?php
}