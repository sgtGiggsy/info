<?php
if(@$irhat)
{
    $javascriptfiles[] = "modules/epuletek/includes/portmuveletek.js";
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/epulet?action=szamtarsitas<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
            <div class="portparositas"><?php

                $magyarazat = "<h2>Portok telefonszámmal társítása</h2>";
                $magyarazat .= "<strong>Előzetes tudnivaló</strong><p>A rendszer limitációja miatt
                    erről a felületről nagyjából 500 portra lehet kirendezni számokat. Valószínűleg ezt a limitet
                    a gyakorlatban sosem érjük el, de azért mindig ajánlatos visszaellenőrizni, hogy az utolsó
                    kirendezett porton is megjelennek-e a telefonszámok.</p>
                    <p>A listában csak azok a telefonszámok jelennek meg, amelyek nincsenek még portra kirendezve.
                    Ha itt nem szerepel egy kirendezni kívánt telefonszám, úgy az megtalálható egy másik portra
                    kirendezve valahol.</p>";
                $magyarazat .= "<strong>Újabb számmező</strong><p>Ezzel a gombbal adhatunk hozzá egy
                    újabb számot a porthoz.</p>";
                $i = 1;
                $elem = null;
                // Tömb a már megtalált számok részére. Erre azért van szükség, mert egy porton
                // akár négy szám is futhat, de egy szám nem futhat több porton.
                $megtalalt = array();

                // Végigmegyünk az épület összes végpontján
                foreach($epuletportok as $x)
                {
                    ?><div class="infobox">
                        <input type ="hidden" id="portid-<?=$i?>" name="portid-<?=$i?>" value="<?=$x['id']?>">
                        <div class="infoboxtitle"><?=$x['port']?></div>
                        
                        <div class="infoboxbodybp"><?php

                            // Mivel egy nyolceres végponton négy szám lehet,
                            // négy formot hozunk létre hozzá
                            for($j = 1; $j < $maxhidra + 1; $j++)
                            {
                                $elem = $j;
                                $hozzaadott = false;
                                ?><div>
                                    <select id="telefonszam-<?=$i?>-<?=$j?>" name="telefonszam-<?=$i?>-<?=$j?>">
                                        <option value=""></option><?php

                                        // Végigmegyünk az összes telefonszámon.
                                        foreach($telefonszamok as $telefonszam)
                                        {
                                            if(($telefonszam['port'] == $x['id'] && !in_array($telefonszam['id'], $megtalalt)) || !$telefonszam['port']) // Ha a telefonszám nem szerepelt még az adott port egyik érpárján sem, megjeleníthetjük
                                            {
                                                ?><option value="<?=$telefonszam["id"]?>"
                                                        <?php 
                                                            if($telefonszam['port'] == $x['id'] && !$hozzaadott)
                                                            {
                                                                echo "selected";

                                                                // Hozzáadjuk a megtalált számok tömbjéhez,
                                                                // majd kitesszük az ID-ját egy rejtett inputba,
                                                                // hogy törlés esetén az adatbázis műveleteket intéző
                                                                // oldal tudja, melyik telefonszámról kell törölni
                                                                // a port társítást
                                                                $megtalalt[] = $telefonszam['id'];
                                                                $hozzaadott = $telefonszam['id'];
                                                            }
                                                        ?>
                                                    ><?=$telefonszam['szam']?>
                                                </option><?php
                                            }
                                        }
                                    ?></select>
                                </div><?php
                                // Ez a rejtett input csak olyan portokhoz jön létre, amik
                                // számhoz vannak társítva
                                if($hozzaadott)
                                {
                                    ?><input type ="hidden" id="nullid-<?=$i?>-<?=$j?>" name="nullid-<?=$i?>-<?=$j?>" value="<?=$hozzaadott?>"><?php
                                }
                                else
                                {
                                    break;
                                }
                            }
                            
                            for($s = $elem + 1; $s < $maxhidra + 1; $s++)
                            {
                                ?><div id="telefondiv-<?=$i?>-<?=$s?>" style="display: none"></div><?php
                            }
                            
                            ?><div style="grid-column-start: 1; grid-column-end: 3;">
                                <button id="button-<?=$i?>" type="button" onclick="addSelect(<?=$i?>, <?=$elem + 1?>); return false;">Újabb számmező</button>
                            </div>
                        </div>
                    </div><?php
                    $i++;
                }

                ?></div>
            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
    </div>

    <div id="selecttoadd" style="display: none">
            <option value=""></option><?php
            // Végigmegyünk az összes telefonszámon.
            foreach($telefonszamok as $telefonszam)
            {
                if(!$telefonszam['port'])
                {
                    ?><option value="<?=$telefonszam["id"]?>"><?=$telefonszam['szam']?></option><?php
                }
            }
        ?></select>
    </div>
    
    <script>
        
    </script><?php
}