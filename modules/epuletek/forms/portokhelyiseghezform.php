<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/portdb?action=generate&tipus=helyiseg<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;">
                <input type ="hidden" id="helyiseg" name="helyiseg" value=<?=$helyisegid?>>

                <div>
                    <label for="elsoport">Első port:</label><br>
                    <select id="elsoport" name="elsoport"><?php
                        foreach($epuletportok as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div>
                    <label for="utolsoport">Utolsó port:</label><br>
                    <select id="utolsoport" name="utolsoport"><?php
                        foreach($epuletportok as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['port']?></option><?php
                        }
                    ?></select>
                </div>

                <div class="submit"><input type="submit" name="beKuld" value="Portok helyiséghez kötése"></div>
            </form>
        </div>
    </div><?php
}