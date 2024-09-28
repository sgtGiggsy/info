<?php
if(@$irhat)
{
    ?><div class="contentcenter">
        <form action="<?=$RootPath?>/felhasznalo&action=permissions" method="post">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Olvasás</th>
                        <th>Írás</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
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
                        
                        ?><tr id="<?='szerk' . $x['id']?>" class='valtottsor'>
                            <td><label for="<?=$x['oldal']?>"><?=$x['menupont']?></label></td>
                            <td>
                                <select id="olvasas-<?=$x['id']?>" name="olvasas-<?=$x['id']?>">
                                    <option value="0" <?=(!isset($jogosultsag['olvasas']) || $jogosultsag['olvasas'] == 0) ? "" : "selected" ?>></option>
                                    <option value="1" <?=(isset($jogosultsag['iras']) && $jogosultsag['olvasas'] == 1) ? "selected" : "" ?>>Saját</option>
                                    <option value="2" <?=(isset($jogosultsag['iras']) && $jogosultsag['olvasas'] == 2) ? "selected" : "" ?>>Csoport</option>
                                    <option value="3" <?=(isset($jogosultsag['iras']) && $jogosultsag['olvasas'] == 3) ? "selected" : "" ?>>Minden</option>
                                </select>
                            </td>
                            <td>
                                <select id="olvasas-<?=$x['id']?>" name="iras-<?=$x['id']?>">
                                    <option value="0" <?=(!isset($jogosultsag['iras']) || $jogosultsag['iras'] == 0) ? "" : "selected" ?>></option>
                                    <option value="1" <?=(isset($jogosultsag['iras']) && $jogosultsag['iras'] == 1) ? "selected" : "" ?>>Saját</option>
                                    <option value="2" <?=(isset($jogosultsag['iras']) && $jogosultsag['iras'] == 2) ? "selected" : "" ?>>Csoport</option>
                                    <option value="3" <?=(isset($jogosultsag['iras']) && $jogosultsag['iras'] == 3) ? "selected" : "" ?>>Minden</option>
                                </select>
                            </td>
                            <td><button onclick="checkAll('<?='szerk' . $x['id']?>', '3'); return false;">Minden jog</button></td>
                            <td><button onclick="checkAll('<?='szerk' . $x['id']?>', '2'); return false;">Csoport jogok</button></td>
                            <td><button onclick="checkAll('<?='szerk' . $x['id']?>', '1'); return false;">Saját jogok</button></td>
                        </tr><?php
                    }
                    ?>
                </tbody>
            </table>
            <div class="submit"><input type="submit" value="<?=$button?>"></div>
            <?= cancelForm(); ?>
        </form>
    </div><?php
}