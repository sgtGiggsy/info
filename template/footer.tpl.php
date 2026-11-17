<?php
namespace Infoinventory\template;

?><div class="footertop">
    <nav class="usermenu">
        <ul>
            <li><a href="<?=ROOT_PATH?>/impresszum">Impresszum</a></li>
            <li><a href="<?=ROOT_PATH?>/adatkezelesi-tajekoztato">Adatvédelmi irányelvek</a></li>
            <li><a href="<?=ROOT_PATH?>/media-ajanlat">Média ajánlat</a></li>
            <li><a href="<?=ROOT_PATH?>/kapcsolat">Kapcsolat</a></li>
        </ul>
    </nav>
</div>
<div class="footermiddle">
    <div>
        <?=GrafikaiElemek::$ikonok['oldallogo']?>
    </div>
    <div>
        <h4>Kövess minket közösségi médiáinkon is</h4>
        <ul class="socials">
            <li><a href="https://www.facebook.com/KaloriaFalo" target="_blank" rel="noopener noreferrer"><img src="<?=ROOT_PATH?>/template/assets/images/Facebook.png" alt="KalóriaFaló a Facebookon"  title="Facebookon is faljuk a kalóriákat" /></a></li>
            <li><a href="https://twitter.com/KaloriaFalo" target="_blank" rel="noopener noreferrer"><img src="<?=ROOT_PATH?>/template/assets/images/X.png" alt="KalóriaFaló az X-en"  title="X-en is faljuk a kalóriákat" /></a></li>
            <li><a href="https://instagram.com/KaloriaFalo" target="_blank" rel="noopener noreferrer"><img src="<?=ROOT_PATH?>/template/assets/images/Instagram.png" alt="KalóriaFaló az Instagramon" title="Instagramon is faljuk a kalóriákat" /></a></li>
        </ul>
    </div>
</div>
<div class="footerbottom">
    <small>&copy; <?= date('Y') ?> Király Béla - Minden jog fenntartva!</small>
</div>
