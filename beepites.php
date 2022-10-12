<?php

if(@!$sajatolvas)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $tipus = null;
    if(isset($_GET['tipus']))
    {
        $tipus = $_GET['tipus'];
    }

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/beepitesdb.php");
    }

    $beepid = $beepnev = $beepeszk = $beepip = $beeprack = $beephely = $beeppoz = $beepido = $beepkiep = $admin = $pass = $megjegyzes = $vlan = $switchport = null;
    $button = "Beépítés";

    if(isset($_GET['eszkoz']))
    {
        $beepeszk = $_GET['eszkoz'];
    }

    $where = null;
    if(!isset($_GET['id']))
    {
        $where = "WHERE beepitesek.beepitesideje IS NULL OR beepitesek.kiepitesideje IS NOT NULL";
    }

    $ipcimek = mySQLConnect("SELECT ipcimek.id AS id, ipcimek.ipcim AS ipcim
        FROM ipcimek
            LEFT JOIN beepitesek ON ipcimek.id = beepitesek.ipcim
        $where
        ORDER BY ipcimek.vlan, ipcimek.ipcim;");

    if(isset($_GET['id']))
    {
        $beepid = $_GET['id'];
        $beepitve = mySQLConnect("SELECT beepitesek.nev AS nev,
                beepitesek.eszkoz AS eszkid,
                (SELECT CONCAT(gyartok.nev, ' ', modellek.modell, COALESCE(eszkozok.varians, ''), ' (', eszkozok.sorozatszam, ')')
                    FROM eszkozok
                        LEFT JOIN modellek ON eszkozok.modell = modellek.id
                        LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
                    WHERE eszkozok.id = eszkid) AS eszkoz,
                beepitesek.ipcim AS ipcimid,
                ipcimek.ipcim AS ipcim,
                rackszekrenyek.id AS rackid,
                rackszekrenyek.nev AS rack,
                beepitesek.helyiseg AS helyisegid,
                (SELECT CONCAT(COALESCE(epuletek.szam, ''), ' (', COALESCE(epuletek.nev, ''), ') épület', COALESCE(helyisegek.helyisegszam, ''), ' (', COALESCE(helyisegek.helyisegnev, ''), ')')
                    FROM helyisegek
                        INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                    WHERE helyisegek.id = helyisegid) AS helyiseg,
                pozicio,
                beepitesideje,
                kiepitesideje,
                beepitesek.megjegyzes AS megjegyzes,
                admin,
                pass,
                vlanok.id AS vlanid,
                vlanok.nev AS vlan,
                switchport AS switchportid,
                (SELECT CONCAT(COALESCE(beepitesek.nev, ''), ' (', COALESCE(ipcimek.ipcim, ''), ') ', portok.port) 
                    FROM beepitesek
                        INNER JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
                        INNER JOIN switchportok ON eszkozok.id = switchportok.eszkoz
                        INNER JOIN portok ON switchportok.port = portok.id
                        LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    WHERE portok.id = switchportid
                    ORDER BY beepitesek.id DESC
         	        LIMIT 1) AS switchport,
                beepitesek.letrehozo AS letrehozoid,
                (SELECT nev FROM felhasznalok WHERE id = letrehozoid) AS letrehozo,
                beepitesek.utolsomodosito AS utolsomodositoid,
                (SELECT nev FROM felhasznalok WHERE id = utolsomodositoid) AS utolsomodosito,
                beepitesek.letrehozasideje AS letrehozasideje,
                beepitesek.utolsomodositasideje AS utolsomodositasideje
            FROM beepitesek
                LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
                LEFT JOIN vlanok ON beepitesek.vlan = vlanok.id
            WHERE beepitesek.id = $beepid;");

        //$beepitve = mySQLConnect("SELECT * FROM beepitesek WHERE id = $beepid;");
        $beepitve = mysqli_fetch_assoc($beepitve);

        $elozmenyek = mySQLConnect("SELECT beepitesek_history.nev AS nev,
                beepitesek_history.eszkoz AS eszkid,
                (SELECT CONCAT(gyartok.nev, ' ', modellek.modell, COALESCE(eszkozok.varians, ''), ' (', eszkozok.sorozatszam, ')')
                    FROM eszkozok
                        LEFT JOIN modellek ON eszkozok.modell = modellek.id
                        LEFT JOIN gyartok ON modellek.gyarto = gyartok.id
                    WHERE eszkozok.id = eszkid) AS eszkoz,
                beepitesek_history.ipcim AS ipcimid,
                ipcimek.ipcim AS ipcim,
                rackszekrenyek.id AS rackid,
                rackszekrenyek.nev AS rack,
                beepitesek_history.helyiseg AS helyisegid,
                (SELECT CONCAT(COALESCE(epuletek.szam, ''), ' (', COALESCE(epuletek.nev, ''), ') épület', COALESCE(helyisegek.helyisegszam, ''), ' (', COALESCE(helyisegek.helyisegnev, ''), ')')
                    FROM helyisegek
                        INNER JOIN epuletek ON helyisegek.epulet = epuletek.id
                    WHERE helyisegek.id = helyisegid) AS helyiseg,
                pozicio,
                beepitesideje,
                kiepitesideje,
                beepitesek_history.megjegyzes AS megjegyzes,
                admin,
                pass,
                vlanok.id AS vlanid,
                vlanok.nev AS vlan,
                switchport AS switchportid,
                (SELECT CONCAT(COALESCE(beepitesek.nev, ''), ' (', COALESCE(ipcimek.ipcim, ''), ') ', portok.port) 
                    FROM beepitesek
                        INNER JOIN eszkozok ON beepitesek.eszkoz = eszkozok.id
                        INNER JOIN switchportok ON eszkozok.id = switchportok.eszkoz
                        INNER JOIN portok ON switchportok.port = portok.id
                        LEFT JOIN ipcimek ON beepitesek.ipcim = ipcimek.id
                    WHERE portok.id = switchportid
                    ORDER BY beepitesek.id DESC
         	        LIMIT 1) AS switchport,
                beepitesek_history.utolsomodosito AS utolsomodositoid,
                (SELECT nev FROM felhasznalok WHERE id = utolsomodositoid) AS utolsomodosito,
                beepitesek_history.utolsomodositasideje AS utolsomodositasideje
            FROM beepitesek_history
                LEFT JOIN ipcimek ON beepitesek_history.ipcim = ipcimek.id
                LEFT JOIN rackszekrenyek ON beepitesek_history.rack = rackszekrenyek.id
                LEFT JOIN vlanok ON beepitesek_history.vlan = vlanok.id
            WHERE beepitesid = $beepid;");

        $beepeszk = $beepitve['eszkid'];
        $beepnev = $beepitve['nev'];
        $beepip = $beepitve['ipcimid'];
        $beeprack = $beepitve['rackid'];
        $beephely = $beepitve['helyisegid'];
        $beeppoz = $beepitve['pozicio'];
        $beepido = $beepitve['beepitesideje'];
        $beepkiep = $beepitve['kiepitesideje'];
        $admin = $beepitve['admin'];
        $pass = $beepitve['pass'];
        $megjegyzes = $beepitve['megjegyzes'];
        $vlan = $beepitve['vlanid'];
        $switchport = $beepitve['switchportid'];

        $eszkoztipus = mySQLConnect("SELECT tipus FROM eszkozok INNER JOIN modellek ON eszkozok.modell = modellek.id WHERE eszkozok.id = $beepeszk");
        $tip = mysqli_fetch_assoc($eszkoztipus);
        $tipus = eszkozTipusValaszto($tip['tipus'])['tipus'];

        $button = "Szerkesztés";

// Szerkesztési előzmények
        if(mysqli_num_rows($elozmenyek) > 0)
        {
            ?><button type='button' onclick=rejtMutat("elozmenyek")>Szerkesztési előzmények</button><?php
        }

        if(mysqli_num_rows($elozmenyek) > 0)
        {
            ?><div id="elozmenyek" style="display: none">
                <div class="oldalcim">Szerkesztési előzmények</div>
                <table id="verzioelozmenyek">
                    <thead>
                        <th>Létrehozás / Módosítás ideje</th>
                        <th>Létrehozó / Módosító</th>
                        <th>Eszköz</th>
                        <th>Beépítési név</th>
                        <th>IP cím</th>
                        <th>VLAN</th>
                        <th>Rack</th>
                        <th>Helyiség</th>
                        <th>Pozíció</th>
                        <th>Switchport</th>
                        <th>Beépítés ideje</th>
                        <th>Kiépítés ideje</th>
                        <th>Admin</th>
                        <th>Jelszó</th>
                        <th>Megjegyzés</th>
                    </thead>
                    <tbody>
                        <?php
                        $szamoz = 1;
                        $elozoverzio = null;
                        foreach($elozmenyek as $x)
                        {
                            ?><tr style="font-weight: normal;" class='valtottsor-<?=($szamoz % 2 == 0) ? "2" : "1" ?>'>
                                <td><?=($x['utolsomodositasideje']) ? $x['utolsomodositasideje'] : $beepitve['letrehozasideje'] ?></td>
                                <td><?=($x['utolsomodosito']) ? $x['utolsomodosito'] : $beepitve['letrehozo'] ?></td>
                                <td <?=($elozoverzio && $elozoverzio['eszkid'] != $x['eszkid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['eszkoz']?></td>
                                <td <?=($elozoverzio && $elozoverzio['nev'] != $x['nev']) ? "style='font-weight: bold;'" : "" ?>><?=$x['nev']?></td>
                                <td <?=($elozoverzio && $elozoverzio['ipcimid'] != $x['ipcimid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['ipcim']?></td>
                                <td <?=($elozoverzio && $elozoverzio['vlanid'] != $x['vlanid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['vlan']?></td>
                                <td <?=($elozoverzio && $elozoverzio['rackid'] != $x['rackid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['rack']?></td>
                                <td <?=($elozoverzio && $elozoverzio['helyisegid'] != $x['helyisegid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['helyiseg']?></td>
                                <td <?=($elozoverzio && $elozoverzio['pozicio'] != $x['pozicio']) ? "style='font-weight: bold;'" : "" ?>><?=$x['pozicio']?></td>
                                <td <?=($elozoverzio && $elozoverzio['switchportid'] != $x['switchportid']) ? "style='font-weight: bold;'" : "" ?>><?=$x['switchport']?></td>
                                <td <?=($elozoverzio && $elozoverzio['beepitesideje'] != $x['beepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$x['beepitesideje']?></td>
                                <td <?=($elozoverzio && $elozoverzio['kiepitesideje'] != $x['kiepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$x['kiepitesideje']?></td>
                                <td <?=($elozoverzio && $elozoverzio['admin'] != $x['admin']) ? "style='font-weight: bold;'" : "" ?>><?=$x['admin']?></td>
                                <td <?=($elozoverzio && $elozoverzio['pass'] != $x['pass']) ? "style='font-weight: bold;'" : "" ?>><?=$x['pass']?></td>
                                <td <?=($elozoverzio && $elozoverzio['megjegyzes'] != $x['megjegyzes']) ? "style='font-weight: bold;'" : "" ?>><?=$x['megjegyzes']?></td>
                            </tr><?php
                            $szamoz++;
                            $elozoverzio = $x;
                        }
                        ?><tr style="font-weight: normal;" class='valtottsor-<?=($szamoz % 2 == 0) ? "2" : "1" ?>'>
                            <td><?=$beepitve['utolsomodositasideje']?></td>
                            <td><?=$beepitve['utolsomodosito']?></td>
                            <td <?=($elozoverzio['eszkid'] != $beepitve['eszkid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['eszkoz']?></td>
                            <td <?=($elozoverzio['nev'] != $beepitve['nev']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['nev']?></td>
                            <td <?=($elozoverzio['ipcimid'] != $beepitve['ipcimid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['ipcim']?></td>
                            <td <?=($elozoverzio['vlanid'] != $beepitve['vlanid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['vlan']?></td>
                            <td <?=($elozoverzio['rackid'] != $beepitve['rackid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['rack']?></td>
                            <td <?=($elozoverzio['helyisegid'] != $beepitve['helyisegid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['helyiseg']?></td>
                            <td <?=($elozoverzio['pozicio'] != $beepitve['pozicio']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['pozicio']?></td>
                            <td <?=($elozoverzio['switchportid'] != $beepitve['switchportid']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['switchport']?></td>
                            <td <?=($elozoverzio['beepitesideje'] != $beepitve['beepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['beepitesideje']?></td>
                            <td <?=($elozoverzio['kiepitesideje'] != $beepitve['kiepitesideje']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['kiepitesideje']?></td>
                            <td <?=($elozoverzio['admin'] != $beepitve['admin']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['admin']?></td>
                            <td <?=($elozoverzio['pass'] != $beepitve['pass']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['pass']?></td>
                            <td <?=($elozoverzio['megjegyzes'] != $beepitve['megjegyzes']) ? "style='font-weight: bold;'" : "" ?>><?=$beepitve['megjegyzes']?></td>
                        </tr>
                    </tbody>
                </table>
            </div><?php
        }

// Form
        ?><form action="<?=$RootPath?>/beepites&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$beepid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/beepites&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    if(!$switchport)
    {
        $switchport = 0;
    }

    $switchportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
    FROM portok
        INNER JOIN switchportok ON portok.id = switchportok.port
        INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
        INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
        LEFT JOIN rackszekrenyek ON beepitesek.rack = rackszekrenyek.id
        LEFT JOIN helyisegek ON beepitesek.helyiseg = helyisegek.id OR rackszekrenyek.helyiseg = helyisegek.id
        LEFT JOIN epuletek ON helyisegek.epulet = epuletek.id
    WHERE switchportok.tipus = 1 AND (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL) AND ((SELECT count(id) FROM beepitesek WHERE switchport = portok.id) = 0 OR portok.id = $switchport)
    ORDER BY telephely, epuletek.szam + 1, helyisegszam, pozicio, aktiveszkoz, id;");

    ?><div class="oldalcim">Eszköz beépítése</div>
    <div class="contentcenter"><?php

    eszkozPicker($beepeszk, ($beepid) ? true : false);

    if(!$tipus || $tipus == "aktiv" || $tipus == "nyomtato" || $tipus == "telefonkozpont" || $tipus == "soho")
    {
        
        ?><div>
            <label for="nev">Beépítési név:</label><br>
            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$beepnev?>"></input>
        </div><?php
    }
    
    if(!$tipus || $tipus == "aktiv" || $tipus == "nyomtato" || $tipus == "soho")
    {
        ?><div>
            <label for="ipcim">IP cím:</label><br>
            <select id="ipcim" name="ipcim">
                <option value="" selected></option><?php
                foreach($ipcimek as $x)
                {
                    ?><option value="<?php echo $x["id"] ?>" <?= ($beepip == $x['id']) ? "selected" : "" ?>><?=$x['ipcim']?></option><?php
                }
            ?></select>
        </div><?php
    }

    if(!$tipus || $tipus == "aktiv" || $tipus == "nyomtato" || $tipus == "telefonkozpont" || $tipus == "mediakonverter" || $tipus == "soho")
    {
        
        helyisegPicker($beephely, "helyiseg");
    }

    if(!$tipus || $tipus == "aktiv" || $tipus == "mediakonverter" || $tipus == "soho")
    {
        rackPicker($beeprack);
    }

    if(!$tipus || $tipus == "bovitomodul")
    {
        ?><div>
            <label for="switchport">Switchport:</label><br>
            <select name="switchport">
                <option value=""></option><?php
                foreach($switchportok as $x)
                {
                    ?><option value="<?=$x['id']?>" <?=($x['id'] == $switchport) ? "selected" : "" ?>><?=$x['aktiveszkoz']?> - <?=$x['port']?></option><?php
                }
            ?></select>
        </div><?php
    }

    if(!$tipus || $tipus == "aktiv" || $tipus == "mediakonverter" || $tipus == "soho")
    {
        vlanPicker($vlan);
    }

    if(!$tipus || $tipus == "aktiv")
    {
        ?><div>
            <label for="pozicio">Pozíció:</label><br>
            <input type="text" id="pozicio" name="pozicio" value="<?=$beeppoz?>">
        </div><?php
    }

    ?><div>
        <label for="beepitesideje">Beépítés ideje</label><br>
        <input type="datetime-local" id="beepitesideje" name="beepitesideje" value="<?=timeStampToDateTimeLocal($beepido)?>"><button style="margin-left: 10px;" onclick="getMost('beepitesideje'); return false;">Most</button>
    </div>

    <div>
        <label for="kiepitesideje">Kiépítés ideje</label><br>
        <input type="datetime-local" id="kiepitesideje" name="kiepitesideje" value="<?=timeStampToDateTimeLocal($beepkiep)?>"><button style="margin-left: 10px;" onclick="getMost('kiepitesideje'); return false;">Most</button>
    </div><?php

    if(!$tipus || $tipus == "aktiv" || $tipus == "nyomtato" || $tipus == "soho")
    {
        ?><div>
            <label for="admin">Admin user:</label><br>
            <input type="text" accept-charset="utf-8" name="admin" id="admin" value="<?=$admin?>"></input>
        </div>

        <div>
            <label for="pass">Jelszó:</label><br>
            <input type="text" accept-charset="utf-8" name="pass" id="pass" value="<?=$pass?>"></input>
        </div><?php
    }
    
    ?><div>
        <label for="megjegyzes">Megjegyzés:</label><br>
        <textarea accept-charset="utf-8" name="megjegyzes" id="megjegyzes"><?=$megjegyzes?></textarea>
    </div>

    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form>
    <?php
    cancelForm();
    if(isset($_GET['id']) && (!$tipus || $tipus == "aktiv" || $tipus == "soho"))
    {
        ?><br>
        <form action="<?=$RootPath?>/portdb&action=clearportassign" method="post" onsubmit="return confirm('Figyelem!!!\nEzzel a switch ÖSSZES porthozzárendelését törlöd, nem csak a jelen beépítéshez tartozókat!\nBiztosan törölni szeretnéd a switch porthozzárendeléseit?');">
            <input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$beepeszk?>>
            <div class="submit"><input type="submit" name="beKuld" value="Porthozzárendelések törlése"></div>
        </form><?php
    }
?></div>
<script>
    function getMost(dateselect)
    {
        var most = new Date();
        var dd = String(most.getDate()).padStart(2, '0');
        var mm = String(most.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = most.getFullYear();
        var hour = String(most.getHours()).padStart(2, '0');
        var minute = String(most.getMinutes()).padStart(2, '0');

        most = yyyy + '-' + mm + '-' + dd + ' ' + hour + ':' + minute;
        document.getElementById(dateselect).value = most;
    }
</script>
<?php
}

