<?php
if(@$irhat)
{
    ?><form action="<?=$RootPath?>/valtozasfelulvizsgalat&action=review<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        if($modositasoka)
        {
            ?><div class="infobox modmessage">
                <div class="infoboxtitle">A módosítást végző <?=$modosito?> üzenete:</div>
                <div class="infoboxbody modmessagebody">
                    <small><?=$modositasideje?></small>
                    <p><?=$modositasoka?></p>
                </div>
            </div><?php
        }
        ?><div class="ketharmad">
            <input type ="hidden" id="id" name="id" value="<?=$_GET['id']?>">
            <input type ="hidden" id="allapot" name="allapot" value="3">
            <input type ="hidden" id="eredetisor" name="eredetisor" value="<?=$origsorrend?>">
            <div>
                <div>
                    <label for="beosztas">Beosztás</label>
                    <div class="delbuttoninput">
                        <select name="beosztas" id="beosztas">
                            <option value=""></option><?php
                            $elozocsop = 0;
                            foreach($beosztasok as $x)
                            {
                                if($elozocsop != $x['csoportid'])
                                {
                                    if($elozocsop != 0)
                                    {
                                        ?></optgroup><?php
                                    }

                                    ?><optgroup label="<?=$x['csoportnev']?>"><?php
                                    $elozocsop = $x['csoportid'];
                                }
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $origbeoid) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                            ?></optgroup>
                        </select><?php
                        if($beoid != $origbeoid)
                        {
                            ?><button onclick="restoreOriginal('beosztas', '<?=$origbeoid?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?>
                    </div>
                </div>

                <?php $magyarazat .= "<strong>Beosztás</strong><p>A dolgozó jelenleg eláttott beosztása.
                    <b>Nem</b> szükségszerűen egyezik meg a dolgozó állománytábla szerinti beosztásával.
                    Amennyiben a jelen beosztást a dolgozó megbízással, vagy vezényléssel látja el,
                    azt a <i>Megjegyzés</i> mezőben lehet/kell feltüntetni.<br>
                    Kötelező mező.</p>" ?>
                
                <div>
                    <label for="beosztasnev" id="beosztasnevcimke">Beosztás megnevezése</label>
                    <div class="delbuttoninput">
                        <input type="text" id="beosztasnev" name="beosztasnev" value="<?=$beosztas?>" <?=($beosztas != $origbeosztas) ? "style='background-color: yellow; color: black'; title='" . (($origbeosztas) ? $origbeosztas : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()"><?php
                        if($beosztas != $origbeosztas)
                        {
                            ?><button onclick="restoreOriginal('beosztasnev', '<?=$origbeosztas?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                    <?php $magyarazat .= "<strong>Beosztás megnevezése</strong><p>Itt adhatjuk meg a szerkeszteni/létrehozni kívánt beosztás nevét.
                        <b>EGY MEGLÉVŐ BEOSZTÁS ÁTNEVEZÉSE NEM HOZ LÉTRE ÚJ BEOSZTÁST!</b></p>"; ?>

                <div>
                    <label for="csoport">Alegység</label>
                    <div class="delbuttoninput">
                        <select name="csoport" id="csoport" <?=($csoportid != $origcsoportid) ? "style='background-color: yellow; color: black'; title='" . (($origcsoport) ? $origcsoport : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial(); refreshList()">
                            <option value=""></option><?php
                            foreach($csoportok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $csoportid) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select><?php
                        if($csoportid != $origcsoportid)
                        {
                            ?><button onclick="restoreOriginal('csoport', '<?=$origcsoportid?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                    <?php $magyarazat .= "<strong>Alegység</strong><p>Itt adhatjuk meg a szerkeszteni/létrehozni kívánt beosztás alegységét.
                        Nem létező alegység esetén a válaszuk ki az első, üres lehetőséget a listából, és a <i>Módosítás oka</i> mezőben írjuk be a létrehozni kívánt alegység nevét,
                        valamint hogy a listában mely alegységek között jelenjen meg.</p>"; ?>

                <div>
                    <label for="sorrend">Sorrend</label>
                    <div class="delbuttoninput">
                        <select name="sorrend" id="sorrend" <?=($sorrend != $origsorrend) ? "style='background-color: yellow; color: black'; title='" . (($origsorrend) ? $origsorrend : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()">
                            <?php $novaltozatlan = true; include("./modules/telefonkonyv/includes/beosztaslist.php"); ?>
                        </select><?php
                        if($sorrend != $origsorrend)
                        {
                            ?><button onclick="restoreOriginal('sorrend', '<?=$origsorrend?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                    <?php $magyarazat .= "<strong>Sorrend</strong><p>Itt adhatjuk meg a szerkeszteni/létrehozni kívánt beosztás
                        megjelenési helyét a listában. Csak válasszuk ki, hogy mely beosztás alatt, vagy felett jelenjen meg, vagy hagyjuk a <i>JELENLEGI HELY</i> álláson,
                        ha nem szeretnénk módosítani a jelenlegi sorrenden. Új beosztás létrehozása esetén, ha nem választunk ki semmit, akkor az adott alegység utolsó helyére kerül.</p>"; ?>


                <div>
                    <label for="elotag">Előtag</label>
                    <div class="delbuttoninput">
                        <select name="elotag" id="elotag" <?=($elotagid != $origelotagid) ? "style='background-color: yellow; color: black'; title='" . (($origelotag) ? $origelotag : "ÜRES VOLT" )  . "'" : "" ?> onchange="setAllapotPartial()">
                            <option value=""></option><?php
                            foreach($nevelotagok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $elotagid) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select><?php
                        if($elotagid != $origelotagid)
                        {
                            ?><button onclick="restoreOriginal('elotag', '<?=$origelotagid?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Előtag</strong><p>A dolgozó nevének előtagja (pl Dr, Professzor, Ifj, Id, stb).<br>
                    Nem kötelező mező.</p>" ?>

                <div>
                    <label for="nev">Név</label>
                    <div class="delbuttoninput">
                        <input type="text" name="nev" id="nev" value="<?=$nev?>" <?=($nev != $orignev) ? "style='background-color: yellow; color: black'; title='" . (($orignev) ? $orignev : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()"><?php
                        if($nev != $orignev)
                        {
                            ?><button onclick="restoreOriginal('nev', '<?=$orignev?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Név</strong><p>A dolgozó neve az előtagok nélkül.<br>Kötelező mező.</p>" ?>

                <div>
                    <label for="titulus">Titulus</label>
                    <div class="delbuttoninput">
                        <select name="titulus" id="titulus" <?=($titulusid != $origtitulusid) ? "style='background-color: yellow; color: black'; title='" . (($origtitulus) ? $origtitulus : "ÜRES VOLT") . "'" : "" ?> onchange="setAllapotPartial()">
                            <option value=""></option><?php
                            foreach($titulusok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $titulusid) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select><?php
                        if($titulusid != $origtitulusid)
                        {
                            ?><button onclick="restoreOriginal('titulus', '<?=$origtitulusid?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Titulus</strong><p>A dolgozó jogállása. Szerződéses, ÖMT, stb.<br>Nem kötelező mező.</p>" ?>

                <div>
                    <label for="rendfokozat">Rendfokozat</label>
                    <div class="delbuttoninput">
                        <select name="rendfokozat" id="rendfokozat" <?=($rendfokozatid != $origrendfokozatid) ? "style='background-color: yellow; color: black'; title='" . (($origrendfokozat) ? $origrendfokozat : "ÜRES VOLT") . "'" : "" ?> onchange="setAllapotPartial()">
                            <option value=""></option><?php
                            foreach($rendfokozatok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $rendfokozatid) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select><?php
                        if($rendfokozatid != $origrendfokozatid)
                        {
                            ?><button onclick="restoreOriginal('rendfokozat', '<?=$origrendfokozatid?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Rendfokozat</strong><p>A dolgozó rendfokozata.<br>Kötelező mező.</p>" ?>

                <div>
                    <label for="felhasznalo">Felhasználó</label>
                    <div class="delbuttoninput">
                        <select name="felhasznalo" id="felhasznalo" <?=($aduserid != $origaduserid) ? "style='background-color: yellow; color: black'; title='" . (($origadusernev) ? $origadusernev : "ÜRES VOLT") . "'" : "" ?> onchange="setAllapotPartial()">
                            <option value=""></option><?php
                            foreach($felhasznalok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $aduserid) ? "selected" : "" ?>><?=$x['nev']?> (<?=$x['felhasznalonev']?>)</option><?php
                            }
                        ?></select><?php
                        if($aduserid != $origaduserid)
                        {
                            ?><button onclick="restoreOriginal('felhasznalo', '<?=$origaduserid?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Felhasználó</strong><p>Amennyiben a dolgozó rendelkezik
                    eléréssel az intranethez, itt lehetőség van feltüntetni azt.<br>
                    Nem kötelező mező.</p>" ?>

                <div>
                    <label for="megjegyzes">Megjegyzés</label>
                    <textarea name="megjegyzes" <?=($megjegyzes != $origmegjegyzes) ? "style='background-color: yellow; color: black'; title='" . $origmegjegyzes . "'" : "" ?> onchange="setAllapotPartial()"><?=$megjegyzes?></textarea>
                </div>

                <?php $magyarazat .= "<strong>Megjegyzés</strong><p>Itt lehet feltüntetni bármi olyan közhasznú
                    információt a dolgozóval kapcsolatban, amire egyéb mezők nem adnak lehetőséget.<br>
                    Nem kötelező mező.</p>" ?>
            </div>

            <div>
                <div>
                    <label for="belsoszam">Belső szám*</label>
                    <div class="delbuttoninput">
                        <input type="text" id="belsoszam" name="belsoszam" value="<?=$belsoszam?>" <?=($belsoszam != $origbelsoszam) ? "style='background-color: yellow; color: black'; title='" . (($origbelsoszam) ? $origbelsoszam : "ÜRES VOLT" ). "'" : "" ?> required onchange="setAllapotPartial()"><?php
                        if($belsoszam != $origbelsoszam)
                        {
                            ?><button onclick="restoreOriginal('belsoszam', '<?=$origbelsoszam?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Belső szám</strong><p>A dolgozó irodai mellékének száma.<br>Kötelező mező.</p>" ?>

                <div>
                    <label for="belsoszam2">Alternatív belső szám:</label>
                    <div class="delbuttoninput">
                        <input type="text" id="belsoszam2" name="belsoszam2" value="<?=$belsoszam2?>" <?=($belsoszam2 != $origbelsoszam2) ? "style='background-color: yellow; color: black'; title='" . (($origbelsoszam2) ? $origbelsoszam2 : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()">
                        <?php
                        if($belsoszam2 != $origbelsoszam2)
                        {
                            ?><button onclick="restoreOriginal('belsoszam2', '<?=$origbelsoszam2?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Alternatív belső szám</strong><p> Amennyiben a dolgozó
                    egy második melléken is elérhető, azt itt lehet megadni.<br>Nem kötelező mező.</p>" ?>

                <div>
                    <label for="kozcelu">Közcélú szám</label>
                    <div class="delbuttoninput">
                        <input type="text" id="kozcelu" name="kozcelu" value="<?=$kozcelu?>" <?=($kozcelu != $origkozcelu) ? "style='background-color: yellow; color: black'; title='" . (($origkozcelu) ? $origkozcelu : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()"><?php
                        if($kozcelu != $origkozcelu)
                        {
                            ?><button onclick="restoreOriginal('kozcelu', '<?=$origkozcelu?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Közcélú szám</strong><p>Amennyiben a dolgozó rendelkezik
                    külső számról direktben hívható belső vonallal, úgy a behívó számot itt lehet megadni.<br>
                    Nem kötelező mező.</p>" ?>

                <div>
                    <label for="fax">Fax szám</label>
                    <div class="delbuttoninput">
                        <input type="text" id="fax" name="fax" value="<?=$fax?>" <?=($fax != $origfax) ? "style='background-color: yellow; color: black'; title='" . (($origfax) ? $origfax : "ÜRES VOLT" ). "'" : "" ?> onchange="setAllapotPartial()"><?php
                        if($fax != $origfax)
                        {
                            ?><button onclick="restoreOriginal('fax', '<?=$origfax?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Fax szám</strong><p>Amennyiben a dolgozó rendelkezik fax számmal,
                    úgy azt itt lehet megadni.<br>Nem kötelező mező.</p>" ?>

                <div>
                    <label for="kozcelufax">Közcélú fax szám</label>
                    <div class="delbuttoninput">
                        <input type="text" id="kozcelufax" name="kozcelufax" value="<?=$kozcelufax?>" <?=($kozcelufax != $origkozcelufax) ? "style='background-color: yellow; color: black'; title='" . (($origkozcelufax) ? $origkozcelufax : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()"><?php
                        if($kozcelufax != $origkozcelufax)
                        {
                            ?><button onclick="restoreOriginal('kozcelufax', '<?=$origkozcelufax?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Közcélú fax szám</strong><p>Amennyiben a dolgozó rendelkezik városi
                    vonalról direkten elérhető fax számmal, úgy azt itt lehet megadni.<br>Nem kötelező mező.</p>" ?>

                <div>
                    <label for="mobil">Szolgálati mobil száma</label>
                    <div class="delbuttoninput">
                        <input type="text" id="mobil" name="mobil" value="<?=$mobil?>" <?=($mobil != $origmobil) ? "style='background-color: yellow; color: black'; title='" . (($origmobil) ? $origmobil : "ÜRES VOLT" ) . "'" : "" ?> onchange="setAllapotPartial()"><?php
                        if($mobil != $origmobil)
                        {
                            ?><button onclick="restoreOriginal('mobil', '<?=$origmobil?>'); return false;">Eredeti állapot</button><?php
                        }
                    ?></div>
                </div>

                <?php $magyarazat .= "<strong>Szolgálati mobil száma</strong><p> Amennyiben a dolgozó
                    rendelkezik szolgálati mobiltelefonnal, annak a számát itt lehet megadni.<br>
                    Nem kötelező mező.</p>" ?>

                <div>
                    <label for="adminmegjegyzes" id="admincommentlabel">Adminisztrátori megjegyzés</label>
                    <textarea name="adminmegjegyzes" id="adminmegjegyzes"><?=$adminmegjegyzes?></textarea>
                </div>

                <?php $magyarazat .= "<strong>Módosítás oka</strong><p>Itt a telefonkönyv adminja részére lehet jelezni
                    a dolgozó telefonszám módosításának okát.<br>Saját alegységhez tartozó dolgozó módosítása esetén,
                    nem kötelező mező, egyébként az.</p>" ?>
                
                <div><input type="submit" name="beKuld" value='<?=$button?>'></div>
                <div class="submit">
                    <button type='button' onclick='confirmDiscard()'>Minden módosítás elvetése</button>
                </div>
                <?php cancelForm(); ?>
            </div>
        </div>
    </form>
    
    <script>
        function setAllapotPartial()
        {
            allapotvaltozas = document.getElementById("allapot");
            allapotvaltozas.value = 2;
            admincomment = document.getElementById("adminmegjegyzes");
            admincomment.required = true;
            admincommentlabel = document.getElementById("admincommentlabel");
            admincommentlabel.innerHTML = "Adminisztrátori megjegyzés*";
        }

        function confirmDiscard()
        {
            admincomment = document.getElementById("adminmegjegyzes");
            if(!admincomment.value)
            {
                window.alert("Nem adtál magyarázatot az elvetés okáról!");
                return false;
            }
            else
            {
                var x = confirm("Biztosan el akarod vetni a móodításokat?");
                if (x)
                    window.location.href="<?=$RootPath?>/valtozasfelulvizsgalat&action=discard&discardid=<?=$id?>&adminmegjegyzes="+admincomment.value
                else
                    return false;
            }
        }

        function refreshList() {
            let xhttp = new XMLHttpRequest();
            let csopid, eredeti;
            csopid = document.getElementById("csoport").value;
            eredetielem = document.getElementById("eredetisor");
            eredeti = eredetielem.value;
            console.log(eredetielem.value);
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("sorrend").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "<?=$RootPath?>/modules/telefonkonyv/includes/beosztaslist.php?csoport=" + csopid + "&eredeti=" + eredeti + "&novaltozatlan", true);
            xhttp.send();
        }

        function restoreOriginal(inputid, origvalue)
        {
            input = document.getElementById(inputid);
            input.value = origvalue;
        }
    </script>
    <?php
}