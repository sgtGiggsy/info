<?php

use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;

if (!defined('ROOT_PATH') || !FELHASZNALO->godadmin) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1>Felhasználók</h1>
    <table class="regulartable">
        <thead>
            <tr>
                <th></th>
                <th>Felhasználónév</th>
                <th>Felhasználói szint</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($this->felhasznalo as $felhasznalo) {
                $kep = ($felhasznalo['profilkep']) ? ROOT_PATH . $felhasznalo['profilkep'] : ROOT_PATH . '/template/assets/images/default_profilkep.png';
                $keplink = "<img src='" . $kep . "' alt='" . $felhasznalo['usernev'] . " profilképe'>";
                ?><tr>
                    <?= Helpers::CellaLink(ROOT_PATH . '/felhasznalo/' . $felhasznalo['felhasznalo_id'], $keplink, $felhasznalo['usernev'], $this->szintek[$felhasznalo['szint']]) ?>
                </tr><?php
            }
        ?></tbody>
    </table>
    <?=$this->Lapozo()?>
</div>
