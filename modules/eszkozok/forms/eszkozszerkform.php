<?php
if(@$irhat)
{
    ?><form action="<?=$RootPath?>/<?=$eszkoztipus?><?=(isset($_GET['id'])) ? '/' . $_GET['id'] . '&action=update' : '&action=new' ?>&tipus=<?=$eszkoztipus?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        if(isset($_GET['id']))
        {
            ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
        }
        ?><div class="doublecolumn">
            <div><?php
                if($eszkoztipus != "simkartya")
                {
                    ?><div>
                        <label for="modell">Modell:</label><br>
                        <select id="modell" name="modell" required>
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
                ?>
                <datalist id="sorozatszamok"><?php
                    foreach($sorozatszamok as $x)
					{
						?><option><?=$x['sorozatszam']?></option><?php
					}
                ?></datalist>
                <div class="ttipcontainer">
                    <label for="sorozatszam"><?=($eszkoztipus != "simkartya") ? "Sorozatszám:" : "IMEI szám" ?></label><br>
                    <div class="tooltip" id="sorozatszamcheck">A megadott <?=($eszkoztipus != "simkartya") ? "sorozatszám" : "IMEI szám" ?> már létezik az adatbázisban!</div>
                    <input type="text" onkeyup="verifyExist('sorozatszamok', 'sorozatszam', 'sorozatszamcheck')" accept-charset="utf-8" name="sorozatszam" id="sorozatszam" value="<?=$sorozatszam?>"></input>
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

                if($eszkoztipus == "aktiveszkoz" || $eszkoztipus == "sohoeszkoz")
                {
                    ?><div>
                        <label for="mac">MAC Address:</label><br>
                        <input type="text" accept-charset="utf-8" name="mac" id="mac" value="<?=$mac?>"></input>
                    </div>

                    <?php $magyarazat .= "<strong>MAC Address</strong><p>Az eszköz MAC címe.</p>"; ?>

                    <div>
                        <label for="portszam"><?=($eszkoztipus == "aktiveszkoz") ? "Access" : "LAN" ?> portok száma:</label><br>
                        <input type="text" accept-charset="utf-8" name="portszam" id="portszam" value="<?=$portszam?>"></input>
                    </div>

                    <?php $magyarazat .= "<strong>Access/LAN portok száma</strong><p>A végponti eszközök csatlakoztatására tervezett portok száma.</p>"; ?>

                    <div>
                        <label for="uplinkportok"><?=($eszkoztipus == "aktiveszkoz") ? "Uplink" : "WAN" ?> portok száma:</label><br>
                        <input type="text" accept-charset="utf-8" name="uplinkportok" id="uplinkportok" value="<?=$uplinkportok?>"></input>
                    </div>

                    <?php $magyarazat .= "<strong>Uplink/WAN portok száma</strong><p>A hálózati eszközök csatlakoztatására tervezett portok száma.</p>"; ?>


                    <div>
                        <label for="wifi">WiFi:</label><br>
                        <select id="wifi" name="wifi">
                            <option value="" selected></option><?php
                            foreach($wifiszabvanyok as $x)
                            {
                                ?><option value="<?=$x["id"]?>" <?= ($wifi == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select>
                    </div>

                    <?php $magyarazat .= "<strong>WiFi</strong><p>Az eszköz által ismert legfrisebb WiFi szabvány.</p>"; ?>

                    <datalist id="firmwarelist"><?php
                    foreach($firmwarelista as $firmware)
                    {
                        ?><option><?=$firmware['nev']?></option><?php
                    }
                    ?></datalist>

                    <div>
                        <label for="szoftver">Firmware:</label><br>
                        <input type="text" accept-charset="utf-8" name="szoftver" id="szoftver" value="<?=$szoftver?>" list="firmwarelist"></input>
                    </div><?php

                    $magyarazat .= "<strong>Firmware</strong><p>Az eszközön futó szoftver verziószáma.</p>";
                }

                if($eszkoztipus == "mediakonverter")
                {
                    ?><div>
                        <label for="transzportszam">Transzport portszám:</label><br>
                        <select name="transzportszam">
                            <option value="" selected></option><?php
                            for($i = 1; $i <= 8; $i++)
                            {
                                ?><option value="<?=$i?>"><?=$i?></option><?php
                            }
                        ?></select>
                    </div>

                    <?php $magyarazat .= "<strong>PoE képes</strong><p>Akkor kell bejelölni, ha az eszköz rendelkezik PoE képességgel bíró portokkal.</p>"; ?>
                    
                    <div>
                        <label for="accessportszam">Access portszám:</label><br>
                        <select name="accessportszam">
                            <option value="" selected></option><?php
                            for($i = 1; $i <= 8; $i++)
                            {
                                ?><option value="<?=$i?>"><?=$i?></option><?php
                            }
                        ?></select>
                    </div><?php
                }

                if($eszkoztipus == "aktiveszkoz")
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
                        <label for="snmp">SNMP állapota:</label><br>
                        <input type="checkbox" accept-charset="utf-8" name="snmp" id="snmp" value="1" <?= ($snmp) ? "checked" : "" ?>></input>
                    </div>

                    <?php $magyarazat .= "<strong>SNMP állapota</strong><p>Akkor kell bejelölni, ha az eszköz adatai a jelen beállítások szerint lekérhetőek SNMP protokolon keresztük.</p>"; ?>

                    <div>
                        <label for="snmpcommunity">SNMP közösség:</label><br>
                        <input type="text" accept-charset="utf-8" name="snmpcommunity" id="snmpcommunity" value="<?=$snmpcommunity?>"></input>
                    </div>

                    <?php $magyarazat .= "<strong>SNMP közösség</strong><p>Az eszközön megadott, SNMP kapcsolódáshoz engedélyezett közösség neve.</p>"; ?>

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
                <div class="submit"><?php cancelForm(); ?></div>
            </div>
        </div>
    </form><?php
}