<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/rack<?=(isset($id)) ? '?id=' . $id . '&action=update' : '?action=new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
            if(isset($_GET['id']))
            {
                ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
            }
            ?><div>
                <label for="nev">Rack neve:</label><br>
                <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$racknev?>"></input>
            </div>

            <div>
                <label for="unitszam">Rack unitszáma:</label><br>
                <input type="text" accept-charset="utf-8" name="unitszam" id="unitszam" value="<?=$rackunitszam?>"></input>
            </div>

            <?=helyisegPicker($rackhely, "helyiseg")?>

            <?=gyartoPicker($rackgyarto)?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form><?php
        cancelForm();
    ?></div><?php
}