<?php
?><div class="headlogo" id="headlogo">
    <a href="<?=$RootPath?>">
        <img src="<?=$RootPath?>/images/headlogo.png" width="180px" height="45px" alt="Oldal logó" title="Oldal logó"></a>
</div><?php

if(isset($enablekeres) && $enablekeres)
{
    $keresoszoveg = null;
    if(isset($_GET['kereses']))
        $keresoszoveg = $_GET['kereses'];
    ?><div class="kereses" id="kereses">
        <form name="kereses" method="GET">
            <div class="felkormezo">
                <input type="search" name="kereses" id="searchbar" placeholder="Keresés" aria-label="Keresés" <?=($keresoszoveg) ? "value=\"{$keresoszoveg}\"" : "" ?> />
                <button class="felkorbutton" class="searchicon"><?=$icons['search']?></button>
            </div>
        </form>
    </div><?php
}

if(isset($contextmenujogok))
{
    ?><div class="topmenuszoveges"><?php
        ContextMenu();
    ?></div><?php
}

?><div class="topmenuikonok"><?php
if($_SESSION['id'])
{
    $usernev = $_SESSION['nev'];
    TopMenu();
    $notifications = Ertesites::GetErtesitesek();

    ?><div id="notifications">
        <p onclick="showPopup('notifpopup');updateNotif()">
            <?=$icons['notifications']?>
        </p>
        <div id="notifcount" onclick="showPopup('notifpopup')" style="display: <?=($notifications['olvasatlanszam'] > 0) ? 'block' : 'none' ?> "><?=$notifications['olvasatlanszam']?></div>
    </div>
    
    <p class="profil" onclick="showPopup('profilpopup')"> 
        <img src= <?=($_SESSION['profilkep']) ? "data:image/jpeg;base64," . base64_encode($_SESSION["profilkep"]) : "$RootPath/images/profil.png " ?> title="<?=$usernev?>" alt="<?=$usernev?>">
    </p><?php
}
else
{
    ?><a href="<?=$RootPath?>/belepes" title="Belépés" alt="Belépés"><span>Belépés</span><!--<img src="<?=$RootPath?>/images/login.png">--></a><?php
}
?></div><?php

// Előugró menük
//// Értesítések
if($_SESSION['id'])
{
    ?><div id="notifpopup" onmouseleave="hidePopup('notifpopup')"><?php
        foreach($notifications['ertesitesek'] as $notification)
        {
            $kotkarakter = "?";
            if(str_contains($notification['url'], "?"))
            {
                $kotkarakter = "&";
            }
            ?><a href="<?=$RootPath?>/<?=$notification['url']?><?=$kotkarakter?>ertesites=<?=$notification['id']?>">
                <div id="notif-<?=$notification['id']?>" class="notifitem<?=($notification['latta']) ? '-latta' : '' ?>">
                    <p class="notiftitle"><?=$notification['cim']?></p>
                    <p class="notifbody"><?=$notification['szoveg']?></p>
                    <p class="notiftime"><?=$notification['timestamp']?></p>
                </div>
            </a><?php
        }
        ?><div id="seenallnotif"><p onclick="seenAllNotif();reloadPageDelay(1000)">Mind megnézve</p></div>
    </div><?php

//// Profil
    ?><div id="profilpopup" onmouseleave="hidePopup('profilpopup')">
        <a href="<?=$RootPath?>/felhasznalo"><?=$icons['person']?>Profil</a>
        <a href="<?=$RootPath?>/felhasznalo?beallitasok"><?=$icons['customize']?>Beállítások</a>
        <a href="<?=$RootPath?>/kilep"><?=$icons['logout']?>Kilépés</a>
    </div><?php
}