<?php

use Infoinventory\Classes\Helpers;

if (!defined('ROOT_PATH') || !FELHASZNALO->uid) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1>Felhasználók</h1>
    <table class="regulartable">
        <thead>
            <tr>
                <th></th>
                <th>Felhasználó</th>
                <th>Szervezet</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($this->felhasznalok as $felhasznalo) {
                if(!is_null($felhasznalo->profilkep) && str_contains($felhasznalo->profilkep, 'uploads'))
                    $kep = ROOT_PATH . $felhasznalo->profilkep;
                elseif(!is_null($felhasznalo->profilkep))
                    $kep = "data:image/jpeg;base64," . base64_encode($felhasznalo->profilkep);
                else
                    $kep = ROOT_PATH . '/template/assets/images/default_profilkep.png';
                $keplink = "<img src='" . $kep . "' alt='" . $felhasznalo->usernev . " profilképe'>";
                ?><tr>
                    <?php Helpers::CellaLink(ROOT_PATH . '/felhasznalo/' . $felhasznalo->usernev, $keplink, $felhasznalo->nev, $felhasznalo->szervezetnev) ?>
                </tr><?php
            }
        ?></tbody>
    </table>
    <?=$this->Lapozo()?>
</div>
