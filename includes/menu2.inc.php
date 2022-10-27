<?php
$kinyit = false;
if($menuterulet == 1)
{
	$fomenu = null;
	$keresszulo = null;
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
				
				if($menupont['oldal'] == "#")
				{
					$fomenu = $menupont['id'];
					?><li class="leftmenuitem"><a style="cursor: pointer" onclick="rejtMutat('<?=$menupont['id']?>')"><?=trim($menupont['menupont'])?><?php
					if($menupont['szerkoldal']) { ?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew">+</span><?php }
				?></a>
						<ul class='leftmenu-sub' id="<?=$menupont['id']?>" style="display: none;">
						<div class='leftmenu-subtop'></div><?php
				}
				elseif($fomenu && $fomenu == $menupont['szulo'])
				{
					?><li <?=(($menupont['oldal'] == $pagetofind || $menupont['gyujtooldal'] == $pagetofind || $menupont['szerkoldal'] == $pagetofind || $menupont['id'] == $keresszulo) ? 'class="leftmenusub-active"' : 'class="leftmenusubitem"')?>>
						<a href="<?= (($menupont['oldal'] == '/') ? $RootPath : $RootPath."/".$menupont['gyujtooldal']) ?>"><?=trim($menupont['menupont'])?><?php
							if($menupont['szerkoldal']) { ?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew">+</span><?php }
						?></a>
					</li><?php
				}
				elseif($menupont['aktiv'] > 0)
				{
					?><li <?=(($menupont['oldal'] == $pagetofind || $menupont['gyujtooldal'] == $pagetofind) ? 'class="leftmenuitem-active"' : 'class="leftmenuitem"')?>>
						<a href="<?= (($menupont['oldal'] == '/') ? $RootPath : $RootPath."/".$menupont['gyujtooldal']) ?>"><?=trim($menupont['menupont'])?><?php
							if($menupont['szerkoldal']) { ?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew">+</span><?php }
						?></a>
					</li><?php
				}

				if(($menupont['gyujtooldal'] == $pagetofind || $menupont['oldal'] == $pagetofind || $menupont['szerkoldal'] == $pagetofind) && ($menupont['aktiv'] > 0 || $menupont['id'] == $keresszulo))
				{
					$kinyit = $menupont['szulo'];
				}
				elseif($menupont['oldal'] == $pagetofind)
				{
					$keresszulo = $menupont['szulo'];
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

if($kinyit)
{
	?><script>
		window.onload = function()
		{
			document.getElementById("<?=$kinyit?>").style.display = "block";
		}
	</script><?php
}