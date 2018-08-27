<?php
$title = '';
$subtitle = '';
$autor = '';
$tradutor = '';
$ilustrador = '';
$organizador = '';
$tipo = '';
$w = $id;
$link = '<a href="'.base_url(PATH.'a/'.$w).'">';
//echo $link.'[ed]</a>';
for ($r=0;$r < count($work);$r++)
    {
        $line = $work[$r];
        $class = $line['c_class'];
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class)
            {
            case 'hasTitle':
                $title = trim($line['n_name']);
                break;
            case 'hasSubtitle':
				if (strlen($line['n_name']) > 0)
					{
                		$subtitle = ': '.trim($line['n_name']);
					}
                break;
            case 'hasAuthor':
                if (strlen($autor) > 0)
                    {
                        $autor .= '; ';
                    }
                $autor .= '<a href="'.base_url(PATH.'v/'.$line['d_r2']).'" style="color: #00008;">';
                $autor .= trim($line['n_name']);
                $autor .= '</a>';
                break;
            case 'hasOrganizator':
                if (strlen($autor) > 0)
                    {
                        $autor .= '; ';
                    }
                $autor .= '<a href="'.base_url(PATH.'v/'.$line['d_r2']).'" style="color: #00008;">';
                $autor .= trim($line['n_name']).' (org.)';
                $autor .= '</a>';
                break;                 
            case 'hasTranslator':
                if (strlen($tradutor) > 0)
                    {
                        $tradutor .= '; ';
                    }
                $tradutor .= '<a href="'.base_url(PATH.'v/'.$line['d_r2']).'" style="color: #000080;">';
                $tradutor .= trim($line['n_name']);
                $tradutor .= '</a>';
                break;                                
            case 'hasFormWork':
                $tipo = trim($line['n_name']);
                break;
            }
    }
?>
<!---------------- WORK --------------------------------------------------------------->
<div class="container">
    <div class="row">
        <div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">
            <tt style="font-size: 100%;"><?php echo msg('Work');?></tt>
        </div>
        <div class="col-md-10">
            <a href="<?php echo base_url(PATH.'v/'.$w);?>">
            <span style="font-size: 140%; color: #000000;"><b><?php echo $title.$subtitle; ?></b></span>
            </a>
            <br>
            <i><?php echo '<b>'.$autor.'</b>';?></i>
            <?php if (perfil("#ADM")) {
                   echo '<br><a href="'.base_url(PATH.'a/' . $id).'"  class="btn btn-secondary">editar</a>';
            }
            ?>            
            
            
        </div>
    </div>
</div>
<br>