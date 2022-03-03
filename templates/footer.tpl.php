<div class="footerinside">
    <div>Információ</div>
    <div>Impresszum</div>
    <div>Felhasználói menü</div>
    <div class="footerdiv2"><?=$_SESSION[getenv('SESSION_NAME').'lablecinfo']?></div>
    <div class="footerdiv2"><p><strong>Design, backend:</strong></p><p><a href="mailto:kiraly.bela@mil.hu">Király Béla ftőrm</a></p><p><small>02-43/2488</small></p></div>
    <div class="footerdiv2">
    <?php
    if (isset($_SESSION[getenv('SESSION_NAME').'jogosultsag']) && $_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 0)
    {
        $usernev = $_SESSION[getenv('SESSION_NAME').'nev'];
        ?>
            
                <p><a href="<?=$RootPath?>/felhasznalo"><?=$usernev?></a></p><br>
                <?php if($_SESSION[getenv('SESSION_NAME').'jogosultsag'] > 1)
                {
                    ?><p><a href="<?=$RootPath?>/beallitasok">Beállítások</a></p><br><?php
                }
                ?><p><a href="<?=$RootPath?>/kilep">Kilépés</a></p>
        <?php
    }
    else
    {
        ?>
            <p><a href="<?=$RootPath?>/belepes">Belépés</a></p>
        <?php
    }
    ?></div>
</div>
