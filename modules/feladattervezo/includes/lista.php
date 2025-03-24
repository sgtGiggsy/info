<?php
// Megállapítjuk, hogy a felhasználó írhatja-e a feladatot
$irhat = false;
if($mindir)
{
    $irhat = true;
}
elseif($csoportir)
{
    foreach($csoporttagsagok as $csoport)
    {
        if($csoport['szervezet'] == $feladattervszuloszerv || $csoport['szervezet'] == $szervezet)
        {
            $irhat = true;
            break;
        }
    }
}

?><div class="allapotjelentesek"><?php
    $kattinthatolink = null;
    foreach($feladatterv->Result() as $feladatelem)
    {
        $felelosok = concatToAssocArray(array('id', 'nev'), $feladatelem['felelosids'], $feladatelem['felelosnevek']);
        $untildeadline = $urgentdeadline = false;
        if($feladatelem['ido_hatarido'])
        {
            $untildeadline = strtotime($feladatelem['ido_hatarido']) - time();
            $urgentdeadline = ($untildeadline < 172800) ? true : false;
        }

        if($egyenioldal)
        {
            $fajlok = concatToAssocArray(array('fajl', 'felhasznalo_id', 'timestamp', 'nev'), $feladatelem['fajlok'], $feladatelem['fajlfeltoltoids'], $feladatelem['feltoltesidok'], $feladatelem['fajlfeltoltonevek']);
            $kommentek = concatToAssocArray(array('szoveg', 'timestamp', 'nev'), $feladatelem['kommentek'], $feladatelem['kommentidok'], $feladatelem['kommenterek']);
            $kepek = array();
            $doksik = array();
    
            foreach($fajlok as $fajl)
            {
                if(str_contains_any($fajl['fajl'], array('jpg', 'jpeg', 'png', 'gif')))
                    $kepek[] = $fajl;
                else
                    $doksik[] = $fajl;
            }
        }

        $fajlszam = $feladatelem['fajlszam'];
        $kommentszam = $feladatelem['kommentszam'];
        
        $feladatelem['epulet'];
        $feladatelem['felvitte'];
        $feladatelem['modositotta'];
        $feladatelem['ido_tenyleges'];
        $feladatelem['szervezet'];
        
        switch($feladatelem['prioritas'])
        {
            case 2 : $urgclass = "fontos"; break;
            case 3 : $urgclass = "surgos"; break;
            case 4 : $urgclass = "kritikus"; break;
            default: $urgclass = "allapotsorszam";
        }

        switch($feladatelem['allapot'])
        {
            case 0 : $allapot = "Sikertelen"; break;
            case 1 : $allapot = "Megkezdetlen"; break;
            case 2 : $allapot = "Folyamatban"; break;
            case 3 : $allapot = "Befejezve"; break;
        }

        ?><div class="feladatelem <?=($feladatelem['szulo']) ? 'gyermek' : '' ?> <?=(!$egyenioldal) ? 'fullelem' : '' ?>"
            data-surgosseg="<?=$feladatelem['prioritas']?>"
            data-szakid="<?=$feladatelem['szakid']?>"
            data-id="<?=$feladatelem['feladat_id']?>"
            data-szulo="<?=($feladatelem['szulo']) ? $feladatelem['szulo'] : "0" ?>"
            id="feladat-<?=$feladatelem['feladat_id']?>"
        >
            <div class="feladatelemdiv">
                <div class="<?=$urgclass?> feladatid" title="<?=$feladatelem['prioritasnev']?>">
                    <div class="allapotelemparent">
                        <strong><?=$feladatelem['ido_tervezett']?></strong>
                        <p><small><?=$allapot?></small></p>
                        <p><small><?=$feladatelem['szaknev']?></small></p>
                    </div>
                    <div class="feladatactions">
                        <button title="Befejezettként jelöl"><?=$icons['checkmark']?></button>
                        <button title="Szerkeszt" onclick="elemFelkeres('<?=$feladatelem['feladat_id']?>?action=edit')"><?=$icons['edit']?></button><?php
                        if(!$feladatelem['szulo'])
                        {
                            ?><button title="Alfeladat hozzáadása" onclick="szerkesztNyit('ujfeladat-<?=$newelemid?>', <?=$newelemid?>)">
                                <span id="addbutton-<?=$newelemid?>"><?=$icons['add']?></span><span id="removebutton-<?=$newelemid?>" style="display: none"><?=$icons['remove']?></span>
                            </button><?php
                        }
                        ?><button title="Törlés" onclick="confirmSend('Biztosan törölni szeretnéd ezt az elemet?', '<?=$RootPath?>/feladatterv/<?=$feladatelem['feladat_id']?>?action=delete')"><?=$icons['delete']?></button>
                    </div>
                </div>
                <div class="feladatmain" <?=($egyenioldal) ? 'onclick="elemNyit(' . $feladatelem['feladat_id'] . ')"' : 'onclick="elemFelkeres(' . $feladatelem['feladat_id'] .')"' ?>>
                    <div class="feladatnev" id="feladatnev-<?=$feladatelem['feladat_id']?>">
                        <h2><?=$feladatelem['rovid']?></h2>
                    </div>
                    <div class="feladatleiras" id="leiras-<?=$feladatelem['feladat_id']?>" style="display: none">
                        <?=$feladatelem['leiras']?>
                    </div>
                </div>
                
                <div class="basedeets">
                    <div class='felelosok'><?php
                    if($felelosok)
                    {
                        ?><div><h2>Felelősök:</h2></div><?php
                        foreach($felelosok as $felelos)
                        {
                            ?><div><a href="<?=ROOT_PATH?>/felhasznalo/<?=$felelos['id']?>"><?=$felelos['nev']?></a></div><?php
                        }
                    }
                    ?></div><?php
                    if($feladatelem['telephely'] || $feladatelem['eszkoz'])
                    {
                        ?><div class="felelosok">
                            <div class="vertflex">
                                <div><h2>Érintettek:</h2></div>
                                <p><?=$feladatelem['telephely']?><br>
                                    <?=($feladatelem['epulet_szam']) ? IntRagValaszt($feladatelem['epulet_szam']) . " épület" : "" ?><?=($feladatelem['epulet_szam'] && $feladatelem['epulet_nev']) ? " (" . $feladatelem['epulet_nev'] . ")" : "" ?></div>
                                </p>
                        </div><?php
                    }
                    ?><div class="felelosok"><?php
                        if($untildeadline) {
                            //$urgentdeadline = true;
                            ?><div class="vertflex <?=($urgentdeadline) ? "warning" : "" ?>">
                                <div><h2><?=($urgentdeadline) ? (($untildeadline < 0 ) ? "LEJÁRT HATÁRIDŐ" : "KÖZELI HATÁRIDŐ!") : "Határidőig hátravan:" ?></h2></div>
                                <p><?=secondsToFullFormat($untildeadline, false)?></p>
                            </div><?php
                        }
                        ?><div class="vertflex tablecardbody">
                            <div id="fajldb-<?=$feladatelem['feladat_id']?>">Mellékelt fájlok: <?=$fajlszam?></div>
                            <div id="megjegyzesdb-<?=$feladatelem['feladat_id']?>">Megjegyzések: <?=$kommentszam?></div>
                        </div>
                    </div>
                </div><?php

                if($egyenioldal)
                {
                    ?><div class="feladatkommentek" id="kommentek-<?=$feladatelem['feladat_id']?>" style="display: none"><?php
                        if($kommentszam > 0)
                        {
                            ?><h2>Megjegyzések</h2>
                            <div class="kommentek"><?php
                                foreach($kommentek as $komment)
                                {
                                    ?><div class="allapotvaltozas">
                                        <div class="allapotvaltozasbody"><?=$komment['szoveg']?></div>
                                        <div class="allapotvaltozasmeta">👤<?=$komment['nev']?> 🕓<?=$komment['timestamp']?></div>
                                    </div><?php
                                }
                            ?></div><?php
                        }
                        ?><div class="feladatkommenteles">
                            <form action="<?=ROOT_PATH?>/feladatterv/<?=$feladatelem['feladat_id']?>?action=komment" method="post">
                                <textarea name="szoveg" placeholder="Új megjegyzés írása..."></textarea>
                                <input type="submit" value="Küldés">
                            </form>
                        </div>
                    </div>
                    <div class="feladatfajlok" id="fajlok-<?=$feladatelem['feladat_id']?>" style="display: none"><?php
                        if($fajlszam > 0)
                        {
                            ?><div><h2>Feltöltött fájlok</h2></div><?php
                            if(count($kepek) > 0)
                            {
                                ?><div class="feladatkepek <?=(count($doksik) == 0) ? "fullwidthfajl" : "" ?>"><?php
                                    foreach($kepek as $kep)
                                    {
                                        ?><div>
                                            <img src="<?=ROOT_PATH?>/uploads/<?=$kep['fajl']?>" alt="<?=fajlnevFromPath($kep['fajl'])?>">
                                            <div><small>Feltöltő: <?=$kep['nev']?><br>Feltöltés ideje: <?=$kep['timestamp']?></small></div>
                                        </div><?php
                                    }
                                ?></div><?php
                            }
                            if(count($doksik) > 0)
                            {
                                ?><div <?=(count($kepek) == 0) ? "class='fullwidthfajl'" : "" ?>><?php
                                    foreach($doksik as $doksi)
                                    {
                                        ?><div>
                                            <div><a href="<?=ROOT_PATH?>/uploads/<?=$doksi['fajl']?>" target="_blank"><?=fajlnevFromPath($doksi['fajl'])?></a></div>
                                            <div><small>Feltöltő: <?=$doksi['nev']?><br>Feltöltés ideje: <?=$doksi['timestamp']?></small></div>
                                        </div><?php
                                    }
                                ?></div><?php
                            }
                        }
                    ?></div><?php
                }
            ?></div>
            <div class="boxnyit"
                title="Feladat ID: <?=$feladatelem['feladat_id']?>&#013;Létrehozta: <?=$feladatelem['felvivo_nev']?>&#013;Létrehozva: <?=$feladatelem['ido_letrehoz']?>&#013;Modosította: <?=$feladatelem['modosito_nev']?>">
            </div>
        </div><?php
        if(!$feladatelem['szulo'])
        {
            $szulo = $feladatelem['feladat_id'];
            ?><div class="feladatelem" id="ujfeladat-<?=$newelemid?>" style="display: none"><?php
                include("./modules/feladattervezo/forms/feladattervform.php");
            ?></div><?php
            $newelemid++;
        }
    }
?></div>