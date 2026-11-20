<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

?><div class="normalcontent">
    <h1><?= $this->title ?></h1>
        <?= $this->form->Render() ?>
        <?= isset($this->form2) ? $this->form2->Render() : '' ?>
</div>