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
            $telefonszamid = $_GET['id'];
            $telefonszamszerk = mySQLConnect("SELECT * FROM telefonszamok WHERE id = $telefonszamid;");
            $telefonszamszerk = mysqli_fetch_assoc($telefonszamszerk);

            $portok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, epuletek.szam AS epuletszam, epuletek.nev AS epuletnev
                FROM portok
                    INNER JOIN vegpontiportok ON portok.id = vegpontiportok.port
                    LEFT JOIN epuletek ON vegpontiportok.epulet = epuletek.id
                ORDER BY epuletek.szam + 0, LENGTH(portok.port), portok.port;");
            $jogok = mySQLConnect("SELECT * FROM telefonjogosultsagok;");
            $tkozpontportok = mySQLConnect("SELECT portok.id AS id, portok.port AS port, telefonkozpontok.nev AS kozpontnev
                FROM portok
                    INNER JOIN tkozpontportok ON portok.id = tkozpontportok.port
                    INNER JOIN telefonkozpontok ON tkozpontportok.eszkoz = telefonkozpontok.eszkoz
                ORDER BY telefonkozpontok.nev, LENGTH(portok.port), portok.port;");
            $tipusok = mySQLConnect("SELECT * FROM telefonkeszulektipusok;");

            $id = $telefonszamszerk['id'];
            $szam = $telefonszamszerk['szam'];
            $cimke = $telefonszamszerk['cimke'];
            $port = $telefonszamszerk['port'];
            $jog = $telefonszamszerk['jog'];
            $tkozpontport = $telefonszamszerk['tkozpontport'];
            $megjegyzes = $telefonszamszerk['megjegyzes'];
            $tipus = $telefonszamszerk['tipus'];

            $button = "Telefonszám szerkesztése";

            ?><div class="oldalcim"><?=$szam?> telefonszám szerkesztése</div>
            <div class="contentcenter">
                <form action="<?=$RootPath?>/telefonszamszerkeszt?action=update" method="post" onsubmit="beKuld.disabled = true; return true;">
                    <input type ="hidden" id="id" name="id" value=<?=$id?>>
                    
                    <div>
                        <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                        <label for="cimke">Címke:</label><br>
                        <input type="text" accept-charset="utf-8" name="cimke" id="cimke" value="<?=$cimke?>"></input>
                    </div>
                    
                    <div>
                        <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                        <label for="jog">Jog:</label><br>
                        <select name="jog">
                            <option value=""></option><?php
                            foreach($jogok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $jog) ? "selected" : "" ?>><?=$x['id']?></option><?php
                            }
                        ?></select>
                    </div>

                    <div>
                        <label for="port">Végpont:</label><br>
                        <select name="port">
                            <option value=""></option><?php
                            foreach($portok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $port) ? "selected" : "" ?>><?=$x['epuletszam'] . ". épület, " . $x['port'] . " port" ?></option><?php
                            }
                        ?></select>
                    </div>

                    <div>
                        <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                        <label for="tkozpontport">Lage:</label><br>
                        <select name="tkozpontport">
                            <option value=""></option><?php
                            foreach($tkozpontportok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $tkozpontport) ? "selected" : "" ?>><?=$x['kozpontnev'] . " központ, " . $x['port'] . " port" ?></option><?php
                            }
                        ?></select>
                    </div>

                    <div>
                        <small style="color: #940e0e;">Importálás során felülírásra kerül!</small><br>
                        <label for="tipus">Típus:</label><br>
                        <select name="tipus">
                            <option value=""></option><?php
                            foreach($tipusok as $x)
                            {
                                ?><option value="<?=$x['id']?>" <?=($x['id'] == $tipus) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                            }
                        ?></select>
                    </div>

                    <div>
                        <label for="megjegyzes">A számhoz tartozó megjegyzés:</label><br>
                        <input type="text" accept-charset="utf-8" name="megjegyzes" id="megjegyzes" value="<?=$megjegyzes?>"></input>
                    </div>

                    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                </form><?php
                cancelForm();
            ?></div><?php
        }
    }
}