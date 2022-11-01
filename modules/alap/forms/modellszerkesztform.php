<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/modellszerkeszt?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }

                gyartoPicker($gyarto)

                ?><div>
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
            </form>
            <?php cancelForm(); ?>
        </div>
    </div><?php
}