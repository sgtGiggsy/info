<?php
if(@$irhat)
{
    $PHPvarsToJS['selectors'][] = 'leiras-' . $newelemid;
    if($newelemid == 0)
    {    
        $javascriptfiles[] = "includes/tinymce/tinymce.min.js";
	    $javascriptfiles[] = "includes/js/tinyMCEinitializer.js";
    }
    $formnew = true;
    $button = "Új feladat létrehozása";
    if(isset($_GET['action']) && $_GET['action'] == "edit")
    {
        $szulo = $elsofeladat['szulo'];
        $rovid = $elsofeladat['rovid'];
        $ido_tervezett = $elsofeladat['ido_tervezett'];
        $ido_hatarido = $elsofeladat['ido_hatarido'];
        $leiras = $elsofeladat['leiras'];
        $currpri = $elsofeladat['prioritas'];
        $currbuild = $elsofeladat['epulet'];
        $szakid = $elsofeladat['szakid'];
        $ido_tenyleges = $elsofeladat['ido_tenyleges'];
        $felelosok = concatToAssocArray(array('id', 'nev'), $elsofeladat['felelosids'], $elsofeladat['felelosnevek']);
        if($felelosok)
        {
            foreach($felelosok as $felelos)
            {
                $selectedfelelosok[] = $felelos['id'];
            }
        }
        $formnew = false;
        $button = "Feladat szerkesztése";
    }
    ?><form action="<?=$RootPath?>/feladatterv&action=<?=(!$formnew) ? 'update' : 'new' ?>" method="post" enctype="multipart/form-data" onsubmit="beKuld.disabled = true; return true;"><?php
        if(isset($id))
        {
            ?><input type ="hidden" id="id" name="id" value=<?=$id?>><?php
        }
        ?><input type ="hidden" id="szulo" name="szulo" value=<?=$szulo?>>
        <div class="feladatformgrid">
            <div class="feladatform-textdiv">
                <div class="feladatform-selects">
                    <div>
                        <label for="rovid">Rövid összegzés</label><br>
                        <textarea name="rovid"id="rovid" placeholder="Feladat rövid leírása"><?=$rovid?></textarea>
                    </div>
                    
                    <div>
                        <label for="ido_tervezett">Tervezett végrehajtás</label><br>
                        <input type="datetime-local" id="ido_tervezett" name="ido_tervezett" value="<?=$ido_tervezett?>"><button style="margin-left: 10px;" onclick="getMost('ido_tervezett'); return false;" type="button">Most</button>
                    </div>
                    
                    <div>
                        <label for="ido_hatarido">Feladat határideje</label><br>
                        <input type="datetime-local" id="ido_hatarido" name="ido_hatarido" value="<?=$ido_hatarido?>"><button style="margin-left: 10px;" onclick="getMost('ido_hatarido'); return false;" type="button">Most</button>
                    </div>

                    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                </div>

                <div>
                    <label for="leiras">Részletes leírás</label><br>
                    <textarea name="leiras" id='leiras-<?=$newelemid?>' placeholder="Feladat részletes leírása"><?=$leiras?></textarea>
                </div>
            </div>
            
            <div class="feladatform-selects" id="hiddenextra-<?=$newelemid?>" style="visibility: hidden; width: 0">
                <?php priorityPicker($currpri); ?>

                <?php multiSelectDropdown($felhasznalok, $selectedfelelosok, "felelosok", "Felelősök", "felelosok") ?>

                <?php epuletPicker($currbuild) ?>

                <div>
                    <label for="szak">Szakterület</label><br>
                    <select name="szakid" id="szakid">
                        <option value=""></option><?php
                        foreach($szakok as $szak)
                        {
                            ?><option value="<?=$szak['id']?>" <?=($szak['id'] == $szakid) ? "selected" : "" ?>><?=ucfirst($szak['nev'])?></option><?php
                        }
                    ?></select>
                </div>
                
                <div>
                    <label for="fajlok">Csatolni kívánt képek/dokumentumok</label><br>
                    <input type="file" name="fajlok[]"
                            accept="image/jpeg, image/png,
                                    image/bmp, application/msword,
                                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                    application/vnd.ms-excel application/pdf,
                                    application/vnd.ms-excel,
                                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                                    application/zip,
                                    application/x-zip-compressed,
                                    application/vnd.rar"
                    multiple>
                </div><?php

                if(!$formnew)
                {
                    ?><div>
                        <label for="ido_tenyleges">Feladat végrehajtása befejezve</label><br>
                        <input type="datetime-local" id="ido_tenyleges" name="ido_tenyleges" value="<?=$ido_tenyleges?>"><button style="margin-left: 10px;" onclick="getMost('ido_tenyleges'); return false;" type="button">Most</button>
                    </div><?php
                }

            ?></div>
            <div class="sidebutton">
                <button type="button" class="vertbutton" onclick="vertButton('hiddenextra-<?=$newelemid?>', 'hiddenbutton-<?=$newelemid?>')" id="hiddenbutton-<?=$newelemid?>" data-open="Extra mezők elrejtése" data-closed="Extra mezők megnyitása">Extra mezők megnyitása</button>
            </div>
        </div>
    </form><?php
}