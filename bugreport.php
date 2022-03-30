<?php

if(!$_SESSION[getenv('SESSION_NAME').'id'])
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $button = "Bug jelentése";

    $id	= $cim = $leiras = $lezarva	= $oldal = $tipus = $prioritas = null;

    if(isset($_GET['oldal']))
    {
        $oldal = $_GET['oldal'];
    }
    
    ?><div class="oldalcim">Bug report</div>

    <div class="contentcenter">
        <form action="<?=$RootPath?>/bugreportdb?action=new" method="post" onsubmit="beKuld.disabled = true; return true;">

            <div>
                <label for="cim">Cím:</label><br>
                <input type="text" accept-charset="utf-8" name="cim" id="cim" value="<?=$cim?>"></input>
            </div>

            <div>
                <label for="leiras">Hiba leírása:</label><br>
                <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
            </div>

            <div>
                <label for="oldal">A hiba helye:</label><br>
                <input type="text" accept-charset="utf-8" name="oldal" id="oldal" value="<?=$oldal?>"></input>
            </div><?php

            bugTypePicker($tipus);

            priorityPicker($prioritas);

            ?><div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form><?php
        cancelForm();
    ?></div><?php
}