<?php

$rlt = $item;
$w = $id;

if (count($rlt) == 0) {
	return ('');
}
$local = '';
$nr = '';
$biblioteca = '';

for ($r = 0; $r < count($rlt); $r++) {
	$line = $rlt[$r];
	$status = '<span style="color: green"><b>Disponível</b></span>';

	$class = $line['c_class'];
	//echo '<br>' . $class . '=' . $line['n_name'];
	switch($class) {
		case 'hasRegisterId' :
			$link = '<a href="' . base_url(PATH.'a/' . $line['d_r1']) . '">';
			$linka = '</a>';
			$nr .= $link . trim($line['n_name']) . $linka;
			break;
		case 'hasLocatedIn' :
			$link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
			$local = $link . trim($line['n_name']) . '</a>';
			break;
		case 'isOwnedBy' :
			$link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
			$biblioteca = $link . trim($line['n_name']) . '</a>';
			break;
		case 'isItemSituation' :
			$status .= trim($line['n_name']);
			break;
	}
}
/* regras */
?>
<!---------------- ITEM ------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Item'); ?></tt>
        </div> 
		<div class="col-md-3">
			<?php
			echo $biblioteca;
			?>
		</div>
		<div class="col-md-3">
			<?php
			echo $local;
            ?>
        </div>
		               
		<div class="col-md-2">
			<?php
			echo $status;
			?>
		</div>
		
        <div class="col-md-2">
            <?php
			echo $nr;
			?>
		</div>    </div>
</div>
<br>    