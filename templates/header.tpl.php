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
                <button class="felkorbutton" class="searchicon">
                    <svg enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24" width="24" focusable="false">
                        <path d="m20.87 20.17-5.59-5.59C16.35 13.35 17 11.75 17 10c0-3.87-3.13-7-7-7s-7 3.13-7 7 3.13 7 7 7c1.75 0 3.35-.65 4.58-1.71l5.59 5.59.7-.71zM10 16c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"></path>
                    </svg>
                </button>
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