
<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="normalcontent">
    <h1><?=$this->kozlemeny['cim']?></h1><?php
    if($this->kozlemeny)
    {
        ?><div class="kozlemenyek">
            <div class='szovegtartalom'>
                <div class='kozlemenyszoveg'>
                    <small><b>Szerző: <?=$this->kozlemeny['nev']?></b><br><i><?=$this->kozlemeny['ido']?></i></small><br><br>
                    <?=$this->kozlemeny['bevezetes']?>
                    <br><?=$this->kozlemeny['szovegtorzs']?>
                </div>
            </div>
        </div><?php
    }
?></div>