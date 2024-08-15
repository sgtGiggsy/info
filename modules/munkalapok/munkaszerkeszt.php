<?php

if(@!$sajatir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(isset($_GET['action']) && $_GET['action'] == "print")
    {
        include("./modules/munkalapok/munkaprint.php");
        die();
    }
    
    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./modules/munkalapok/db/munkadb.php");
    }

    $igenylo = $igenylesideje = $vegrehajtasideje = $munkavegzo2 = $leiras = $eszkoz = null;
    $javascriptfiles[] = "modules/munkalapok/includes/templatebeszur.js";
    $hely = $_SESSION[getenv('SESSION_NAME')."defaultmunkahely"];
    $ugyintezo = $_SESSION[getenv('SESSION_NAME')."defaultugyintezo"];
    $munkavegzo1 = $_SESSION[getenv('SESSION_NAME').'id'];
    $datalist = mySQLConnect("SELECT DISTINCT leiras FROM munkalapok ORDER BY leiras DESC");
    $templateek = mySQLConnect("SELECT id, szoveg FROM munkalaptemplateek ORDER BY hasznalva DESC, szoveg ASC;");

    $button = "Munka rögzítése";
    $button2 = "Munka rögzítése és nyomtatása";

    ?><datalist id="munkaleirasok"><?php
    foreach($datalist as $elem)
    {
        ?><option><?=$elem['leiras']?></option><?php
    }
    ?></datalist><?php

    if(isset($_GET['id']))
    {
        $munkaid = $_GET['id'];
        
        $munka = mySQLConnect("SELECT * FROM munkalapok WHERE id = $munkaid");
        $munka = mysqli_fetch_assoc($munka);

        $hely = $munka['hely'];
        $igenylo = $munka['igenylo'];
        $igenylesideje = $munka['igenylesideje'];
        $vegrehajtasideje = $munka['vegrehajtasideje'];
        $munkavegzo1 = $munka['munkavegzo1'];
        $munkavegzo2 = $munka['munkavegzo2'];
        $leiras = $munka['leiras'];
        $eszkoz = $munka['eszkoz'];
        $ugyintezo = $munka['ugyintezo'];

        $button = "Munka szerkesztése";
        $button2 = "Munka szerkesztése és nyomtatása";

        ?><form action="<?=$RootPath?>/munkaszerkeszt&action=update" method="post" onsubmit="beKuld.disabled = true; return true; nyomtat.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$munkaid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/munkaszerkeszt&action=new" method="post" onsubmit="beKuld.disabled = true; return true; nyomtat.disabled = true; return true;"><?php
    }

    ?><div class="szerkcard">
        <div class="szerkcardtitle">Munka szerkesztése</div>
        <div class="szerkcardbody">
            <div class="doublecolumn">
                <div>

                    <?=helyisegPicker($hely, "hely");?>

                    <div>
                        <label for="tipus">Igénylő:</label><br>
                        <?=felhasznaloPicker($igenylo, "igenylo", false)?>
                    </div>

                    <div>
                        <label for="igenylesideje">Igénylés ideje</label><br>
                        <input type="date" id="igenylesideje" name="igenylesideje" value="<?=$igenylesideje?>"><button style="margin-left: 10px;" onclick="getMa('igenylesideje'); return false;">Ma</button>
                    </div>

                    <div>
                        <label for="vegrehajtasideje">Végrehajtás ideje</label><br>
                        <input type="date" id="vegrehajtasideje" name="vegrehajtasideje" value="<?=$vegrehajtasideje?>"><button style="margin-left: 10px;" onclick="getMa('vegrehajtasideje'); return false;">Ma</button>
                    </div>

                    <div>
                        <label for="tipus">Feladat végrehajtója:</label><br>
                        <?=felhasznaloPicker($munkavegzo1, "munkavegzo1", 2)?>
                    </div>

                    <div>
                        <label for="tipus">Végrehajtásban közreműködött:</label><br>
                        <?=felhasznaloPicker($munkavegzo2, "munkavegzo2", 2)?>
                    </div>

                    <div>
                        <label for="tipus">Záradékban nevezett ügyintéző:</label><br>
                        <?=felhasznaloPicker($ugyintezo, "ugyintezo", 2)?>
                    </div>
                </div>

                <div>
                    <div class="munkatemplatebeszur"><?php
                        foreach($templateek as $template)
                        {
                            ?><div>
                                <button onclick="templateBeszur('<?=$template['szoveg']?>', '<?=$template['id']?>'); return false;" type="button"><?=$template['szoveg']?></button>
                            </div><?php
                        }
                    ?></div>
                    <div>
                        <label for="leiras">Elvégzett munka:</label><br>
                        <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                    </div>

                    <div>
                        <label for="eszkoz">Felhasznált eszköz/anyag:</label><br>
                        <textarea name="eszkoz" id="eszkoz"><?=$eszkoz?></textarea>
                    </div>

                    <div class="submit">
                        <input type="submit" name="beKuld" value="<?=$button?>">
                    </div>
                    <div class="submit" style="padding-top: 5px;">        
                        <input type="submit" onclick="window.open('<?=$RootPath?>/munkaszerkeszt/<?=$munka['id']?>?action=print')" name="nyomtat" value="<?=$button2?>">
                    </div>
                </form><?php
                cancelForm();
                ?></div>
            </div>
        </div>
    </div>
<?php
}