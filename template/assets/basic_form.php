<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

?><div class="normalcontent">
    <h1><?=$this->sitedesc?></h1>
        <?= $this->form->Render() ?>
    </form>
</div>