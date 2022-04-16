<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
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
    </div>

    <?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/epuletszerkeszt/$epid'\">Épület szerkesztése</button>" : "" ?>
    <div class="oldalcim"><?=$epulet['telephely']?> - <?=$epulet['epuletszam']?>. <?=$epulet['tipus']?> (<?=$epulet['nev']?>)</div><?php

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
                    foreach($rackek as $rack)
                    {
                        $rackid = $rack['id']
                        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/rack/<?=$rack['id']?>'>
                            <td><?=($rack['emelet'] == 0) ? "Földszint" : $rack['emelet'] . ". emelet" ?></td>
                            <td nowrap><?=$rack['helyisegszam']?> (<?=$rack['helyisegnev']?>)</td>
                            <td><?=$rack['nev']?></td>
                            <td><?=$rack['gyarto']?></td>
                            <td><?=$rack['unitszam']?></td>
                            <td><?=($csoportir) ? "<a href='$RootPath/rackszerkeszt/$rackid'><img src='$RootPath/images/edit.png' alt='Rack szerkesztése' title='Rack szerkesztése'/></a>" : "" ?></td>
                        </tr><?php
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

        ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/helyiseg/<?=$helyiseg['id']?>'>
            <td><?=$helyiseg['helyisegszam']?></td>
            <td><?=$helyiseg['helyisegnev']?></td>
            <td><a href='<?=$RootPath?>/helyisegszerkeszt/<?=$helyiseg['id']?>'><img src='<?=$RootPath?>/images/edit.png' alt='Helyiség szerkesztése' title='Helyiség szerkesztése'/></a></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}