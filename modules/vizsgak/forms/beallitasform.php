<?php
if(@$irhat)
{
    $PHPvarsToJS[] = array(
            'name' => 'selectors',
            'val' => array('udvozloszoveg', 'vendegudvozlo', 'lablec')
        );
    $javascriptfiles[] = "includes/tinymce/tinymce.min.js";
	$javascriptfiles[] = "includes/js/tinyMCEinitializer.js";
    $nyithelp = true;
    ?><form action="<?=$vizsgabeallitasurl?>" enctype="multipart/form-data" method="post">
        <div class="ketharmad">
            <input type ="hidden" id="vizsgaid" name="vizsgaid" value=<?=$vizsgaid?>>
            <div>
                <div>
                    <label for="udvozloszoveg">Üdvözlőszöveg</label><br>
                    <textarea name="udvozloszoveg" id="udvozloszoveg"><?=$udvozloszoveg?></textarea>
                </div>

                <?php $magyarazat .= "<strong>Üdvözlőszöveg</strong><p>Ez jelenik meg, mikor egy bejelentkezett felhasználó megnyitja a vizsga főoldalát. Itt éri meg
                            elmagyarázni a felhasználók részére a vizsga okát, a vele elérni kívánt célokat, és a teljesítésének feltételeit.</p>"; ?>

                <div>
                    <label for="vendegudvozlo">Üdvözlőszöveg be nem jelentkezett felhasználók részére</label><br>
                    <textarea name="vendegudvozlo" id="vendegudvozlo"><?=$vendegudvozlo?></textarea>
                </div>

                <?php $magyarazat .= "<strong>Üdvözlőszöveg be nem jelentkezett felhasználók részére</strong><p>Ezt csak olyan felhasználók látják, akik bejelentkezés nélkül
                            érkeznek a vizsga főoldalára. Olyan üzenetet éri meg ideírni, ami segíti a bejelentkezésüket.</p>"; ?>

                <div>
                    <label for="lablec">Infó rész a láblécben</label><br>
                    <textarea name="lablec" id="lablec"><?=$lablecszoveg?></textarea>
                </div>

                <?php $magyarazat .= "<strong>Infó rész a láblécben</strong><p>Ide rövid ismertetőt írhatunk, amit a felhasználók a vizsga miden oldalán látni fognak.
                            Ha nincs rá szükségünk, nyugodtan üresen hagyhatjuk.</p>"; ?>
            </div>
            <div><?php
                if($fejleckep)
                {
                    ?><div>
                        <img src="<?=$RootPath?>/uploads/<?=$fejleckep?>" width="500px">
                        <label for="keptorol">Feltöltött kép eltávolítása</label><br>
                        <input type="checkbox" name="keptorol" />
                    </div><?php
                    $magyarazat .= "<strong>Feltöltött kép eltávolítása</strong><p>Ha nem akarunk más fejlécképet beállítani a vizsgához, ezzel törölhetjük a már meglévőt.
                            Ha <b>cserélni</b> szeretnénk a fejlécképet, úgy ezzel a jelölővel nem kell foglalkoznunk.</p>";
                }
                ?><div>
                    <label for="fejleckep">A vizsga fejléceként használt kép</label><br>
                    <input type="file" name="fejleckep" accept="image/jpeg, image/png, image/bmp">
                </div>
                
                <?php $magyarazat .= "<strong>A vizsga fejléceként használt kép</strong><p>Az itt kitallózott képet fogja a rendszer a vizsga fejléceként használni.
                            Jelenleg jpg, png, vagy bmp formátumú képet tölthetünk itt fel. Ajánlatos olyan képet használni, ami legalább 1800 pixel széles,
                            hogy minden monitoron kitöltse a képrenyőt teljes szélességében. A fejléc magassága legfeljebb 250 pixel lehet. Feltölthetünk ennél nagyobbat,
                            de a rendszer abból is csak egy 250 pixel magasságú szeletet fog mutatni.</p>"; ?>
                
                <?php
                if($mindir)
                {
                    ?><div>
                        <label for="url">A vizsga címsorban használt neve</label><br>
                        <input type="text" accept-charset="utf-8" name="url" id="url" value="<?=$url?>"></input>
                    </div><?php

                    $magyarazat .= "<strong>A vizsga címsorban használt neve</strong><p>Itt adhatjuk meg a vizsga \"nevét\". Az itt megadott érték fog megjelenni a címsorban,
                            pl.: <i>http://szervernev/vizsga/vizsganev</i>. Semmi nem tiltja, hogy ékezetes nevet használjunk itt, de inkább kerüljük az ékezetek
                            és speciális karakterek használatát. Az itt megadott névnek EGYEDINEK kell lennie, tehát nem lehet olyan korábbi vizsga, ami ugyanezt a nevet kapta.
                            Emellett figyeljünk rá, hogy ha felhasználók már ismerik a vizsga jelenlegi elérési útját, úgy már nem célszerű megváltoztatni, mivel nincs semmi,
                            ami a régi címről átirányítaná őket az újra.</p>";
                }
                ?><div>
                    <label for="nev">A vizsga neve</label><br>
                    <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                </div>

                <?php $magyarazat .= "<strong>A vizsga neve</strong><p>A vizsga általános megnevezése. Szabadon választható, lehet benne ékezet, és nem szükséges egyedinek lennie
                            (bár azért természetesen erősen ajánlott).</p>"; ?>
                <div>
                    <label for="leiras">Leírás</label><br>
                    <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                </div>

                <?php $magyarazat .= "<strong>Leírás</strong><p>A vizsga rövid leírása. Ez jelenik meg a <i>Vizsgák</i> menüpont főoldalán, ahol az összes élesített vizsga listázásra kerül.
                            Segítséget adhat a felhasználóknak a vizsga beazonosításában, de nem kötelező megadni.</p>"; ?>
                
                <div>
                    <label for="vizsgahossz">A vizsga kérdésszáma</label><br>
                    <input type="text" accept-charset="utf-8" name="kerdesszam" id="kerdesszam" value="<?=$kerdesszam?>"></input>
                </div>

                <?php $magyarazat .= "<strong>A vizsga kérdésszáma</strong><p>Itt adhatjuk meg, hogy a rendszer hány kérdést tegyen fel a vizsgázóknak. Bár a rendszer ezt itt külön nem ellenőrzi
                            figyeljünk arra, hogy a helyes működéshez legalább 20-25%-kal több kérdést vigyünk fel a rendszerbe, mint amennyit szeretnénk, hogy a vizsgázónak meg kelljen válaszolnia!
                            Tehát ha azt szeretnénk, hogy egy vizsgán 20 kérdést kelljen megválaszolni, úgy itt állítsunk be 20-at, és mielőtt a vizsgát élesítenénk,
                            a kérdések listáján legyen legalább 24-25 kérdés, amiből a rendszer a vizsgákon válogatni tud.</p>"; ?>

                <div>
                    <label for="minimumhelyes">Minimális helyes válaszok száma</label><br>
                    <input type="text" accept-charset="utf-8" name="minimumhelyes" id="minimumhelyes" value="<?=$minimumhelyes?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Minimális helyes válaszok száma</strong><p>A rendszer a vizsgát az itt megadott helyes válaszszám elérése után tekinti sikeresnek.</p>"; ?>

                <div>
                    <label for="vizsgaido">A vizsgára adott idő</label><br>
                    <input type="text" accept-charset="utf-8" name="vizsgaido" id="vizsgaido" value="<?=$vizsgaido?>"></input>
                </div>

                <?php $magyarazat .= "<strong>A vizsgára adott idő</strong><p>Itt állíthatjuk be, hogy mennyi időt szeretnénk biztosítani a vizsgázóknak a vizsga kitöltésére.
                            Ha nem szeretnénk időkorlátot megszabni, úgy hagyjuk a mezőt üresen, vagy állítsunk be 0-t.</p>"; ?>

                <div>
                    <label for="ismetelheto">A vizsga ismételhető</label><br>
                    <select id="ismetelheto" name="ismetelheto">
                        <option value="1" <?=($ismetelheto == 1) ? "selected" : "" ?>>Igen</option>
                        <option value="0" <?=($ismetelheto != 1) ? "selected" : "" ?>>Nem</option>
                    </select>
                </div>

                <?php $magyarazat .= "<strong>A vizsga ismételhető</strong><p>Itt tehetjük a vizsgát megismételhetővé. Amennyiben a \"Nem\" értéket választjuk itt ki,
                            úgy ebben a vizsgázási periódusban a felhasználó már nem próbálhatja újra a vizsgát.</p>"; ?>

                <div>
                    <label for="maxismetles">Újrapróbálkozások maximális száma</label><br>
                    <input type="text" accept-charset="utf-8" name="maxismetles" id="maxismetles" value="<?=$maxismetles?>"></input>
                </div>

                <?php $magyarazat .= "<strong>Újrapróbálkozások maximális száma</strong><p>Itt adhatjuk meg, hogy a vizsgázó hányszor tehet kísérletet a vizsga kitöltésével.
                            Az itt megadott értéket a rendszer csak akkor veszi figyelembe, ha a vizsgát ismételhetőként állítjuk be.</p>"; ?>

                <div>
                    <label for="eles">A vizsga élesítve</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="eles" id="publikalthidden" value="">
                        <input type="checkbox" name="eles" id="eles" value="1" <?=($vizsgaeles) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <?php $magyarazat .= "<strong>A vizsga élesítve</strong><p>Amíg a vizsgát nem élesítjük, csak a vizsga adminjainak számára jelenik meg.
                            Az éles időszakon kívül kitöltött vizsgák <b>kizárólag</b> a vizsga adminjait segítő gyakorlási, tanulási célokat szolgálnak, az élesítést követően
                            <b>NEM</b> jelennek meg az eredmények között. A vizsgalistán éles időszakban <b>kizárólag</b> azok a vizsgák jelennek meg,
                            amiket felhasználók az éles periódus alatt töltöttek ki. Élesítetlen időszakban csak az éles időszakon kívüli vizsgákat láthatjuk.
                            Az élesítetlen időszakban kitöltött próbavizsgák <b>nem</b> kapnak folyószámot, így a vizsgák listáján sosem téveszthetjük el,
                            hogy élesítve van-e a vizsga, vagy sem.</p>"; ?>

                <div class="submit"><input type="submit" value="<?=$button?>"></div><?php
                cancelForm();
                if(isset($contextmenujogok))
                {
                    if($contextmenujogok['ujkornyitas'])
                    {
                        ?><div class="submit"><input type="submit" name="ujornyitas" value="Jelen vizsgaperiódus lezárása, új periódus indítása" onclick="return confirm('Biztos vagy benne, hogy lezárnád a jelenleg futó vizsgaperiódust, és újat indítanál?');"></div><?php
                        
                        $magyarazat .= "<strong>Jelen vizsgaperiódus lezárása, új periódus indítása</strong><p>Ezzel indíthatunk új vizsgaperiódust. Ennek akkor van haszna,
                                ha például egy adott vizsgát a felhasználóknak periodikusan ismétlődve (havonta, félévente, évente, stb) kell kitöltenie. Az új periódus indításával
                                az előző periódus eredményei nem keverednek a új periódus eredményeivel.</p>";

                        if(1 > 2) // Ha lesz törlőmód, az 1 > 2 feltétel kivétele!!!
                        {
                            if(isset($_GET['torlomod']))
                            {
                                ?><div><a href="?vizsgareset" style="color: red" onclick="return confirm('Biztos vagy benne, hogy törölni akarod az ÖSSZES VISZGÁT?')">Korábbi vizsgák törlése</a></div><?php
                            }
                            elseif(!isset($_GET['torlomod']))
                            {
                                ?><div class="submit"><button onclick="return confirm('Biztos vagy benne, hogy bekapcsolod a törlő módot?'); return false;" style="background: #990000; border: #660000;">Törlőmód bekapcsolása</button></div><?php
                            }
                        }
                    }
                    
                }
            ?></div>
        </div>
    </form><?php
}