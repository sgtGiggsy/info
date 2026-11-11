<?php

use Infoinventory\Classes\FormBuilder;

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?>
<h1>Új közlemény rögzítése</h1>
<form action="<?=ROOT_PATH?>/kozlemeny/uj" method="post" enctype="multipart/form-data" class="receptoldal">
    <div class="formbuilder">
        <?=$this->form->Render();?>
        <div class="submit"><input type="submit" value="Közlemény rögzítése"></div>
    </div>
    <div>
        <div>
            <label for="kepek">Kép csatolása a közleményhez</label>
            <div class="dropzone" id="dropzone">
                <p>Húzd ide a fájlokat vagy kattints</p>
                <input type="file" name="kepek[]" id="kepek" accept="image/jpeg, image/png, image/bmp, image/webp" multiple>
            </div>
            <div id="preview"></div>
        </div>
    </div>
</form>