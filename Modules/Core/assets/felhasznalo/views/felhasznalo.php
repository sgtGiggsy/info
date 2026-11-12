<?php

use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
$img = ROOT_PATH . '/template/assets/images/default_profilkep.png';
if($this->felhasznalo['profilkep']) {
    $img = ROOT_PATH . $this->felhasznalo['profilkep'];
}
?><div class="normalcontent"><?php
    if(FELHASZNALO->godadmin) {
        ?><a class="anchbutton" href="<?= ROOT_PATH . '/felhasznalo/szerkesztes/' . $this->felhasznalo['felhasznalo_id'] ?>">
            Felhasználó szerkesztése
        </a><?php
    }
    ?><div class="profilcard">
        <div class="profilkep">
            <div class="imgrightsep">
                <img src="<?=$img?>" alt="Profilkép" />
            </div>
        </div>
        <div class="profiladatok">
            <h1><?=$this->felhasznalo['usernev']?></h1>
            <p><?=$this->felhasznalo['teljesnev']?></p>
            <p><?=$this->felhasznalo['email']?></p>
            <p><?=$this->szintek[$this->felhasznalo['szint']]?></p>
            <p>Tag <?=Helpers::SQLTimeStampToDate($this->felhasznalo['regisztracio'], true)?> óta</p>
        </div>
    </div>
</div>