
<script src="<?=$RootPath?>/includes/jquery.min.js"></script>
<script type="text/javascript">
    const urlParams = new URLSearchParams(window.location.search);
    <?php foreach($PHPvarsToJS as $key => $value)
    {
		if(is_array($value))
		{
			?>const <?=$key?> = [ <?php
			foreach($value as $val)
			{
				echo '"' . $val . '", ';
			}
			?> ] <?php
		}	
		else
		{
			?>const <?=$key?> = '<?=$value?>'; <?php
		}

    }
?></script><?php
foreach($javascriptfiles as $js)
{
	?><script src="<?=$RootPath?>/<?=$js?>"></script><?php
}
?><script type="text/javascript"><?php
	if(isset($ujoldalcim))
	{
		?>document.title = '<?=$ujoldalcim?>'<?php
	}

	if(@$succesmessage)
	{
		?>showToaster("<?=$succesmessage?>");<?php
	}

	if(@$nyithelp)
	{
		?>rejtMutat('magyarazat');<?php
	}

	if(@$csoportfilter)
	{
		?>document.getElementById("<?=$csoportfilter?>").addEventListener("search", function(event) {
			filterCsoport('<?=$csoportfilter?>', '<?=$tipus?>');
		});<?php
	}
?></script>