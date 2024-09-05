<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/telefonszam?action=csvimport" method="post" enctype="multipart/form-data">
            <label for="csvinput">Importálni kívánt CSV:</label>
            <input type="file" name="csvinput" accept="text/csv" required>
            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form><?php
        cancelForm();
    ?></div><?php
}