<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/telefonszam?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
            <input type ="hidden" id="id" name="id" value=<?=$id?>>
            
            <div>
                <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                <label for="cimke">Címke:</label><br>
                <input type="text" accept-charset="utf-8" name="cimke" id="cimke" value="<?=$cimke?>"></input>
            </div>

            <?php $magyarazat .= "<strong>Címke</strong><p>A telefonszámhoz tartozó címke.<br><span style='color: #940e0e;'>Módosítható, de amint importálás történik a központból,
                a módosítás felülírásra kerül.</span></p>"; ?>
            
            <div>
                <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                <label for="jog">Jog:</label><br>
                <select name="jog">
                    <option value=""></option><?php
                    foreach($jogok as $x)
                    {
                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $jog) ? "selected" : "" ?>><?=$x['id']?></option><?php
                    }
                ?></select>
            </div>

            <?php $magyarazat .= "<strong>Jog</strong><p>A telefonszámhoz rendelt jog kódja.<br><span style='color: #940e0e;'>Módosítható, de amint importálás történik a központból,
                a módosítás felülírásra kerül.</span></p>"; ?>

            <div>
                <label for="port">Végpont:</label><br>
                <select name="port">
                    <option value=""></option><?php
                    foreach($portok as $x)
                    {
                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $port) ? "selected" : "" ?>><?=$x['epuletszam'] . ". épület, " . $x['port'] . " port" ?></option><?php
                    }
                ?></select>
            </div>

            <?php $magyarazat .= "<strong>Végpont</strong><p>A végpont, amire a számhoz tartozó készülék fizikailag csatlakoztatva van jelenleg.</span></p>"; ?>

            <div>
                <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                <label for="tkozpontport">Lage:</label><br>
                <select name="tkozpontport">
                    <option value=""></option><?php
                    foreach($tkozpontportok as $x)
                    {
                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $tkozpontport) ? "selected" : "" ?>><?=$x['kozpontnev'] . " központ, " . $x['port'] . " port" ?></option><?php
                    }
                ?></select>
            </div>

            <?php $magyarazat .= "<strong>Lage</strong><p>A központ lage portja, amelyen a telefonszám jelenleg található.<br><span style='color: #940e0e;'>Módosítható, de amint importálás történik a központból,
                a módosítás felülírásra kerül.</span></p>"; ?>

            <div>
                <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                <label for="tipus">Típus:</label><br>
                <select name="tipus">
                    <option value=""></option><?php
                    foreach($tipusok as $x)
                    {
                        ?><option value="<?=$x['id']?>" <?=($x['id'] == $tipus) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                    }
                ?></select>
            </div>

            <?php $magyarazat .= "<strong>Típus</strong><p>A vonal végén található eszköz pontos típusa.<br><span style='color: #940e0e;'>Módosítható, de amint importálás történik a központból,
                a módosítás felülírásra kerül.</span></p>"; ?>

            <div>
                <label for="megjegyzes">Megjegyzés:</label><br>
                <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
            </div>

            <?php $magyarazat .= "<strong>Megjegyzés</strong><p>A számhoz tartozó megjegyzés. Fontos, hogy ez a megjegyzés a <b>telefonszámhoz</b> kötött,
                és <b>nem</b> a lage porthoz, amin a központban található. A lage porthoz tartozó megjegyzést a port menüpontjában lehet felvinni.</span></p>"; ?>

            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form><?php
        cancelForm();
    ?></div><?php
}