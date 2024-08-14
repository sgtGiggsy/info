<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $templatelista = mySQLConnect("SELECT id, szoveg FROM munkalaptemplateek ORDER BY szoveg ASC;");
    if($csoportir)
    {
        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/munkatemplateszerkeszt?action=addnew'">Új template létrehozása</button>
        </div><?php
    }
    ?><div class="PrintArea">
        <div class="oldalcim">A munkalap szöveg template-ek listája</div>
        <div class="normallist"><?php
            foreach($templatelista as $x)
            {
                ?><div><a href="<?=$RootPath?>/munkatemplateszerkeszt/<?=$x['id']?>"><?=$x['szoveg']?></a></div><?php
            }
        ?></div>
    </div><?php
}