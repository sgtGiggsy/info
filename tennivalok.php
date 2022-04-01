<?php

if(!$_SESSION[getenv('SESSION_NAME').'id'])
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $button = "Feladat listához adása";

    $id	= $cim = $leiras = $befejezve = $csatolthiba = $prioritas = null;

    // id cim leiras felhasznalo timestamp befejezve befejeztimestamp csatolthiba prioritas
/*
    if(isset($_POST['befejeve']))
    {
        $nev = "tenniműx";
        $stmt = $con->prepare('INSERT INTO teszt (nev) VALUES (?)');
        $stmt->bind_param('s', $nev);
        $stmt->execute();
    }
    echo "Getek: <br>";
    foreach($_GET as $key => $value)
    {
        echo "$key: $value";
        echo "<br>";
    }

    echo "Postok: <br>";
    foreach($_POST as $key => $value)
    {
        echo "$key: $value";
        echo "<br>";
    }
*/
    if(isset($_GET['oldal']))
    {
        $oldal = $_GET['oldal'];
    }

    $tennivalok = mySQLConnect("SELECT tennivalok.id AS id, cim, leiras, felhasznalok.nev AS felhasznalo, csatolthiba, prioritasok.nev AS prioritas, tennivalok.prioritas AS prioritasid, befejezve
        FROM tennivalok
            LEFT JOIN prioritasok ON tennivalok.prioritas = prioritasok.id
            LEFT JOIN felhasznalok ON tennivalok.felhasznalo = felhasznalok.id
        ORDER BY prioritas DESC;");
    
    ?><div class="oldalcim">Fejlesztési tervek</div>
    <div class="contentcenter"><?php

        foreach($tennivalok as $tennivalo)
        {
            ?><div style="margin: 0 0 15px 0; padding: 4px; background-color: <?php if(!$tennivalo['befejezve']) { switch($tennivalo['prioritasid']) { case 5: echo 'red'; break; case 4: echo 'orange'; break; case 3: echo 'yellow'; break; case 2: echo 'blue'; break; case 1: echo 'grey'; } } else { echo 'green'; } ?> ">
                <div>
                    <div style="font-size: 0.9em; padding: 0px; word-wrap: break-word"><?=$tennivalo['felhasznalo']?></div>
                    <form id="tennivalo" action="<?=$RootPath?>/tennivalodb?action=update" method="POST">
                        <input type ="hidden" id="id" name="id" value=<?=$tennivalo['id']?>>
                        <label class="checkbox" style="white-space: pre-wrap"><?=$tennivalo['leiras']?><?php
                        if($mindir)
                        {
                            ?><input type="checkbox" name="befejezve" id="befejezve" value="1" onChange='submit()' <?=($tennivalo['befejezve']) ? "checked" : "" ?>><?php
                        }
                        ?></label>
                    </form>
                </div>
            </div><?php
        }
        ?>
        <form action="<?=$RootPath?>/tennivalodb?action=new" method="post" onsubmit="beKuld.disabled = true; return true;">
            <input type ="hidden" id="cim" name="cim" value="">
            <div style="display: grid; grid-template-columns: 1fr 1fr">
                <div style="margin: 0 20px 0 0">
                    <label for="leiras">Feladat leírása:</label><br>
                    <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                </div>
                <?php priorityPicker($prioritas); ?>
            </div>
            <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
        </form>
    
    </div><?php
}