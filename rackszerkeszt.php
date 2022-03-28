<?php

if(@!$sajatolvas)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/rackdb.php");
    }

    $racknev = $rackhely = $rackgyarto = $rackunitszam = null;
    $button = "Beépítés";

    $ipcimek = mySQLConnect("SELECT * FROM ipcimek ORDER BY ipcim ASC");

    if(isset($_GET['id']))
    {
        $rackid = $_GET['id'];
        $rack = mySQLConnect("SELECT rackszekrenyek.nev AS nev, rackszekrenyek.helyiseg AS helyiseg, gyarto, unitszam, helyisegek.epulet AS epulet
            FROM rackszekrenyek
                INNER JOIN helyisegek ON rackszekrenyek.helyiseg = helyisegek.id
            WHERE rackszekrenyek.id = $rackid;");
        $rack = mysqli_fetch_assoc($rack);

        $racknev = $rack['nev'];
        $rackhely = $rack['helyiseg'];
        $rackgyarto = $rack['gyarto'];
        $rackunitszam = $rack['unitszam'];
        $epulet = $rack['epulet'];

        $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port
            FROM portok
                INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epulet
            UNION
            SELECT portok.id AS id, portok.port AS port
            FROM portok
                INNER JOIN transzportportok ON transzportportok.port = portok.id
                LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
            WHERE epulet = $epulet;");

        ?><div class="oldalcim"><p onclick="rejtMutat('portokrackbe')" style="cursor: pointer">Épület portok rackhez kötése</p></div>
        <div class="contentcenter" id="portokrackbe" style='display: none'>
            <form action="<?=$RootPath?>/portdb?action=generate&tipus=rack" method="post" onsubmit="beKuld.disabled = true; return true;">
                <input type ="hidden" id="rack" name="rack" value=<?=$rackid?>>

                <div>
                    <label for="elsoport">Első port:</label><br>
                    <select id="elsoport" name="elsoport"><?php
                        foreach($epuletportok as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div>
                    <label for="utolsoport">Utolsó port:</label><br>
                    <select id="utolsoport" name="utolsoport"><?php
                        foreach($epuletportok as $x)
                        {
                            ?><option value="<?=$x["id"]?>" selected><?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="Portok rackhez kötése"></div>
            </form>
        </div><?php

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/rackszerkeszt&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$rackid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/rackszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Rack szerkesztése</div>
    <div class="contentcenter">

        <div>
            <label for="nev">Rack neve:</label><br>
            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$racknev?>"></input>
        </div>

        <div>
            <label for="unitszam">Rack unitszáma:</label><br>
            <input type="text" accept-charset="utf-8" name="unitszam" id="unitszam" value="<?=$rackunitszam?>"></input>
        </div>

        <?=helyisegPicker($rackhely)?>

        <?=gyartoPicker($rackgyarto)?>

        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
    cancelForm();
?></div><?php
}