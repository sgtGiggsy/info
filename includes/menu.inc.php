<?php

function MainMenu()
{
	$RootPath = $GLOBALS['RootPath'];
	$pagetofind = $GLOBALS['pagetofind'];
	$menuterulet = $GLOBALS['menuterulet'][1];
	$szulonyit = $GLOBALS['szulonyit'];
	$felhasznaloid = $GLOBALS['felhasznaloid'];
	$icons = $GLOBALS['icons'];

	?><div class="leftmenuarea">
		<nav>
			<ul class="leftmenu"><?php
				$fomenu = null;
				foreach($menuterulet as $menupont)
				{
					$addnewjog = false;
					if(isset($felhasznaloid) && $menupont['iras'] > 1)
					{
						$addnewjog = true;
					}
					
					if($fomenu && $fomenu != $menupont['szulo'])
					{
						?></ul><?php
						$fomenu = null;
					}
					
					if($menupont['oldal'] == "#")
					{
						$fomenu = $menupont['id'];
						?><li class="leftmenuitem">
							<p onclick="subRejtMutat('<?=$menupont['id']?>')">
								<?=trim($menupont['menupont'])?><?php
								if($menupont['szerkoldal'] && $addnewjog)
								{
									?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew"><?=$icons['add']?></span><?php
								}
							?></p>
						</li>
						<ul class='leftmenu-sub' id="<?=$menupont['id']?>" style="display: <?=($szulonyit == $menupont['id']) ? '' : 'none' ?>"><?php
					}
					elseif($fomenu && $fomenu == $menupont['szulo'] && $menupont['aktiv'] > 0)
					{
						?><li <?=(($menupont['oldal'] == $pagetofind || $menupont['gyujtooldal'] == $pagetofind || $menupont['szerkoldal'] == $pagetofind) ? 'class="leftmenusub-active"' : 'class="leftmenusubitem"')?>>
							<a href="<?= (($menupont['oldal'] == '/') ? $RootPath : $RootPath."/".$menupont['gyujtooldal']) ?>">
								<?=trim($menupont['menupont'])?><?php
								if($menupont['szerkoldal'] && $addnewjog)
								{
									?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew"><?=$icons['add']?></span><?php
								}
							?></a>
						</li><?php
					}
					elseif($menupont['aktiv'] > 0)
					{
						?><li <?=(($menupont['oldal'] == $pagetofind || $menupont['gyujtooldal'] == $pagetofind) ? 'class="leftmenuitem-active"' : 'class="leftmenuitem"')?>>
							<a href="<?= (($menupont['oldal'] == '/') ? $RootPath : $RootPath."/".$menupont['gyujtooldal']) ?>">
								<?=trim($menupont['menupont'])?><?php
								if($menupont['szerkoldal'] && $addnewjog)
								{
									?><span onclick="window.open('<?=$RootPath?>/<?=$menupont['szerkoldal']?>', '_self'); return false;" class="addnew"><?=$icons['add']?></span><?php
								}
							?></a>
						</li><?php
					}
				}
				?></ul>
		</nav>
	</div><?php
}

function TopMenu()
{
	$RootPath = $GLOBALS['RootPath'];
	$menuterulet = $GLOBALS['menuterulet'][2];
	$icons = $GLOBALS['icons'];
	foreach($menuterulet as $menupont)
	{
		?><a href="<?=$RootPath?>/<?=$menupont['gyujtooldal']?>" aria-label="<?=$menupont['menupont']?>" title="<?=$menupont['menupont']?>"><?=$icons[$menupont['oldal']]?></a><?php
	}
}

function ContextMenu()
{
	$RootPath = $GLOBALS['RootPath'];
	$pagetofind = $GLOBALS['pagetofind'];
	@$menuterulet = $GLOBALS['menuterulet'][3];
	@$contextmenujogok = $GLOBALS['contextmenujogok'];
	@$GLOBALS['pagename'] ? $pagename = $GLOBALS['pagename'] . '/' : $pagename = null;
	@$aloldal = $GLOBALS['aloldal'];
	
	if($contextmenujogok)
	{
		?><nav class="topmenuarea">
			<ul class="topmenu"><?php
			foreach($menuterulet as $menupont)
			{
				if(@$contextmenujogok[$menupont['oldal']] || @$contextmenujogok[$menupont['gyujtooldal']])
				{
					?><li <?=($aloldal && ($menupont['oldal'] == $aloldal || $menupont['gyujtooldal'] == $aloldal)) ? 'class="topmenuitem-active"' : 'class="topmenuitem"' ?>>
						<a href="<?=$RootPath?>/<?=$pagetofind?>/<?=$pagename?><?=$menupont['gyujtooldal']?>">
							<?=trim($menupont['menupont'])?>
						</a>
					</li><?php
				}
			}
			?></ul>
		</nav><?php
	}
}