<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{

    $ipcimek = mySQLConnect("SELECT ipcimek.id AS id, ipcim, ipcimek.vlan AS vlan, ipcimek.eszkoz AS eszkoz, vlanok.nev AS vlannev
        FROM ipcimek
            LEFT JOIN vlanok ON ipcimek.vlan = vlanok.id
        ORDER BY vlan, ipcimek.id;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/ipszerkeszt'">Új rezervált IP</button><?php
    }

    ?><div class="oldalcim">Rezervált IP címek listája</div><?php
    $zar = false;
    foreach($ipcimek as $ipcim)
    {
        if(@$vlan != $ipcim['vlan'])
        {
            if($zar)
            {
                ?></tbody>
                </table><?php
            }

            $vlan = $ipcim['vlan']
            ?><h1 style="text-transform: capitalize;"><?=$ipcim['vlannev']?> (<?=$ipcim['vlan']?>)</h1>
            <table id="<?=$vlan?>">
            <thead>
                <tr>
                    <th class="tsorth" onclick="sortTable(0, 's', '<?=$vlan?>')">IP cím</th>
                    <th class="tsorth" onclick="sortTable(1, 's', '<?=$vlan?>')">VLAN</th>
                    <th class="tsorth" onclick="sortTable(2, 's', '<?=$vlan?>')">Eszköz</th>
                </tr>
            </thead>
            <tbody><?php
            $zar = true;
        }
        $ipid = $ipcim['id'];
        ?><tr <?=($mindir) ? "class='kattinthatotr'" . "data-href='$RootPath/ipszerkeszt/$ipid'" : "" ?>>
            <td><?=$ipcim['ipcim']?></td>
            <td><?=$ipcim['vlan']?></td>
            <td><?=$ipcim['eszkoz']?></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}