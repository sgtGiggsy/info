<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

?><div class="normalcontent">
    <h1><?=$this->formvars['pagetitle']?></h1>
    <form action="<?=$this->formvars['actiontarget']?>" method="post">
        <?= $this->form->Render() ?>
        <div class="submit"><input type="submit" value="<?=$this->formvars['buttontext']?>"></div>
    </form>
</div>