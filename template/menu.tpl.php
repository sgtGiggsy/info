<?php

namespace Infoinventory\template;

use Infoinventory\Classes\Helpers;
use Infoinventory\Classes\ModuleLoader;
use Infoinventory\Classes\Settings;
use Infoinventory\includes\Jogszint;

$navopen = false;
$parent = null;
$jobbraigazit = false;
//print_r(GrafikaiElemek::$ikonok);
?><ul class="mobile">
    <li class="dropdown">
        <button class="menubutton" id="menunyitzar" aria-label="Menü">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </li>
    <li>
        <a href="<?=ROOT_PATH?>/"><?=GrafikaiElemek::$ikonok['oldalminilogo']?></a>
    </li>
    <li></li>
</ul>
<ul class="links" id="linkek"><?php
    foreach (ModuleLoader::Menupontok() as $kulcs => $menuelem) {
        if($menuelem['menuben']
            && ($menuelem['lathatosag'] == "mindenki"
                || ($menuelem['lathatosag'] != "vendeg" && FELHASZNALO->uid)
                || ($menuelem['lathatosag'] == "vendeg" && !FELHASZNALO->uid))
            ) {

            if($menuelem['ikon']) {
                $renderelem = GrafikaiElemek::$ikonok[$menuelem['ikon']] . "<span class='fomenuelem onlymobile'>" . $menuelem['menupont'] . "</span>";
            }
            else {
                $renderelem = $menuelem['menupont'];
            }

            $gyokerelem = $menuelem['szulo'] === true || $menuelem['szulo'] === null;

            if($kulcs == 'fooldal')
                $url = ROOT_PATH;
            elseif($menuelem['szulo'] === true)
                $url = "#";
            else
                $url = ROOT_PATH . "/" . $kulcs;

            if($kulcs == 'felhasznalo')
                $menuelem['menupont'] = FELHASZNALO->usernev;

            if($gyokerelem && $navopen) {
                ?></nav></li><?php
                $navopen = false;
            }

            if($menuelem['szulo'] === true) {
                $parent = $kulcs;
                $navopen = true;
            }

            if(!$jobbraigazit && ($kulcs == 'adminisztracio' || $kulcs == 'felhasznalo' || $kulcs == 'belepes')) {
                $jobbraigazit = true;
                ?><li id="menugrow"></li><?php
            }

            ?><li<?=($gyokerelem) ? ' class="fomenugyoker"' : ''?> <?=($kulcs == 'fooldal') ? ' id="menu_oldallogo"' : ''?>>
                <a href="<?=$url?>"<?=($gyokerelem && !$menuelem['ikon']) ? ' class="fomenuelem"' : '' ?>>
                    <?=(!$gyokerelem) ? "<span>" : ""?>
                        <?=$renderelem?>
                    <?=(!$gyokerelem) ? "</span>" : ""?>
                </a>
            <?= ($menuelem['szulo'] === true) ? '<nav class="dropdown-content">' : '</li>' ?><?php
        }
    }
    if($navopen) {
        ?></nav></li><?php
    }
?></ul>
