<?php
if(@$irhat)
{
    ?><div class="contentcenter">

            <form action="<?=$RootPath?>/epulet?action=szamtarsitas<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
                <div class="portparositas"><?php

                    $i = 1;
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
                                for($j = 1; $j < 5; $j++)
                                {
                                    $hozzaadott = false;
                                    ?><select id="telefonszam-<?=$i?>-<?=$j?>" name="telefonszam-<?=$i?>-<?=$j?>">
                                        <option value=""></option><?php

                                        // Végigmegyünk az összes telefonszámon.
                                        foreach($telefonszamok as $telefonszam)
                                        {
                                            ?><option value="<?=$telefonszam["id"]?>"
                                                    <?php if($telefonszam['port'] == $x['id'] && !in_array($telefonszam['id'], $megtalalt)) // Ha a telefonszám nem szerepelt még az adott port egyik érpárján sem, megjeleníthetjük
                                                        {
                                                            if(!$hozzaadott)
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
                                                        }
                                                    ?>
                                                ><?=$telefonszam['szam']?>
                                            </option><?php
                                        }
                                    ?></select><?php
                                    // Ez a rejtett input csak olyan portokhoz jön létre, amik
                                    // számhoz vannak társítva
                                    if($hozzaadott)
                                    {
                                        ?><input type ="hidden" id="nullid-<?=$i?>-<?=$j?>" name="nullid-<?=$i?>-<?=$j?>" value="<?=$hozzaadott?>"><?php
                                    }
                                }
                            ?></div>
                        </div><?php
                        $i++;
                    }

                    ?></div>
                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form>

    </div><?php
}