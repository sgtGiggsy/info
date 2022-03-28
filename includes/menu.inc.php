<?php
if($menuterulet == 1)
{
	?><nav class="greedy">
		<ul class="links"><?php
		foreach($menuk[1] as $menupont)
		{
			?><li <?=(($menupont['url'] == $pagetofind) ? 'class="nav-active"' : '')?>>
				<a href="<?= (($menupont['url'] == '/') ? $RootPath : $RootPath."/".$menupont['url']) ?>"><?=trim($menupont['menupont'])?></a>	
			</li><?php
		}
		?></ul>
		<button aria-label="További oldalak"><img src="<?=$RootPath?>/images/hamburger.png" alt="További oldalak"></button>
		<ul class='hidden-links hidden'></ul>
	</nav><?php
}