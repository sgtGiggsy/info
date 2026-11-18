<?php

//print_r($this->csoport);


use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\ModuleLoader;

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

?><div class="normalcontent"><?php
    if(!$this->csoport) {
        ?><h1>Hiba</h1>
        <p>A csoport nem található!</p><?php
    }
    else
    {
        ?><div class="profilcard">
            <div class="profiladatok">
                <h1><?=$this->csoport['adatok']['csoport_nev']?></h1>
                <p><?=$this->csoport['adatok']['rovid_nev']?></p>
                <p><?=$this->csoportstatuszok[$this->csoport['adatok']['statusz']]?></p>
                <p><?=$this->csoport['adatok']['leiras']?></p>
            </div>
            <div class="profiladatok"><?php
            if($this->irasjog){
                ?><h2>LDAP szervezet beazonosító részlet</h2><?php
                foreach($this->csoport['ldap'] as $ldapazonosito) {
                    ?><p><?=$ldapazonosito['needle']?></p><?php
                }
            }
            ?></div>
            <div class="profiladatok">
                <h2>Tagok</h2><?php
                if(!empty($this->csoport['tagok'])) {
                    ?><ul><?php
                    foreach($this->csoport['tagok'] as $tag) {
                        ?><li>
                            <a href="<?=ROOT_PATH . '/felhasznalo/' . $tag['usernev']?>">
                                <?=($tag['sajatszervezet']) ? '<strong>' . $tag['nev'] . '</strong>' : $tag['nev']?>
                            </a>
                        </li><?php
                    }
                    ?></ul><?php
                } else {
                    ?><p>A csoportnak nincsenek tagjai.</p><?php
                }
            ?></div>
        </div><?php
    }
?></div>