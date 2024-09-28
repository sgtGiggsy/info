<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/portdb?action=generate&tipus=<?=$generalandotipus?>" method="post" onsubmit="beKuld.disabled = true; return true;">
            <input type ="hidden" id="epulet" name="epulet" value=<?=$id?>>
            
            <div>
                <label for="portelotag">Port előtag<small> (pl R11/)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="portelotag" id="portelotag"></input>
            </div>

            <div>
                <label for="kezdoport">Kezdő port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="kezdoport" id="kezdoport"></input>
            </div>

            <div>
                <label for="zaroport">Záró port<small> (csak a száma)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="zaroport" id="zaroport"></input>
            </div>

            <div>
                <label for="nullara">Nullára kiegészítés számjegye<br><small> (pl.: ha 001 legyen 1 helyett, akkor 3)</small>:</label><br>
                <input type="text" accept-charset="utf-8" name="nullara" id="nullara"></input>
            </div>

            <div>
                <label for="csatlakozo">Csatlakozó típusa:</label><br>
                <select id="csatlakozo" name="csatlakozo"><?php
                    foreach($csatlakozok as $x)
                    {
                        ?><option value="<?=$x["id"]?>"><?=$x['nev']?></option><?php
                    }
                ?></select>
            </div><?php

            if($generalandotipus == "transzport")
            {
                ?><div>
                    <label for="fizikaireteg">Transzporthálózat típusa:</label><br>
                    <select id="fizikaireteg" name="fizikaireteg">
                        <option value="" selected></option><?php
                        foreach($fizikairetegek as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['nev']?></option><?php
                        }
                    ?></select>
                </div><?php
            }
            ?><div class="submit"><input type="submit" name="beKuld" value="<?=$gomb?>"></div>
        </form>
    </div><?php
}