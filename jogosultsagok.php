<form action="<?=$RootPath?>/felhasznaloszerkeszt&action=update" method="post">
    <table>
        <thead>
            <tr>
                <th></th>
                <th style="width: 10%">Saját olvas</th>
                <th style="width: 10%">Csoport olvas</th>
                <th style="width: 10%">Mind olvas</th>
                <th style="width: 10%">Saját ír</th>
                <th style="width: 10%">Csoport ír</th>
                <th style="width: 10%">Mind ír</th>
            </tr>
        </thead>
        <tbody>
    <input type ="hidden" id="id" name="id" value=<?=$id?> /><?php
    $menu = mySQLConnect("SELECT * FROM menupontok ORDER BY menupont ASC");
    $jogosultsagok = mySQLConnect("SELECT * FROM jogosultsagok WHERE felhasznalo = $id");
    //uasort($menu, function ($a, $b) { return $a->menupont > $b->menupont; });
    foreach($menu as $x)
    {
        $jogosultsag = array("sajatolvas" => null, "csoportolvas" => null, "mindolvas" => null, "sajatir" => null, "csoportir" => null, "mindir" => null);
        foreach($jogosultsagok as $y)
        {
            if($y['menupont'] == $x['id'])
            {
                $jogosultsag = $y;
                break;
            }
        }
        ?><tr id="<?='szerk' . $x['id']?>">
            <td>
                <label for="<?=$x['oldal']?>"><?=$x['menupont']?></label>
            </td>
            <td>
                <input type="checkbox" value= "1" name="sajatolvas-<?=$x['id']?>" <?=($jogosultsag['sajatolvas']) ? 'checked' : '' ?>>
            </td>
            <td>
                <input type="checkbox" value= "1" name="csoportolvas-<?=$x['id']?>" <?=($jogosultsag['csoportolvas']) ? 'checked' : '' ?>>
            </td>
            <td>
                <input type="checkbox" value= "1" name="mindolvas-<?=$x['id']?>" <?=($jogosultsag['mindolvas']) ? 'checked' : '' ?>>
            </td>
            <td>
                <input type="checkbox" value= "1" name="sajatir-<?=$x['id']?>" <?=($jogosultsag['sajatir']) ? 'checked' : '' ?>>
            </td>
            <td>
                <input type="checkbox" value= "1" name="csoportir-<?=$x['id']?>" <?=($jogosultsag['csoportir']) ? 'checked' : '' ?>>
            </td>
            <td>
                <input type="checkbox" value= "1" name="mindir-<?=$x['id']?>" <?=($jogosultsag['mindir']) ? 'checked' : '' ?>>
            </td>
            <td>
                <button style="margin-left: 10px; cursor: pointer;" onclick="checkAll('<?='szerk' . $x['id']?>'); return false;">Mind</button>
            </td>
            <?php
    }
    ?></tbody></table>
    <div class="submit"><input type="submit" value=<?=$button?>></div>
</form>
<script>
    function checkAll(sor)
    {
        var tr = document.getElementById(sor);
        var ele = tr.querySelectorAll('input');
        for(var i = 0; i < ele.length; i++){
            if(ele[i].type == 'checkbox')
            {
                if(ele[i].checked == false)
                {
                    ele[i].checked = true;
                }
                else
                {
                    ele[i].checked = false;
                }
            }
        }
    }
</script>