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
	$fomenu = null;
	?><div class="leftmenuareabase">
		<nav class="leftmenuarea">
			<ul class="leftmenu"><?php
			foreach($menuk[1] as $menupont)
			{
				if($fomenu && $fomenu != $menupont['szulo'])
				{
					?></ul><?php
					$fomenu = null;
				}
				
				if($menupont['url'] == "#")
				{
					$fomenu = $menupont['id'];
					?><li class="leftmenuitem"><a href="#" onclick="rejtMutat('<?=$menupont['id']?>')"><?=trim($menupont['menupont'])?></a>
						<ul class='leftmenu-sub' id="<?=$menupont['id']?>" style="display: none;"><?php
				}
				elseif($fomenu && $fomenu == $menupont['szulo'])
				{
					?><li <?=(($menupont['url'] == $pagetofind) ? 'class="leftmenusub-active"' : 'class="leftmenusubitem"')?>>
						<a href="<?= (($menupont['url'] == '/') ? $RootPath : $RootPath."/".$menupont['url']) ?>"><?=trim($menupont['menupont'])?></a>
					</li><?php
				}
				else
				{
					?><li <?=(($menupont['url'] == $pagetofind) ? 'class="leftmenuitem-active"' : 'class="leftmenuitem"')?>>
						<a href="<?= (($menupont['url'] == '/') ? $RootPath : $RootPath."/".$menupont['url']) ?>"><?=trim($menupont['menupont'])?></a>	
					</li><?php
				}
			}
			?></ul>
		</nav>
   </div><?php
}
?><script>
	function hideSubmenu(id)
	{
		document.getElementById(id).style.display = "block";
	}
</script>