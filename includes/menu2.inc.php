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

if($menuterulet == 2)
{
	?><div class="leftmenuareabase">
		<nav class="leftmenuarea">
			<ul class="leftmenu"><?php
			foreach($menuk[1] as $menupont)
			{
				?><li <?=(($menupont['url'] == $pagetofind) ? 'class="leftmenuitem-active"' : 'class="leftmenuitem"')?>>
					<a href="<?= (($menupont['url'] == '/') ? $RootPath : $RootPath."/".$menupont['url']) ?>"><?=trim($menupont['menupont'])?></a>	
				</li><?php
			}
			?></ul>
		</nav>
   </div><?php
}