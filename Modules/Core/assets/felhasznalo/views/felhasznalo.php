<?php

use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\Modulkezelo;
use Infoinventory\Classes\FormBuilder;
use Infoinventory\Classes\Settings;

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
$kep = ROOT_PATH . '/template/assets/images/default_profilkep.png';
if($this->felhasznalo->profilkep) {
    if(!is_null($this->felhasznalo->profilkep) && str_contains($this->felhasznalo->profilkep, 'uploads'))
        $kep = ROOT_PATH . $this->felhasznalo->profilkep;
    else
        $kep = "data:image/jpeg;base64," . base64_encode($this->felhasznalo->profilkep);
}
?><div class="normalcontent"><?php
    if(!$this->felhasznalo->uid) {
        ?><h1>Hiba</h1>
        <p>A felhasználó nem található!</p><?php
    }
    else
    {
        if($this->irasjog) {
            ?><a class="anchbutton" href="<?= ROOT_PATH . '/felhasznalo/' . $this->felhasznalo->usernev . '/szerkesztes' ?>">
                Felhasználó szerkesztése
            </a><?php
        }
        ?><div class="profilcard">
            <div class="profilkep">
                <div class="imgrightsep">
                    <img src="<?=$kep?>" alt="Profilkép" />
                </div>
            </div>
            <div class="profiladatok">
                <h1><?=$this->felhasznalo->nev?></h1>
                <p><?=$this->felhasznalo->leiras?></p>
                <p><?=$this->felhasznalo->usernev?></p>
                <p><?=$this->felhasznalo->szervezetnev?></p>
                <p><?=$this->felhasznalo->osztaly?></p>
                <p><?=$this->felhasznalo->beosztas?></p>
                <p><?=$this->felhasznalo->email?></p>
                <p><?=$this->felhasznalo->aktiv ? 'Aktív' : 'Inaktív'?></p>
                <p>Első bejelentkezés: <?=Helpers::SQLTimeStampToDate($this->felhasznalo->elsobelepes, true)?></p>
            </div>
            <div class="profiladatok">
                <h1>Csoporttagságok</h1><?php
                if(!empty($this->felhasznalo->csoporttagsagok)) {
                    ?><ul><?php
                    foreach($this->felhasznalo->csoporttagsagok as $csoport) {
                        ?><li><?= ($csoport['csoport_id'] == $this->felhasznalo->szervezet ? '<strong> ' : '') ?>
                                <?=$csoport['csoport_nev']?> (<?=($csoport['irasjog'] ? 'Írás' : 'Csak olvasás')?>)
                            <?= ($csoport['csoport_id'] == $this->felhasznalo->szervezet ? '</strong>' : '') ?>
                        </li><?php
                    }
                    ?></ul><?php
                } else {
                    ?><p>Nincs csoporttagság.</p><?php
                }
            ?></div>
            <div class="profiladatok">
                <h1>Modul jogosultságok</h1>
                <ul>
                    <?php foreach($this->felhasznalo->GetModulJogosultsagok() as $slug => $modul) { ?>
                        <li><?=$modul['modulnev']?> (Olvasás: <?= Modulkezelo::$jogosultsagnevek[$modul['olvasas']] ?? $modul['olvasas'] ?>, Írás: <?= Modulkezelo::$jogosultsagnevek[$modul['iras']] ?? $modul['iras'] ?>)</li>
                    <?php } ?>
                </ul>
            </div>
        </div><?php
    }
?></div>