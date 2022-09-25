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
    
    $switchportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, beepitesek.nev AS aktiveszkoz, csatlakozas
        FROM portok
            INNER JOIN switchportok ON portok.id = switchportok.port
            INNER JOIN eszkozok ON switchportok.eszkoz = eszkozok.id
            INNER JOIN beepitesek ON eszkozok.id = beepitesek.eszkoz
        WHERE switchportok.tipus = 1 AND (beepitesek.beepitesideje IS NOT NULL AND beepitesek.kiepitesideje IS NULL)
        ORDER BY aktiveszkoz, id;");

    if(isset($_GET['id']))
    {
        $beepid = $_GET['id'];
        $beepitve = mySQLConnect("SELECT * FROM beepitesek WHERE id = $beepid;");
        $beepitve = mysqli_fetch_assoc($beepitve);

        $beepeszk = $beepitve['eszkoz'];
        $beepnev = $beepitve['nev'];
        $beepip = $beepitve['ipcim'];
        $beeprack = $beepitve['rack'];
        $beephely = $beepitve['helyiseg'];
        $beeppoz = $beepitve['pozicio'];
        $beepido = $beepitve['beepitesideje'];
        $beepkiep = $beepitve['kiepitesideje'];
        $admin = $beepitve['admin'];
        $pass = $beepitve['pass'];
        $megjegyzes = $beepitve['megjegyzes'];
        $vlan = $beepitve['vlan'];
        $switchport = $beepitve['switchport'];

        $eszkoztipus = mySQLConnect("SELECT tipus FROM eszkozok INNER JOIN modellek ON eszkozok.modell = modellek.id WHERE eszkozok.id = $beepeszk");
        $tip = mysqli_fetch_assoc($eszkoztipus);
        $tipus = eszkozTipusValaszto($tip['tipus'])['tipus'];

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/beepites&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$beepid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/beepites&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Eszköz beépítése</div>
    <div class="contentcenter"><?php

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

    eszkozPicker($beepeszk, ($beepid) ? true : false);

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
        <input type="datetime-local" id="beepitesideje" name="beepitesideje" value="<?=timeStampToDateTimeLocal($beepido)?>"><button style="margin-left: 10px;" onclick="getMa('beepitesideje'); return false;">Most</button>
    </div>

    <div>
        <label for="kiepitesideje">Kiépítés ideje</label><br>
        <input type="datetime-local" id="kiepitesideje" name="kiepitesideje" value="<?=timeStampToDateTimeLocal($beepkiep)?>"><button style="margin-left: 10px;" onclick="getMa('kiepitesideje'); return false;">Most</button>
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
        <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
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
    function getMa(dateselect)
    {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        var hour = today.getHours();
        var minute = today.getMinutes();

        today = yyyy + '-' + mm + '-' + dd + ' ' + hour + ':' + minute;
        document.getElementById(dateselect).value = today;
    }
</script>
<?php
}

