<?php
class sources extends CI_Model {
    var $table = 'source_source';

	function cp($id='')
		{
			$cp = array();
			array_push($cp,array('$H8','id_jnl','',False,True));
			array_push($cp,array('$S100','jnl_name',msg('jnl_name'),False,True));
			array_push($cp,array('$S30','jnl_name_abrev',msg('jnl_name_abrev'),False,True));
			
			array_push($cp,array('$S100','jnl_url',msg('jnl_url'),False,True));
			array_push($cp,array('$S100','jnl_url_oai',msg('jnl_url_oai'),False,True));
			
			array_push($cp,array('$S30','jnl_issn',msg('jnl_issn'),False,True));
			array_push($cp,array('$S30','jnl_eissn',msg('jnl_eissn'),False,True));
			array_push($cp,array('$[1950-'.date("Y").']','jnl_ano_inicio',msg('jnl_ano_inicio'),False,True));
			array_push($cp,array('$[1950-'.date("Y").']','jnl_ano_final',msg('jnl_ano_final'),False,True));
			
			
			array_push($cp,array('$HV','jnl_oai_last_harvesting',date("Y-m-d"),True,True));
			array_push($cp,array('$HV','jnl_cidade','0',False,True));
			array_push($cp,array('$HV','jnl_scielo','0',False,True));
			array_push($cp,array('$HV','jnl_collection','',False,True));
			$op = '1:'.msg('yes, with OAI');
			$op .= '&2:'.msg('yes, without OAI');
			$op .= '&3:'.msg('No, finished');
			$op .= '&0:'.msg('canceled');
			array_push($cp,array('$O 1:Yes','jnl_active',msg('active'),True,True));
			
			return($cp);
		}

    function jnl_name($line) {
        $link = '<a href="' . base_url(PATH . 'jnl/' . $line['id_jnl']) . '">';
        $sx = $link . $line['jnl_name'] . '</a>';

        $link = '<a href="' . $line['jnl_url'] . '" target="_new"><sup>(l)</sup></a>';
        if (strlen($line['jnl_url']) > 0) {
            $sx .= ' ' . $link;
        }
        return ($sx);
    }

    function le($id) {
        $sql = "select * from " . $this -> table . " where id_jnl = " . $id;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line);
        } else {
            return ( array());
        }
    }
	
	function button_new_sources($id='')
		{
			$sx = '';
			$sx .= '<div class="row">';
			$sx .= '<div class="col-1">';
			if (strlen($id) == 0)
				{
					$sx .= '<a href="'.base_url(PATH.'jnl_edit').'" class="btn btn-secondary">'.msg("new_source").'</a>';		
				} else {
					$sx .= '<a href="'.base_url(PATH.'jnl_edit/'.$id).'" class="btn btn-secondary">'.msg("edit_source").'</a>';
				}
			
			$sx .= '</div>';
			$sx .= '</div>'.CR;
			return($sx);
		}

    function list_sources() {
        $sql = "select * from " . $this -> table . " 
                            where jnl_active = 1
                            order by jnl_name
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        /**************************** MOUNT HTML ***********/
        $sx = '<div class="col-12">'.CR;
        $sx .= '<ul class="journals">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<li>' . $this -> jnl_name($line) . '</li>';
        }
        $sx .= '</ul>';
		$sx .= '</div>'.CR;
        return ($sx);
    }

    /********************************************************************** INFO **********/
    function info($id=0) {
        if (is_array($id))
            {
                $line = $id;
            } else {
                $line = $this->le($id);        
            }
        
        $sx = '';
        $sx .= '<div class="col-md-6">';
        $sx .= '<span class="h3">' . $this -> jnl_name($line) . '</span>' . CR;
        if (strlen($line['jnl_name_abrev']) > 0) {
            $sx .= '<br>(' . $line['jnl_name_abrev'] . ')';
        }

        $sx .= '<br>';
        $sx .= '<span>ISSN: ' . $line['jnl_issn'] . '</span>' . CR;
        if (isset($line['jnl_eissn'])) {
            $sx .= ' <span class="small">eISSN: ' . $line['jnl_eissn'] . '</span>' . CR;
        }

        /************************************************************ COBERTURA ********/
        $ini = $line['jnl_ano_inicio'];
        $fim = $line['jnl_ano_final'];
        if ($ini > 0) {
            $sx .= '<br>';
            $sx .= msg('Validity') . ': ' . $this -> year($ini);
            if ($fim > 0) {
                $sx .= '-' . $this -> year($fim);
            } else {
                $sx .= '-' . msg('current');
            }
        } else {
            if ($fim > 0) {
                $sx .= '<br>';
                $sx .= msg('Validity') . ': ' . '-' . $this -> year($fim);
            }
        }
        
        if (strlen($line['jnl_url_oai']) > 0)
            {
                $sx .= '<br>';
                $sx .= $this->oai_pmh->menu($line['id_jnl']);
            }
        
        $sx .= $this->oai_pmh->cache_resume($line['id_jnl']);
        
        $sx .= '</div>';

        return ($sx);
    }

    /********************************************* Time Line *********************/
    function year($i) {
        if ($i > 1900) {
            $sx = '<a href="' . base_url(PATH . 'timeline/' . $i) . '">' . $i . '</a>';
        } else {
            $sx = '';
        }
        return ($sx);
    }

    /*********************************************************** Timeline *********/
    function timelines($i = 0) {
        $sql = "select * from " . $this -> table . "
                            where jnl_ano_inicio >= $i 
                            order by jnl_ano_inicio desc";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $xano = date("Y");
        $sx = '<div class="row">';
		$sx .= '<div class="col-md-12">';
        $sx .= '<h2>'.msg('journal_timeline').'</h2>';
        $sx .= '<tt>';
		$i = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            /*****************************/
            $ano = $line['jnl_ano_inicio'];
            while ($xano >= $ano) {
            	if ($i > 0)
					{ $sx .= '<br>'; }
                $sx .= $this -> year($xano) . ' +';
                $xano--;
				$i++;
            }
            $sx .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this -> jnl_name($line);
        }

        while ($xano <= $i) {
            $sx .= '<br>' . $this -> year($xano) . ' +';
            $xano--;
        }
        $sx .= '</tt>';
        $sx .= '</div></div>';
        return ($sx);
    }

    function agents_list() {
            $prop = 'Person';
            $prop_id = $this->frbr->find_class($prop);
            
            $sql = "select * from ";
            echo '===>'.$prop.'=='.$prop_id;
    }

}
