<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <div>
            <form action="<?=$RootPath?>/felhasznalo&action=permissions<?=$kuldooldal?>" method="post">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Olvasás</th>
                            <th>Írás</th>
                        </tr>
                    </thead>
                    <tbody>
                        <input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
                        $szamoz = 1;
                        //uasort($menu, function ($a, $b) { return $a->menupont > $b->menupont; });
                        foreach($menu as $x)
                        {
                            $jogosultsag = array("olvasas" => null, "iras" => null);
                            foreach($jogosultsagok as $y)
                            {
                                if($y['menupont'] == $x['id'])
                                {
                                    $jogosultsag = $y;
                                    break;
                                }
                            }
                            
                            ?><tr id="<?='szerk' . $x['id']?>" class='valtottsor-<?=($szamoz % 2 == 0) ? "2" : "1" ?>'>
                                <td><label for="<?=$x['oldal']?>"><?=$x['menupont']?></label></td>
                                <td>
                                    <select id="olvasas" name="olvasas-<?=$x['id']?>">
                                        <option value="0" <?=(!isset($jogosultsag['olvasas']) || $jogosultsag['olvasas'] == 0) ? "" : "selected" ?>></option>
                                        <option value="1" <?=(isset($jogosultsag['iras']) && $jogosultsag['olvasas'] == 1) ? "selected" : "" ?>>Saját</option>
                                        <option value="2" <?=(isset($jogosultsag['iras']) && $jogosultsag['olvasas'] == 2) ? "selected" : "" ?>>Csoport</option>
                                        <option value="3" <?=(isset($jogosultsag['iras']) && $jogosultsag['olvasas'] == 3) ? "selected" : "" ?>>Minden</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="iras" name="iras-<?=$x['id']?>">
                                        <option value="0" <?=(!isset($jogosultsag['iras']) || $jogosultsag['iras'] == 0) ? "" : "selected" ?>></option>
                                        <option value="1" <?=(isset($jogosultsag['iras']) && $jogosultsag['iras'] == 1) ? "selected" : "" ?>>Saját</option>
                                        <option value="2" <?=(isset($jogosultsag['iras']) && $jogosultsag['iras'] == 2) ? "selected" : "" ?>>Csoport</option>
                                        <option value="3" <?=(isset($jogosultsag['iras']) && $jogosultsag['iras'] == 3) ? "selected" : "" ?>>Minden</option>
                                    </select>
                                </td>
                            </tr><?php
                            $szamoz++;
                        }
                        ?>
                    </tbody>
                </table>
                <div class="submit"><input type="submit" value="<?=$button?>"></div>
                <?= cancelForm(); ?>
            </form>
        </div>
    </div><?php
}