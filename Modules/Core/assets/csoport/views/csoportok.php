<?php

use Infoinventory\Classes\Helpers;

if (!defined('ROOT_PATH') || !FELHASZNALO->uid) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <?=$this->RenderKereso()?>
    <h1>Csoportok</h1>
    <table class="regulartable">
        <thead>
            <tr>
                <th></th>
                <th>Szervezet</th>
                <th>Cél</th>
                <th>Tagok száma</th>
                <th>Leírás</th>
            </tr>
        </thead>
        <tbody><?php
            $irhatocsoportok = FELHASZNALO->GetIrhatoCsoportIDs();
            foreach($this->csoportok['dataset'] as $csoport) {
                ?><tr<?=(in_array($csoport['csoport_id'], $irhatocsoportok)) ? ' class="bold"' : '' ?>>
                    <?php Helpers::CellaLink(ROOT_PATH . '/csoport/' . $csoport['slug'], null, $csoport['csoport_nev'], $this->csoportstatuszok[$csoport['statusz']], $csoport['tagok_szama'], $csoport['leiras']) ?>
                </tr><?php
            }
        ?></tbody>
    </table>
    <?=$this->RenderLapozo()?>
</div>
