<?php
if(@$irhat)
{
    $fieldsetstate = $indexnumber = "";
    if(!$globaltelefonkonyvadmin && ($allapot === null || ($allapot > 0 && $allapot < 3)))
    {
        $fieldsetstate = "disabled";
    }
    ?><form action="<?=$RootPath?>/telefonkonyv/valtozas&action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
        if($adminmegjegyzes)
        {
            ?><div class="infobox modmessage">
                <div class="infoboxtitle">A Telefonkönyv adminisztrátorának megjegyzése</div>
                <div class="infoboxbody modmessagebody">
                    <small><?=$admintimestamp?></small>
                    <p><?=$adminmegjegyzes?></p>
                </div>
            </div><?php
        }
        ?><div class="ketharmad">
            <fieldset <?=$fieldsetstate?>><?php
                $magyarazat .= "<strong>Tudnivalók</strong><p>Minden *-gal megjelölt mező kitöltése kötelező.
                    A <i>SÚGÓ</i> bármikor bezárható a felette jobboldalt látható ?-re kattintva.</p>";
                if($id)
                {
                    ?><input type ="hidden" id="felhid" name="felhid" value=<?=$telefonszam['felhid']?>>
                    <?php
                }
                ?><input type ="hidden" id="szerkbeo" name="szerkbeo" value="">
                <input type ="hidden" id="belsoelohivo" name="belsoelohivo" value="<?=$belsoelohivo?>">
                <input type ="hidden" id="varosielohivo" name="varosielohivo" value="<?=$varosielohivo?>">
                <input type ="hidden" id="mobilelohivo" name="mobilelohivo" value="<?=$mobilelohivo?>">
                <input type ="hidden" id="eredetisor" name="eredetisor" value="<?=$sorrend?>">
                <input type ="hidden" id="origbeoid" name="origbeoid" value="<?=$beosztas?>">
                <input type ="hidden" id="origfelhid" name="origfelhid" value="<?=$felhid?>">
                <input type ="hidden" id="removebeo" name="removebeo" value="0">
                <div>
                    <div id="beosztasalap">
                        <button onclick="switchBeosztas(); return false;">Beosztás szerkesztése</button>
                        <div class="right"><button onclick="delBeosztas(); return false;" class="redbutton" id="beodelbutton">Beosztás törlése</button></div><br>
                        <label for="beosztas">Beosztás*</label>
                        <select name="beosztas" id="beosztas" onchange="checkIfNew(<?=$felhasznalo?>);refreshSelections();checkIfAvailable();" required>
                            <option></option>
                            <option value="0" id="ujbeo" <?=($addnew) ? "selected" : "" ?> style="font-size: 1em;">Új beosztás létrehozása</option><?php
                            $elozocsop = 0;
                            $ind = 2;
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
                                if($x['id'] == $beosztas)
                                {
                                    $indexnumber = $ind;
                                }
                                ?><option value="<?=$x['id']?>" class=<?=($x['foglalt']) ? "'selectdontselect' title='Foglalt'" : "'selectavailable'" ?> <?=($x['id'] == $beosztas) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                                $ind++;
                            }
                            ?></optgroup>
                        </select>
                    </div>

                    <?php $magyarazat .= "<span class='egyszeru'><strong>Beosztás szerkesztése</strong><p>Ezt a gombot <b>KIZÁRÓLAG AKKOR</b> kell megnyomni
                        ha a dolgozó beosztásának megnevezése változott, átkerült más alegységhez, vagy a listában szereplő sorrendjén
                        kell módosítani.</p></span>"; ?>

                    <div id="beosztasreszletes" style="display: none">
                        <button onclick="switchBeosztas(); return false;">Beosztás listából kiválasztása</button><br><br>

                        <?php $magyarazat .= "<span class='reszletes' style='display: none;'><strong>Beosztás listából kiválasztása</strong><p>Ezzel a gombbal lehet visszatérni a beosztás
                        listából történő kiválasztásához. A listából kiválasztott elem felülír <b>MINDEN</b> módosítást, amit itt elvégeztünk.</p></span>"; ?>
                        
                        <label for="beosztasnev" id="beosztasnevcimke">Beosztás megnevezése*</label>
                        <input type="text" id="beosztasnev" name="beosztasnev" value="<?=$beosztasnev?>" required><br><br>

                        <?php $magyarazat .= "<span class='reszletes' style='display: none;'><strong>Beosztás megnevezése</strong><p>Itt adhatjuk meg a szerkeszteni/létrehozni kívánt beosztás nevét.
                            <b>EGY MEGLÉVŐ BEOSZTÁS ÁTNEVEZÉSE NEM HOZ LÉTRE ÚJ BEOSZTÁST!</b></p></span>"; ?>

                        <label for="csoport">Alegység</label>
                        <select name="csoport" id="csoport" onchange="refreshList()">
                            <option value=""></option><?php
                            foreach($csoportok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $csoport) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select><br><br>

                        <?php $magyarazat .= "<span class='reszletes' style='display: none;'><strong>Alegység</strong><p>Itt adhatjuk meg a szerkeszteni/létrehozni kívánt beosztás alegységét.
                            Nem létező alegység esetén a válaszuk ki az első, üres lehetőséget a listából, és a <i>Módosítás oka</i> mezőben írjuk be a létrehozni kívánt alegység nevét,
                            valamint hogy a listában mely alegységek között jelenjen meg.</p></span>"; ?>

                        <label for="sorrend">Sorrend</label>
                        <select name="sorrend" id="sorrend">

                        </select>

                        <?php $magyarazat .= "<span class='reszletes' style='display: none;'><strong>Sorrend</strong><p>Itt adhatjuk meg a szerkeszteni/létrehozni kívánt beosztás
                            megjelenési helyét a listában. Csak válasszuk ki, hogy mely beosztás alatt, vagy felett jelenjen meg, vagy hagyjuk a <i>JELENLEGI HELY</i> álláson,
                            ha nem szeretnénk módosítani a jelenlegi sorrenden. Új beosztás létrehozása esetén, ha nem választunk ki semmit, akkor az adott alegység utolsó helyére kerül.</p></span>"; ?>

                    </div>

                    <?php $magyarazat .= "<span class='egyszeru'><strong>Beosztás</strong><p>A dolgozó jelenleg eláttott beosztása.
                        <b>Nem</b> szükségszerűen egyezik meg a dolgozó állománytábla szerinti beosztásával.
                        Amennyiben a jelen beosztást a dolgozó megbízással, vagy vezényléssel látja el,
                        azt a <i>Megjegyzés</i> mezőben lehet/kell feltüntetni.<br>
                        Amennyiben a listában nem szerepel a kiválasztani kívánt beosztás, úgy a lista első elemének kiválasztásával
                        <i>(Új beosztás létrehozása</i>) van módunk újat felvenni.
                        Kötelező mező.</p></span>" ?>

                    <div>
                        <label for="elotag">Előtag</label>
                        <select name="elotag" id="elotag">
                            <option value=""></option><?php
                            foreach($nevelotagok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $elotag) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select>
                    </div>

                    <?php $magyarazat .= "<strong>Előtag</strong><p>A dolgozó nevének előtagja (pl Dr, Professzor, Ifj, Id, stb).<br>
                        Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="nev" id="nevlabel">Név*</label>
                        <div class="delbuttoninput">
                            <input type="text" name="nev" id="nev" onkeyup="verifyExist('tkonyvfelhasznalok', 'nev', 'felhasznalocheck')" value="<?=$nev?>" required>
                            <div class="tooltip" id="felhasznalocheck">A megadott névhez már tartozik legalább egy telefonszám.<br>Biztos vagy benne, hogy most egy másik dolgozó adatait viszed fel?</div><?php
                            if(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] != "addnew"))
                            {
                                ?><button onclick="delUser(); return false;">Dolgozó törlése</button><?php
                            }
                            else
                            {
                                ?><button onclick="delUser(); return false;">Beosztás felvétele üresen</button><?php
                            }

                        ?></div>
                    </div>

                    <?php $magyarazat .= "<strong>Név</strong><p>A dolgozó neve az előtagok nélkül.<br>Kötelező mező.</p>" ?>

                    <?php $magyarazat .= "<strong>Dolgozó törlése</strong><p>Ezzel a gombbal lehet egy dolgozót törölni egy beosztásról. Akkor használjuk,
                        ha a dolgozó bármilyen okból távozik a jelen beosztásból, legyen az áthelyezés, vagy leszerelés.<br>
                        Törlés esetén a <i>Módosítás oka</i> mező kitöltése kötelező!</p>" ?>

                    <div>
                        <label for="titulus" id="tituluscimke">Titulus*</label>
                        <select name="titulus" id="titulus" required>
                            <option value=""></option><?php
                            foreach($titulusok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $titulus) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select>
                    </div>

                    <?php $magyarazat .= "<strong>Titulus</strong><p>A dolgozó jogállása. Szerződéses, ÖMT, stb.<br>Kötelező mező.</p>" ?>

                    <div>
                        <label for="rendfokozat" id="rendfokozatcimke">Rendfokozat*</label>
                        <select name="rendfokozat" id="rendfokozat" required>
                            <option value=""></option><?php
                            foreach($rendfokozatok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $rendfokozat) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select>
                    </div>

                    <?php $magyarazat .= "<strong>Rendfokozat</strong><p>A dolgozó rendfokozata.<br>Kötelező mező.</p>" ?>

                    <div>
                        <label for="felhasznalo">Felhasználó</label>
                        <select name="felhasznalo" id="felhasznalo">
                            <option value=""></option><?php
                            foreach($felhasznalok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $felhasznalo) ? "selected" : "" ?>><?=$x['nev']?> (<?=$x['felhasznalonev']?>)</option><?php
                            }
                        ?></select>
                    </div>

                    <?php $magyarazat .= "<strong>Felhasználó</strong><p>Amennyiben a dolgozó rendelkezik
                        eléréssel az intranethez, itt lehetőség van feltüntetni azt.<br>
                        Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="megjegyzes">Megjegyzés</label>
                        <textarea name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></textarea>
                    </div>

                    <?php $magyarazat .= "<strong>Megjegyzés</strong><p>Itt lehet feltüntetni bármi olyan közhasznú
                        információt a dolgozóval kapcsolatban, amire egyéb mezők nem adnak lehetőséget.<br>
                        Nem kötelező mező.</p>" ?>
                </div>

            </fieldset>
            <fieldset <?=$fieldsetstate?>>
                <div>
                    <div>
                        <label for="belsoszam">Belső szám*</label>
                        <div class="telinput">
                            <span class="telprefix"><?=$belsoelohivo?></span>
                            <input style="padding-left: <?=strlen($belsoelohivo)?>ch;" type="tel" maxlength="4" onkeypress="return onlyNumberKey(event)" name="belsoszam" id="belsoszam" value="<?=$belsoszam?>" required>
                        </div>
                    </div>

                    <?php $magyarazat .= "<strong>Belső szám</strong><p>A dolgozó irodai mellékének száma.<br>Kötelező mező.</p>" ?>

                    <div>
                        <label for="belsoszam2">Alternatív belső szám:</label>
                        <div class="telinput">
                            <span class="telprefix"><?=$belsoelohivo?></span>
                            <input style="padding-left: <?=strlen($belsoelohivo)?>ch;" type="tel" maxlength="4" onkeypress="return onlyNumberKey(event)" name="belsoszam2" id="belsoszam2" value="<?=$belsoszam2?>">
                        </div>
                    </div>

                    <?php $magyarazat .= "<strong>Alternatív belső szám</strong><p> Amennyiben a dolgozó
                        egy második melléken is elérhető, azt itt lehet megadni.<br>Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="kozcelu">Közcélú szám</label>
                        <div class="telinput">
                            <span class="telprefix"><?=$varosielohivo?></span>
                            <input style="padding-left: <?=strlen($varosielohivo)?>ch;" type="tel" maxlength="7" onkeypress="return onlyNumberKey(event)" onkeyup="addDash(this)" name="kozcelu" id="kozcelu" value="<?=$kozcelu?>">
                        </div>
                    </div>

                    <?php $magyarazat .= "<strong>Közcélú szám</strong><p>Amennyiben a dolgozó rendelkezik
                        külső számról direktben hívható belső vonallal, úgy a behívó számot itt lehet megadni.<br>
                        Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="fax">Fax szám</label>
                        <div class="telinput">
                            <span class="telprefix"><?=$belsoelohivo?></span>
                            <input style="padding-left: <?=strlen($belsoelohivo)?>ch;" type="tel" maxlength="4" onkeypress="return onlyNumberKey(event)" name="fax" id="fax" value="<?=$fax?>">
                        </div>
                    </div>

                    <?php $magyarazat .= "<strong>Fax szám</strong><p>Amennyiben a dolgozó rendelkezik fax számmal,
                        úgy azt itt lehet megadni.<br>Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="kozcelufax">Közcélú fax szám</label>
                        <div class="telinput">
                            <span class="telprefix"><?=$varosielohivo?></span>
                            <input style="padding-left: <?=strlen($varosielohivo)?>ch;" type="tel" maxlength="7" onkeypress="return onlyNumberKey(event)" onkeyup="addDash(this)" name="kozcelufax" id="kozcelufax" value="<?=$kozcelufax?>">
                        </div>
                    </div>

                    <?php $magyarazat .= "<strong>Közcélú fax szám</strong><p>Amennyiben a dolgozó rendelkezik városi
                        vonalról direkten elérhető fax számmal, úgy azt itt lehet megadni.<br>Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="mobil">Szolgálati mobil száma</label>
                        <div class="telinput">
                            <span class="telprefix"><?=$mobilelohivo?></span>
                            <input style="padding-left: <?=strlen($mobilelohivo)?>ch;" type="tel" maxlength="8" onkeypress="return onlyNumberKey(event)" onkeyup="addDash(this)" name="mobil" id="mobil" value="<?=$mobil?>">
                        </div>
                    </div>

                    <?php $magyarazat .= "<strong>Szolgálati mobil száma</strong><p> Amennyiben a dolgozó
                        rendelkezik szolgálati mobiltelefonnal, annak a számát itt lehet megadni.<br>
                        Nem kötelező mező.</p>" ?>

                    <div>
                        <label for="modositasoka" id="modositasokcimke">Módosítás oka<?=(isset($onloadfelugro)) ? "*" : "" ?></label>
                        <textarea name="modositasoka" id="modositasoka" <?=(isset($onloadfelugro)) ? "required" : "" ?>></textarea>
                    </div>

                    <?php $magyarazat .= "<strong>Módosítás oka</strong><p>Itt a telefonkönyv adminja részére lehet jelezni
                        a dolgozó telefonszám módosításának okát.<br>Egyszerű számmódosítás esetén nem kötelező mező, minden más esetben az.</p>" ?>
                    
                    <div>
                        <?php
                        if(!$fieldsetstate)
                        {
                            ?><input type="submit" name="beKuld" value='<?=$button?>'><?php
                        }
                    ?></div>
                    <div class="submit">
                        <button type="button" onclick="window.close()">Mégsem</button>
                    </div>
                </div>
            </fieldset>
        </div>
    </form><?php
    $PHPvarsToJS['jelenbeo'] = $indexnumber;
}