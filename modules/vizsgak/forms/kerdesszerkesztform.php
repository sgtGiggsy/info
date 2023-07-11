<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="?page=kerdesdb&action=<?=(isset($_GET['id'])) ? "update" : "addnew" ?>" method="post" enctype="multipart/form-data" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
                }
                ?><div>
                    <label for="kerdes">Kérdés szövege:</label><br>
                    <textarea name="kerdes" id="kerdes"><?php if(isset($kerdes['kerdes'])) { echo $kerdes['kerdes']; } ?></textarea>
                </div>

                <div>
                    <label for="kerdeskep">Kép csatolása a kérdéshez:</label><br>
                    <input type="file" name="kerdeskep" accept="image/jpeg, image/png, image/bmp">
                </div><?php

                if(isset($_GET['id']) && $kerdes['kep'])
                {
                    ?><div>
                        <img src="<?=$kep?>" width="500px">
                        <label for="keptorol">Feltöltött kép eltávolítása</label>
                        <input type="checkbox" name="keptorol" />
                    </div><?php
                }

                if(isset($_GET['id']))
                {
                    $i = 1;
                    foreach($kerdesadat as $x)
                    {
                        ?>
                        <div>
                        <label for="valasz<?=$i?>">Válasz <?=$i?>:<br></label>
                            <textarea name="valasz<?=$i?>" id="valasz<?=$i?>"><?=$x['valaszszoveg']?></textarea><input type="radio" name="helyes" id="helyes<?=$i?>" value="<?=$x['valaszid']?>" <?php if($x["helyes"] == 0) { } else { ?> checked <?php } ?>>
                            <input type ="hidden" id="vid<?=$i?>" name="vid<?=$i?>" value=<?=$x['valaszid']?> />
                        </div>
                        <?php
                        $i++;
                    }
                }
                else
                {
                    ?><div>
                        <label for="valasz1">Válasz 1:<br></label>
                        <textarea name="valasz1" id="valasz1"></textarea><input type="radio" name="helyes" value="1" required>
                    </div>
                    <div>
                        <label for="valasz2">Válasz 2:<br></label>
                        <textarea name="valasz2" id="valasz2"></textarea><input type="radio" name="helyes" value="2" required>
                    </div>
                    <div>
                        <label for="valasz3">Válasz 3:<br></label>
                        <textarea name="valasz3" id="valasz3"></textarea><input type="radio" name="helyes" value="3" required>
                    </div>
                    <div>
                        <label for="valasz4">Válasz 4:<br></label>
                        <textarea name="valasz4" id="valasz4"></textarea><input type="radio" name="helyes" value="4" required>
                    </div><?php
                }
                ?><div class="submit"><input type="submit" value="<?=$button?>"></div>
                <?php cancelForm(); ?>
            </form>
        </div>
    </div><?php
}