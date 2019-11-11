<?php
require("journal_process.php");
?>
<!---------------- WORK --------------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Journal');?></tt>            
        </div>
        <div class="col-md-8">
            <font style="font-size: 200%"><?php echo $nome;?><?php echo $dates;?></font>
            <?php

            if (strlen($editor) > 0)
                {
                    echo '<br><tt>'.msg('Editor').': '.$editor.'</tt>';
                }                                

            if (strlen($issn) > 0)
                {
                    echo '<br><tt>ISSN: '.$issn.'</tt>';
                } 

            if (strlen($validity) > 0)
                {
                    echo '<br><tt>'.msg('validity').': '.$validity.'</tt>';
                }                 

            if (strlen($url) > 0)
                {
                    echo '<br><tt>Site (URL): '.$url.'</tt>';
                }                

            if (strlen($contact) > 0)
                {
                    echo '<br><tt>'.msg('Contact').': '.$contact.'</tt>';
                }               


            if (strlen($coll) > 0)
                {
                    echo '<br><tt>'.msg('collections').': '.$coll.'</tt>';
                }                             
            if (strlen($cc_origin) > 0)
                {
                    echo '<br><tt>'.$this->frbr->show_rdf($cc_origin).'</tt>';
                }

                 
            
            if (strlen($alt.$hid) > 0)
                {
                    echo '<table width="100%">';
                    echo '<tr valign="top"><td width="50%">';
                    echo msg('alternativeNames').': <ul>'.$alt.'</ul>';
                    echo '</td>';
                    echo '<td width="50%">';
                    echo '<div id="hden" style="display: none;">'.cr();
                    echo msg('hiddenNames').': <ul>'.$hid.'</ul>';
                    echo '</div>'.cr();
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                } 
                
                /**********************************/
                if (perfil("#ADM#CAT"))
                    {
                        echo '<br/>';
                        echo $this->frad->find_remissiva($w);
                        echo ' ';
                        echo '<a href="'.base_url(PATH.'a/'.$id).'" class="btn btn-outline-primary">'.msg('edit').'</a>';
                        echo ' ';
                        echo '<a href="'.base_url(PATH.'jnl_edit/'.$id_jnl).'" class="btn btn-outline-primary">'.msg('edit_source').'</a>';                        
                        echo '<br/>';
                        echo '<br/>';
                    }               
            ?>
        </div>
        <div class="col-md-2 text-center">
              <?php echo $img;?>
        </div>        
        <?php
            if (strlen($notas) > 0)
                {
                    echo '<div class="col-md-1 text-right" style="border-right: 4px solid #8080FF;">';
                    echo '</div>';
                    echo '<div class="col-md-11" style="margin-top: 15px; font-size: 80%;">';
                    echo '<b>Notas</b><br>';
                    echo $notas;
                    echo '</div>';
                }
        ?>        
    </div>

</div>
<br>