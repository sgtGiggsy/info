<?php
if(@$irhat)
{
    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus
        FROM epuletek
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id;");
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/helyisegdb?action=<?=($_GET['page'] == "helyiseg" && isset($id)) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if($_GET['page'] == "helyiseg" && isset($id))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
            }
            ?><div>
                <label for="epulet">Épület:</label><br>
                <select id="epulet" name="epulet">
                    <option value=""></option><?php
                    foreach($epuletek as $x)
                    {
                        ?><option value="<?=$x["id"]?>" <?=($x['id'] == $epid) ? "selected" : "" ?>><?=$x['szam']?>. <?=$x['tipus']?> (<?=$x['nev']?>)</option><?php
                    }
                ?></select>
            </div>

            <div>
                <label for="emelet">Emelet:</label><br>
                <select id="emelet" name="emelet">
                    <option value=""></option>
                    <option value="0" <?=($emelet != null && $emelet == 0) ? "selected" : "" ?>>Földszint</option>
                    <option value="1" <?=($emelet == 1) ? "selected" : "" ?>>Első emelet</option>
                    <option value="2" <?=($emelet == 2) ? "selected" : "" ?>>Második emelet</option>
                    <option value="3" <?=($emelet == 3) ? "selected" : "" ?>>Harmadik emelet</option>
                    <option value="4" <?=($emelet == 4) ? "selected" : "" ?>>Negyedik emelet</option>
                </select>
            </div>

            <div>
                <label for="helyisegszam">Helyiség száma:</label><br>
                <input type="text" accept-charset="utf-8" name="helyisegszam" id="helyisegszam" value="<?=@$helyisegszam?>"></input>
            </div>

            <div>
                <label for="helyisegnev">Helyiség megnevezése:</label><br>
                <input type="text" accept-charset="utf-8" name="helyisegnev" id="helyisegnev" value="<?=@$helyisegnev?>"></input>
            </div>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$helyisegbutton?>"></div>
        </form>
    </div><?php
}