<?php
if(@$irhat)
{
    ?><form action="<?=$RootPath?>/feladatterv&action=<?=(isset($id)) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        if(isset($id))
        {
            ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
        }
        ?><input type ="hidden" id="szulo" name="szulo" value=<?=$szulo?>>

        <div>
            <label for="rovid">Rövid összegzés</label><br>
            <textarea name="rovid"id="rovid" placeholder="Feladat rövid leírása"></textarea>
        </div>

        <div>
            <label for="leiras">Részletes leírás</label><br>
            <textarea name="rovid"id="rovid" placeholder="Feladat rövid leírása"></textarea>
        </div>

        <div>
            <label for="szak">Szakterület</label>
            <select name="szak" id="szak">
                <option value="0"></option><?php
                foreach($szakok as $szak)
                {
                    ?><option value="<?=$szak['id']?>"><?=$szak['nev']?></option><?php
                }
            ?></select>
        </div>

        <?php priorityPicker($currpri); ?>

        <?php epuletPicker($currbuild) ?>

        <div>
            <label for="ido_tervezett">Feladatt elvégzésének tervezett ideje</label><br>
            <input type="datetime-local" id="ido_tervezett" name="ido_tervezett" value="<?=$ido_tervezett?>"><button style="margin-left: 10px;" onclick="getMost('ido_tervezett'); return false;" type="button">Most</button>
        </div>

        <div>
            <label for="ido_hatarido">Feladat határideje</label><br>
            <input type="datetime-local" id="ido_hatarido" name="ido_hatarido" value="<?=$ido_hatarido?>"><button style="margin-left: 10px;" onclick="getMost('ido_hatarido'); return false;" type="button">Most</button>
        </div>

        <div>
            <label for="ido_tenyleges">Feladat végrehajtása befejezve</label><br>
            <input type="datetime-local" id="ido_tenyleges" name="ido_tenyleges" value="<?=$ido_tenyleges?>"><button style="margin-left: 10px;" onclick="getMost('ido_tenyleges'); return false;" type="button">Most</button>
        </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
    cancelForm();
}