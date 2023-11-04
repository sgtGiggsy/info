<?php
$javascriptfiles[] = "modules/alap/includes/portreset.js";
$portok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, IF(vegpontiportok.epulet IS NULL, IF(transzportportok.epulet IS NOT NULL, transzportportok.epulet, NULL), vegpontiportok.epulet) AS epuletid, (SELECT nev FROM epuletek WHERE id = epuletid) AS epuletszam
        FROM portok
            LEFT JOIN vegpontiportok ON vegpontiportok.port = portok.id
            LEFT JOIN transzportportok ON transzportportok.port = portok.id
            LEFT JOIN switchportok ON switchportok.port = portok.id
            LEFT JOIN mediakonverterportok ON mediakonverterportok.port = portok.id
            LEFT JOIN sohoportok ON sohoportok.port = portok.id
            LEFT JOIN tkozpontportok ON tkozpontportok.port = portok.id
        WHERE switchportok.eszkoz IS NULL AND mediakonverterportok.eszkoz IS NULL AND sohoportok.eszkoz IS NULL AND tkozpontportok.eszkoz IS NULL;");
$magyarazat .= "<strong>Portok resetelése</strong>
    <p>Ezzel lehet a kiválasztott tartomány portjainak helyiség és rack hozzárendeléseit törölni.
    Technikai okokból, itt az ÖSSZES port feltüntetésre kerül!</p>";

if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/portdb?action=reset<?=$kuldooldal?>" method="post" id="portresetform">
                <div>
                    <label for="elsoport">Első port:</label><br>
                    <select id="elsoport" name="elsoport"><?php
                        foreach($portok as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['epuletszam']?> - <?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div>
                    <label for="utolsoport">Utolsó port:</label><br>
                    <select id="utolsoport" name="utolsoport"><?php
                        foreach($portok as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['epuletszam']?> - <?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="Portok resetelése"></div>
            </form>
            <?= cancelForm(); ?>
        </div>
    </div><?php
}