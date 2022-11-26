<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/csoport?action=addresponsibility<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }
   
                alakulatPicker(null);

                $magyarazat .= "<strong>Alakulat</strong><p>Az alakulat,
                    amihez jogosultságot akarunk adni a csoportnak.</p>"

                ?><div>
                    <label for="telephely">Telephely:</label><br>
                    <select id="telephely" name="telephely">
                        <option value="" selected></option><?php
                        foreach($telephelyek as $x)
                        {
                            ?><option value="<?=$x["id"]?>"><?=$x['telephely']?></option><?php
                        }
                    ?></select>
                </div>

                <?php $magyarazat .= "<strong>Telephely</strong><p>A telephely, amihez jogosultságot
                    akarunk adni a csoportnak.</p>"?>
                
                <div class="submit"><input type="submit" value='<?=$button?>'></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div>
    
    <script>
        document.getElementById('telephely').onchange = function() {
            select = document.getElementById('alakulat');
            select.value = '';
        };

        document.getElementById('alakulat').onchange = function() {
            select = document.getElementById('telephely');
            select.value = '';
        };
    </script><?php
}