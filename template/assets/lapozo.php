<div class="leftright">
    <div><?php
        if($this->lapozas['elozo'])
        {
            ?><a class="anchbutton" href="<?=ROOT_PATH?>/<?=$this->selectedpage?>/oldal/<?=$this->lapozas['elozo']?>">Előző oldal</a><?php
        }
    ?></div>
    <div class="center"><?php
        if($this->lapozas['osszlapszam'] > 1)
        {
            $utolsoharom = $this->lapozas['osszlapszam'] - 3;
            $utolso = null; // Azt az egyébként lényegtelen problémát oldja meg, mikor az utolsó oldalon a "..." jelzés duplán jelenik meg.
            for($oldal = 1; $oldal <= $this->lapozas['osszlapszam']; $oldal++)
            {
                if($oldal < 4
                        || $oldal > $utolsoharom
                        || ($oldal >= $this->lapozas['elozo']
                                && $oldal <= $this->lapozas['kovetkezo']))
                {
                    ?><a class="anchbutton<?=($oldal == $this->lapozas['aktualis'] || ($oldal == 1 && !$this->lapozas['aktualis'])) ? '_active' : ''?>" href="<?=ROOT_PATH?>/<?=$this->selectedpage?>/oldal/<?=$oldal?>"><?=$oldal?></a><?php
                    $utolso = $oldal;
                }
                else if(is_int($utolso)
                        && (($oldal == 4 && $this->lapozas['elozo'] > 4)
                        || ($oldal == $utolsoharom
                                && $this->lapozas['kovetkezo'] < $utolsoharom)))
                {
                    ?><span class="anchbutton">...</span><?php
                    $utolso = null;
                }
            }
        }
    ?></div>
    <div><?php
        if($this->lapozas['kovetkezo'])
        {
            ?><a class="anchbutton" href="<?=ROOT_PATH?>/<?=$this->selectedpage?>/oldal/<?=$this->lapozas['kovetkezo']?>">Következő oldal</a><?php
        }
    ?></div>
</div>