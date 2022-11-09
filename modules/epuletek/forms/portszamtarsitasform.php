<?php
if(@$irhat)
{
    ?><div class="contentcenter">

            <form action="<?=$RootPath?>/epulet?action=szamtarsitas<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
                <div class="portparositas"><?php

                    $i = 1;
                    $megtalalt = array();
                    foreach($epuletportok as $x)
                    {
                        ?><div class="infobox">
                            <input type ="hidden" id="portid-<?=$i?>" name="portid-<?=$i?>" value="<?=$x['id']?>">
                            <div class="infoboxtitle"><?=$x['port']?></div>
                            
                            <div class="infoboxbodybp"><?php
                                for($j = 1; $j < 5; $j++)
                                {
                                    $hozzaadott = false;
                                    ?><select id="telefonszam-<?=$i?>-<?=$j?>" name="telefonszam-<?=$i?>-<?=$j?>">
                                        <option value=""></option><?php
                                        foreach($telefonszamok as $telefonszam)
                                        {
                                            ?><option value="<?=$telefonszam["id"]?>"
                                                    <?php if($telefonszam['port'] == $x['id'] && !in_array($telefonszam['id'], $megtalalt))
                                                        {
                                                            if(!$hozzaadott)
                                                            {
                                                                echo "selected";
                                                                $megtalalt[] = $telefonszam['id'];
                                                                $hozzaadott = $telefonszam['id'];
                                                            }
                                                        }
                                                    ?>
                                                ><?=$telefonszam['szam']?>
                                            </option><?php
                                        }
                                    ?></select><?php
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