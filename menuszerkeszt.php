<?php

if(!@$mindir)
{
	echo "Nincs jogosultsága az oldal megtekintésére!";
}
else
{
    if(count($_POST) > 0 && !isset($_POST['telephely']))
    {
        $irhat = true;
        include("./db/menudb.php");
    }

    $szulo = array();
    foreach($menu as $x)
    {
        $szulo[] = array("id" => $x['id'], "menupont" => $x['menupont']);
    }
    $sortcriteria = 'menupont';
    usort($szulo, function($a, $b)
    {
		return $a['menupont'] > $b['menupont'];
    });
    ?><div class="oldalcim"><p onclick="rejtMutat('menuk')" style="cursor: pointer">Menük</p></div>
    <div id="menuk" style='display: none'>
    <table id="menupontok">
        <thead>
            <tr>
                <th class="tsorth" onclick="sortTable(0, 's', 'menupontok')">ID</th>
                <th class="tsorth" onclick="sortTable(1, 's', 'menupontok')">Menüpont</th>
                <th class="tsorth" onclick="sortTable(2, 's', 'menupontok')">Szülő</th>
                <th class="tsorth" onclick="sortTable(3, 's', 'menupontok')">URL</th>
                <th class="tsorth" onclick="sortTable(4, 's', 'menupontok')">Oldal</th>
                <th class="tsorth" onclick="sortTable(5, 's', 'menupontok')">Címszöveg</th>
                <th class="tsorth" onclick="sortTable(6, 's', 'menupontok')">Szerkesztő</th>
                <th class="tsorth" onclick="sortTable(7, 's', 'menupontok')">Megjelenik</th>
                <th class="tsorth" onclick="sortTable(8, 's', 'menupontok')">Terület</th>
                <th class="tsorth" onclick="sortTable(9, 's', 'menupontok')">Sorrend</th>
            </tr>
        </thead>
        <tbody><?php
            foreach($menu as $menup)
            {
                ?><tr>
                    <form action="<?=$RootPath?>/menuszerkeszt&action=update" method="post">
                        <td><input type ="hidden" id="id" name="id" value=<?=$menup['id']?>><h3><?=$menup['id']?></h3></td>
                        <td><input style="width: 20ch;" type="text" name="menupont" value="<?=$menup['menupont']?>"></td>
                        <td><select name="szulo">
                                <option value="">Nincs</option><?php
                                foreach($szulo as $x)
                                {
                                    ?><option value="<?=$x['id']?>" <?=($x['id'] == $menup['szulo']) ? "selected" : "" ?>><?=$x['menupont']?></option><?php
                                }
                            ?></select>
                        </td>
                        <td><input style="width: 20ch;" type="text" name="url" value="<?=$menup['url']?>"></td>
                        <td><input style="width: 20ch;" type="text" name="oldal" value="<?=$menup['oldal']?>"></td>
                        <td><input type="text" name="cimszoveg" value="<?=$menup['cimszoveg']?>"></td>
                        <td><input style="width: 20ch;" type="text" name="szerkoldal" value="<?=$menup['szerkoldal']?>"></td>
                        <td><select name="aktiv">
                                <option value="" <?=(!$menup['aktiv']) ? "selected" : "" ?>>Senkinek</option>
                                <option value="1" <?=($menup['aktiv'] == "1") ? "selected" : "" ?>>Jogosultaknak</option>
                                <option value="2" <?=($menup['aktiv'] == "2") ? "selected" : "" ?>>Bejelentkezetteknek</option>
                                <option value="3" <?=($menup['aktiv'] == "3") ? "selected" : "" ?>>Mindenkinek</option>
                                <option value="4" <?=($menup['aktiv'] == "4") ? "selected" : "" ?>>Csak látogatóknak</option>
                            </select>
                        </td>
                        <td><input style="width: 6ch;" type="text" name="menuterulet" value="<?=$menup['menuterulet']?>"></td>
                        <td><input style="width: 6ch;" type="text" name="sorrend" value="<?=$menup['sorrend']?>"></td>
                        <td><input type="submit" value="Módosítás"></td>
                    </form>
                </tr><?php
            }
            ?><tr>
                <form action="<?=$RootPath?>/menuszerkeszt&action=new" method="post">
                    <td><h3>Új menü</h3></td>
                    <td><input style="width: 20ch;" type="text" name="menupont"></td>
                    <td><select name="szulo">
                            <option value="">Nincs</option><?php
                            foreach($szulo as $x)
                            {
                                ?><option value="<?=$x['id']?>"><?=$x['menupont']?></option><?php
                            }
                        ?></select>
                    </td>
                    <td><input style="width: 20ch;" type="text" name="url"></td>
                    <td><input style="width: 20ch;" type="text" name="oldal"></td>
                    <td><input type="text" name="cimszoveg"></td>
                    <td><input style="width: 20ch;" type="text" name="szerkoldal"></td>
                    <td><select name="aktiv">
                            <option value="">Senkinek</option>
                            <option value="1">Jogosultaknak</option>
                            <option value="2">Bejelentkezetteknek</option>
                            <option value="3">Mindenkinek</option>
                            <option value="4">Csak látogatóknak</option>
                        </select>
                    </td>
                    <td><input style="width: 6ch;" type="text" name="menuterulet"></td>
                    <td><input style="width: 6ch;" type="text" name="sorrend"></td>
                    <td><input type="submit" value="Új"></td>
                </form>
            </tr>
        </tbody>
    </table>
    </div>
    <script>
    window.onload = function()
	{
        //sortTable(1, 's', "menupontok");
    }
    </script>
    <?php
}