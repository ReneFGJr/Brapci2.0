<?php
if (!isset($content)) { $content = ''; }
if (!isset($title)) { $title = ''; }
if ($title=='Brapci 2.0') { $title = ''; }
$title = troca($title,':: Brapci 2.0','');
if (isset($fluid)) { $fluid = '-fluid'; } else { $fluid = ''; }
?>
<div class="container<?php echo $fluid;?>">
    <div class="row">
        <?php if (strlen($title) > 0) { echo '<div class="col-md-12"><h2>'.$title.'</h2></div>'; } ?>
        <?php echo $content; ?>
    </div>
</div>
