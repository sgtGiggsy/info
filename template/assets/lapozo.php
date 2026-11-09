<div class="leftright">
    <div><?php
        if($this->lapozas['elozo'])
        {
            ?><a class="anchbutton" href="<?=ROOT_PATH?>/<?=$this->selectedpage?>/oldal/<?=$this->lapozas['elozo']?>">Előző oldal</a><?php
        }
    ?></div>
    <div><?php
        if($this->lapozas['kovetkezo'])
        {
            ?><a class="anchbutton" href="<?=ROOT_PATH?>/<?=$this->selectedpage?>/oldal/<?=$this->lapozas['kovetkezo']?>">Következő oldal</a><?php
        }
    ?></div>
</div>