<?php
?><div class="headlogo" id="headlogo">
    <a href="<?=$RootPath?>">
        <img src="<?=$RootPath?>/images/headlogo.png" height="45px"></a>
</div><?php

if(isset($enablekeres) && $enablekeres)
{
    ?><div class="kereses" id="kereses">
        <form name="kereses" method="GET">
            <div class="felkormezo">
                <input type="text" name="kereses" placeholder="Keresés" aria-label="Keresés" />
                <button class="felkorbutton" class="searchicon"><?=$searchicon?></button>
            </div>
        </form>
    </div><?php
}

if(isset($topmenuszoveges) && $topmenuszoveges)
{
    ?><div class="topmenuszoveges"><?php
        $menuterulet = 3; include('./includes/menu.inc.php');
    ?></div><?php
}

?><div class="topmenuikonok"><?php
if($_SESSION[getenv('SESSION_NAME').'id'])
{
    $usernev = $_SESSION[getenv('SESSION_NAME').'nev'];
    $menuterulet = 2; include('./includes/menu.inc.php');
    $notifications = getNotifications();
    $ujertesites = 0;
    foreach($notifications as $notification)
    {
        if(!$notification['latta'])
        {
            $ujertesites++;
        }
    }

    ?><div id="notifications">
        <a style="cursor: pointer" onclick="showPopup('notifpopup');updateNotif()">
            <img src="<?=$RootPath?>/images/notification.png" title="Értesítések" alt="Értesítések">
        </a>
        <div id="notifcount" onclick="showPopup('notifpopup')" style="display: <?=($ujertesites) ? 'block' : 'none' ?> "><?=$ujertesites?></div>
    </div>
    
    <a style="cursor: pointer" onclick="showPopup('profilpopup')"> 
        <img src= <?=($_SESSION['profilkep']) ? "data:image/jpeg;base64," . base64_encode($_SESSION["profilkep"]) : "$RootPath/images/profil.png " ?> title="<?=$usernev?>" alt="<?=$usernev?>">
    </a><?php
}
else
{
    ?><a href="<?=$RootPath?>/belepes" title="Belépés" alt="Belépés"><span>Belépés</span><!--<img src="<?=$RootPath?>/images/login.png">--></a><?php
}
?></div><?php

// Előugró menük
//// Értesítések
if($_SESSION[getenv('SESSION_NAME').'id'])
{
    ?><div id="notifpopup" onmouseleave="hidePopup('notifpopup')"><?php
        foreach($notifications as $notification)
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
        <a href="<?=$RootPath?>/felhasznalo">Profil</a>
        <a href="<?=$RootPath?>/felhasznalo?beallitasok">Beállítások</a>
        <a href="<?=$RootPath?>/kilep">Kilépés</a>
    </div><?php
}