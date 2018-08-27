<?php
$langague = 'none';
$form = '';
$w = $id;

for ($r=0;$r < count($expr);$r++)
    {
        $line = $expr[$r];
        $class = $line['c_class'];
        //echo '<br>'.$class.'='.$line['n_name'];
                
        
        switch($class)
            {
            case 'hasLanguageExpression':
                $language = trim($line['n_name']);
                break;
            case 'hasFormExpression':
                $form = trim($line['n_name']);
                break;
            }
    }
?>
<!---------------- EXPRESSION --------------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Expression');?></tt>
        </div>
        <div class="col-md-10">
            <a href="<?php echo base_url(PATH.'v/'.$w);?>">
            <span style="font-size: 100%; color: #000000;"><b><?php echo $form; ?></b></span>
            <br>
            <span style="font-size: 100%; color: #000000;"><i><?php echo $language; ?></i></span>
            </a>            
            <?php if (perfil("#ADM")) {
                   echo '<br><a href="'.base_url(PATH.'a/' . $id).'"  class="btn btn-secondary">editar</a>';
            }
            ?>            
            <br>
        </div>
    </div>
</div>
<br>