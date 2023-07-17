<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/vizsga/<?=$vizsgaadatok['url']?>/kerdesszerkeszt&action=<?=(isset($_GET['id'])) ? "update" : "addnew" ?>" method="post" enctype="multipart/form-data" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
                }
                ?><div>
                    <label for="kerdes">Kérdés szövege:</label><br>
                    <textarea name="kerdes" id="kerdes"><?=$kerdesszoveg?></textarea>
                </div>

                <div>
                    <label for="kerdeskep">Kép csatolása a kérdéshez:</label><br>
                    <input type="file" name="kerdeskep" id="kerdeskep" accept="image/jpeg, image/png, image/bmp">
                </div><?php

                if(isset($_GET['id']) && $kerdes['kep'])
                {
                    ?><div>
                        <img src="<?=$kep?>" width="500px">
                        <label for="keptorol">Feltöltött kép eltávolítása</label>
                        <input type="checkbox" name="keptorol" />
                    </div><?php
                }

                $valaszlehetosegszam = 4;
                if(count($valaszlehetosegek) > 0)
                {
                    $valaszlehetosegszam = count($valaszlehetosegek);
                }

                for($i = 1; $i <= $valaszlehetosegszam; $i++)
                {
                    ?><div>
                        <label for="valasz<?=$i?>">Válasz <?=$i?>:<br></label>
                        <textarea name="valasz[]" id="valasz<?=$i?>"><?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszszoveg'] : "" ?></textarea>
                        <input type="checkbox"
                                name="helyes[<?=$i?>]"
                                id="helyes<?=$i?>"
                                value="<?=(count($valaszlehetosegek) > 0) ? $valaszlehetosegek[$i-1]['valaszid'] : $i ?>"
                                <?=(count($valaszlehetosegek) > 0 && $valaszlehetosegek[$i-1]['helyes']) ? "checked" : "" ?>><?php
                                if(count($valaszlehetosegek) > 0)
                                {
                                    ?><input type ="hidden" id="vid<?=$i?>" name="vid[<?=$i?>]" value=<?=$valaszlehetosegek[$i-1]['valaszid']?> /><?php
                                }
                      ?></div><?php
                }
                ?><div class="submit"><input type="submit" value="<?=$button?>"></div>
                <?php cancelForm(); ?>
            </form>
        </div>
    </div><?php
}