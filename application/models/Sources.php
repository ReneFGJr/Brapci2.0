<?php
class sources extends CI_Model {
    var $table = 'source_source';

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

    function list_sources() {
        $sql = "select * from " . $this -> table . " 
                            where jnl_active = 1
                            order by jnl_name
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        /**************************** MOUNT HTML ***********/
        $sx = '<ul class="journals">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<li>' . $this -> jnl_name($line) . '</li>';
        }
        $sx .= '</ul>';
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
                            where jnl_ano_inicio > 0 
                            order by jnl_ano_inicio desc";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $xano = date("Y");
        $sx = '';
        $sx .= '<h2>'.msg('journal_timeline').'</h2>';
        $sx .= '<br>';
        $sx .= '<tt>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            /*****************************/
            $ano = $line['jnl_ano_inicio'];
            while ($xano >= $ano) {
                $sx .= '<br>' . $this -> year($xano) . ' +';
                $xano--;
            }
            $sx .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this -> jnl_name($line);
        }

        while ($xano >= 1950) {
            $sx .= '<br>' . $this -> year($xano) . ' +';
            $xano--;
        }
        $sx .= '</tt>';
        return ($sx);
    }

    function agents_list() {
            $prop = 'Person';
            $prop_id = $this->frbr->find_class($prop);
            
            $sql = "select * from ";
            echo '===>'.$prop.'=='.$prop_id;
    }

}
