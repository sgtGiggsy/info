<?php
if(@$irhat)
{
    $currpage = $RootPath . "/" . $_GET['page'] . ((isset($_GET['id'])) ? "/" . $_GET['id'] : "" ) . "?beepites" . (($_GET['beepites']) ? "=" . $beepid : "") . "&action=" . (($_GET['beepites']) ? 'update' : 'new' ) . $kuldooldal;
    ?><form action="<?=$currpage?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        if($_GET['beepites'])
        {
            ?><input type ="hidden" id="id" name="id" value=<?=$beepid?>><?php
        }

        ?><div class="doublecolumn">
            <div><?php

                eszkozPicker($beepeszk, ($beepid) ? true : false);
                $magyarazat .= "<strong>Eszköz</strong><p>Csak a beépítés során módosítható. Amennyiben új eszköz kerül a helyére,
                    a jelen eszköz beépítését le kell zárni a kiépítési idő megadásával, majd a másik eszközt új beépítésként felvinni.</p>";

                if(!$tipus || $tipus == "aktiveszkoz" || $tipus == "nyomtato" || $tipus == "telefonkozpont" || $tipus == "sohoeszkoz" || $tipus == "mediakonverter")
                {
                            
                    ?><div>
                        <label for="nev">Beépítési név:</label><br>
                        <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$beepnev?>"></input>
                    </div><?php
                    $magyarazat .= "<strong>Beépítési név</strong><p>Az eszköz hálózati neve</p>";
                }
                        
                if(!$tipus || $tipus == "aktiveszkoz" || $tipus == "nyomtato" || $tipus == "sohoeszkoz")
                {
                    ?><div>
                        <label for="ipcim">IP cím:</label><br>
                        <select id="ipcim" name="ipcim" style="width: 80%;">
                            <option value="" selected></option><?php
                            foreach($ipcimek as $x)
                            {
                                ?><option value="<?php echo $x["id"] ?>" <?= ($beepip == $x['id']) ? "selected" : "" ?>><?=$x['ipcim']?></option><?php
                            }
                        ?></select><?php
                        $ipreuselink = str_replace("update", "edit", $currpage);
                        $ipreuselink = str_replace("new", "addnew", $ipreuselink);
                        $ipreuselink .= "&ipreuse";
                        ?><button  type="button" onclick="location.href='<?=$ipreuselink?>'" style="width: 19%;">Foglalt IP használata</button>
                    </div><?php
                    $magyarazat .= "<strong>IP cím</strong><p>Az eszköz jelenlegi IP címe.</p>";
                }

                if(!$tipus || $tipus == "aktiveszkoz" || $tipus == "nyomtato" || $tipus == "telefonkozpont" || $tipus == "mediakonverter" || $tipus == "sohoeszkoz")
                {
                    
                    helyisegPicker($beephely, "helyiseg");
                    $magyarazat .= "<strong>Helyiség</strong><p>Csak abban az esetben kell megadni, ha az eszköz <b>nincs</b> rack szekrénybe építve.</p>";
                }

                if(!$tipus || $tipus == "aktiveszkoz" || $tipus == "mediakonverter" || $tipus == "sohoeszkoz")
                {
                    rackPicker($beeprack);
                    $magyarazat .= "<strong>Rackszekrény</strong><p>Amennyiben az eszköz rackszekrénybe van építve, csak ezt kell megadni.</p>";
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
                    $magyarazat .= "<strong>Switchport</strong><p>A switch portja, amibe az adott bővítő csatlakoztatva van. A listában csak <b>az üres uplink</b> portok jelennek meg.
                    Ha a keresett port nem található itt, úgy a rendszer foglaltként ismeri, vagy nem uplink típusúként van beállítva.</p>";
                }

                if(!$tipus || $tipus == "aktiveszkoz" || $tipus == "mediakonverter" || $tipus == "sohoeszkoz")
                {
                    vlanPicker($vlan);
                    $magyarazat .= "<strong>VLAN</strong><p>A VLAN, amelyhez az eszköz csatlakoztatva van. Trunk-höz csatlakoztatott eszközök esetében nem szükséges megadni.</p>";
                }

                if(!$tipus || $tipus == "aktiveszkoz")
                {
                    ?><div>
                        <label for="pozicio">Pozíció:</label><br>
                        <input type="text" id="pozicio" name="pozicio" value="<?=$beeppoz?>">
                    </div><?php
                    $magyarazat .= "<strong>Pozíció</strong><p>Az eszköz helye a rackszekrényben, felülről lefelé haladva.</p>";
                }

                ?><div>
                    <label for="beepitesideje">Beépítés ideje</label><br>
                    <input type="date" id="beepitesideje" name="beepitesideje" value="<?=$beepido?>"><button style="margin-left: 10px;" onclick="getMa('beepitesideje'); return false;">Ma</button>
                    <?php $magyarazat .= "<strong>Beépítés ideje</strong><p>Az időpont amikor az eszköz a jelen helyére került.</p>"; ?>
                </div>

                <div>
                    <label for="kiepitesideje">Kiépítés ideje</label><br>
                    <input type="date" id="kiepitesideje" name="kiepitesideje" value="<?=$beepkiep?>"><button style="margin-left: 10px;" onclick="getMa('kiepitesideje'); return false;">Ma</button>
                    <?php $magyarazat .= "<strong>Kiépítés ideje</strong><p>Az időpont amikor az eszköz a kiépítésre került.</p>"; ?>
                </div>

            </div>
            <div><?php

                if(!$tipus || $tipus == "aktiveszkoz" || $tipus == "nyomtato" || $tipus == "sohoeszkoz")
                {
                    ?><div>
                        <label for="admin">Admin user:</label><br>
                        <input type="text" accept-charset="utf-8" name="admin" id="admin" value="<?=$admin?>"></input>
                    </div>
                    <?php $magyarazat .= "<strong>Admin user</strong><p>Az eszköz adminisztrációjához használt felhasználónév. Központilag menedzselt eszközök esetében nem szükséges megadni.</p>"; ?>

                    <div>
                        <label for="pass">Jelszó:</label><br>
                        <input type="text" accept-charset="utf-8" name="pass" id="pass" value="<?=$pass?>"></input>
                        <?php $magyarazat .= "<strong>Jelszó</strong><p>Az eszköz adminisztrációjához használt jelszó. Központilag menedzselt eszközök esetében nem szükséges megadni.</p>"; ?>
                    </div><?php
                }
                
                ?><div>
                    <label for="megjegyzes">Megjegyzés:</label><br>
                    <textarea accept-charset="utf-8" name="megjegyzes" id="megjegyzes"><?=$megjegyzes?></textarea>
                    <?php $magyarazat .= "<strong>Megjegyzés</strong><p>A <b>beépítéshez</b> kapcsolódó esetleges magyarázat. Az eszközhöz kapcsolódó, <b>beépítési helytől független</b> megjegyzéseket <b>nem</b> itt kell megadni.</p>"; ?>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                <div class="submit"><?php cancelForm();?></div>
    </form><?php    
                if(isset($_GET['id']) && (!$tipus || $tipus == "aktiveszkoz" || $tipus == "sohoeszkoz"))
                {
                    ?><form style="margin-top: 1em" action="<?=$RootPath?>/portdb&action=clearportassign<?=$kuldooldal?>" method="post" onsubmit="return confirm('Figyelem!!!\nEzzel a switch ÖSSZES porthozzárendelését törlöd, nem csak a jelen beépítéshez tartozókat!\nBiztosan törölni szeretnéd a switch porthozzárendeléseit?');">
                        <input type ="hidden" id="eszkoz" name="eszkoz" value=<?=$beepeszk?>>
                        <div class="submit"><input type="submit" name="beKuld" value="Porthozzárendelések törlése"></div>
                    </form><?php
                    $magyarazat .= "<strong>Porthozzárendelések törlése</strong><p>Kiépítéskor törölni kell a switchez csatlakoztatott portok listáját, máskülönben azok továbbra is foglaltnak lesznek jelezve.</p>";
                }
            ?></div>
        </div><?php
}