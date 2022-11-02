<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $telefonjogosultsagok = mySQLConnect("SELECT * FROM telefonjogosultsagok;");
    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/telefonjogszerk'">Új telefonjogosultság</button><?php
    }

    ?><div class="oldalcim">Telefonjogosultságok listája</div>
    <table id="telefonjogosultsagok">
    <thead>
        <tr>
            <th class="tsorth" onclick="sortTable(0, '1', 'telefonjogosultsagok')">Jogosultság azonosító</th>
            <th class="tsorth" onclick="sortTable(1, 's', 'telefonjogosultsagok')">Jogosultság megnevezése</th>
        </tr>
    </thead>
    <tbody><?php
    foreach($telefonjogosultsagok as $telefonjogosultsag)
    {
        $telefonjogosultsagid = $telefonjogosultsag['id'];
        ?><tr class='kattinthatotr' <?=($mindir) ? "data-href='$RootPath/telefonjogszerk/$telefonjogosultsagid'" : "" ?>>
            <td><?=$telefonjogosultsag['id']?></td>
            <td><?=$telefonjogosultsag['nev']?></td>
        </tr><?php
    }
    ?></tbody>
    </table><?php
}