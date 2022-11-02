<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $where = null;
    if(isset($_GET['id']))
    {
        $modelltipid = $_GET['id'];
        $where = "WHERE modellek.tipus = $thelyid";
    }

    $vlanok = mySQLConnect("SELECT * FROM vlanok;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/vlanszerkeszt'">Új VLAN</button><?php
    }

    ?><div class="oldalcim">VLAN-ok listája</div>
    <table id="vlanok">
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, 'i', 'vlanok')">Azonosító</th>
            <th class="tsorth" onclick="sortTable(1, 's', 'vlanok')">Név</th>
            <th class="tsorth" onclick="sortTable(2, 's', 'vlanok')">Leírás</th>
            <th class="tsorth" onclick="sortTable(3, 's', 'vlanok')">KCEH</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($vlanok as $vlan)
    {
        $vlanid = $vlan['id'];
        ?><tr class='kattinthatotr' <?=($mindir) ? "data-href='$RootPath/vlanszerkeszt/$vlanid'" : "" ?>>
            <td><?=$vlan['id']?></td>
            <td><?=$vlan['nev']?></td>
            <td><?=$vlan['leiras']?></td>
            <td><?=($vlan['kceh']) ? "Igen" : "Nem" ?></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}