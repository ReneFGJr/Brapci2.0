<?php
class genero extends CI_model
{
    function find_without()
    {
        $class = 'hasGender';
        $sql = "se";
    }        
    
    function starter()
    {
        $class = 'Gender';
        /************** MALE *****************/
        $term = $this->frbr_core->frbr_name('Genero indefinido (pelo nome)','pt-BR');            
        $r1 = $this->frbr_core->rdf_concept($term, $class,'');
        $prop = 'prefLabel';
        $this->frbr_core->set_propriety($r1, $prop, 0, $term);           
        
        
        /************** MALE *****************/
        $term = $this->frbr_core->frbr_name('Masculino','pt-BR');            
        $r1 = $this->frbr_core->rdf_concept($term, $class,'');
        $prop = 'prefLabel';
        $this->frbr_core->set_propriety($r1, $prop, 0, $term);
        
        $prop = 'altLabel';
        $term = $this->frbr_core->frbr_name('Male','en');
        $this->frbr_core->set_propriety($r1, $prop, 0, $term);
        
        /************** FEMALE ***************/
        $term = $this->frbr_core->frbr_name('Feminino','pt-BR');
        $r1 = $this->frbr_core->rdf_concept($term, $class,'');
        $prop = 'prefLabel';
        $this->frbr_core->set_propriety($r1, $prop, 0, $term);

        $prop = 'altLabel';
        $term = $this->frbr_core->frbr_name('Female','en');
        $this->frbr_core->set_propriety($r1, $prop, 0, $term);
        
    }
    function update($id)
    {
            //$this->starter();
        
        $data = $this->frbr_core->le_data($id);
        $no_genere = true;
        for ($r=0;$r < count($data);$r++)
        {
            $line = $data[$r];
            if ($line['c_class'] == 'prefLabel')
            {
                $name = $line['n_name'];
                $g = $this->consulta($name);
            }
            if ($line['c_class'] == 'hasGender')
            {
             $no_genere = false; 
         }
     }
     
     /* genero não definido */
     if ($no_genere == true)
     {
        $prop = 'hasGender';
        if (isset($g))
        {
        $r1 = $this->frbr_core->find($g,'Gender',1);
        if ($r1 > 0)
            {
            $this->frbr_core->set_propriety($id, $prop, $r1, 0);
            }
        }
  }
  
}
function author_check_instituicoes()
{        
    $rdf = new rdf;
    $t = 0;
    $sx = '<div class="col-md-12">';
    $q = 'Genero indefinido';
    $id = $rdf->find($q,'Gender');
    $idg = $rdf->find('Outro (instituição)','Gender');

    $wh = " (n_name like '%editor%') OR (n_name like '%grupo%') OR (n_name like '%revista%') 
    OR (n_name like '%comissao%') OR (n_name like '%instituto%')
    OR (n_name like '%equipe%')  OR (n_name like '%bibliotec%')
    OR (n_name like '%arquivist%')  OR (n_name like '%departament%')
    OR (n_name like '%gestor%') OR (n_name like '%admin%')
    OR (n_name like '%autor%') OR (n_name like '%comite%')
    OR (n_name like '%ibict%') OR (n_name like '%centro%')
    OR (n_name like '%universid%') OR (n_name like '%unesco%')
    OR (n_name like '%informa%')";

    $sql = "SELECT * FROM rdf_data
    INNER JOIN rdf_concept ON id_cc = d_r1
    INNER JOIN rdf_name ON cc_pref_term = id_n
    where d_r2 = $id  
    AND ($wh)
    order by id_d
    limit 20
    ";
    $rlt = $this->db->query($sql);
    $rlt = $rlt->result_array();
    $sx .= '<ul>';
    for ($r=0;$r < count($rlt);$r++)
    {
        $line = $rlt[$r];
        $link = '<a href="'.base_url(PATH.'a/'.$line['d_r1']).'" target="_new'.$line['d_r1'].'">';
        $sx .= '<li>'.$link.$line['n_name'].'</a>'.'</li>';

        $sql = "update rdf_data set d_r2 = ".$idg." where id_d = ".$line['id_d'];
        $rrr = $this->db->query($sql);                   
    }
    $sx .= '</ul>';
    $sx .= '</div>';





    return($sx);
}
function author_check($p,$i)
{
    $off = round($i);
    $rdf = new rdf;
    $t = 0;
    $sx = '<div class="col-md-12">';
    $q = 'Genero indefinido';
    $id = $rdf->find($q,'Gender');
    $sql = "SELECT * FROM `rdf_data` 
    where d_r2 = $id  
    and id_d > $off
    order by id_d
    limit 20
    ";
    $rlt = $this->db->query($sql);
    $rlt = $rlt->result_array();
    $sx .= '<ul>';
    for ($r=0;$r < count($rlt);$r++)
    {
        $t++;
        $line = $rlt[$r];
        $idr = $line['d_r1'];
        $off = $line['id_d'];
        
        $data = $rdf->le_data($idr);
        $nome = '';
        for ($y=0;$y < count($data);$y++)
        {
            $ln = $data[$y];
            if ($ln['c_class'] == 'prefLabel')
            {
                $nome = UpperCaseSql($ln['n_name']);
                $nome = nbr_autor($nome,7);
            }
        }

        if (strlen($nome) > 0)
        {
            $genero = $this->consulta($nome);
        }
        $link = '<a href="'.base_url(PATH.'a/'.$line['d_r1']).'" target="_new'.$line['d_r1'].'">';
        $sx .= '<li>'.$link.$nome.'</a>- '.$genero.' '.$idr;
        if ($genero != $q)
        {
            $sx .= ' - <span style="color:green;"><b>Update to '.$genero.'</b></span>';
            $idg = $rdf->find($genero,'Gender');
            $sql = "update rdf_data set d_r2 = ".$idg." where id_d = ".$line['id_d'];
            $rrr = $this->db->query($sql);
        }
        $sx .= '</li>';
    }
    $sx .= '</ul>';
    $sx .= '</div>';
    
    if ($t > 0)
    {
        $sx .= '<br><hr><a href="'.base_url(PATH.'tools/genere/'.$off).'" class="btn btn-primary">'.msg('next').'</a>';
        $sx .= '<meta http-equiv="refresh" content="5;URL='.base_url(PATH.'tools/genere/'.$off).'">';
    } else {
        $sx .= '<h1>'.msg("end of job").'</h1>';
        $sx .= '<br><hr><a href="'.base_url(PATH).'" class="btn btn-primary">'.msg('return').'</a>';

        $sx .= $this->author_check_instituicoes();
    }
    return($sx);
}
function export()   
{
    $sx = '';
    $sql = "select * from genre order by gn_first_name";
    $rlt = $this->db->query($sql);
    $rlt = $rlt->result_array();
    $xlt = '';
    for ($r=0;$r < count($rlt);$r++)
    {
        $line = $rlt[$r];
        $lt = strtolower(substr($line['gn_first_name'],0,1));
        if ($xlt != $lt)
        {                        
            if (strlen($xlt) > 0) { fclose($file); }
            $file = fopen('_blnp/genere_'.$lt.'.txt','w');
            $sx .= '_blnp/genere_'.$lt.'.txt'.'<br>';
        }
        $s = $line['gn_first_name'].";";
        $s .= $line['gn_group_name'].";";
        $s .= $line['gn_genre_o'].";";
        $s .= $line['gn_genre'].";";
        $s .= $line['gn_frequency_female'].";";
        $s .= $line['gn_frequency_male'].";";             
        $s .= cr();
        $xlt = $lt;
        fwrite($file,$s);
    }
    fclose($file);
    return($sx);
}
function api($names='')
{
   $rsa = $this->consulta($names);
   return($rsa);
}
function consulta($name)
{
    $name = trim(nbr_autor($name,7));
    $p = null;
    $g = 'Genero indefinido';
    
    if (strpos($name,' ') > 0)
    {
        $name1 = trim(substr($name,0,strpos($name,' ')));
        $nameZ = trim(substr($name,strpos($name,' '),strlen($name)));
        $name2 = trim(substr($nameZ,0,strpos($nameZ,' ')));
        $nameZ = trim(substr($nameZ,strpos($nameZ,' '),strlen($nameZ)));
        $name3 = trim(substr($nameZ,0,strpos($nameZ,' ')));
    } else {
        $name1 = 'xxxxxx';
        $name2 = 'xxxxxx';
        $name3 = 'xxxxxx';
    }
    $sql = "select * from genre where gn_first_name = '$name1' ";
    $rlt = $this->db->query($sql);
    $rlt = $rlt->result_array();
    if (count($rlt) > 0)
    {
        $line = $rlt[0];
        $f = $line['gn_frequency_female'];
        $m = $line['gn_frequency_male'];
        $p = 2*(((int)(100 * $m / ($m + $f))) - 50);
        if ($p > 90)
        {
            $g = 'masculino';
        }
        if ($p < -90)
        {
            $g = 'feminino';
        }
    }

    /*************************************** Segundo nome ************/
    if ($g == 'Genero indefinido')
    {
        $sql = "select * from genre where gn_first_name = '$name2' ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0)
        {
            $line = $rlt[0];
            $f = $line['gn_frequency_female'];
            $m = $line['gn_frequency_male'];
            $p = 2*(((int)(100 * $m / ($m + $f))) - 50);
            if ($p > 90)
            {
                $g = 'masculino';
            }
            if ($p < -90)
            {
                $g = 'feminino';
            }
        }

    }
    return($g);
}        
}
?>
