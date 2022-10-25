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
    
    $modell = $sorozatszam = $tulajdonos = $varians = $mac = $portszam = 
    $uplinkportok = $szoftver = $nev = $leadva = $hibas = $raktar =
    $megjegyzes = $poe = $ssh = $web = $felhasznaloszam = $simtipus =
    $telefonszam = $pinkod = $pukkod = $magyarazat = null;
    $button = "Új eszköz";
    $oldalcim = "Új eszköz létrehozása";

    if($eszkoztipus == "simkartya")
    {
        $felhasznaloszamok = mySQLConnect("SELECT * FROM simfelhasznaloszamok");
        $simtipusok = mySQLConnect("SELECT * FROM simtipusok");
    }
    
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
        }

        if($eszkoztipus == "telefonkozpont")
        {
            $telefonkozpont = mySQLConnect("SELECT * FROM telefonkozpontok WHERE eszkoz = $eszkid;");
            $telefonkozpont = mysqli_fetch_assoc($telefonkozpont);

            $nev = $telefonkozpont['nev'];
        }

        if($eszkoztipus == "simkartya")
        {
            $simkartya = mySQLConnect("SELECT * FROM simkartyak WHERE eszkoz = $eszkid");
            $simkartya = mysqli_fetch_assoc($simkartya);

            $felhasznaloszam = @$simkartya['felhasznaloszam'];
            $simtipus = @$simkartya['tipus'];
            $telefonszam = @$simkartya['telefonszam'];
            $pinkod = @$simkartya['pinkod'];
            $pukkod = @$simkartya['pukkod'];
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
        $oldalcim = "Eszköz szerkesztése";
    }

    ?><div class="szerkcard">
        <div class="szerkcardtitle"><?=$oldalcim?><a class="help" onclick="rejtMutat('magyarazat')">?</a></div><?php
        if($eszkoztipus == "aktiv" || $eszkoztipus == "soho" || $eszkoztipus == "telefonkozpont")
        {
            ?><div class="szerkcardoptions">
                <div class="szerkcardoptionelement"><span onclick="showSlideIn('1')">Portok generálása <?=($eszkoztipus == "telefonkozpont") ? "a központhoz" : "az eszközhöz" ?></span></div>
            </div><?php
        }
        ?><div class="szerkcardbody">
            <div class="szerkeszt">
                <form action="<?=$RootPath?>/eszkozszerkeszt&action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?>&tipus=<?=$eszkoztipus?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$eszkid?>><?php
                }
                ?><div class="doublecolumn">
                    <div><?php
                        if($eszkoztipus != "simkartya")
                        {
                            ?><div>
                                <label for="modell">Modell:</label><br>
                                <select id="modell" name="modell">
                                    <option value="" selected></option><?php
                                    foreach($modellek as $x)
                                    {
                                        ?><option value="<?=$x["id"]?>" <?= ($modell == $x['id']) ? "selected" : "" ?>><?=$x['gyarto'] . " " . $x['modell'] . " (" . $x['tipus'] . ")"?></option><?php
                                    }
                                ?></select>
                            </div>

                            <?php $magyarazat .= "<strong>Modell</strong><p>A hozzáadni kívánt eszköz modellje. Listából kell kiválasztani.</p>"; ?>

                            <div>
                                <label for="varians">Modell variáns:</label><br>
                                <input type="text" accept-charset="utf-8" name="varians" id="varians" value="<?=$varians?>"></input>
                            </div><?php
                            $magyarazat .= "<strong>Modell variáns</strong><p>A modell alverziója, ami részletesen leírja az eszköz paramétereit. (Például a portok számát.)</p>";
                        }
                        ?><div>
                            <label for="sorozatszam"><?=($eszkoztipus != "simkartya") ? "Sorozatszám:" : "IMEI szám" ?></label><br>
                            <input type="text" accept-charset="utf-8" name="sorozatszam" id="sorozatszam" value="<?=$sorozatszam?>"></input>
                        </div><?php
                        $magyarazat .= "<strong>Sorozatszám</strong><p>Az eszköz sorozatszáma. <b>Egyedinek kell lennie!</b> Amennyiben a sorozatszám nem ismert,
                            valami olyat kell ide beírni, ami egyértelműen beazonosíthatóvá teszi az eszközt.</p>";

                        if($eszkoztipus == "telefonkozpont")
                        {
                            ?><div>
                                <label for="nev">Központ neve:</label><br>
                                <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                            </div><?php
                            $magyarazat .= "<strong>Központ neve</strong><p>A fizikai központ neve. A központ beépítését követően nem jelenik meg sehol.</p>";
                        }

                        if($eszkoztipus == "aktiv" || $eszkoztipus == "soho")
                        {
                            ?><div>
                                <label for="mac">MAC Address:</label><br>
                                <input type="text" accept-charset="utf-8" name="mac" id="mac" value="<?=$mac?>"></input>
                            </div>

                            <?php $magyarazat .= "<strong>MAC Address</strong><p>Az eszköz MAC címe.</p>"; ?>

                            <div>
                                <label for="portszam"><?=($eszkoztipus == "aktiv") ? "Access" : "LAN" ?> portok száma:</label><br>
                                <input type="text" accept-charset="utf-8" name="portszam" id="portszam" value="<?=$portszam?>"></input>
                            </div>

                            <?php $magyarazat .= "<strong>Access/LAN portok száma</strong><p>A végponti eszközök csatlakoztatására tervezett portok száma.</p>"; ?>

                            <div>
                                <label for="uplinkportok"><?=($eszkoztipus == "aktiv") ? "Uplink" : "WAN" ?> portok száma:</label><br>
                                <input type="text" accept-charset="utf-8" name="uplinkportok" id="uplinkportok" value="<?=$uplinkportok?>"></input>
                            </div>

                            <?php $magyarazat .= "<strong>Uplink/WAN portok száma</strong><p>A hálózati eszközök csatlakoztatására tervezett portok száma.</p>"; ?>

                            <div>
                                <label for="szoftver">Szoftver:</label><br>
                                <input type="text" accept-charset="utf-8" name="szoftver" id="szoftver" value="<?=$szoftver?>"></input>
                            </div><?php

                            $magyarazat .= "<strong>Szoftver</strong><p>Az eszközön futó szoftver verziószáma.</p>";
                        }

                        if($eszkoztipus == "aktiv")
                        {
                            ?><div>
                                <label for="poe">PoE képes:</label><br>
                                <input type="checkbox" accept-charset="utf-8" name="poe" id="poe" value="1" <?= ($poe) ? "checked" : "" ?>></input>
                            </div>

                            <?php $magyarazat .= "<strong>PoE képes</strong><p>Akkor kell bejelölni, ha az eszköz rendelkezik PoE képességgel bíró portokkal.</p>"; ?>
                            
                            <div>
                                <label for="ssh">SSH képes:</label><br>
                                <input type="checkbox" accept-charset="utf-8" name="ssh" id="ssh" value="1" <?= ($ssh) ? "checked" : "" ?>></input>
                            </div>

                            <?php $magyarazat .= "<strong>SSH képes</strong><p>Akkor kell bejelölni, ha az eszközhöz <b>jelenleg</b> lehetséges SSH-n keresztül kapcsolódni.
                                Amennyiben az eszköz, kizárólag nem támogatott verziójú SSH-ra képes, vagy nincs rajta beállítva az SSH elérés, úgy <b>ne</b> jelöljük ezt be.</p>"; ?>

                            <div>
                                <label for="web">Webes felület:</label><br>
                                <input type="checkbox" accept-charset="utf-8" name="web" id="web" value="1" <?= ($web) ? "checked" : "" ?>></input>
                            </div><?php
                            $magyarazat .= "<strong>Webes felület</strong><p>Akkor kell bejelölni, ha az eszköz <b>jelenleg</b> menedzselhető a saját webes felületén keresztül.</p>";
                        }

                        if($eszkoztipus == "simkartya")
                        {
                            ?><div>
                                <label for="telefonszam">Telefonszám:</label><br>
                                <input type="text" accept-charset="utf-8" name="telefonszam" id="telefonszam" value="<?=$telefonszam?>"></input>
                            </div>

                            <?php $magyarazat .= "<strong>Telefonszám</strong><p>A SIM kártyához tartozó telefonszám.</p>"; ?>
                            
                            <div>
                                <label for="pinkod">PIN kód:</label><br>
                                <input type="text" accept-charset="utf-8" name="pinkod" id="pinkod" value="<?=$pinkod?>"></input>
                            </div>

                            <?php $magyarazat .= "<strong>PIN kód</strong><p>A SIM kártya legutolsó ismert PIN kódja.</p>"; ?>

                            <div>
                                <label for="pukkod">PUK kód:</label><br>
                                <input type="text" accept-charset="utf-8" name="pukkod" id="pukkod" value="<?=$pukkod?>"></input>
                            </div>

                            <?php $magyarazat .= "<strong>PUK kód</strong><p>A SIM kártya PUK kódja.</p>"; ?>

                            <div>
                                <label for="tipus">Típus:</label><br>
                                <select id="tipus" name="tipus">
                                    <option value="" selected></option><?php
                                    foreach($simtipusok as $x)
                                    {
                                        ?><option value="<?=$x["id"]?>" <?=($simtipus == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                    }
                                ?></select>
                            </div>

                            <?php $magyarazat .= "<strong>Típus</strong><p>A SIM kártya felhasználási típusa.</p>"; ?>

                            <div>
                                <label for="felhasznaloszam">Felhasználók száma:</label><br>
                                <select id="felhasznaloszam" name="felhasznaloszam">
                                    <option value="" selected></option><?php
                                    foreach($felhasznaloszamok as $x)
                                    {
                                        ?><option value="<?=$x["id"]?>" <?= ($felhasznaloszam == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                    }
                                ?></select>
                            </div>
                            <?php

                            $magyarazat .= "<strong>Felhasználók száma</strong><p>A SIM kártya jogállása a felhasználók száma alapján. (Egyéni/csoportos)</p>";

                        }
                    ?></div>
                    <div><?php

                        alakulatPicker($tulajdonos, "tulajdonos", true);

                        $magyarazat .= "<strong>Alakulat</strong><p>Az alakulat, amelyet az eszköz terhel.</p>";

                        if(isset($_GET['id']))
                        {
                            ?><div>
                                <label for="leadva">Leadva:</label><br>
                                <input type="checkbox" accept-charset="utf-8" name="leadva" id="leadva" value="1" <?= ($leadva) ? "checked" : "" ?>></input>
                            </div>

                            <?php $magyarazat .= "<strong>Leadva</strong><p>Akkor kell bejelölni, ha az eszköz leadásra került. Eszköz létrehozása során <b>nem</b> jelenik meg.</p>"; ?>

                            <div>
                                <label for="hibas">Hibás:</label><br>
                                <select name="hibas">
                                    <option value="" selected></option>
                                    <option value="1" <?= ($hibas == "1") ? "selected" : "" ?>>Részlegesen</option>
                                    <option value="2" <?= ($hibas == "2") ? "selected" : "" ?>>Működésképtelen</option>
                                </select>
                            </div><?php

                            $magyarazat .= "<strong>Hibás</strong><p>Akkor kell kiválasztani, ha az eszköz hibás. A pontos hibát a Megjegyzés rovatba kell beírni.</p>";
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

                        <?php $magyarazat .= "<strong>Raktárban</strong><p>A raktár, ahol az eszköz jelenleg található. Beépítés után kézzel kell törölni.</p>"; ?>

                        <div>
                            <label for="megjegyzes">Megjegyzés:</label><br>
                            <textarea accept-charset="utf-8" name="megjegyzes" id="megjegyzes"><?=$megjegyzes?></textarea>
                        </div>

                        <?php $magyarazat .= "<strong>Megjegyzés</strong><p>Az eszközhöz tartozó megjegyzés, ami független attól, hogy hol van jelenleg fizikálisan.</p>"; ?>

                        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                    </form><?php $valami = "<b>Valami<br>Semmi</b>";
                    cancelForm();?> 
                    </div>
                </div>

                <div id="magyarazat">
                    <h2 style="text-align: center">Magyarázat</h2>
                    <?=$magyarazat?>
                </div>
                
            </div>
        </div>
    </div><?php
    if($eszkoztipus == "aktiv" || $eszkoztipus == "soho")
    {
        ?><div id="slidein-1" onmouseleave="showSlideIn('1')">
            <div class="szerkcard">
                <div class="szerkcardtitle">Portok generálása az eszközhöz</div>
                <div class="szerkcardbody">
                    <div class="contentcenter">
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
                    </div>
                </div>
            </div>
        </div><?php
    }
    if($eszkoztipus == "telefonkozpont")
    {
        ?><div id="slidein-1" onmouseleave="showSlideIn('1')">
            <div class="szerkcard">
                <div class="szerkcardtitle">Portok generálása az eszközhöz</div>
                <div class="szerkcardbody">
                    <div class="contentcenter">
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
                    </div>
                </div>
            </div>
        </div><?php
    }
}