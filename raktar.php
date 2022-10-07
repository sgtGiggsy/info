<?php

if(!$sajatolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $szuresek = getWhere("raktarak.id = $id AND ((beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL) OR beepitesek.id IS NULL)");
    $where = $szuresek['where'];

    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            beepitesek.id AS beepid,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            alakulatok.rovid AS tulajdonos,
            beepitesek.nev AS beepitesinev,
            ipcimek.ipcim AS ipcim,
            beepitesek.megjegyzes AS megjegyzes,
            eszkozok.megjegyzes AS emegjegyzes,
            hibas
        FROM eszkozok
            INNER JOIN raktarak ON eszkozok.raktar = raktarak.id
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
        WHERE $where
        ORDER BY modellek.tipus, modellek.gyarto, modellek.modell, varians, sorozatszam;");

    $raktar = mySQLConnect("SELECT raktarak.id AS id,
                raktarak.nev AS raktar,
                epuletek.id AS epid,
                epuletek.nev AS epuletnev,
                epuletek.szam AS epuletszam,
                epulettipusok.tipus AS tipus,
                helyisegek.id AS helyisegid,
                helyisegszam,
                helyisegnev,
                alakulatok.rovid AS alakulat,
                telephelyek.id AS thelyid,
                telephelyek.telephely AS telephely
            FROM raktarak
                INNER JOIN helyisegek ON raktarak.helyiseg = helyisegek.id
                INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                INNER JOIN alakulatok ON raktarak.alakulat = alakulatok.id
                INNER JOIN telephelyek ON epuletek.telephely = telephelyek.id
                INNER JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            WHERE raktarak.id = $id");
    $raktar = mysqli_fetch_assoc($raktar);

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
                    href="<?=$RootPath?>/epuletek/<?=$raktar['thelyid']?>">
                <span property="name"><?=$raktar['telephely']?></span></a>
                <meta property="position" content="2">
            </li>
            <li><b>></b></li>
            <li property="itemListElement" typeof="ListItem">
                <a property="item" typeof="WebPage"
                    href="<?=$RootPath?>/epulet/<?=$raktar['epid']?>">
                <span property="name"><?=$raktar['epuletszam']?>. <?=$raktar['tipus']?></span></a>
                <meta property="position" content="3">
            </li>
            <li><b>></b></li>
            <li property="itemListElement" typeof="ListItem">
                <a property="item" typeof="WebPage"
                    href="<?=$RootPath?>/helyiseg/<?=$raktar['helyisegid']?>">
                <span property="name"><?=$raktar['helyisegszam']?>. helyiség (<?=$raktar['helyisegnev']?>)</span></a>
                <meta property="position" content="4">
            </li>
            <li><b>></b></li>
            <li property="itemListElement" typeof="ListItem">
                <span property="name"><?=$raktar['raktar']?></span>
                <meta property="position" content="4">
            </li>
        </ol>
    </div>

    <?=($mindir) ? "<button type='button' onclick=\"location.href='$RootPath/raktarszerkeszt/$id'\">Raktár szerkesztése</button>" : "" ?>
    <div class="PrintArea">
        <div class="oldalcim"><?=$raktar['raktar']?> raktár <?=$szuresek['szures']?> <?=raktarKeszlet(null, $szuresek['filter'])?></div><?php
        $zar = false;
        $szamoz = 1;
        foreach($mindeneszkoz as $eszkoz)
        {
            if(@$tipus != $eszkoz['tipus'])
            {
                if($zar)
                {
                    ?></tbody>
                    </table><?php
                }

                $tipus = $eszkoz['tipus']
                ?><h1 style="text-transform: capitalize;"><?=$tipus?></h1>
                <table id="<?=$tipus?>">
                <thead>
                    <tr>
                        <th class="tsorth" onclick="sortTable(0, 's', '<?=$tipus?>')">IP cím</th>
                        <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>')">Eszköznév</th>
                        <th class="tsorth" onclick="sortTable(2, 's', '<?=$tipus?>')">Gyártó</th>
                        <th class="tsorth" onclick="sortTable(3, 's', '<?=$tipus?>')">Modell</th>
                        <th class="tsorth" onclick="sortTable(4, 's', '<?=$tipus?>')">Sorozatszám</th>
                        <th class="tsorth" onclick="sortTable(8, 's', '<?=$tipus?>')">Tulajdonos</th>
                        <th class="tsorth" onclick="sortTable(9, 's', '<?=$tipus?>')">Beépítve</th>
                        <th class="tsorth" onclick="sortTable(10, 's', '<?=$tipus?>')">Kiépítve</th><?php
                        if($csoportir)
                        {
                            ?><th class="tsorth" onclick="sortTable(11, 's', '<?=$tipus?>')">Megjegyzés</th>
                            <th class="dontprint"></th>
                            <th class="dontprint"></th>
                            <th class="dontprint"></th><?php
                        }
                    ?></tr>
                </thead>
                <tbody><?php
                $zar = true;
            }
            

            $eszkid = $eszkoz['id'];
            $eszktip = eszkozTipusValaszto($eszkoz['tipusid']);

            ?><tr style='font-weight: normal <?= ($eszkoz['hibas'] == 2) ? "; text-decoration: line-through; color: grey" : (($eszkoz['hibas'] == 1) ? "; font-style: italic; color: grey" : "") ?>' class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' data-href='<?=$RootPath?>/<?=$eszktip['teljes']?>/<?=$eszkoz['id']?>'>
                <td><?=$eszkoz['ipcim']?></td>
                <td><?=$eszkoz['beepitesinev']?></td>
                <td><?=$eszkoz['gyarto']?></td>
                <td nowrap><?=$eszkoz['modell']?><?=$eszkoz['varians']?></td>
                <td><?=$eszkoz['sorozatszam']?></td>
                <td><?=$eszkoz['tulajdonos']?></td>
                <td nowrap><?=timeStampToDate($eszkoz['beepitesideje'])?></td>
                <td nowrap><?=timeStampToDate($eszkoz['kiepitesideje'])?></td><?php
                if($csoportir)
                {
                    ?><td><?=$eszkoz['megjegyzes']?><?=($eszkoz['megjegyzes'] && $eszkoz['emegjegyzes']) ? "<br>" : ""?><?=$eszkoz['emegjegyzes']?></td><?php
                    if($csoportir)
                    {
                        szerkSor($eszkoz['beepid'], $eszkoz['id'], $eszktip['tipus']);
                    }
                }
            ?></tr><?php
            $szamoz++;
        }
        ?></tbody>
        </table>
    </div><?php
}