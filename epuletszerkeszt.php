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
        include("./db/epuletdb.php");
    }
    
    $szam = $telephely = $nev = $tipus = null;

    $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
    $epulettipusok = mySQLConnect("SELECT * FROM epulettipusok;");
    $tulajdonosok = mySQLConnect("SELECT * FROM alakulatok;");
    $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
    
    $button = "Új épület";
    
    if(isset($_GET['id']))
    {
        $epid = $_GET['id'];
        $epulet = mySQLConnect("SELECT * FROM epuletek WHERE id = $epid;");
        $epulet = mysqli_fetch_assoc($epulet);

        ?><div class="oldalcim"><p onclick="rejtMutat('helyiseggeneralas')" style="cursor: pointer">Helyiségek generálása az épülethez</p></div>
        <div class="contentcenter" id="helyiseggeneralas" style='display: none'>
            <form action="<?=$RootPath?>/helyisegdb?action=generate" method="post" onsubmit="beKuld.disabled = true; return true;">
                <input type ="hidden" id="epulet" name="epulet" value=<?=$epid?>>
                
                <div>
                    <label for="emelet">Emelet<small> (földszint = 0)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="emelet" id="emelet"></input>
                </div>

                <div>
                    <label for="kezdohelyisegszam">Kezdő helyiség szám<small> (csak a száma)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="kezdohelyisegszam" id="kezdohelyisegszam"></input>
                </div>

                <div>
                    <label for="zarohelyisegszam">Záró helyiség szám<small> (csak a száma)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="zarohelyisegszam" id="zarohelyisegszam"></input>
                </div>

                <div>
                    <label for="szamjegyszam">Számjegyek száma<small> (pl.: 002-es helyiség = 3)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="szamjegyszam" id="szamjegyszam"></input>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="Helyiségek generálása"></div>
            </form>
        </div><?php

        ?><div class="oldalcim"><p onclick="rejtMutat('vegpontgeneralas')" style="cursor: pointer">Végpontok generálása az épülethez</p></div>
        <div class="contentcenter" id="vegpontgeneralas" style='display: none'>
            <form action="<?=$RootPath?>/portdb?action=generate&tipus=vegpont" method="post" onsubmit="beKuld.disabled = true; return true;">
                <input type ="hidden" id="epulet" name="epulet" value=<?=$epid?>>

                <div>
                    <label for="portelotag">Port előtag<small> (pl R11/)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="portelotag" id="portelotag"></input>
                </div>

                <div>
                    <label for="kezdoport">Kezdő port<small> (csak a száma)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="kezdoport" id="kezdoport"></input>
                </div>

                <div>
                    <label for="zaroport">Záró port<small> (csak a száma)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="zaroport" id="zaroport"></input>
                </div>

                <div>
                    <label for="nullara">Nullára kiegészítés számjegye<br><small> (pl.: ha 001 legyen 1 helyett, akkor 2)</small>:</label><br>
                    <input type="text" accept-charset="utf-8" name="nullara" id="nullara"></input>
                </div>

                <div>
                    <label for="csatlakozo">Csatlakozó típusa:</label><br>
                    <select id="csatlakozo" name="csatlakozo"><?php
                        foreach($csatlakozok as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['nev']?></option><?php
                        }
                    ?></select>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="Végpontok generálása"></div>
            </form>
        </div>
        
        <div class="oldalcim"><p onclick="rejtMutat('ujhelyiseg')" style="cursor: pointer">Új helyiség létrehozása</p></div>
        <div id="ujhelyiseg" style='display: none'>
            <?php $mindir = true; include("./helyisegszerkeszt.php"); ?>
        </div><?php

        $szam = $epulet['szam'];
        $telephely = $epulet['telephely'];
        $nev = $epulet['nev'];
        $tipus = $epulet['tipus'];

        $button = "Szerkesztés";

        ?><div class="oldalcim">Épület szerkesztése</div>
        <div class="contentcenter">
        <form action="<?=$RootPath?>/epuletszerkeszt&action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$epid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/epuletszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }
    
    ?><div>
        <label for="telephely">Telephely:</label><br>
        <select id="telephely" name="telephely">
            <option value="" selected></option><?php
            foreach($telephelyek as $x)
            {
                ?><option value="<?=$x["id"]?>" <?= ($telephely == $x['id']) ? "selected" : "" ?>><?=$x['telephely']?></option><?php
            }
        ?></select>
    </div>

    <div>
        <label for="szam">Épület rajzszáma:</label><br>
        <input type="text" accept-charset="utf-8" name="szam" id="szam" value="<?=$szam?>"></input>
    </div>

    <div>
        <label for="nev">Épület neve:</label><br>
        <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
    </div>

    <div>
        <label for="tipus">Épülettipus:</label><br>
        <select id="tipus" name="tipus">
            <option value="" selected></option><?php
            foreach($epulettipusok as $x)
            {
                ?><option value="<?=$x["id"]?>" <?= ($tipus == $x['id']) ? "selected" : "" ?>><?=$x['tipus']?></option><?php
            }
        ?></select>
    </div>

    <div class="submit"><input type="submit" name="beKuld" value=<?=$button?>></div>
    </form>
    </div><?php
}