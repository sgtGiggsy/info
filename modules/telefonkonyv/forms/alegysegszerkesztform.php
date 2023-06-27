<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/telefonkonyvalegyseg?action=<?=(isset($_GET['id'])) ? 'update' : 'new' ?><?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }   
                ?><div>
                    <label for="nev">Alegység teljes megnevezése:</label><br>
                    <textarea name="nev" id="nev"><?=$nev?></textarea>
                </div>
    
                <div>
                    <label for="sorrend">Alegység sorrendje:</label><br>
                    <select name="sorrend" id="sorrend">
                        <option value=""></option><?php
                        $sorrendszamlalo = 0;
                        $plushalf = 0;
                        foreach($alegysegek as $x)
                        {
                            if($sorrendszamlalo == 0)
                            {
                                $minushalf = $x['sorrend'] - 0.5;
                                ?><option value="<?=$minushalf?>"><?=$x['nev']?> =>> Felett</option><?php
                            }
                            if($sorrend == $x['sorrend'])
                            {
                                ?><option value="<?=$sorrend?>" selected><?=$x['nev']?> ==>> !! JELENLEGI HELY !!</option><?php
                            }
                            $plushalf = $x['sorrend'] + 0.5;
                            ?><option value="<?=$plushalf?>"><?=$x['nev']?> =>> Alatt</option><?php
                            $sorrendszamlalo++;
                        }
                        ?><option value="<?=$plushalf + 0.5?>" <?=(isset($_GET['action']) && $_GET['action'] == 'addnew') ? "selected" : "" ?>>UTOLSÓ ELEM</option>
                    </select>
                </div>
    
                <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
            </form>
            <?php cancelForm(); ?>
        </div>
    </div><?php
}