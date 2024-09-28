<?php
if(@$irhat)
{
    $javascriptfiles[] = "modules/epuletek/includes/portmuveletek.js";
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/epulet?action=vegponthurkolas" method="post" onsubmit="beKuld.disabled = true; return true;">
            <div class="portparositas"><?php

                $magyarazat = "<h2>Épület végponti portjainak összehurkolása</h2>";
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
                                    <input type ="hidden" id="portid-<?=$i?>" name="portid[]" value="<?=$x['id']?>">
                                    <label for="hurok-<?=$i?>">Épületen belüli áthurkolás</label>
                                    <select class="hurkok" id="hurok-<?=$x['id']?>" name="hurok[]" onchange="atHurkolas(<?=$x['id']?>);">
                                        <option value=""></option><?php
                                        foreach($epuletportok as $hurok)
                                        {
                                            ?><option value="<?=$hurok['id']?>" <?=($x['id'] == $hurok['athurkolas']) ? "selected" : "" ?>><?=$hurok['port']?></option><?php
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
        <?= cancelForm(); ?>
    </div><?php
}