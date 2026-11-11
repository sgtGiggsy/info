
<?php

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}
?><div class="oldalcim">Közlemények</div><?php
if($this->kozlemeny)
{
	?><div class="ketharmad"><?php
		$fo = 2;
		$darab = count($this->kozlemeny);
		$i = 1;
		foreach ($this->kozlemeny as $elem)
		{
			if($i < $fo + 1)
			{
				if($i == 1)
				{
					?><div class='kozlemeny'><?php
				}
					?><div class='kozlemenyfej'>
						<h2><a href='<?=ROOT_PATH?>/kozlemeny/<?=$elem['slug']?>'><?=$elem['cim']?></a></h2>
					</div>
					<div class='szovegtartalom'>
						<small><b>Szerző: <?=$elem['nev']?></b><br><i><?=$elem['ido']?></i></small><br><br>
						<?=$elem['bevezetes']?>
						<?=$elem['szovegtorzs']?>
					</div><?php
				if ($i != $fo)
				{
					?><div class='elvalaszto'></div><?php
				}
				if($i == $fo)
				{
					?></div><?php
				}
			}
			else
			{
				if($i == $fo + 1)
				{
					?><div class="kozlemenyside"><?php
				}
					?><div class='kozlemeny'>
						<div class='kozlemenyfej'>
							<h2><a href='<?=ROOT_PATH?>/kozlemeny/<?=$elem['slug']?>'><?=$elem['cim']?></a></h2>
						</div>
						<div class='szovegtartalom'>
							<?=$elem['bevezetes']?>
							<?php
							if ($elem['szovegtorzs'])
							{
								?><a class="left" href='<?=ROOT_PATH?>/kozlemeny/<?=$elem['slug']?>'>Tovább...</a><?php
							}
						?></div>
					</div><?php
				if ($i != $darab)
				{
					?><div class='elvalaszto'></div><?php
				}
				if($i == $darab)
				{
					?></div><?php	
				}
			}
			
			$i++;
		}
	?></div><?php
}