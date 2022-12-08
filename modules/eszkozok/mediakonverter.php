<?php

// Elsőként annak ellenőrzése, hogy a felhasználó olvashatja-e,
// majd megvizsgálni, hogy ha olvashatja, de írni szeretné, ahhoz van-e joga
if(!@$csoportolvas || (isset($_GET['action']) && !$csoportir) || (!isset($_GET['action']) && !$id))
{
    getPermissionError();
}
// Ha van valamilyen módosítási kísérlet, ellenőrizni, hogy van-e rá joga a felhasználónak
elseif(isset($_GET['action']) && $csoportir)
{
    $meghiv = true;
    
    // Az eszközszerkesztő oldal includeolása
    include('./modules/eszkozok/includes/eszkozszerkeszt.inc.php');
}
else
{
	$csoportwhere = null;
    if(!$mindolvas)
    {
        // A CsoportWhere űrlapja
        $csopwhereset = array(
            'tipus' => null,                        // A szűrés típusa, null = mindkettő, alakulat = alakulat, telephely = telephely
            'and' => true,                          // Kerüljön-e AND a parancs elejére
            'alakulatelo' => null,                  // A tábla neve, ahonnan az alakulat neve jön
            'telephelyelo' => "epuletek",           // A tábla neve, ahonnan a telephely neve jön
            'alakulatnull' => false,                // Kerüljön-e IS NULL típusú kitétel a parancsba az alakulatszűréshez
            'telephelynull' => true,                // Kerüljön-e IS NULL típusú kitétel a parancsba az telephelyszűréshez
            'alakulatmegnevezes' => "tulajdonos"    // Az alakulatot tartalmazó mező neve a felhasznált táblában
        );

        $csoportwhere = csoportWhere($csoporttagsagok, $csopwhereset);
    }
    
    $mindeneszkoz = mySQLConnect("SELECT
            eszkozok.id AS id,
            sorozatszam,
            gyartok.nev AS gyarto,
            modellek.modell AS modell,
            varians,
            eszkoztipusok.nev AS tipus,
            epuletek.id AS epuletid,
            epuletek.nev AS epuletnev,
            epuletek.szam AS epuletszam,
            alakulatok.nev AS tulajdonos,
            helyisegek.id AS helyisegid,
            helyisegszam,
            helyisegnev,
            beepitesideje,
            kiepitesideje,
            modellek.tipus AS tipusid,
            beepitesek.nev AS beepitesinev,
            beepitesek.id AS beepid,
            beepitesek.megjegyzes AS megjegyzes,
            epulettipusok.tipus AS epulettipus,
            telephelyek.telephely AS telephely,
            telephelyek.id AS thelyid,
            helyisegek.id AS helyisegid,
            fizikairetegek.nev AS fizikaireteg,
            transzpszabvany,
            transzpcsatlakozo,
            transzpsebesseg,
            lanszabvany,
            lancsatlakozo,
            lansebesseg,
            vlanok.nev AS vlan,
            raktarak.nev AS raktar,
            (SELECT nev FROM atviteliszabvanyok WHERE id = transzpszabvany) AS transzportszabvany,
            (SELECT nev FROM atviteliszabvanyok WHERE id = lanszabvany) AS lanportszabvany,
            (SELECT sebesseg FROM sebessegek WHERE id = transzpsebesseg) AS transzportsebesseg,
            (SELECT sebesseg FROM sebessegek WHERE id = lansebesseg) AS lanportsebesseg,
            (SELECT nev FROM csatlakozotipusok WHERE id = transzpcsatlakozo) AS transzportcsatlakozo,
            (SELECT nev FROM csatlakozotipusok WHERE id = lancsatlakozo) AS lanportcsatlakozo
        FROM eszkozok
            INNER JOIN modellek ON eszkozok.modell = modellek.id
            LEFT JOIN mediakonvertermodellek ON mediakonvertermodellek.modell = modellek.id
            INNER JOIN gyartok ON modellek.gyarto = gyartok.id
            INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
            LEFT JOIN beepitesek ON beepitesek.eszkoz = eszkozok.id
            LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
            LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
            LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN alakulatok ON eszkozok.tulajdonos = alakulatok.id
            LEFT JOIN fizikairetegek ON mediakonvertermodellek.fizikaireteg = fizikairetegek.id
            LEFT JOIN vlanok ON beepitesek.vlan = vlanok.id
            LEFT JOIN raktarak ON eszkozok.raktar = raktarak.id
        WHERE eszkozok.id = $id $csoportwhere
        ORDER BY beepitesek.id DESC;");
	
	if(mysqli_num_rows($mindeneszkoz) == 0)
    {
        echo "Nincs ilyen sorszámú médiakonverter";
    }
    else
    {
		$eszkoz = mysqli_fetch_assoc($mindeneszkoz);

        $epuletid = $eszkoz['epuletid'];
        $helyisegid = $eszkoz['helyisegid'];

        $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
        $switchportok = mySQLConnect("SELECT mediakonverterportok.id AS id, eszkoz, sebesseg, portok.port, csatlakozo, portok.id AS portid, csatlakozas, szabvany
            FROM mediakonverterportok
                INNER JOIN portok ON mediakonverterportok.port = portok.id
                WHERE eszkoz = $id;");

        if($epuletid)
        {
            $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, null AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                    WHERE epulet = $epuletid
                UNION
                    SELECT portok.id AS id, portok.port AS port, null AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN transzportportok ON transzportportok.port = portok.id
                    WHERE epulet = $epuletid
                UNION
                    SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN switchportok ON portok.id = switchportok.port
                        INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
                        INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                        LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                        LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                    WHERE helyisegek.id = $helyisegid AND eszkozok.id != $id AND beepitesek.kiepitesideje IS NULL
                UNION
                    SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
                    FROM portok
                        INNER JOIN sohoportok ON portok.id = sohoportok.port
                        INNER JOIN eszkozok ON sohoportok.eszkoz = eszkozok.id
                        INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
                        LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                        LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
                    WHERE helyisegek.id = $helyisegid AND eszkozok.id != $id AND beepitesek.kiepitesideje IS NULL
                    ORDER BY aktiveszkoz, port;");
        }
        
        $csatlakozotipusok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
        $atviteliszabvanyok = mySQLConnect("SELECT * FROM atviteliszabvanyok;");

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
                        href="<?=$RootPath?>/epuletek/<?=$eszkoz['thelyid']?>">
                    <span property="name"><?=$eszkoz['telephely']?></span></a>
                    <meta property="position" content="2">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/epulet/<?=$eszkoz['epuletid']?>">
                    <span property="name"><?=$eszkoz['epuletszam']?>. <?=$eszkoz['epulettipus']?></span></a>
                    <meta property="position" content="3">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <a property="item" typeof="WebPage"
                        href="<?=$RootPath?>/helyiseg/<?=$eszkoz['helyisegid']?>">
                    <span property="name"><?=$eszkoz['helyisegszam']?> (<?=$eszkoz['helyisegnev']?>)</span></a>
                    <meta property="position" content="4">
                </li>
                <li><b>></b></li>
                <li property="itemListElement" typeof="ListItem">
                    <span property="name"><?=$eszkoz['gyarto']?> <?=$eszkoz['modell']?> (<?=$eszkoz['sorozatszam']?>)</span>
                    <meta property="position" content="4">
                </li>
            </ol>
        </div><?php

    // Szerkesztő gombok
        if($csoportir)
        {
            ?><div style='display: inline-flex'>
                <button type='button' onclick="location.href='./<?=$id?>?action=edit'">Eszköz szerkesztése</button><?php
                if(isset($elozmenyek) && mysqli_num_rows($elozmenyek) > 0)
                {
                    ?><button type='button' onclick='rejtMutat("elozmenyek")'>Szerkesztési előzmények</button><?php
                }
            ?></div><?php
        }
        
        ?><div class="infobox">
            <div class="infoboxtitle"><?=(isset($_GET['beepites'])) ? "Korábbi beépítés adatai" : "Eszköz adatai" ?></div>
            <div class="infoboxbody">
                <div class="infoboxbodytwocol">
                    <div>Tulajdonos</div>
                    <div><?=$eszkoz['tulajdonos']?></div>
                    <div>Sorozatszám</div>
                    <div><?=$eszkoz['sorozatszam']?></div>
                    <div>Megjegyzés</div>
                    <div><?=$eszkoz['megjegyzes']?></div><?php
                    if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
                    {
                        ?><div>Állapot</div>
                        <div>Beépítve</div>
                        <div>Beépítés helye</div>
                        <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                        <div>Beépítés ideje</div>
                        <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                        <div>Hálózat</div>
                        <div><?=$eszkoz['vlan']?></div>
                        <?php
                    }
                    elseif(!$eszkoz['beepitesideje'])
                    {
                        ?><div>Állapot</div>
                        <div>Új, sosem beépített</div>
                        <div>Raktár</div>
                        <div><?=$eszkoz['raktar']?></div><?php
                    }
                    else
                    {
                        ?><div>Állapot</div>
                        <div>Kiépítve</div>
                        <div>Utolsó beépítési helye</div>
                        <div><?=$eszkoz['epuletszam']?> <?=($eszkoz['epuletnev']) ? "(" . $eszkoz['epuletnev'] . ")" : "" ?> <?=$eszkoz['helyisegszam']?> <?=($eszkoz['helyisegnev']) ? "(" . $eszkoz['helyisegnev'] . ")" : "" ?></div>
                        <div>Utolsó beépítés ideje</div>
                        <div><?=timeStampToDate($eszkoz['beepitesideje'])?></div>
                        <div>Kiépítés ideje</div>
                        <div><?=timeStampToDate($eszkoz['kiepitesideje'])?></div>
                        <div>Raktár</div>
                        <div><?=$eszkoz['raktar']?></div>
                        <div>Hálózat</div>
                        <div><?=$eszkoz['vlan']?></div>
                        <?php
                    }
                    ?>
                    <div>Gyártó</div>
                    <div><?=$eszkoz['gyarto']?></div>
                    <div>Modell</div>
                    <div><?=$eszkoz['modell'] . $eszkoz['varians']?></div>
                    <div>Eszköz fajtája</div>
                    <div><?=$eszkoz['tipus']?></div>
                    <div>Fizikai adatátvitel módja</div>
                    <div><?=$eszkoz['fizikaireteg']?></div>
                    <div>Transzport port átviteli szabványa</div>
                    <div><?=$eszkoz['transzportszabvany']?></div>
                    <div>Transzport port csatlakozója</div>
                    <div><?=$eszkoz['transzportcsatlakozo']?></div>
                    <div>Transzport port sebessége</div>
                    <div><?=$eszkoz['transzportsebesseg']?> Mbit</div>
                    <div>LAN oldal átviteli szabványa</div>
                    <div><?=$eszkoz['lanportszabvany']?></div>
                    <div>LAN oldal csatlakozója</div>
                    <div><?=$eszkoz['lanportcsatlakozo']?></div>
                    <div>LAN oldali sebesség</div>
                    <div><?=$eszkoz['lanportsebesseg']?> Mbit</div>
                </div>
            </div>
        </div>
        
        <div class="oldalcim">Portok</div>
        <table id="switchportok">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', 'switchportok')">Portnév</th>
                    <th class="tsorth" onclick="sortTable(1, 's', 'switchportok')">Sebesség</th>
                    <th class="tsorth" onclick="sortTable(2, 's', 'switchportok')">Szabvány</th>
                    <th class="tsorth" onclick="sortTable(3, 's', 'switchportok')">Csatlakozó</th>
                    <th class="tsorth" onclick="sortTable(4, 's', 'switchportok')">Végpont</th>
                </tr>
            </thead>
            <tbody><?php
                foreach($switchportok as $port)
                {
                    ?><tr>
                        <!--<form action="">-->
                        <form action="?page=portdb&action=update&tipus=mediakonverter" method="post">
                            <input type ="hidden" id="id" name="id" value=<?=$port['id']?>>
                            <input type ="hidden" id="portid" name="portid" value=<?=$port['portid']?>>
                            <td><input style="width: max-content" type="text" name="port" value="<?=$port['port']?>"></td>
                            <td>
                                <select name="sebesseg">
                                    <option value=""></option><?php
                                    foreach($sebessegek as $x)
                                    {
                                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['sebesseg']) ? "selected" : "" ?>><?=$x['sebesseg']?></option><?php
                                    }
                                ?></select>
                            </td>
                            <td>
                                <select id="szabvany" name="szabvany">
                                    <option value="" selected></option><?php
                                    foreach($atviteliszabvanyok as $x)
                                    {
                                        ?><option value="<?=$x["id"]?>" <?= ($x['id'] == $port['szabvany']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                    }
                                ?></select>
                            </td>
                            <td>
                                <select name="csatlakozo">
                                    <option value=""></option><?php
                                    foreach($csatlakozotipusok as $x)
                                    {
                                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['csatlakozo']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                    }
                                ?></select>
                            </td>
                            <td><?php
                                if($eszkoz['beepitesideje'] && !$eszkoz['kiepitesideje'])
                                {
                                    ?><select name="csatlakozas">
                                        <option value="" selected></option><?php
                                        $elozo = null;
                                        foreach($epuletportok as $x)
                                        {
                                            // Bug, de egyelőre így marad. Ha egy portra előbb kerül kirendezésre a végpont, mint a switchre,
                                            // duplán jelenik meg itt a listában. Használatot nem befolyásolja.
                                            if($x['id'] != $elozo /*|| $x['kapcsolat'] && $x['kapcsolat'] == $port['kapcsolat'] */)
                                            {
                                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $port['csatlakozas']) ? "selected" : "" ?>><?=$x['aktiveszkoz'] . " " . $x['port']?></option><?php
                                            }
                                            $elozo = $x['id'];
                                        }
                                    ?></select><?php
                                }
                            ?></td>
                            <td><input type="submit" value="Módosítás"></td>
                        </form>
                    </tr><?php
                }
            ?></tbody>
        </table>

        <script>
            $("form").on("submit", function (e) {
                var dataString = $(this).serialize();

                $.ajax({
                type: "POST",
                data: dataString,
                url: "<?=$RootPath?>/portdb?action=update&tipus=mediakonverter",
                success: function () {
                    showToaster("Port szerkesztése sikeres...");
                }
            });
            e.preventDefault();
            });
        </script><?php
	}
}