<?php
if(isset($_GET['id']))
{
    $kozlemenyget = mySQLConnect("SELECT * FROM kozlemenyek WHERE id = $id");
    $kozlemeny = mysqli_fetch_assoc($kozlemenyget);
    $szerzo = $kozlemeny['szerzo'];
    $cimkelista = getCimkek($kozlemeny['cimke']);
}
if (!$csoportir && !(isset($szerzo) && $szerzo == $felhasznaloid))
{
    echo "Nincs jogosultságod az oldal megtekintésére!";
}
else
{
    if(count($_POST))
    {
        $irhat = true;
        include("./db/kozlemenydb.php");
    }

    ?><script type="text/javascript">
        tinymce.init({
            selector: '#bevezetes',
            plugins : 'advlist autolink link image lists charmap print preview emoticons code'
        });

        tinymce.init({
            selector: '#szovegtorzs',
            plugins : 'advlist autolink link image lists charmap print preview emoticons code'
        });
    </script>

    <div><?php
        $hirek = mySQLConnect("SELECT id, cim FROM kozlemenyek ORDER BY id DESC"); ?>
        <div class="oldalcim"><p onclick="rejtMutat('letezo')" style="cursor: pointer">Közlemények Listája</p></div>
        <div id="letezo" style='display: none'>
            <table>
                <thead>
                    <tr>
                        <th>Sorszám</th>
                        <th>Közlemény címe</th>
                        <th>Szerkeszt</th>
                    </tr>
                </thead>
                <tbody><?php
                foreach($hirek as $x)
                {
                    ?><tr class='kattinthatotr' data-href='<?=$RootPath?>/kozlemenyszerkeszt&id=<?=$x['id']?>'>
                        <td><?=$x['id']?></td>
                        <td><?=$x['cim']?></td>
                        <td><img src='<?=$RootPath?>/images/edit.png' /></td>
                    </tr><?php
                }
                ?></tbody>
            </table>
        </div><?php

        $button = "Ment";
        $cim = $bevezetes = $szovegtorzs = $link = $publikalt = null;
        $cimkek = mySQLConnect("SELECT id, nev FROM kozlemenykategoriak");
 
        if(isset($_GET['id']))
        {
            $cim = $kozlemeny['cim'];
            $bevezetes = $kozlemeny['bevezetes'];
            $szovegtorzs = $kozlemeny['szovegtorzs'];
            $link = $kozlemeny['link'];
            $publikalt = $kozlemeny['publikalt'];
            $button = "Szerkeszt";
            ?><form action="<?=$RootPath?>/kozlemenyszerkeszt&action=update" method="post">
            <div class="oldalcim">Közlemény szerkesztése</div>
            <input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
        }
        else
        {
            ?><form action="<?=$RootPath?>/kozlemenyszerkeszt&action=new" method="post">
            <div class="oldalcim">Közlemény létrehozása</div><?php
        }

        ?><div class="ketharmad">
            <div>
                <div class="szovegdoboz">
                    <label for="bevezetes">Bevezetés:</label>
                    <textarea id="bevezetes" name="bevezetes"><?=$bevezetes?></textarea><br>
                </div>

                <div class="szovegdoboz">
                    <label for="szovegtorzs">Cikk törzs:</label>
                    <textarea id="szovegtorzs" name="szovegtorzs"><?=$szovegtorzs?></textarea><br>
                </div>
            </div>

            <div>
                <div>
                    <label for="cim">Cím:</label><br>
                    <input type="text" accept-charset="utf-8" name="cim" id="cim" value="<?=$cim?>"></input><br>
                </div><?php

                if(isset($_GET['id']))
                {
                    ?><div>
                        <label for="link">Link:</label><br>
                        <input type="text" accept-charset="utf-8" name="link" id="link" value="<?=$link?>"></input><br>
                    </div><?php
                }

                ?><div>
                    <label for="Cimkek">Cimkék:</label><br><?php
                    foreach ($cimkek as $x)
                    {
                        ?><label style="font-weight: normal"><input type="checkbox" name="cimkek[]" value="<?=$x["id"]?>"
                        <?php
                        if(isset($cimkelista))
                        {
                            foreach($cimkelista as $cimke)
                            {
                                if($x['id'] == $cimke)
                                {
                                    echo "checked"; break;
                                }
                            }
                        }
                        ?>/><?=$x["nev"]?></label><br><?php
                    }
                ?></div>

                <div>
                    <label for="publikalt">Publikált:</label><br>
                    <label class="kapcsolo">
                        <input type="hidden" name="publikalt" id="publikalthidden" value="">
                        <input type="checkbox" name="publikalt" id="publikalt" value="1" <?=($publikalt) ? "checked" : "" ?>>
                        <span class="slider"></span>
                    </label>
                </div><br>

                <div><input type="submit" value=<?=$button?>></div><?php
                cancelForm();
            ?></div>    
        </div>
        </form>
    </div><?php
}