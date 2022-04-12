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
            case "aktiv": $wheretip = "WHERE modellek.tipus < 11"; break;
            case "szamitogep": $wheretip = "WHERE modellek.tipus = 11"; break;
            case "nyomtato": $wheretip = "WHERE modellek.tipus = 12"; break;
            case "vegponti": $wheretip = "WHERE modellek.tipus > 10 AND modellek.tipus < 20"; break;
            case "konverter": $wheretip = "WHERE modellek.tipus > 20 AND modellek.tipus < 30"; break;
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
    
    $modell = $sorozatszam = $tulajdonos = $varians = $mac = $portszam = $uplinkportok = $szoftver = null;
    $button = "Új eszköz";
    $oldalcim = "Új eszköz létrehozása";
    
    if(isset($_GET['id']))
    {
        $oldalcim = "Eszköz szerkesztése";
        $eszkid = $_GET['id'];
        $eszkoz = mySQLConnect("SELECT * FROM eszkozok WHERE id = $eszkid;");
        $eszkoz = mysqli_fetch_assoc($eszkoz);

        if($eszkoztipus == "aktiv")
        {
            $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
            $aktiveszkoz = mySQLConnect("SELECT * FROM aktiveszkozok WHERE eszkoz = $eszkid;");
            $aktiveszkoz = mysqli_fetch_assoc($aktiveszkoz);

            $mac = $aktiveszkoz['mac'];
            $portszam = $aktiveszkoz['portszam'];
            $uplinkportok = $aktiveszkoz['uplinkportok'];
            $szoftver = $aktiveszkoz['szoftver'];

            ?><div class="oldalcim"><p onclick="rejtMutat('portgeneralas')" style="cursor: pointer">Portok generálása az eszközhöz</p></div>
            <div class="contentcenter" id="portgeneralas" style='display: none'>
                <form action="<?=$RootPath?>/portdb?action=generate&tipus=switch" method="post" onsubmit="beKuld.disabled = true; return true;">
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
        </div>

        <?php alakulatPicker($tulajdonos, "tulajdonos");

        if($eszkoztipus == "aktiv")
        {
            ?><div>
                <label for="mac">MAC Address:</label><br>
                <input type="text" accept-charset="utf-8" name="mac" id="mac" value="<?=$mac?>"></input>
            </div>

            <div>
                <label for="portszam">Access portok száma:</label><br>
                <input type="text" accept-charset="utf-8" name="portszam" id="portszam" value="<?=$portszam?>"></input>
            </div>

            <div>
                <label for="uplinkportok">Uplink portok száma:</label><br>
                <input type="text" accept-charset="utf-8" name="uplinkportok" id="uplinkportok" value="<?=$uplinkportok?>"></input>
            </div>

            <div>
                <label for="szoftver">Szoftver:</label><br>
                <input type="text" accept-charset="utf-8" name="szoftver" id="szoftver" value="<?=$szoftver?>"></input>
            </div><?php
        }

        ?><div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}