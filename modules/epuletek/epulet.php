<?php

// Ha nincs olvasási jog, vagy van írási kísérlet írási jog nélkül, letilt
if(!@$mindolvas || (isset($_GET['action']) && !$mindir))
{
    getPermissionError();
}
else
{
    $alapform = "modules/epuletek/forms/";
    // Amíg nem tudjuk, hogy a folyamat jár-e tényleges írással, a változót false-ra állítjuk
    $dbir = false;

    // Amíg nem tudjuk, hogy a felhasználó valós műveletet akar végezni, a változót false-ra állítjuk
    $irhat = false;

    // Ellenőrizzük, hogy volt-e műveletvégzésre irányuló kérdés
    if(isset($_GET['action']))
    {
        // Ha a kért művelet nem a szerkesztő oldal betöltése, az adatbázis változót true-ra állítjuk
        if($_GET['action'] == "new" || $_GET['action'] == "update" || $_GET['action'] == "delete")
        {
            $irhat = true;
            $dbir = true;
        }

        // Ha a kért művelet a szerkesztő oldal betöltése, az írás változót true-ra állítjuk
        if($_GET['action'] == "addnew" || $_GET['action'] == "edit")
        {
            $irhat = true;
        }
    }

    // Ha a felhasználó valótlan műveletet akart folytatni, letilt, de olvasási joggal továbbenged
    if(!$irhat && !$dbir && !$mindolvas)
    {
        getPermissionError();
    }

    // Ha a kért művelet jár adatbázisművelettel, az adatbázis műveletekért felelős oldal meghívása
    elseif($irhat && $dbir && count($_POST) > 0)
    {
        include("./modules/epuletek/db/epuletdb.php");

        // Az adatbázisműveleteket követő folyamatokat lebonyolító függvény meghívása
        afterDBRedirect($con);
    }

    // Ha a kért művelet nem jár adatbázisművelettel, a szerkesztési felület meghívása
    elseif($irhat && !$dbir)
    {
        $szam = $telephely = $nev = $tipus = $emelet = $magyarazat = null;
        $beuszok = array();

        $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
        $epulettipusok = mySQLConnect("SELECT * FROM epulettipusok;");
        $tulajdonosok = mySQLConnect("SELECT * FROM alakulatok;");
        $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
        
        $helyisegbutton = "Új helyiség";
        $button = "Új épület";
        $oldalcim = "Új épület hozzáadása";
        $form = $alapform . "epuletform";
        
        if(isset($id))
        {
            $epulet = mySQLConnect("SELECT * FROM epuletek WHERE id = $id;");
            $epulet = mysqli_fetch_assoc($epulet);

            $szam = $epulet['szam'];
            $telephely = $epulet['telephely'];
            $nev = $epulet['nev'];
            $tipus = $epulet['tipus'];

            $button = "Szerkesztés";
            $oldalcim = "Épület szerkesztése";

            // Azért van erre szükség, mert a helyisegszerkform-ot a Helyiség oldal is meghívhatja, ahol más értéket vesz fel az $id változó
            $epid = $id;

            $beuszok[] = array('cimszoveg' => 'Helyiségek generálása', 'formnev' => $alapform . 'helyiseggenform');
            $beuszok[] = array('cimszoveg' => 'Végpontok generálása', 'formnev' => $alapform . 'vegpontgenform');
            $beuszok[] = array('cimszoveg' => 'Új helyiség létrehozása', 'formnev' => $alapform . 'helyisegszerkform');
        }

        include('./templates/edit.tpl.php');
    }

    // Ha írási művelet nem lesz, ellenőrizni kell, hogy van-e kiválasztott épület. Ha nincs, hiba dobása
    elseif(!isset($id))
    {
        getPermissionError();
    }

    // Akkor futunk ki erre az ágra, ha van olvasási jog, és kiválasztott épület, de más nincs. Ez a sima megjelenítő felület
    else
    {
        $epid = $_GET['id'];
        $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam AS epuletszam, epuletek.nev AS nev, telephelyek.telephely AS telephely, telephelyek.id AS thelyid, epulettipusok.tipus AS tipus
            FROM epuletek
                LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
                LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            WHERE epuletek.id = $epid;");
        $helyisegek = mySQLConnect("SELECT id, helyisegszam, helyisegnev, emelet
            FROM helyisegek
            WHERE epulet = $epid
            ORDER BY emelet ASC, helyisegszam ASC;");
        $rackek = mySQLConnect("SELECT rackszekrenyek.id AS id, rackszekrenyek.nev AS nev, gyartok.nev AS gyarto, unitszam, helyisegszam, helyisegnev, emelet
            FROM rackszekrenyek
                INNER JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
                LEFT JOIN gyartok ON rackszekrenyek.gyarto = gyartok.id
            WHERE epulet = $epid
            ORDER BY emelet, helyisegszam + 0;");
        
        $epulet = mysqli_fetch_assoc($epuletek);

        ?><div class="breadcumblist">
            <ol vocab="https://schema.org/" typeof="BreadcrumbList">
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/">
                    <span property="name">Kecskemét Informatika</span></a>
                    <meta property="position" content="1">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epuletek/<?=$epulet['thelyid']?>">
                    <span property="name"><?=$epulet['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$epulet['epuletszam']?>. <?=$epulet['tipus']?></span>
                    <meta property="position" content="3">
                </li>
            </ol>
        </div><?php

        if($mindir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Épület szerkesztése</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick=rejtMutat("elozmenyek")>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }

        ?><div class="oldalcim"><?=$epulet['telephely']?> - <?=$epulet['epuletszam']?>. <?=$epulet['tipus']?> (<?=$epulet['nev']?>)</div><?php
        $ujoldalcim = $ablakcim . " - ". $epulet['telephely'] . " - " . $epulet['epuletszam'] . ". " . $epulet['tipus'] . " (" . $epulet['nev'] . ")";

        if(mysqli_num_rows($rackek) > 0)
        {
            ?><div class="oldalcim">Rackszekrények az épületben</div>
            <div>
                <table id="rackek">
                    <thead>
                        <tr>
                            <th class="tsorth" onclick="sortTable(0, 's', 'rackek')">Emelet</th>
                            <th class="tsorth" onclick="sortTable(1, 's', 'rackek')">Helyiség</th>
                            <th class="tsorth" onclick="sortTable(2, 's', 'rackek')">Azonosító</th>
                            <th class="tsorth" onclick="sortTable(3, 's', 'rackek')">Gyártó</th>
                            <th class="tsorth" onclick="sortTable(4, 'i', 'rackek')">Unitszám</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php
                        $szamoz = 1;
                        foreach($rackek as $rack)
                        {
                            $rackid = $rack['id']
                            ?><tr class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' data-href='<?=$RootPath?>/rack/<?=$rack['id']?>'>
                                <td><?=($rack['emelet'] == 0) ? "Földszint" : $rack['emelet'] . ". emelet" ?></td>
                                <td nowrap><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</td>
                                <td><?=$rack['nev']?></td>
                                <td><?=$rack['gyarto']?></td>
                                <td><?=$rack['unitszam']?></td>
                                <td><?=($csoportir) ? "<a href='$RootPath/rack/$rackid?action=edit'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td>
                            </tr><?php
                            $szamoz++;
                        }
                    ?></tbody>
                </table>
            </div><?php
        }

        ?><div class="oldalcim">Helyiségek</div><?php
        $zar = false;
        foreach($helyisegek as $helyiseg)
        {
            if(@$emelet != $helyiseg['emelet'])
            {
                $szamoz = 1;
                if($zar)
                {
                    ?></tbody>
                    </table><?php
                }

                $emelet = $helyiseg['emelet'];
                ?><h1><?=($helyiseg['emelet'] == 0) ? "Földszint" : $helyiseg['emelet'] . ". emelet" ?></h1>
                <table id="<?=$emelet?>">
                <thead>
                    <tr>
                        <th class="tsorth" onclick="sortTable(0, 'i', '<?=$emelet?>')">Helyiség száma</th>
                        <th class="tsorth" onclick="sortTable(1, 's', '<?=$emelet?>')">Helyiség megnevezése</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody><?php
                $zar = true;
            }

            ?><tr class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' data-href='<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>'>
                <td><?=$helyiseg['helyisegszam']?></td>
                <td><?=$helyiseg['helyisegnev']?></td>
                <td><a href='<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>?action=edit'><img src='<?=$RootPath?>/images/edit.png' alt='Helyiség szerkesztése' title='Helyiség szerkesztése'/></a></td>
            </tr><?php
            $szamoz++;
        }
        ?></tbody>
        </table><?php
    }
}