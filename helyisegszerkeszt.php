<?php

if(!@$mindir)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST))
    {
        $irhat = true;
        include("./db/helyisegdb.php");
    }

    $epuletek = mySQLConnect("SELECT epuletek.id AS id, szam, epuletek.nev AS nev, telephelyek.telephely AS telephely, epulettipusok.tipus AS tipus
        FROM epuletek
            LEFT JOIN telephelyek ON epuletek.telephely = telephelyek.id
            LEFT JOIN epulettipusok ON epuletek.tipus = epulettipusok.id;");

    $helyisegszam = $helyisegnev = $emelet = null;

    $button = "Új helyiség";

    if($_GET['page'] == "helyisegszerkeszt" && isset($_GET['id']))
    {
        $helyisegid = $_GET['id'];
        $button = "Helyiség szerkesztése";
        $helyiseg = mySQLConnect("SELECT * FROM helyisegek WHERE id = $helyisegid;");
        $helyiseg = mysqli_fetch_assoc($helyiseg);

        $epid = $helyiseg['epulet'];
        $helyisegszam = $helyiseg['helyisegszam'];
        $helyisegnev = $helyiseg['helyisegnev'];
        $emelet = $helyiseg['emelet'];

        $epuletportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port
        FROM portok
            INNER JOIN vegpontiportok ON vegpontiportok.port = portok.id
            LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
        WHERE epulet = $epid AND vegpontiportok.helyiseg IS NULL
        UNION
        SELECT portok.id AS id, portok.port AS port
        FROM portok
            INNER JOIN transzportportok ON transzportportok.port = portok.id
            LEFT JOIN kapcsolatportok ON portok.id = kapcsolatportok.port
        WHERE epulet = $epid;");

        ?><div class="oldalcim"><p onclick="rejtMutat('portokhelyisegbe')" style="cursor: pointer">Épület portok helyiséghez kötése</p></div>
        <div class="contentcenter" id="portokhelyisegbe" style='display: none'>
            <form action="<?=$RootPath?>/portdb?action=generate&tipus=helyiseg" method="post" onsubmit="beKuld.disabled = true; return true;">
                <input type ="hidden" id="helyiseg" name="helyiseg" value=<?=$helyisegid?>>

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
                            ?><option value="<?=$x["id"]?>"><?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="Portok helyiséghez kötése"></div>
            </form>
        </div>
        <div class="oldalcim">Helyiség szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/helyisegdb?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$helyisegid?>><?php
    }
    else
    {
        if(!isset($epid))
        {
            ?><div class="oldalcim">Helyiség szerkesztése</div><?php
            $epid = null;
        }
        ?><div class="contentcenter">
        <form action="<?=$RootPath?>/helyisegdb?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div>
        <label for="epulet">Épület:</label><br>
        <select id="epulet" name="epulet">
            <option value=""></option><?php
            foreach($epuletek as $x)
            {
                ?><option value="<?=$x["id"]?>" <?=($x['id'] == $epid) ? "selected" : "" ?>><?=$x['szam']?>. <?=$x['tipus']?> (<?=$x['nev']?>)</option><?php
            }
        ?></select>
    </div>

    <div>
        <label for="emelet">Emelet:</label><br>
        <select id="emelet" name="emelet">
            <option value=""></option>
            <option value="0" <?=($emelet != null && $emelet == 0) ? "selected" : "" ?>>Földszint</option>
            <option value="1" <?=($emelet == 1) ? "selected" : "" ?>>Első emelet</option>
            <option value="2" <?=($emelet == 2) ? "selected" : "" ?>>Második emelet</option>
            <option value="3" <?=($emelet == 3) ? "selected" : "" ?>>Harmadik emelet</option>
            <option value="4" <?=($emelet == 4) ? "selected" : "" ?>>Negyedik emelet</option>
        </select>
    </div>

    <div>
        <label for="helyisegszam">Helyiség száma:</label><br>
        <input type="text" accept-charset="utf-8" name="helyisegszam" id="helyisegszam" value="<?=$helyisegszam?>"></input>
    </div>

    <div>
        <label for="helyisegnev">Helyiség megnevezése:</label><br>
        <input type="text" accept-charset="utf-8" name="helyisegnev" id="helyisegnev" value="<?=$helyisegnev?>"></input>
    </div>

    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form>
    </div><?php
    cancelForm();
}