<?php

if(!$_SESSION[getenv('SESSION_NAME').'id'])
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    $button = "Feladat listához adása";

    $id	= $cim = $leiras = $befejezve = $csatolthiba = $prioritas = $bovitett = null;

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

    $where = "WHERE felhasznalok.id = $felhasznaloid";
    if($mindolvas)
    {
        $where = null;
    }

    $tennivalok = mySQLConnect("SELECT tennivalok.id AS id, cim, leiras, bovitett, felhasznalok.nev AS felhasznalo, csatolthiba, prioritasok.nev AS prioritas, tennivalok.prioritas AS prioritasid, befejezve, timestamp
        FROM tennivalok
            LEFT JOIN prioritasok ON tennivalok.prioritas = prioritasok.id
            LEFT JOIN felhasznalok ON tennivalok.felhasznalo = felhasznalok.id
        $where
        ORDER BY timestamp DESC;");
    
    ?><div class="oldalcim">Tennivalók</div>
    <div class="tennivalokwrap">
        <div class="tennivaloformwrap">
            <form action="<?=$RootPath?>/tennivalodb?action=new" method="post" onsubmit="beKuld.disabled = true; return true;">
                <div class="tennivaloform">
                    <input type ="hidden" id="cim" name="cim" value="">
                        <div>
                            <label for="leiras">Rövid összegzés:</label><br>
                            <textarea name="leiras" id="leiras"><?=$leiras?></textarea>
                        </div>
                        <div>
                            <label for="bovitett">Feladat bővített leírása:</label><br>
                            <textarea name="bovitett" id="bovitett"><?=$bovitett?></textarea>
                        </div>
                        <?php priorityPicker($prioritas); ?>
                    <div class="submit"><input type="submit" name="beKuld" value="<?=$button?>"></div>
                </div>
            </form>
        </div><?php
        if(mysqli_num_rows($tennivalok) > 0)
        {
            foreach($tennivalok as $tennivalo)
            {
                ?><div id="tennivalo-<?=$tennivalo['id']?>">
                    <div id="prioritas-<?=$tennivalo['id']?>" style="border-color: <?php if(!$tennivalo['befejezve']) { switch($tennivalo['prioritasid']) { case 5: echo 'red'; break; case 4: echo 'orange'; break; case 3: echo 'yellow'; break; case 2: echo 'blue'; break; case 1: echo 'grey'; } } else { echo 'green'; } ?> "></div>

                    <div class="tennivalotitle">
                        <div class="tennivalocbdiv"><?php
                            if($mindir)
                            {
                                ?><form id="tennivalo" action="<?=$RootPath?>/tennivalodb?action=update" method="POST">
                                    <input type ="hidden" id="id" name="id" value=<?=$tennivalo['id']?>>
                                    <label class="tennivalokcb">
                                        <input type="checkbox" name="befejezve" id="befejezve" value="1" onChange='submit()' <?=($tennivalo['befejezve']) ? "checked" : "" ?>>
                                        <span class="tenivalokjelolo"></span>
                                    </label></form><?php
                            }
                        ?></div>
                        <?=$tennivalo['leiras']?>
                    </div>

                    <div class="openclosewrapper">
                        <div id="tennivaloopenclose-<?=$tennivalo['id']?>" onclick="openToDo('<?=$tennivalo['id']?>')"></div>
                    </div>

                    <div id="tennivalobody-<?=$tennivalo['id']?>">
                        <div id="tennivalodivide-<?=$tennivalo['id']?>"></div>
                        <div><?=$tennivalo['bovitett']?></div>
                        <div class="tennivalofelhasznalo"><?=$tennivalo['felhasznalo']?></div>
                        <div class="tennivaloido"><?=$tennivalo['timestamp']?></div>
                    </div>
                    
                </div><?php
            }
        }
    ?></div><?php
}