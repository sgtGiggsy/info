<?php
if($modenged)
{
    ?><form action="<?=$RootPath?>/munkalapok/munkaszerkeszt&action=<?=($elemid) ? 'update' : 'new' ?>" method="post" onsubmit="beKuld.disabled = true; return true; nyomtat.disabled = true; return true;"><?php
        if($elemid)
        {
            ?><input type ="hidden" id="id" name="id" value=<?=$elemid?>><?php
        }
        ?><div class="doublecolumn">
            <div>

                <?=helyisegPicker($hely, "hely");?>

                <div>
                    <label for="tipus">Igénylő:</label><br>
                    <?=felhasznaloPicker($igenylo, "igenylo", null)?>
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
                        ?><div class="beszurtorol">
                            <button onclick="templateBeszur('<?=$template['szoveg']?>', '<?=$template['id']?>'); return false;" type="button"><?=$template['szoveg']?></button><button onclick="templateTorol('<?=$template['szoveg']?>'); return false;" type="button"><?=$icons['delete']?></button>
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

                <?php if($modenged)
                {
                    ?><div class="submit">
                        <input type="submit" name="beKuld" value="<?=$button?>">
                    </div>
                    <div class="submit" style="padding-top: 5px;">
                        <input type="submit" onclick="window.open('<?=$RootPath?>/munkalapok/munkaszerkeszt<?=$munkaprint?>')" name="nyomtat" value="<?=$button2?>">
                    </div><?php
                }
            ?><?php
            cancelForm();
            ?></div>
        </div>
    </form><?php
}