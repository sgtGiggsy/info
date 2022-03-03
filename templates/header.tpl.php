<?php
$versenyzo = false;
$parbaj = false;
$header = "headerinside";
if(isset($_GET['page']) & isset($_GET['id']))
{
    $page = $_GET['page'];
    $id = $_GET['id'];
    if ($page == "versenyzo" || $page == "meccslista")
    {
        $versenyzo = true;
        $con = mySQLConnect("SELECT *
        FROM versenyzok
        WHERE id = $id");
        $data = mysqli_fetch_assoc($con);
        $csapat = $data["csapat"];
        if($csapat == 1)
        {
            $header = "header-bajnokok";
        }
        else
        {
            $header = "header-kihivok";
        }
    }
    elseif ($page == "csapatszerinti")
    {
        $con = mySQLConnect("SELECT id
        FROM csapatok
        WHERE id = $id");
        $data = mysqli_fetch_assoc($con);
        $csapat = $data["id"];
        if($csapat == 1)
        {
            $header = "header-bajnokcsapat";
        }
        else
        {
            $header = "header-kihivocsapat";
        }
    }
    elseif ($page == "parbaj")
    {
        $parbaj = true;
        $con = mySQLConnect("SELECT versenyzo1, versenyzo2
        FROM parbajfutamok
        WHERE parbaj = $id");
        $data = mysqli_fetch_assoc($con);
        $header = "header-parbaj";
    }
}

?>
<div class="<?=$header?>">
<?php
    if($versenyzo)
    {
        include('./templates/versenyzo.tpl.php');
    }
    elseif($parbaj)
    {
        include('./templates/parbaj.tpl.php');
    }
    else
    {
        if($admin && olvasatlanUzenofal())
        {
            echo "<h3><a href='./?page=uzenofal'>Új üzenőfal üzenet</a></h3>";
        }
    }
?>
</div>