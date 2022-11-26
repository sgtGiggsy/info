<?php
if(!$mindolvas)
{
    getPermissionError();
}
else
{
    function csoportZar($mindir, $csoportid, $alakulatok, $telephelyek, $RootPath)
    {
        ?><p class="csoportlink"><a href="<?=$RootPath?>/csoport/<?=$csoportid?>?action=addmember"><strong>Új tag hozzáadása</strong></a></p><br>
        </div>
        <div><p><h3>Felelősségi körök:</h3></p>
            <p><strong>Alakulatok:</strong></p><?php
            foreach($alakulatok as $alakulat)
            {
                if($csoportid == $alakulat['csoport'])
                {
                    if($alakulat['alakulat'])
                    {
                        ?><p><?=$alakulat['alakulat']?><?php
                            if($mindir)
                            {
                                ?><span class="right"><a onclick="return confirm('Biztosan szeretnéd a(z) <?=$alakulat['alakulat']?> felelősségi kört elvenni a csoporttól?')" href="<?=$RootPath?>/csoport/<?=$alakulat['csoport']?>?action=removeresponsibility&csopjogid=<?=$alakulat['csopjogid']?>"><b>X</b></a></span><?php
                            }
                        ?></p><?php
                    }
                }
            }
            ?><p><strong>Telephelyek:</strong></p><?php
            foreach($telephelyek as $telephely)
            {
                if($csoportid == $telephely['csoport'])
                {
                    if($telephely['telephely'])
                    {
                        ?><p><?=$telephely['telephely']?><?php
                            if($mindir)
                            {
                                ?><span class="right"><a onclick="return confirm('Biztosan szeretnéd a(z) <?=$telephely['telephely']?> felelősségi kört elvenni a csoporttól?')" href="<?=$RootPath?>/csoport/<?=$telephely['csoport']?>?action=removeresponsibility&csopjogid=<?=$telephely['csopjogid']?>"><b>X</b></a></span><?php
                            }
                        ?></p><?php
                    }
                }
            }
        ?><p class="csoportlink"><a href="<?=$RootPath?>/csoport/<?=$csoportid?>?action=addresponsibility"><strong>Új felelősségi kör hozzáadása</strong></a></p>
        </div>
        </div>
        </div><?php
    }

    $csoportok = mySQLConnect("SELECT csoportok.id AS csopid, csoportok.nev AS csoportnev, csoportok.leiras AS leiras,
            csoportok.szak AS csoportszak, felhasznalok.nev AS felhasznalo, felhasznalok.id AS felhasznaloid, szakok.nev AS szakneve
        FROM csoportok
            LEFT JOIN csoporttagsagok ON csoportok.id = csoporttagsagok.csoport
            LEFT JOIN felhasznalok ON felhasznalok.id = csoporttagsagok.felhasznalo
            LEFT JOIN szakok ON csoportok.szak = szakok.id
        ORDER BY csoportok.nev, felhasznalok.nev;");

    $csoportjogok = mySQLConnect("SELECT csoportjogok.id AS csopjogid, csoportjogok.csoport AS csoport, csoportjogok.alakulat AS alakulatid, csoportjogok.telephely AS telephelyid,
            alakulatok.nev AS alakulat, telephelyek.telephely AS telephely
        FROM csoportjogok
            LEFT JOIN alakulatok ON csoportjogok.alakulat = alakulatok.id
            LEFT JOIN telephelyek ON csoportjogok.telephely = telephelyek.id
        ORDER BY csoport;");

    $alakulatok = array();
    $telephelyek = array();

    foreach($csoportjogok as $csoportjog)
    {
        $alakulat = array('csoport' => $csoportjog['csoport'], 'alakulat' => $csoportjog['alakulat'], 'csopjogid' => $csoportjog['csopjogid']);
        $telephely = array('csoport' => $csoportjog['csoport'], 'telephely' => $csoportjog['telephely'], 'csopjogid' => $csoportjog['csopjogid']);

        $alakulatok[] = $alakulat;
        $telephelyek[] = $telephely;
    }

    if($mindir) 
    {
        ?><button type="button" onclick="location.href='<?=$RootPath?>/csoport?action=addnew'">Új csoport</button><?php
    }
    ?><div class='oldalcim'>Csoportok listája</div>
    <div class="csoportokwrap"><?php
    $zar = false;
    foreach($csoportok as $csoport)
    {
        if(@$csoportid != $csoport['csopid'])
        {
            if($zar)
            {
                csoportZar($mindir, $csoportid, $alakulatok, $telephelyek, $RootPath);
            }
            
            ?><div class="infobox csoport">
                <div class="infoboxtitle"><?=$csoport['csoportnev']?><?php
                    if($mindir)
                    {
                        ?><a class="help" href="<?=$RootPath?>/csoport/<?=$csoport['csopid']?>?action=edit"><img src='<?=$RootPath?>/images/edit.png' alt='Csoport módosítása' title='Csoport módosítása'/></a><?php
                    }
                ?></div>
                <div class="infoboxbody csoportbody">
                    <div><p><h3>Alapadatok:</h3></p>
                        <p><strong>Csoport szakterülete:</strong> <?=$csoport['szakneve']?></p>
                        <p><strong>Csoport leírása:</strong> <?=$csoport['leiras']?></p>
                    </div><br>
                    <div><p><h3>Tagok:</h3></p><?php
                $csoportid = $csoport['csopid'];
                $zar = true;
        }
        if($csoport['felhasznaloid'])
        {
            ?><p><a href="<?=$RootPath?>/felhasznalo/<?=$csoport['felhasznaloid']?>"><?=$csoport['felhasznalo']?></a><?php
                if($mindir)
                {
                    ?><span class="right"><a onclick="return confirm('Biztosan szeretnéd <?=$csoport['felhasznalo']?> felhasználót eltávolítani a(z) <?=$csoport['csoportnev']?> csoportból?')" href="<?=$RootPath?>/csoport/<?=$csoport['csopid']?>?action=removemember&memberid=<?=$csoport['felhasznaloid']?>"><b>X</b></a></span><?php
                }
            ?></p><?php
        }
    }
    csoportZar($mindir, $csoportid, $alakulatok, $telephelyek, $RootPath);
    ?></div><?php
}
