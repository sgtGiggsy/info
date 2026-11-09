<?php
use Infoinventory\Classes\Settings;

echo "\n";
?><script type="text/javascript" nonce="<?=Settings::$nonce?>">
	const policy = trustedTypes.createPolicy('default', {
		createHTML: (input) => input
	});
    const urlParams = new URLSearchParams(window.location.search);<?php echo "\n";
    foreach(Settings::$PHPvarsToJS as $key => $value)
    {
		if(is_array($value))
		{
			$cval = count($value);
            echo "const $key = [";
            for($i = 0; $i < $cval; $i++) {
                echo "'" . $value[$i] . "'";
                if($i < $cval - 1) {
                    echo ",\n";
                }
                else
                    echo "\n";
            }
            echo "]";
		}	
		else
		{
            echo "\tconst $key = '$value';\n";
		}

    }
?></script><?php echo "\n";
foreach(Settings::$jsfiles as $js)
{
	echo "<script src='$RootPath/$js'></script>\n";
}

if(isset($swaltoaster))
{
	?><script nonce="<?=Settings::$nonce?>">
		Swal.fire({
			title: "<?= $swaltoaster['title'] ?>",
			text: "<?= $swaltoaster['text'] ?>",
			icon: "<?= $swaltoaster['icon'] ?>",
		});
	</script><?php
}

if(defined('SWALMIXIN'))
{
	?><script nonce="<?=Settings::$nonce?>">
		toaster.fire({
			icon: "<?= SWALMIXIN['icon'] ?>",
			title: "<?= SWALMIXIN['title'] ?>"
		});
	</script><?php
}

if(Settings::$admin && false/*&& olvasatlanUzenofal()*/)
{
	?><script nonce="<?=Settings::$nonce?>">
		let uzenet = Swal.mixin({
			toast: true,
			position: "top-end",
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			didOpen: (toast) => {
				toast.onmouseenter = Swal.stopTimer;
				toast.onmouseleave = Swal.resumeTimer;
			}
		})
		uzenet.fire({
			icon: "<?= "warning" ?>",
			title: "<?= "Új üzenőfal üzenet" ?>",
			html: `<a href="./uzenofal">Kattints ide az olvasáshoz!</a>`
		});
	</script><?php
}