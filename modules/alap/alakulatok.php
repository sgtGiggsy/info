<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $alakulatok = mySQLConnect("SELECT * FROM alakulatok;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/alakulatszerkeszt'">Új alakulat</button><?php
    }

    ?><div class="oldalcim">Alakulatok listája</div>
    <table id="alakulatok">
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, 'i', 'alakulatok')">Azonosító</th>
            <th class="tsorth" onclick="sortTable(1, 's', 'alakulatok')">Teljes megnevezés</th>
            <th class="tsorth" onclick="sortTable(2, 's', 'alakulatok')">Rövid név</th>
        </tr>
    </thead>
    <tbody><?php
    $szamoz = 1;
    foreach($alakulatok as $alakulat)
    {
        $alakulatid = $alakulat['id'];
        
        ?><tr class='kattinthatotr-<?=($szamoz % 2 == 0) ? "2" : "1" ?>' <?=($mindir) ? "data-href='$RootPath/alakulatszerkeszt/$alakulatid'" : "" ?>>
            <td><?=$alakulat['id']?></td>
            <td><?=$alakulat['nev']?></td>
            <td><?=$alakulat['rovid']?></td>
        </tr><?php
        $szamoz++;
    }
    ?></tbody>
    </table><?php
}