<?php
$nome = '';
$alt = '';
$hid = '';
$born = '';
$dead = '';
$notas = '';
$coll = '';
$contact = '';
$issn = '';
$editor = '';
$validity = '';
$url = '';
$w = $id;
$link = '<a href="'.base_url(PATH.'a/'.$w).'">';
$linkr = '<a href="'.base_url(PATH.'v/'.$w).'">';
$linka = '</a>';
$img = '';
$cutter = ''; 
$id_jnl = $source['id_jnl'];

/***************************************************************** SOURCES *****/
if ($source['jnl_ano_inicio'] > 0)
{
    $validity = $linkr.$source['jnl_ano_inicio'].$linka;

    if ($source['jnl_ano_final'] > 0)
    {
        $validity .= '-'.$linkr.$source['jnl_ano_final'].$linka;
    } else {
        $validity .= '-'.$linkr.'vigente'.$linka;
    }
}

for ($r=0;$r < count($use);$r++)
    {
        $alt .= '<li>'.$use[$r]['n_name'].'</li>'.cr();
    }

for ($r=0;$r < count($person);$r++)
    {
        $line = $person[$r];
        $class = trim($line['c_class']);
        $linkr = '<a href="'.base_url(PATH.'v/'.$line['d_r2']).'">';
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class)
            {
            case 'hasUrl':
				$url  .= '<a href="'.trim($line['n_name']).'" target="_new'.date("mis").'">'.trim($line['n_name']).'</a> ';
				break;
            case 'hasEditor':
            	if (strlen($editor) > 0)
            	{
            		$editor .= '; ';
            	}
            	$editor  .= $linkr.trim($line['n_name']).'</a>';
            	break;
            case 'hasEmail':
            	$contact .= $link.trim($line['n_name']).'</a>';
            	break;
            case 'hasCollection':
            	$coll .= $link.'<span class="btn-primary" style="padding: 2px 4px; border-radius: 4px;">'.trim($line['n_name']).'</span></a> ';
            	break;
            case 'hasISSN':
            	if (strlen($issn) > 0)
            	{
            		$issn .= '; ';
            	}
            	$issn .= $link.trim($line['n_name']).'</a>';
            	break;
			case 'hasCutter':
				$cutter = $link.trim($line['n_name']).'</a>';
				break;
            case 'prefLabel':
                $link = '<a href="'.base_url(PATH.'v/'.$id).'">';
                $nome = $link.trim($line['n_name']).'</a>';
                break;
            case 'altLabel':
                $alt .= '<li>'.trim($line['n_name']).'</li>';
                break;
            case 'hiddenLabel':
                if (strlen($hid) > 0)
                    {
                        $hid .= '; ';
                    }
                $hid .= trim($line['n_name']);
                break;                
            case 'sourceNote':
                if (strlen($notas) > 0)
                    {
                        $notas .= '<br>';
                    }
                    $notas .= $line['n_name'];
                break;                
            case 'hasBorn':
                $link = '<a href="'.base_url(PATH.'v/'.$line['id_d']).'">';
                $born = $link.trim($line['n_name']);
                $born .= '</a>';
                break;
            case 'hasDie':
                $link = '<a href="'.base_url(PATH.'v/'.$line['id_d']).'">';
                $dead = $link.trim($line['n_name']);
                $dead .= '</a>';
                break;  
            case 'hasCover' :
                $img = $this -> frbr -> mostra_imagem($line['d_r2']);
                break;
            }
    }
    $dates = '';
    if (strlen($born.$dead) > 0)
        {
            if (strlen($born) > 0)
                {
                    $dates = ', '.$born.'-';
                } else {
                    $dates .= ', -';
                }
            $dates .= $dead;                
        }
       

?>