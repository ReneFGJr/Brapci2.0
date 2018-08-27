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
                    $r1 = $this->frbr_core->find($g,'Gender',1);
					if ($r1 > 0)
						{
                    		$this->frbr_core->set_propriety($id, $prop, $r1, 0);
						}
                }
                
        }
    function consulta($name)
        {
            $name = trim(nbr_autor($name,7));
            $p = null;
            $g = 'Genero indefinido';
            
            if (strpos($name,' ') > 0)
                {
                    $name = trim(substr($name,0,strpos($name,' ')));
                }
            $sql = "select * from genre where gn_first_name = '$name' ";
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
            return($g);
        }        
    }
?>
