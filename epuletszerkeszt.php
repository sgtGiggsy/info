<?php

if(!@$mindir)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/epuletdb.php");
    }
    
    $szam = $telephely = $nev = $tipus = $magyarazat = null;

    $telephelyek = mySQLConnect("SELECT * FROM telephelyek;");
    $epulettipusok = mySQLConnect("SELECT * FROM epulettipusok;");
    $tulajdonosok = mySQLConnect("SELECT * FROM alakulatok;");
    $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
    
    $button = "Új épület";
    $oldalfejlec = "Új épület hozzáadása";
    
    if(isset($_GET['id']))
    {
        $epid = $_GET['id'];
        $epulet = mySQLConnect("SELECT * FROM epuletek WHERE id = $epid;");
        $epulet = mysqli_fetch_assoc($epulet);

        $szam = $epulet['szam'];
        $telephely = $epulet['telephely'];
        $nev = $epulet['nev'];
        $tipus = $epulet['tipus'];

        $button = "Szerkesztés";
        $oldalfejlec = "Épület szerkesztése";     
    }

    ?><div class="szerkcard">
        <div class="szerkcardtitle"><?=$oldalfejlec?><a class="help" onclick="rejtMutat('magyarazat')">?</a></div><?php
        if(isset($_GET['id']))
        {
            ?><div class="szerkcardoptions">
                <div class="szerkcardoptionelement"><span onclick="showSlideIn('1')">Helyiségek generálása</span></div>
                <div class="szerkcardoptionelement"><span onclick="showSlideIn('2')">Végpontok generálása</span></div>
                <div class="szerkcardoptionelement"><span onclick="showSlideIn('3')">Új helyiség</span></div>
            </div><?php
        }
        ?><div class="szerkcardbody">
            <div class="szerkeszt">
                <div class="contentcenter">
                    <form action="<?=$RootPath?>/epuletszerkeszt&action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                        if(isset($_GET['id']))
                        {
                            ?><input type ="hidden" id="id" name="id" value=<?=$epid?>><?php
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

                        <?php $magyarazat .= "<strong>Telephely</strong><p>A telephely, ahol az épület található.</p>"; ?>

                        <div>
                            <label for="szam">Épület rajzszáma:</label><br>
                            <input type="text" accept-charset="utf-8" name="szam" id="szam" value="<?=$szam?>"></input>
                        </div>

                        <?php $magyarazat .= "<strong>Épület rajzszáma</strong><p>Az épület telephely alaprajz alapján kiosztott száma.</p>"; ?>

                        <div>
                            <label for="nev">Épület neve:</label><br>
                            <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                        </div>

                        <?php $magyarazat .= "<strong>Épület neve</strong><p>Az épület általánosan használt (nem hivatalos) neve.</p>"; ?>

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

                        <?php $magyarazat .= "<strong>Épülettípus</strong><p>Az épület típusa.</p>"; ?>

                        <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                    </form><?php
                    cancelForm();
                ?></div>

                <div id="magyarazat">
                    <h2 style="text-align: center">Magyarázat</h2>
                    <?=$magyarazat?>
                </div>
            </div>
        </div>
    </div><?php

    if(isset($_GET['id']))
    {
        ?><div id="slidein-1" onmouseleave="showSlideIn('1')">
            <div class="szerkcard">
                <div class="szerkcardtitle">Helyiségek generálása az épülethez</div>
                <div class="szerkcardbody">
                    <div class="contentcenter">
                        <form action="<?=$RootPath?>/helyisegdb?action=generate" method="post" onsubmit="beKuld.disabled = true; return true;">
                        <input type ="hidden" id="epulet" name="epulet" value=<?=$epid?>>
                        
                        <div>
                            <label for="emelet">Emelet:</label><br>
                            <select id="emelet" name="emelet">
                                <option value=""></option>
                                <option value="0">Földszint</option>
                                <option value="1">Első emelet</option>
                                <option value="2">Második emelet</option>
                                <option value="3">Harmadik emelet</option>
                                <option value="4">Negyedik emelet</option>
                            </select>
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
                    </div>
                </div>
            </div>
          </div>
          
          <div id="slidein-2" onmouseleave="showSlideIn('2')">
            <div class="szerkcard">
                <div class="szerkcardtitle">Végpontok generálása az épülethez</div>
                <div class="szerkcardbody">
                    <div class="contentcenter">
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
                                <label for="nullara">Nullára kiegészítés számjegye<br><small> (pl.: ha 001 legyen 1 helyett, akkor 3)</small>:</label><br>
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
                </div>
            </div>
          </div>

          <div id="slidein-3" onmouseleave="showSlideIn('3')">
            <div class="szerkcard">
                <div class="szerkcardtitle">Új helyiség létrehozása</div>
                <div class="szerkcardbody">
                    <?php $mindir = true; include("./helyisegszerkeszt.php"); ?>
                </div>
            </div>
          </div><?php
    }
}