<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/telefonkonyv/szerkeszto&action=<?=$action?>" method="POST" onsubmit="beKuld.disabled"><?php
        if(!$felhasznalolista)
        {
            ?><input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
        }
        else
        {
            ?><div>
                <label for="sorrend">Felhasználó:</label><br><?php
                felhasznaloPicker(null, "id");
            ?></div><?php
        }
            ?><div>
                <table>
                    <thead>
                        <tr>
                            <th>Alegység neve</th>
                            <th>Írhatja</th>
                        </tr>
                    </thead>
                    <tbody><?php
                        //uasort($menu, function ($a, $b) { return $a->menupont > $b->menupont; });
                        foreach($alegysegek as $alegyseg)
                        {
                            ?><tr>
                                <td style="font-weight:bold;"><?=$alegyseg['nev']?></td>
                                <td>
                                    <label class="customcb">
                                        <input type="checkbox" name="csoport[]" id="csoport" value="<?=$alegyseg['csopid']?>" <?=($alegyseg['felhid']) ? "checked" : "" ?>>
                                        <span class="customcbjelolo"></span>
                                    </label>
                                </td>
                            </tr><?php
                        }
                    ?></tbody>
                </table>
                </div>
            <div class="submit"><input type="submit" value="<?=$button?>"></div>
            <?= cancelForm(); ?>
        </form>
    </div><?php
}