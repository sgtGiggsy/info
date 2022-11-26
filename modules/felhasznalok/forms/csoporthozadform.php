<?php
if(@$irhat)
{
    ?><div class="contentleft">
        <div>
            <form action="<?=$RootPath?>/csoport?action=addmember<?=$kuldooldal?>" method="post" onsubmit="beKuld.disabled = true; return true;"><?php
                if(isset($_GET['id']))
                {
                    ?><input type ="hidden" id="id" name="id" value=<?=$_GET['id']?>><?php
                }
   
                felhasznaloPicker(null, 'felhasznalo');

                ?><div class="submit"><input type="submit" value='<?=$button?>'></div>
            </form>
            <?= cancelForm() ?>
        </div>
    </div><?php
}