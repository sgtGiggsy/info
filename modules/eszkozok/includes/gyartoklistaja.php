<?php

if(!$contextmenujogok['gyartoklistaja'])
{
	getPermissionError();
}
else
{
    $oszlopok = array(
        array('nev' => 'Gyártó', 'tipus' => 's')
    );
    $gyartok = new MySQLHandler("SELECT * FROM gyartok ORDER BY nev;");
    $gyartok = $gyartok->Result();
    $tipus = "gyartoklistaja";

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/gyartoszerkeszt'">Új gyártó</button><?php
    }

    ?><div class="oldalcim">Gyártók listája</div>
    <table id="<?=$tipus?>">
        <thead>
            <tr><?php
                sortTableHeader($oszlopok, $tipus);
            ?></tr>
        </thead>
        <tbody><?php
        foreach($gyartok as $gyarto)
        {
            $kattinthatolink = "$RootPath/eszkozalap/gyartoszerkeszt/" . $gyarto['id'];
            ?><tr class='trlink'>
                <td><a href="<?=$kattinthatolink?>"><?=$gyarto['nev']?></a></td>
            </tr><?php
        }
        ?></tbody>
    </table><?php
}