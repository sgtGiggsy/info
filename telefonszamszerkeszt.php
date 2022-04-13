<?php

if(@!$mindir)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $id = $szam = $cimke = $port = $jog = $tkozpontport = $megjegyzes = $tipus = null;

    if(isset($_GET['csvimport']))
    {
        if(isset($_POST['beKuld']))
        {
            $filetypes = array('.csv');
            $mediatype = array('application/vnd.ms-excel', 'text/csv');

            $fajl = $_FILES["csvinput"];
            //print_r($fajl);
            if (!in_array($fajl['type'], $mediatype))
            {
                $uzenet = "A fájl típusa nem megengedett: " . $fajl['name'];
            }
            else
            {
                $fajlnev = str_replace(".", time() . ".", $fajl['name']);
                $finalfile = $UPLOAD_FOLDER.strtolower($fajlnev);
                if(file_exists($finalfile))
                {
                    $uzenet = "A feltölteni kívánt fájl már létezik: " . $fajlnev;
                }
                else
                {
                    move_uploaded_file($fajl['tmp_name'], $finalfile);
                    $uzenet = 'A fájl feltöltése sikeresen megtörtént: ' . $fajlnev;

                    $irhat = true;
                    include("./db/telefonszamdb.php");
                }
            }

            if(!isset($errorcount))
            {
                ?><h2><?=$uzenet?></h2><?php
            }
        }
        
        ?><form action="<?=$RootPath?>/telefonszamszerkeszt?csvimport" method="post" enctype="multipart/form-data">
            <label for="csvinput">Importálni kívánt CSV:</label>
            <input type="file" name="csvinput" accept="text/csv" required>
            <div class="submit"><input type="submit" name="beKuld" value="Fájl feltöltése"></div>
        </form><?php
    }
    else
    {
        if(count($_POST) > 0)
        {
            $irhat = true;
            include("./db/telefonszamdb.php");
        }

        if(isset($_GET['id']))
        {
            $vlanid = $_GET['id'];
            $vlanszerk = mySQLConnect("SELECT * FROM vlanok WHERE id = $vlanid;");
            $vlanszerk = mysqli_fetch_assoc($vlanszerk);

            $id = $vlanszerk['id'];
            $nev = $vlanszerk['nev'];
            $leiras = $vlanszerk['leiras'];
            $kceh = $vlanszerk['kceh'];

            $button = "Telefonszám szerkesztése";
            

            ?><div class="oldalcim">Telefonszám szerkesztése</div>
            <div class="contentcenter">
                <small>A telefonszám bizonyos tulajdonságai nem módosíthatóak,
                    mivel azok a telefonközpontból vett adatokkal történő szinkronizálás során felülíródnak.</small>
                <form action="<?=$RootPath?>/telefonszamszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
                    <input type ="hidden" id="id" name="id" value=<?=$id?>>
                    
                    <div>
                        <label for="id">VLAN azonosító:</label><br>
                        <input type="text" accept-charset="utf-8" name="id" id="id" value="<?=$id?>"></input>
                    </div>

                    <div>
                        <label for="nev">VLAN neve:</label><br>
                        <input type="text" accept-charset="utf-8" name="nev" id="nev" value="<?=$nev?>"></input>
                    </div>

                    <div>
                        <label for="leiras">VLAN leírása:</label><br>
                        <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                    </div>

                    <div>
                        <label for="kceh">KCHEH hálózat:</label><br>
                        <select id="kceh" name="kceh">
                            <option value="" <?=(!$kceh) ? "selected" : "" ?>>Nem</option>
                            <option value="1" <?=($kceh == 1) ? "selected" : "" ?>>Igen</option>
                        </select>
                    </div>

                    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                </form><?php
                cancelForm();
            ?></div><?php
        }
    }
}