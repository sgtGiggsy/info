<?php

if(!@$mindir)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $wheretip = $eszkoztipus = null;
    if(isset($_GET['tipus']))
    {
        $eszkoztipus = $_GET['tipus'];
        switch($eszkoztipus)
        {
            case "aktiv": $wheretip = "WHERE modellek.tipus < 6"; break;
            case "soho": $wheretip = "WHERE modellek.tipus > 5 AND modellek.tipus < 11"; break;
            case "szamitogep": $wheretip = "WHERE modellek.tipus = 11"; break;
            case "nyomtato": $wheretip = "WHERE modellek.tipus = 12"; break;
            case "vegponti": $wheretip = "WHERE modellek.tipus > 10 AND modellek.tipus < 20"; break;
            case "mediakonverter": $wheretip = "WHERE modellek.tipus > 20 AND modellek.tipus < 26"; break;
            case "bovitomodul": $wheretip = "WHERE modellek.tipus > 25 AND modellek.tipus < 31"; break;
            case "szerver": $wheretip = "WHERE modellek.tipus > 30 AND modellek.tipus < 40"; break;
            case "telefonkozpont": $wheretip = "WHERE modellek.tipus = 40"; break;
        }
    }
    
    if(count($_POST))
    {
        $irhat = true;
        include("./db/eszkozdb.php");
    }

    $modellek = mySQLConnect("SELECT modellek.id AS id, gyartok.nev AS gyarto, modell, eszkoztipusok.nev AS tipus
    FROM modellek
        INNER JOIN gyartok ON modellek.gyarto = gyartok.id
        INNER JOIN eszkoztipusok ON modellek.tipus = eszkoztipusok.id
    $wheretip
    ORDER BY tipus ASC, gyartok.nev ASC, modell ASC;");

    $raktarak = mySQLConnect("SELECT * FROM raktarak;");
    
    $modell = $sorozatszam = $tulajdonos = $varians = $mac = $portszam = $uplinkportok = $szoftver = $nev = $leadva = $hibas = $raktar = $megjegyzes = $poe = $ssh = $web = null;
    $button = "Új eszköz";
    $oldalcim = "Új eszköz létrehozása";
    
    if(isset($_GET['id']))
    {
        $oldalcim = "Eszköz szerkesztése";
        $eszkid = $_GET['id'];
        $eszkoz = mySQLConnect("SELECT * FROM eszkozok WHERE id = $eszkid;");
        $eszkoz = mysqli_fetch_assoc($eszkoz);

        if($eszkoztipus == "aktiv" || $eszkoztipus == "soho")
        {
            $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
            if($eszkoztipus == "aktiv")
            {
                $aktiveszkoz = mySQLConnect("SELECT * FROM aktiveszkozok WHERE eszkoz = $eszkid;");
                $aktiveszkoz = mysqli_fetch_assoc($aktiveszkoz);
                $mac = @$aktiveszkoz['mac'];
                $poe = @$aktiveszkoz['poe'];
                $ssh = @$aktiveszkoz['ssh'];
                $web = @$aktiveszkoz['web'];
                $portszam = @$aktiveszkoz['portszam'];
                $uplinkportok = @$aktiveszkoz['uplinkportok'];
                $szoftver = @$aktiveszkoz['szoftver'];
            }
            else
            {
                $aktiveszkoz = mySQLConnect("SELECT * FROM sohoeszkozok WHERE eszkoz = $eszkid;");
                $aktiveszkoz = mysqli_fetch_assoc($aktiveszkoz);
                $mac = @$aktiveszkoz['mac'];
                $portszam = @$aktiveszkoz['lanportok'];
                $uplinkportok = @$aktiveszkoz['wanportok'];
                $szoftver = @$aktiveszkoz['szoftver'];
            }

            ?><div class="oldalcim"><p onclick="rejtMutat('portgeneralas')" style="cursor: pointer">Portok generálása az eszközhöz</p></div>
            <div class="contentcenter" id="portgeneralas" style='display: none'>
                <form action="<?=$RootPath?>/portdb?action=generate&tipus=<?=($eszkoztipus == "aktiv") ? "switch" : "soho" ?>" method="post" onsubmit="beKuld.disabled = true; return true;">
                    <input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$eszkid?>>
                    
                    <div>
                        <label for="accportpre">Access port előtag<small> (pl Fa0/)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="accportpre" id="accportpre"></input>
                    </div>

                    <div>
                        <label for="kezdoacc">Kezdő access port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="kezdoacc" id="kezdoacc"></input>
                    </div>

                    <div>
                        <label for="zaroacc">Záró access port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="zaroacc" id="zaroacc"></input>
                    </div>

                    <div>
                        <label for="accportsebesseg">Access portok sebessége:</label><br>
                        <select id="accportsebesseg" name="accportsebesseg">
                            <option value="" selected></option><?php
                            foreach($sebessegek as $x)
                            {
                                ?><option value="<?=$x["id"]?>"><?=$x['sebesseg']?> Mbit</option><?php
                            }
                        ?></select>
                    </div>

                    <div>
                        <label for="uplportpre">Uplink port előtag<small> (pl Gi0/)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="uplportpre" id="uplportpre"></input>
                    </div>

                    <div>
                        <label for="kezdoupl">Kezdő uplink port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="kezdoupl" id="kezdoupl"></input>
                    </div>

                    <div>
                        <label for="zaroupl">Záró uplink port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="zaroupl" id="zaroupl"></input>
                    </div>

                    <div>
                        <label for="uplportsebesseg">Uplink portok sebessége:</label><br>
                        <select id="uplportsebesseg" name="uplportsebesseg">
                            <option value="" selected></option><?php
                            foreach($sebessegek as $x)
                            {
                                ?><option value="<?=$x["id"]?>"><?=$x['sebesseg']?> Mbit</option><?php
                            }
                        ?></select>
                    </div>

                    <div class="submit"><input type="submit" name="beKuld" value="Portok generálása"></div>
                </form>
            </div><?php
        }

        if($eszkoztipus == "telefonkozpont")
        {
            $telefonkozpont = mySQLConnect("SELECT * FROM telefonkozpontok WHERE eszkoz = $eszkid;");
            $telefonkozpont = mysqli_fetch_assoc($telefonkozpont);

            $nev = $telefonkozpont['nev'];

            ?><div class="oldalcim"><p onclick="rejtMutat('portgeneralas')" style="cursor: pointer">Portok generálása az eszközhöz</p></div>
            <div class="contentcenter" id="portgeneralas" style='display: none'>
                <small>Ez a menüpont a portok genrálását végzi el. A túlbonyolítás elkerülése végett csak az utolsó két tag generálása végezhető el egyszere. Tehát ha négy tagból áll a port,
                és van 1-1- valamint 1-2- kezdetű porttartomány is, akkor azokat külön kell legenerálni.</small>
                <form action="<?=$RootPath?>/portdb?action=generate&tipus=telefonkozpont" method="post" onsubmit="beKuld.disabled = true; return true;">
                    <input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$eszkid?>>
                    
                    <div>
                        <label for="portpre">Port előtag<small> (pl 1-1-)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="portpre" id="portpre"></input>
                    </div>

                    <div>
                        <label for="kezdoharmadik">Kezdő harmadik tag port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="kezdoharmadik" id="kezdoharmadik"></input>
                    </div>

                    <div>
                        <label for="zaroharmadik">Záró harmadik tag port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="zaroharmadik" id="zaroharmadik"></input>
                    </div>

                    <div>
                        <label for="kezdonegyedik">Kezdő negyedik tag port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="kezdonegyedik" id="kezdonegyedik" value="0"></input>
                    </div>

                    <div>
                        <label for="zaronegyedik">Záró negyedik tag port<small> (csak a száma)</small>:</label><br>
                        <input type="text" accept-charset="utf-8" name="zaronegyedik" id="zaronegyedik" value="23"></input>
                    </div>

                    <div class="submit"><input type="submit" name="beKuld" value="Portok generálása"></div>
                </form>
            </div><?php
        }

        $modell = $eszkoz['modell'];
        $sorozatszam = $eszkoz['sorozatszam'];
        $tulajdonos = $eszkoz['tulajdonos'];
        $varians = $eszkoz['varians'];
        $leadva = $eszkoz['leadva'];
        $hibas = $eszkoz['hibas'];
        $raktar = $eszkoz['raktar'];
        $megjegyzes = $eszkoz['megjegyzes'];

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/eszkozszerkeszt&action=update&tipus=<?=$eszkoztipus?>" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$eszkid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/eszkozszerkeszt&action=new&tipus=<?=$eszkoztipus?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim"><?=$oldalcim?></div>
    <div class="contentcenter">

        <div>
            <label for="modell">Modell:</label><br>
            <select id="modell" name="modell">
                <option value="" selected></option><?php
                foreach($modellek as $x)
                {
                    ?><option value="<?=$x["id"]?>" <?= ($modell == $x['id']) ? "selected" : "" ?>><?=$x['gyarto'] . " " . $x['modell'] . " (" . $x['tipus'] . ")"?></option><?php
                }
            ?></select>
        </div>

        <div>
            <label for="varians">Modell variáns:</label><br>
            <input type="text" accept-charset="utf-8" name="varians" id="varians" value="<?=$varians?>"></input>
        </div>

        <div>
            <label for="sorozatszam">Sorozatszám:</label><br>
            <input type="text" accept-charset="utf-8" name="sorozatszam" id="sorozatszam" value="<?=$sorozatszam?>"></input>
        </div><?php

        if(isset($_GET['id']))
        {
            ?><div>
                <label for="leadva">Leadva:</label><br>
                <input type="checkbox" accept-charset="utf-8" name="leadva" id="leadva" value="1" <?= ($leadva) ? "checked" : "" ?>></input>
            </div>

            <div>
                <label for="hibas">Hibás:</label><br>
                <select name="hibas">
                    <option value="" selected></option>
                    <option value="1" <?= ($hibas == "1") ? "selected" : "" ?>>Részlegesen</option>
                    <option value="2" <?= ($hibas == "2") ? "selected" : "" ?>>Működésképtelen</option>
                </select>
            </div><?php
        }

        alakulatPicker($tulajdonos, "tulajdonos");

        if($eszkoztipus == "telefonkozpont")
        {
            ?><div>
                <label for="nev">Központ neve:</label><br>
                <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
            </div><?php
        }

        if($eszkoztipus == "aktiv" || $eszkoztipus == "soho")
        {
            ?><div>
                <label for="mac">MAC Address:</label><br>
                <input type="text" accept-charset="utf-8" name="mac" id="mac" value="<?=$mac?>"></input>
            </div>

            <div>
                <label for="portszam"><?=($eszkoztipus == "aktiv") ? "Access" : "LAN" ?> portok száma:</label><br>
                <input type="text" accept-charset="utf-8" name="portszam" id="portszam" value="<?=$portszam?>"></input>
            </div>

            <div>
                <label for="uplinkportok"><?=($eszkoztipus == "aktiv") ? "Uplink" : "WAN" ?> portok száma:</label><br>
                <input type="text" accept-charset="utf-8" name="uplinkportok" id="uplinkportok" value="<?=$uplinkportok?>"></input>
            </div>

            <div>
                <label for="szoftver">Szoftver:</label><br>
                <input type="text" accept-charset="utf-8" name="szoftver" id="szoftver" value="<?=$szoftver?>"></input>
            </div><?php
        }

        if($eszkoztipus == "aktiv")
        {
            ?><div>
                <label for="poe">POE képes:</label><br>
                <input type="checkbox" accept-charset="utf-8" name="poe" id="poe" value="1" <?= ($poe) ? "checked" : "" ?>></input>
            </div>
            
            <div>
                <label for="ssh">SSH képes:</label><br>
                <input type="checkbox" accept-charset="utf-8" name="ssh" id="ssh" value="1" <?= ($ssh) ? "checked" : "" ?>></input>
            </div>

            <div>
                <label for="web">Webes felület:</label><br>
                <input type="checkbox" accept-charset="utf-8" name="web" id="web" value="1" <?= ($web) ? "checked" : "" ?>></input>
            </div><?php
        }

        ?><div>
            <label for="raktar">Raktárban:</label><br>
            <select name="raktar">
                <option value=""></option><?php
                foreach($raktarak as $x)
                {
                    ?><option value="<?=$x['id']?>" <?=($x['id'] == $raktar) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                }
            ?></select>
        </div>

        <div>
                <label for="megjegyzes">Megjegyzés:</label><br>
                <textarea accept-charset="utf-8" name="megjegyzes" id="megjegyzes"><?=$megjegyzes?></textarea>
            </div>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}