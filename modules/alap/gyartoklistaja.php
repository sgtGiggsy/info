<?php

if(!@$mindolvas)
{
	getPermissionError();
}
else
{
    $gyartok = new MySQLHandler("SELECT * FROM gyartok ORDER BY nev;");
    $gyartok = $gyartok->result;
    $tipus = "gyartoklistaja";

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
            </tr>
        </thead>
        <tbody><?php
        foreach($gyartok as $gyarto)
        {
            $gyartoid = $gyarto['id'];
            ?><tr <?=($mindir) ? "class='kattinthatotr'" . "data-href='$RootPath/gyartoszerkeszt/$gyartoid'" : "" ?>>
                <td><?=$gyarto['id']?></td>
                <td><?=$gyarto['nev']?></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}