<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/epulet?action=transzporttarsitas<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
            <div class="portparositas"><?php

                $magyarazat = "<h2>Transzport portok más transzform portokkal társítása</h2>";
                $magyarazat .= "<strong></strong><p></p>";
                $i = 1;

                // Végigmegyünk az épület összes transzport portján
                foreach($epuletportok as $x)
                {
                    ?><div class="infobox">
                        <div class="infoboxtitle"><?=$x['port']?></div>
                        <div class="infoboxbodybp">
                            <div style="grid-column-start: 1; grid-column-end: 3;">
                                <div>
                                    <input type ="hidden" id="portid-<?=$i?>" name="portid-<?=$i?>" value="<?=$x['id']?>">
                                    <input type ="hidden" id="oldszomszed-<?=$i?>" name="oldszomszed-<?=$i?>" value="<?=$x['csatlakozas']?>">
                                    <label for="szomszed-<?=$i?>">Kapcsolódó épület portja</label>
                                    <select id="szomszed-<?=$i?>" name="szomszed-<?=$i?>">
                                        <option value=""></option><?php
                                        foreach($masepuletportok as $szomszed)
                                        {
                                            ?><option value="<?=$szomszed['id']?>" <?=($x['csatlakozas'] == $szomszed['id']) ? "selected" : "" ?>><?=$szomszed['epuletszam']?>. épület - <?=$szomszed['port']?></option>
                                            <?php
                                        }
                                    ?></select>
                                </div>
                                <div>
                                    <label for="hurok-<?=$i?>">Épületen belüli áthurkolás</label>
                                    <select class="hurkok" id="hurok-<?=$x['id']?>" name="hurok-<?=$i?>" onchange="atHurkolas(<?=$i?>, <?=$x['id']?>);">
                                        <option value=""></option><?php
                                        foreach($epuletportok as $hurok)
                                        {
                                            $select = null;
                                            if($x['id'] == $x['port1'])
                                            {
                                                $select = $x['port2'];
                                            }
                                            elseif($x['id'] == $x['port2'])
                                            {
                                                $select = $x['port1'];
                                            }

                                            ?><option value="<?=$hurok['id']?>" <?=($select == $hurok['id']) ? "selected" : "" ?>><?=$hurok['port']?></option>
                                            <?php
                                        }
                                    ?></select>
                                </div>
                            </div>
                        </div>
                    </div><?php
                    $i++;
                }

            ?></div>
            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
    </div>
    
    <script>
        function atHurkolas(sorsz, port) {
            // Nullázás, törölni kell minden korábbi társítást mielőtt az újat felvesszük
            var torlendo = document.getElementsByClassName("hurkok");
            l = torlendo.length;
            for (i = 0; i < l; i++) {
                var selElmnt = torlendo[i];
                if(selElmnt.value == port) {
                    selElmnt.value = "";
                }
            }

            var select = document.getElementById("hurok-" + port);
            var value = select.value;
            var text = select.options[select.selectedIndex].text;

            var tulold = document.getElementById("hurok-" + value);
            if(tulold) {
                tulold.value = port;
            }
        }
    </script><?php
}