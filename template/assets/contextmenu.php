<div class='contextmenu'><?php
    foreach($this->contextmenu as $menuelem) {
        ?><a class='anchbutton' href="<?=$menuelem['link']?>"><?=$menuelem['felirat']?></a><?php
    }
?></div>