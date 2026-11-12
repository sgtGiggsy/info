<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1><?=$this->felhasznalo['usernev']?> profiljának szerkesztése</h1>
    <form action="<?=ROOT_PATH?>/felhasznalo/szerkesztes/<?=$this->felhasznalo['felhasznalo_id']?>" method="post" enctype="multipart/form-data">
        <?= $this->form->Render() ?>
        <div>
            <label for="profilkep">Profilkép</label>
            <input type="file" name="profilkep" id="profilkep" accept="image/jpeg, image/png, image/bmp, image/webp">
        </div>
        <div class="submit"><input type="submit" value="Módosítások mentése"></div>
    </form>
</div>