<?php
if(!$felhasznaloid)
{
    echo "<h2>Az oldal kizárólag bejelentkezett felhasználók számára érhető el!</h2>";
}
else
{   
    $javascriptfiles[] = "modules/vizsgak/includes/vizsga.js";
    $vizsgafolytat = $debug = false;

    $korabbikitoltesek = mySQLConnect("SELECT vizsgak_kitoltesek.id AS id,
            vizsgak_kitoltesek.felhasznalo AS felhasznalo,
            vizsgak_kitoltesek.befejezett AS befejezett,
            vizsgak_kitoltesek.kitoltesideje AS kitoltesideje,
            vizsgak_vizsgakorok.id AS vizsgakorid
        FROM vizsgak_kitoltesek
            INNER JOIN vizsgak_vizsgakorok ON vizsgak_kitoltesek.vizsgakor = vizsgak_vizsgakorok.id
        WHERE felhasznalo = $felhasznaloid AND $korvizsgaszures $vizsgaelszures
        ORDER BY vizsgak_kitoltesek.id DESC;");
    $kitoltesszam = mysqli_num_rows($korabbikitoltesek);

    if($kitoltesszam > 0)
    {
        $legutobbikitoltes = mysqli_fetch_assoc($korabbikitoltesek);
        
        if(!$legutobbikitoltes['befejezett'])
        {
            $kitoltesid = $legutobbikitoltes['id'];
            $vizsgafolytat = true;
        }
    }

    if(!$vizsgafolytat)
    {
        if(isset($_GET['action']) && $_GET['action'] == "startnew")
        {
            $irhat = true;
            include("./modules/vizsgak/db/vizsgadb.php");

            $targeturl = "$RootPath/vizsga/" . $vizsgaadatok['url'] . "/vizsgazas";

            $firstquestion = mySQLConnect("SELECT id FROM vizsgak_kerdesek WHERE vizsga = $vizsgaid ORDER BY RAND() LIMIT 1;");
            $firstquestion = mysqli_fetch_assoc($firstquestion)['id'];

            mySQLConnect("INSERT INTO vizsgak_kitoltesvalaszok (kitoltes, kerdes) VALUES ($lastinsert, $firstquestion);");

            header("Location: $targeturl");
        }

        ?><div class="oldalcim">Vizsgázás</div><?php
        if($kitoltesszam > 0)
        {
            ?><h2>Korábbi kitöltések</h2><?php
            foreach($korabbikitoltesek as $x)
            {
                ?><a href= './vizsgareszletezo/<?=$x['id']?>'><?=$x['kitoltesideje']?></a><?php
            }

            if($vizsgaadatok['ismetelheto'])
            {
                if($kitoltesszam >= $vizsgaadatok['maxismetles'])
                {
                    ?><h2>Ön mind az <?=$vizsgaadatok['maxismetles']?> vizsgalehetőségét felhasználta. A vizsgateszt újabb kitöltése nem lehetséges.</h2><?php
                }
                else
                {
                    $lehetosegszam = $vizsgaadatok['maxismetles'] - $kitoltesszam;
                    ?><h2>Újrapróbálhatja a vizsgát még <?=$lehetosegszam?> alkalommal</h2>
                    <form action='./vizsgazas?action=startnew' method='POST'>
                        <div class='submit'><input type='submit' value='Vizsga megismétlése'></div>
                    </form><?php
                }
            }
        }
        else
        {
            ?><form action='./vizsgazas?action=startnew' method='POST'>
                <div class='submit'><input type='submit' value='Vizsga megkezdése'></div>
            </form><?php
        }
    }
    else
    {
        if($vizsgaadatok['vizsgaido'] && $vizsgaadatok['vizsgaido'] > 0)
        {
            $hatralevoido = $vizsgaadatok['vizsgaido'] * 60;
            $hatralevoido -= time() - strtotime($legutobbikitoltes['kitoltesideje']);

            if($hatralevoido <= 0)
            {
                $_GET['action'] = "finalize";
                $_POST['kitoltesid'] = $kitoltesid;
                $irhat = true;
                
                include("./modules/vizsgak/db/vizsgadb.php");
                header("Location: ./vizsgareszletezo/$kitoltesid");
            }
        }
        else
        {
            $hatralevoido = false;
        }
        
        // Ha volt válaszbeküldés, meghívjuk az adatbázis kezeléséért felelős fájlt
        if(isset($_GET['action']))
        {
            if($_GET['action'] == "answerquestion")
            {
                $irhat = true;
                include("./modules/vizsgak/db/vizsgadb.php");
            }
            elseif($_GET['action'] == "finalize")
            {
                $irhat = true;
                include("./modules/vizsgak/db/vizsgadb.php");
                header("Location: ./vizsgareszletezo/$kitid");
            }
        }
        
        $loopcount = 0; // Ciklusszámláló a véletlen generátorhoz a végtelen ciklus elkerülése miatt.
        $valaszelemsorszam = 1;
        $voltvalasztas = false;
        $kitoltesvalaszid = null; // A jelen kérdés ID-ja
        $kovetkezokerdesid = null; // Változó az itt létrehozott új kérdés adatbázis ID-jával.
        $viszgalezarhato = false; // A vizsga nem zárható le, amíg ez a bool érték false állásban van
        $kerdeshezlep = null; // Ahhoz kell, hogy ha korábbi kérdést szerkesztünk, a rendszer az azt követőhöz lépjen, ne a legfrissebbhez
        $ujkerdes = null; // Ahhoz kell, hogy egy oldal betöltésénél új kérdés jöjjön létre
        $valasznelkul = 0; // Azon feltett kérdések száma, amire a felhasználó még nem adott választ
        $megvalaszoltkerdesek = array(); // Tömb amiben a korábban már megjelent kérdéseket tároljuk az összevetéshez
        $adottvalaszok = array(); // Tömb, hogy a válaszok szerkesztésénél az adott válaszok vizuálisan is ki legyenek jelölve
        $tesztvalaszok = mySQLConnect("SELECT * FROM vizsgak_kitoltesvalaszok WHERE kitoltes = $kitoltesid ORDER BY id ASC");
        $tesztvalaszlista = mysqliToArray($tesztvalaszok);

        $kerdeslista = mySQLConnect("SELECT vizsgak_kerdesek.id AS id,
                vizsgak_kerdesek.kerdes,
                feltoltesek.fajl AS kepurl
            FROM vizsgak_kerdesek
                LEFT JOIN feltoltesek ON vizsgak_kerdesek.kep = feltoltesek.id
            WHERE vizsga = $vizsgaid ORDER BY id DESC;");
        $kerdeslista = mysqliToArray($kerdeslista);

        // Ha még csak az első kérdés lett feltéve, vagy még nem értük el a max kérdésszámot és egy új kérdés megválaszolása felől érkezünk
        if(isset($_GET['ujkerdes']) && (mysqli_num_rows($tesztvalaszok) < $vizsgaadatok['kerdesszam']))
        {
            foreach($tesztvalaszok as $marvolt)
            {
                $megvalaszoltkerdesek[] = $marvolt['kerdes']; // Kigyűjtjük a már megjelent kérdéseket tömbbe.
            }
            
            do {
                $loopcount++;
                $kerdesarrayid = array_rand($kerdeslista); // Véletlenszerűen kiveszünk egy elemet a tömbből
                $kovetkezokerdesid = $kerdeslista[$kerdesarrayid]['id']; // Lekérdezzük az elem adatbázis ID-ját
            } while(in_array($kovetkezokerdesid, $megvalaszoltkerdesek) && $loopcount < 50); // Ha az elem nem jelent még meg, továbblépünk

            if($loopcount < 50) // Ha a ciklus rendeltetésszerűen ért véget, a kérdés hozzáadása az adatbázishoz
            {
                $con = mySQLConnect();
                $stmt = $con->prepare('INSERT INTO vizsgak_kitoltesvalaszok (kitoltes, kerdes)  VALUES (?, ?)');
                $stmt->bind_param('ss', $kitoltesid, $kovetkezokerdesid);
                $stmt->execute();
                if(mysqli_errno($con) != 0)
                {
                    echo "<h2>A kérdés hozzáadása sikertelen!<br></h2>";
                    echo "Hibakód:" . mysqli_errno($con) . "<br>" . mysqli_error($con);
                }
                $kitoltesvalaszid = mysqli_insert_id($con);
            }
            else
            {
                echo "<h2>A következő kérdés kiválasztása sikertelen! Kérlek próbálkozz újra!</h2>
                    <p>Ha második próbálkozásra sem sikerül az új kérdés betöltése,
                    kérlek értesítsd a vizsga adminisztrátorait!</p>";
            }
            //header("Location: ./vizsgazas");
        }
        elseif(mysqli_num_rows($tesztvalaszok) == $vizsgaadatok['kerdesszam']) // Ha elértük az elvárt kérdésszámot, a vizsga lezárható
        {
            $viszgalezarhato = true;
        }

        $osszkerdesszam = count($tesztvalaszlista);
        if(isset($_GET['kerdessorszam'])) // Amennyiben egy meglévő választ szerkesztenénk, itt választjuk ki a tömbből
        {
            $valaszelemsorszam = $_GET['kerdessorszam'];
            $kivalasztottkerdesid = $tesztvalaszlista[$_GET['kerdessorszam']-1]['kerdes'] ?? false;
            $kitoltesvalaszid = $tesztvalaszlista[$_GET['kerdessorszam']-1]['id'] ?? false;
            if($valaszelemsorszam < $osszkerdesszam - 1) // A következő kérdés kiválasztása, amennyiben nem a legutolsó kérdés az
            {
                $kerdeshezlep = "&kerdessorszam=" . $valaszelemsorszam + 1;
            }
        }
        elseif($kitoltesvalaszid)
        {
            $valaszelemsorszam = array_key_last($tesztvalaszlista) + 2;
            $osszkerdesszam++;
            $kivalasztottkerdesid = $kovetkezokerdesid;
            $ujkerdes = "&ujkerdes";
        }
        else // A legutolsó kérdés kiválasztása a tömbből
        {
            $valaszelemsorszam = array_key_last($tesztvalaszlista) + 1;
            $kivalasztottkerdesid = $tesztvalaszlista[$valaszelemsorszam - 1]['kerdes'];
            $kitoltesvalaszid = $tesztvalaszlista[$valaszelemsorszam - 1]['id'];
            $ujkerdes = "&ujkerdes";
        }


        if(!$kivalasztottkerdesid)
        {
            ?><h2>A kiválasztott kérdés nem található az adatbázisban!</h2><?php
        }
        else
        {
            // Kikeressük a tömbből az utolsó kérdés ID-jának megfelelő elemet
            $kivalasztottkerdesarrayid = array_search($kivalasztottkerdesid, array_column($kerdeslista, 'id'));
            $kivalasztottkerdes = $kerdeslista[$kivalasztottkerdesarrayid];
            if(isset($_GET['kerdessorszam']) || ($kivalasztottkerdesid && $viszgalezarhato))
            {
                if(isset($_GET['kerdessorszam']))
                {
                    $arrayid = $_GET['kerdessorszam'] - 1;
                }
                else
                {
                    $arrayid = array_key_last($tesztvalaszlista);
                }
                $adottvalaszok[] = $tesztvalaszlista[$arrayid]['valasz'];
                $adottvalaszok[] = $tesztvalaszlista[$arrayid]['valasz2'];
                $adottvalaszok[] = $tesztvalaszlista[$arrayid]['valasz3'];
            }

            // Lekérjük a kiválasztott kérdéshez tartozó válaszlehetőségeket
            $valaszlehetosegek = mySQLConnect("SELECT id, helyes, valaszszoveg, kerdes,
                    (SELECT COUNT(vizsgak_valaszlehetosegek.helyes = 1) FROM vizsgak_valaszlehetosegek WHERE kerdes = $kivalasztottkerdesid) AS helyesvalaszszam
                FROM vizsgak_valaszlehetosegek
                WHERE kerdes = $kivalasztottkerdesid;");

            if(isset($_GET['debug']) && $_GET['debug'] == "adminvagyok")
            {
                $debug = true;
            }

            // Kérdés megjelenítés rész
            ?><div class="contentcenter">
                <div class="vizsgacard">
                    <div class="szerkcardtitle">
                        <div><?=$valaszelemsorszam?>/<?=$vizsgaadatok['kerdesszam']?> kérdés</div>
                        <div class="buttondropdown">
                            <span style="cursor: pointer;" onclick="showPopup('megvalaszoltkerdesek')">⮟</span>
                            <div id="megvalaszoltkerdesek" onmouseleave="hidePopup('megvalaszoltkerdesek')"><?php
                                $sorszam = 1;
                                foreach($tesztvalaszlista as $tesztvalasz)
                                {
                                    ?><div <?=(!$tesztvalasz['valasz']) ? "style='font-style: italic;'" : "" ?>><a href='./vizsgazas<?=($osszkerdesszam != $sorszam) ? "?kerdessorszam=$sorszam" : "" ?>'><?=$sorszam?>. kérdés</a></div><?php
                                    if(!$tesztvalasz['valasz'])
                                    {
                                        $valasznelkul++;
                                    }
                                    $sorszam++;
                                }
                            ?></div>
                        </div>
                    </div>
                    <div class="vizsgakerdesbody">
                        <div class="backforward">
                            <div><?php
                            if($valaszelemsorszam > 1)
                            {
                                ?><a href="./vizsgazas?kerdessorszam=<?=$valaszelemsorszam-1?>"><< Előző kérdés</a><?php
                            }
                            ?></div><div><?php
                            if($valaszelemsorszam == array_key_last($tesztvalaszlista) && $osszkerdesszam > 1)
                            {
                                ?><a href="./vizsgazas">Következő kérdés >></a><?php
                            }
                            elseif($osszkerdesszam > 1 && isset($_GET['kerdessorszam']))
                            {
                                ?><a href="./vizsgazas?kerdessorszam=<?=$valaszelemsorszam+1?>">Következő kérdés >></a><?php
                            }
                            ?></div>
                        </div>
                        <div class="vizsgakerdeskep"><?=($kivalasztottkerdes['kepurl']) ? "<img src='$RootPath" . '/uploads/' . $kivalasztottkerdes['kepurl'] . "'>" : "" ?></div>
                        <div class="vizsgakerdesszoveg"><?=$kivalasztottkerdes['kerdes']?></div>
                        <form action='./vizsgazas?action=answerquestion<?=$kerdeshezlep?><?=$ujkerdes?>' method='POST' onsubmit="beKuld.disabled = true; return true;">
                            <input type ="hidden" id="kitoltesvalaszid" name="kitoltesvalaszid" value=<?=$kitoltesvalaszid?> />
                            <input type ="hidden" id="kitoltesid" name="kitoltesid" value=<?=$kitoltesid?> />
                            <input type ="hidden" id="kerdesid" name="kerdesid" value=<?=$kivalasztottkerdesid?> />
                            <div class="vizsgavalaszlista">
                                <?php
                                    foreach($valaszlehetosegek as $valasz)
                                    {
                                        $inptype = "radio";
                                        if($valasz['helyesvalaszszam'] > 1)
                                        {
                                            $inptype = "checkbox";
                                        }
                                        if(in_array($valasz['id'], $adottvalaszok))
                                        {
                                            $kivalaszt = "checked";
                                            $voltvalasztas = true;
                                        }
                                        else
                                        {
                                            $kivalaszt = "";
                                        }
                                        ?><div><label <?=($debug && $valasz['helyes'] == 1) ? "style='font-style: italic;'" : "" ?>><input type="<?=$inptype?>" name="valaszok[]" id="valaszok" onclick="halasztKuldSwitch();" value="<?=$valasz['id']?>" <?=$kivalaszt?>><?=$valasz['valaszszoveg']?></label></div><?php
                                        
                                    }
                                ?><div class="submit"><input type="submit" name="beKuld" id="valaszkuld" value='<?=($voltvalasztas) ? "Válasz beküldése" : "Kérdés későbbre halasztása" ?>'></div>
                            </div>
                        </form><?php
                        if($viszgalezarhato)
                        {
                            ?><form action='./vizsgazas?action=finalize' method='POST'>
                                <input type ="hidden" id="kitoltesid" name="kitoltesid" value='<?=$kitoltesid?>' />
                                <div class="submit"><input type="submit" value="Válaszok véglegesítése, vizsga befejezése" onclick="return confirm('Biztosan szeretnéd véglegesíteni a válaszaidat?\nEzt követően már nem fogod tudni módosítani őket.')"></div>
                            </form><?php
                            if((!isset($_GET['kerdessorszam']) && $valasznelkul != 1) || (!isset($_GET['kerdessorszam']) && $valasznelkul > 1) || isset($_GET['kerdessorszam']))
                            {
                                ?><div class="vizsgabottommessage"><?php
                                    if($valasznelkul > 0)
                                    {
                                        ?>Még maradt <?=$valasznelkul?> megválaszolatlan, későbbre halasztott kérdés<?php
                                    }
                                    else
                                    {
                                        ?>Nem maradt megválaszolatlan kérdés<?php
                                    }
                                ?></div><?php
                            }
                        }
                    ?></div>
                </div>
            </div><?php
            if($hatralevoido)
            {
                ?><div id="hatralevoido" class="hatralevoido"></div>
                <div id="lejartido">
                    <div>
                        <h2>Lejárt a vizsgára kapott idő!</h2><br>
                        <p>További kérdések megválaszolására nincs lehetőség.</p>
                        <p>Átirányítunk a vizsga kiértékelése oldalra.</p>
                    </div>
                </div><?php
                $PHPvarsToJS[] = array(
                        'name' => 'hatralevoido',
                        'val' => $hatralevoido
                    );
            }
        }
    }
}