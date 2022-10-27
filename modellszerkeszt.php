<?php

if(@!$sajatolvas)
{
    echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $gyarto = $modell = $tipus = $szines = $scanner = $fax = $defadmin = $defpass = $maxmeret = null;
    if(isset($_GET['tipus']))
    {
        $tipusnev = $_GET['tipus'];
        if($tipusnev == "nyomtato")
        {
            $tipus = "12";
        }
    }

    if(count($_POST) > 0)
    {
        $irhat = true;
        include("./db/modelldb.php");
    }
    
    $button = "Új modell";

    $tipusok = mySQLConnect("SELECT * FROM eszkoztipusok ORDER BY nev ASC");

    if(isset($_GET['id']))
    {
        $modellid = $_GET['id'];
        $modellszerk = mySQLConnect("SELECT * FROM modellek WHERE id = $modellid;");
        $modellszerk = mysqli_fetch_assoc($modellszerk);

        $gyarto = $modellszerk['gyarto'];
        $modell = $modellszerk['modell'];
        $tipus = $modellszerk['tipus'];

        if(@$tipusnev == "nyomtato" || @$tipus == "12")
        {
            $tipusnev = "nyomtato";
            $tipus = "12";
            $nyomtato = mySQLConnect("SELECT * FROM nyomtatomodellek WHERE modell = $modellid;");
            $nyomtato = mysqli_fetch_assoc($nyomtato);

            $szines = @$nyomtato['szines'];
            $scanner = @$nyomtato['scanner'];
            $fax = @$nyomtato['fax'];
            $maxmeret = @$nyomtato['maxmeret'];
        }

        if(@$tipusnev == "mediakonverter" || @$tipus > 20 && @$tipus < 26)
        {
            $tipusnev = "mediakonverter";
            $fizikairetegek = mySQLConnect("SELECT * FROM fizikairetegek;");
            $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
            $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
            $atviteliszabvanyok = mySQLConnect("SELECT * FROM atviteliszabvanyok;");

            $mediakonverter = mySQLConnect("SELECT * FROM mediakonvertermodellek WHERE modell = $modellid;");
            $mediakonverter = mysqli_fetch_assoc($mediakonverter);
            $fizikaireteg = @$mediakonverter['fizikaireteg'];
            $transzpszabvany = @$mediakonverter['transzpszabvany'];
            $transzpcsatlakozo = @$mediakonverter['transzpcsatlakozo'];
            $transzpsebesseg = @$mediakonverter['transzpsebesseg'];
            $lanszabvany = @$mediakonverter['lanszabvany'];
            $lancsatlakozo = @$mediakonverter['lancsatlakozo'];
            $lansebesseg = @$mediakonverter['lansebesseg'];
        }

        if(@$tipusnev == "bovitomodul" || @$tipus > 25 && @$tipus < 31)
        {
            $tipusnev = "bovitomodul";
            $fizikairetegek = mySQLConnect("SELECT * FROM fizikairetegek;");
            $csatlakozok = mySQLConnect("SELECT * FROM csatlakozotipusok;");
            $sebessegek = mySQLConnect("SELECT * FROM sebessegek;");
            $atviteliszabvanyok = mySQLConnect("SELECT * FROM atviteliszabvanyok;");

            $mediakonverter = mySQLConnect("SELECT * FROM bovitomodellek WHERE modell = $modellid;");
            $mediakonverter = mysqli_fetch_assoc($mediakonverter);
            $fizikaireteg = @$mediakonverter['fizikaireteg'];
            $transzpszabvany = @$mediakonverter['transzpszabvany'];
            $transzpcsatlakozo = @$mediakonverter['transzpcsatlakozo'];
            $transzpsebesseg = @$mediakonverter['transzpsebesseg'];
        }

        if (!isset($tipusnev))
        {
            $tipusnev = eszkozTipusValaszto($tipus);
        }

        $button = "Szerkesztés";

        ?><form action="<?=$RootPath?>/modellszerkeszt?action=update&tipus=<?=$tipusnev?>" method="post" onsubmit="beKuld.disabled = true; return true;">
        <input type ="hidden" id="id" name="id" value=<?=$modellid?>><?php
    }
    else
    {
        ?><form action="<?=$RootPath?>/modellszerkeszt?action=new" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
    }

    ?><div class="oldalcim">Modell szerkesztése</div>
    <div class="contentcenter">

        <?=gyartoPicker($gyarto)?>

        <div>
            <label for="modell">Modellnév:</label><br>
            <input type="text" accept-charset="utf-8" name="modell" id="modell" value="<?=$modell?>"></input>
        </div>

        <div>
            <label for="tipus">Eszköz fajtája:</label><br>
            <select id="tipus" name="tipus">
                <option value="" selected></option><?php
                foreach($tipusok as $x)
                {
                    ?><option value="<?php echo $x["id"] ?>" <?= ($tipus == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                }
            ?></select>
        </div><?php

        if(@$tipusnev == "nyomtato")
        {
            ?><div>
                <label for="szines">Színmód:</label><br>
                <select id="szines" name="szines">
                    <option value="" <?=(!$szines) ? "selected" : "" ?>>Fekete-Fehér</option>
                    <option value="1" <?=($szines == 1) ? "selected" : "" ?>>Színes</option>
                </select>
            </div>

            <div>
                <label for="maxmeret">Max nyomtatási méret:</label><br>
                <select id="maxmeret" name="maxmeret">
                    <option value="1" <?=($maxmeret == 1) ? "selected" : "" ?>>A4</option>
                    <option value="2" <?=($maxmeret == 2) ? "selected" : "" ?>>A3</option>
                    <option value="3" <?=($maxmeret == 3) ? "selected" : "" ?>>A2</option>
                    <option value="4" <?=($maxmeret == 4) ? "selected" : "" ?>>A1</option>
                    <option value="5" <?=($maxmeret == 5) ? "selected" : "" ?>>A0</option>
                </select>
            </div>
            
            <div>
                <label for="scanner">Szkenner:</label><br>
                <select id="scanner" name="scanner">
                    <option value="" <?=(!$scanner) ? "selected" : "" ?>>Nincs</option>
                    <option value="1" <?=($scanner == 1) ? "selected" : "" ?>>Van</option>
                </select>
            </div>

            <div>
                <label for="fax">Fax:</label><br>
                <select id="fax" name="fax">
                    <option value="" <?=(!$fax) ? "selected" : "" ?>>Nincs</option>
                    <option value="1" <?=($fax == 1) ? "selected" : "" ?>>Bővíthető modullal</option>
                    <option value="2" <?=($fax == 2) ? "selected" : "" ?>>Bépített</option>
                </select>
            </div>
            
            <div>
                <label for="defadmin">Alapértelmezett admin:</label><br>
                <input type="text" accept-charset="utf-8" name="defadmin" id="defadmin" value="<?=$defadmin?>"></input>
            </div>

            <div>
                <label for="defpass">Alapértelmezett jelszó:</label><br>
                <input type="text" accept-charset="utf-8" name="defpass" id="defpass" value="<?=$defpass?>"></input>
            </div><?php
        }

        if(@$tipusnev == "mediakonverter" || @$tipusnev == "bovitomodul")
        {
            ?><div>
                <label for="fizikaireteg">Transzporthálózat típusa:</label><br>
                <select id="fizikaireteg" name="fizikaireteg">
                    <option value="" selected></option><?php
                    foreach($fizikairetegek as $x)
                    {
                        ?><option value="<?=$x["id"]?>" <?= ($fizikaireteg == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                    }
                ?></select>
            </div>

            <div>
                <label for="transzpszabvany">Transzport szabvány:</label><br>
                <select id="transzpszabvany" name="transzpszabvany">
                    <option value="" selected></option><?php
                    foreach($atviteliszabvanyok as $x)
                    {
                        ?><option value="<?=$x["id"]?>" <?= ($transzpszabvany == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                    }
                ?></select>
            </div>

            <div>
                <label for="transzpcsatlakozo">Transzport csatlakozó:</label><br>
                <select id="transzpcsatlakozo" name="transzpcsatlakozo">
                    <option value="" selected></option><?php
                    foreach($csatlakozok as $x)
                    {
                        ?><option value="<?=$x["id"]?>" <?= ($transzpcsatlakozo == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                    }
                ?></select>
            </div>
            
            <div>
                <label for="transzpsebesseg">Transzport sebesség:</label><br>
                <select id="transzpsebesseg" name="transzpsebesseg">
                    <option value="" selected></option><?php
                    foreach($sebessegek as $x)
                    {
                        ?><option value="<?=$x["id"]?>" <?= ($transzpsebesseg == $x['id']) ? "selected" : "" ?>><?=$x['sebesseg']?></option><?php
                    }
                ?></select>
            </div><?php

            if(@$tipusnev == "mediakonverter")
            {
                ?><div>
                    <label for="lanszabvany">LAN szabvány:</label><br>
                    <select id="lanszabvany" name="lanszabvany">
                        <option value="" selected></option><?php
                        foreach($atviteliszabvanyok as $x)
                        {
                            ?><option value="<?=$x["id"]?>" <?= ($lanszabvany == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                        }
                    ?></select>
                </div>

                <div>
                    <label for="lancsatlakozo">LAN csatlakozó:</label><br>
                    <select id="lancsatlakozo" name="lancsatlakozo">
                        <option value="" selected></option><?php
                        foreach($csatlakozok as $x)
                        {
                            ?><option value="<?=$x["id"]?>" <?= ($lancsatlakozo == $x['id']) ? "selected" : "" ?>><?=$x['nev']?></option><?php
                        }
                    ?></select>
                </div>
                
                <div>
                    <label for="lansebesseg">LAN sebesség:</label><br>
                    <select id="lansebesseg" name="lansebesseg">
                        <option value="" selected></option><?php
                        foreach($sebessegek as $x)
                        {
                            ?><option value="<?=$x["id"]?>" <?= ($lansebesseg == $x['id']) ? "selected" : "" ?>><?=$x['sebesseg']?></option><?php
                        }
                    ?></select>
                </div><?php
            }
        }

        ?><div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
    </form><?php
        cancelForm();
    ?></div><?php
}