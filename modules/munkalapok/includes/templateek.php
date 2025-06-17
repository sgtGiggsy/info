<?php

if(!@$csoportolvas)
{
	getPermissionError();
}
else
{
    $templatelista = new MySQLHandler("SELECT id, szoveg FROM munkalaptemplateek ORDER BY szoveg ASC;");
    if($csoportir)
    {
        ?><div class="szerkgombsor">
            <button type="button" onclick="location.href='<?=$RootPath?>/munkalapok/templateszerkeszt?action=addnew'">Új template létrehozása</button>
        </div><?php
    }
    ?><div class="PrintArea">
        <div class="oldalcim">A munkalap szöveg template-ek listája</div>
        <div class="normallist"><?php
            foreach($templatelista->Result() as $x)
            {
                ?><div><a href="<?=$RootPath?>/munkalapok/templateszerkeszt/<?=$x['id']?>"><?=$x['szoveg']?></a></div><?php
            }
        ?></div>
    </div><?php
}