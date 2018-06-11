<?php
if (!isset($content)) { $content = ''; }
if (!isset($title)) { $title = ''; }
if (isset($fluid)) { $fluid = '-fluid'; } else { $fluid = ''; }
?>
<div class="container<?php echo $fluid;?>">
    <div class="row">
        <?php echo $content; ?>
    </div>
</div>
