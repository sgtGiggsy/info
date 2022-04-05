<?php

if($menuterulet == 1)
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
					?><li class="leftmenuitem"><a style="cursor: pointer" onclick="rejtMutat('<?=$menupont['id']?>')"><?=trim($menupont['menupont'])?><?php
					if($menupont['szerkoldal']) { ?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew">+</span><?php }
				?></a>
						<ul class='leftmenu-sub' id="<?=$menupont['id']?>" style="display: none;"><?php
				}
				elseif($fomenu && $fomenu == $menupont['szulo'])
				{
					?><li <?=(($menupont['url'] == $pagetofind) ? 'class="leftmenusub-active"' : 'class="leftmenusubitem"')?>>
						<a href="<?= (($menupont['url'] == '/') ? $RootPath : $RootPath."/".$menupont['url']) ?>"><?=trim($menupont['menupont'])?><?php
							if($menupont['szerkoldal']) { ?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew">+</span><?php }
						?></a>
					</li><?php
					if($menupont['url'] == $pagetofind)
					{
						?><script>
							window.onload = function()
							{
								document.getElementById("<?=$menupont['szulo']?>").style.display = "grid";
							}
						</script><?php
					}
				}
				else
				{
					?><li <?=(($menupont['url'] == $pagetofind) ? 'class="leftmenuitem-active"' : 'class="leftmenuitem"')?>>
						<a href="<?= (($menupont['url'] == '/') ? $RootPath : $RootPath."/".$menupont['url']) ?>"><?=trim($menupont['menupont'])?><?php
							if($menupont['szerkoldal']) { ?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew">+</span><?php }
						?></a>
					</li><?php
				}
			}
			?></ul>
		</nav>
   </div><?php
}

if($menuterulet == 2)
{
	foreach($menuk[2] as $menupont)
	{
		?><a href="<?=$RootPath?>/<?=$menupont['url']?>"><img src="<?=$RootPath?>/images/<?=$menupont['url']?>.png" title="<?=$menupont['menupont']?>" alt="<?=$menupont['menupont']?>"></a><?php
	}
}
?><script>
</script>