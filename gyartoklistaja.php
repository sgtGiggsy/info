<?php

if(!@$mindolvas)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $gyartok = mySQLConnect("SELECT * FROM gyartok ORDER BY nev;");

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/gyartoszerkeszt'">Új gyártó</button><?php
    }

    ?><div class="oldalcim">Gyártók listája</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 'i', '<?=$tipus?>?>')">ID</th>
                <th class="tsorth" onclick="sortTable(1, 's', '<?=$tipus?>?>')">Gyártó</th>
                <th></th>
            </tr>
        </thead>
        <tbody><?php
        foreach($gyartok as $gyarto)
        {
            ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/gyartoszerkeszt/<?=$gyarto['id']?>'>
                <td><?=$gyarto['id']?></td>
                <td><?=$gyarto['nev']?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}