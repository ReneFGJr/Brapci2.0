<?php
class sources extends CI_Model {
    var $table = 'source_source';

    function summary($cmd = '') {

        /******************** limpa logs ************************/
        if ($cmd == 'clear') {
            $dir = $_SERVER['SCRIPT_FILENAME'];
            $dir = troca($dir, 'index.php', '');
            $filename = $dir . 'script/cron.oai.html';
            if (file_exists($filename)) {
                unlink($filename);
            }
            $filename = $dir . 'script/cron.pdf.html';
            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        /******************** summary_index ************************/
        $wh = '';
        $sx = '';
        $sx .= '<div class="col-md-8">';
        $sx .= '<h1>' . msg('summary_index') . '</h1>';
        $sx .= '<ul>';
        $cl = array('Journal', 'Article', 'Person');
        $sa = '';
        for ($r = 0; $r < count($cl); $r++) {
            $sa .= '<li>' . date("d-m-Y H:i:s") . ' - ' . $cl[$r] . '</li>';
            $f = $this -> frbr_core -> find_class($cl[$r]);
            $sql = "select C1.id_cc as id_cc        
                        FROM rdf_concept as C1
                        where C1.cc_class = " . $f . " $wh  and cc_use = 0";

            $rlt = $this -> db -> query($sql);

            $rlt = $rlt -> result_array();
            $sx .= '<li class="big">' . number_format(count($rlt), 0, ',', '.') . ' ' . msg($cl[$r]) . '</li>';
        }
        $sx .= '</ul>';
        $sx .= '<h1>' . msg('summary_remissive') . '</h1>';
        $sx .= '<ul>';
        for ($r = 0; $r < count($cl); $r++) {
            $sa .= '<li>' . date("d-m-Y H:i:s") . ' - ' . $cl[$r] . '</li>';
            $f = $this -> frbr_core -> find_class($cl[$r]);
            $sql = "select C1.id_cc as id_cc        
                        FROM rdf_concept as C1
                        where C1.cc_class = " . $f . " $wh  and cc_use <> 0";

            $rlt = $this -> db -> query($sql);

            $rlt = $rlt -> result_array();
            $sx .= '<li class="big">' . number_format(count($rlt), 0, ',', '.') . ' ' . msg($cl[$r]) . '</li>';
        }
        /******************** summary_index ************************/

        $prop1 = $this -> frbr_core -> find_class('hasUrl');
        $prop2 = $this -> frbr_core -> find_class('hasFileStorage');
        $sa .= '<li>' . date("d-m-Y H:i:s") . ' - PDFs' . '</li>';
        $sql = "
				select count(*) as total from rdf_data AS R1
					left JOIN rdf_data AS R2 ON R1.d_r1 = R2.d_r1 and R2.d_p = $prop2
				  where R1.d_p = $prop1 and R2.d_p is null
			";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $total = $rlt[0]['total'];
        $sx .= '<li>' . $total . ' ' . msg('pdf_to_harveting') . '</li>';

        $sx .= '</ul>';
        $sx .= '<h1>' . msg('summary_oai') . '</h1>';
        $sx .= '<ul>';

        $sql = "SELECT count(*) as total, li_s FROM `source_listidentifier` group by li_s order by li_s";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx .= '<ul>';
        $sa .= '<li>' . date("d-m-Y H:i:s") . ' - Works' . '</li>';
        for ($r = 0; $r < count($rlt); $r++) {
            $total = $rlt[$r]['total'];
            $st = $rlt[$r]['li_s'];
            $sx .= '<li>' . number_format($total, 0, ',', '.') . ' ' . msg('cache_status_' . $st) . '</li>';
        }
        $sx .= '</ul>';

        /******************* LOG DE TEMPO ***************/
        $sx .= '</div>';

        $sx .= '<div class="col-md-4">';
        $sx .= '<ul>' . $sa . '</ul>';
        $sx .= '</div>';

        $sx .= '<div class="col-md-12">';

        $sx .= '<h1>Logs</h1>';
        $sx .= '<a href="' . base_url(PATH . 'summary/clear') . '" class="btn btn-outline-primary">' . msg('clear_logs') . '</a>';

        $sx .= '<ul>';

        $dir = $_SERVER['SCRIPT_FILENAME'];
        $dir = troca($dir, 'index.php', '');

        $filename = $dir . 'script/cron.oai.html';
        $sx .= '<li><h5>OAI Status</h5>';
        $sx .= '<h6>' . $filename . '</h6>';
        if (file_exists($filename)) {
            $ttt = file_get_contents($filename);
            $ttt = troca($ttt, '<meta', '<mmmm');
            $sx .= '<pre style="color: blue;">' . mst($ttt) . '</pre>';
        } else {
            $sx .= '<pre style="color: red;">OAI Status not found</pre>';
        }
        $sx .= '</li>';

        $sx .= '<li><h5>PDF Status</h5>';
        $filename = $dir . 'script/cron.pdf.html';
        if (file_exists($filename)) {
            $sx .= '<pre style="color: green;">' . mst(file_get_contents($filename)) . '</pre>';
        } else {
            $sx .= '<pre style="color: red;">PDF Status not found</pre>';
        }
        $sx .= '</li>';
        $sx .= '</ul>';
        $sx .= '</div>';
        $sx .= '</div>';
        $sx .= '</div>';
        //$sx .= '<style> div { border:1px solid #000000;"}<style>';
        return ($sx);
    }

    function next_harvesting($p = '') {
        if (strlen($p) == 0) { $p = 0;
        }
        $p = round($p);

        $sql = "SELECT * FROM `source_source` WHERE jnl_url_oai <> '' and id_jnl > $p order by id_jnl limit 1 ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        if (count($rlt) > 0) {
            $id = $rlt[0]['id_jnl'];
        } else {
            $id = (-1);
        }

        return ($id);
    }

    function button_harvesting_status() {
        $sx = '&nbsp;<a href="' . base_url(PATH . 'journals/harvesting/999999') . '" class="btn btn-outline-secondary">';
        $sx .= msg('button_harvesting_status');
        $sx .= '</a>';
        return ($sx);
    }

    function button_harvesting_all() {
        $sx = '&nbsp;<a href="' . base_url(PATH . 'journals/harvesting') . '" class="btn btn-outline-secondary">';
        $sx .= msg('harvesting_all');
        $sx .= '</a>';
        return ($sx);
    }

    function cp($id = '') {
        $cp = array();
        array_push($cp, array('$H8', 'id_jnl', '', False, True));
        array_push($cp, array('$S100', 'jnl_name', msg('jnl_name'), False, True));
        array_push($cp, array('$S30', 'jnl_name_abrev', msg('jnl_name_abrev'), False, True));

        array_push($cp, array('$S100', 'jnl_url', msg('jnl_url'), False, True));
        array_push($cp, array('$S100', 'jnl_url_oai', msg('jnl_url_oai'), False, True));

        array_push($cp, array('$S30', 'jnl_issn', msg('jnl_issn'), False, True));
        array_push($cp, array('$S30', 'jnl_eissn', msg('jnl_eissn'), False, True));
        array_push($cp, array('$[1950-' . date("Y") . ']', 'jnl_ano_inicio', msg('jnl_ano_inicio'), False, True));
        array_push($cp, array('$[1950-' . date("Y") . ']', 'jnl_ano_final', msg('jnl_ano_final'), False, True));

        array_push($cp, array('$HV', 'jnl_oai_last_harvesting', date("Y-m-d"), True, True));
        array_push($cp, array('$HV', 'jnl_cidade', '0', False, True));
        array_push($cp, array('$HV', 'jnl_scielo', '0', False, True));
        array_push($cp, array('$HV', 'jnl_collection', '', False, True));
        $op = '1:' . msg('yes, with OAI');
        $op .= '&2:' . msg('yes, without OAI');
        $op .= '&3:' . msg('No, finished');
        $op .= '&0:' . msg('canceled');
        array_push($cp, array('$O 1:Yes', 'jnl_active', msg('active'), True, True));

        return ($cp);
    }

    function jnl_name($line) {
        if (count($line) == 0) {
            return ("");
        }
        $link = '<a href="' . base_url(PATH . 'jnl/' . $line['id_jnl']) . '">';
        $sx = $link . $line['jnl_name'] . '</a>';

        $link = '<a href="' . $line['jnl_url'] . '" target="_new"><sup>(l)</sup></a>';
        if (strlen($line['jnl_url']) > 0) {
            $sx .= ' ' . $link;
        }
        return ($sx);
    }

    function le($id) {
        if (($id == 0) or ($id == '')) {
            return ( array());
        }
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

    function button_new_issue($id = '') {
        $sx = '';
        $sx .= '<div class="row">';
        $sx .= '<div class="col-1">';
        $sx .= '<a href="#" class="btn btn-secondary" onclick="newwin(\''.base_url(PATH.'issue/new/'.$id).'\',800,600);">' . msg("new_issue") . '</a>';
        $sx .= '</div>';
        $sx .= '</div>' . CR;
        return ($sx);
    }

    function button_new_sources($id = '') {
        $sx = '';
        $sx .= '<div class="row">';
        $sx .= '<div class="col-1">';
        if (strlen($id) == 0) {
            $sx .= '<a href="' . base_url(PATH . 'jnl_edit') . '" class="btn btn-secondary">' . msg("new_source") . '</a>';
        } else {
            $sx .= '<a href="' . base_url(PATH . 'jnl_edit/' . $id) . '" class="btn btn-secondary">' . msg("edit_source") . '</a>';
        }

        $sx .= '</div>';
        $sx .= '</div>' . CR;
        return ($sx);
    }

    function list_sources() {
        $sql = "select * from " . $this -> table . " 
                            where jnl_active = 1
                            order by jnl_name
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        /**************************** MOUNT HTML ***********/
        $sx = '<div class="col-12">' . CR;
        $sx .= '<ol class="journals">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<li>' . $this -> jnl_name($line) . '</li>';
        }
        $sx .= '</ol>';
        $sx .= '</div>' . CR;
        return ($sx);
    }

    /********************************************************************** INFO **********/
    function info($id = 0) {
        if (is_array($id)) {
            $line = $id;
        } else {
            $line = $this -> le($id);
        }
        if (count($line) == 0) {
            return ("");
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

        if (strlen($line['jnl_url_oai']) > 0) {
            $sx .= '<br>';
            $sx .= $this -> oai_pmh -> menu($line['id_jnl'],$line['jnl_frbr']);
        } else {
            $sx .= '<br>';
            $sx .= $this -> frbr -> journal_manual($line['id_jnl'],$line['jnl_frbr']);            
        }
        

        $sx .= $this -> oai_pmh -> cache_resume($line['id_jnl']);

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
        $sx .= '<h2>' . msg('journal_timeline') . '</h2>';
        $sx .= '<tt>';
        $i = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            /*****************************/
            $ano = $line['jnl_ano_inicio'];
            while ($xano >= $ano) {
                if ($i > 0) { $sx .= '<br>';
                }
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
        $prop_id = $this -> frbr -> find_class($prop);

        $sql = "select * from ";
        echo '===>' . $prop . '==' . $prop_id;
    }

    function show_issues($idx = 0) {
        $id = $this -> oai_pmh -> check_oai_index($idx);
        if ($id == 0) {
            return ("");
        }
        $sx = '<h3>' . msg('ISSUE') . '</h3>';
        //$id = 164;
        $dt = $this -> frbr_core -> le_data($id);
        $ar = array();

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];
            if ($line['c_class'] == 'hasIssue') {
                $n = $line['n_name'] . '#' . $line['d_r1'];
                array_push($ar, $n);
            }
        }
        asort($ar);

        $sx = '';
        $ed = 0;
        $xano = '';
        $sx = '<table>';
        foreach ($ar as $key => $value) {
            $n = $value;
            $name_use = substr($n, 0, strpos($n, '#'));
            $idx = substr($n, strpos($n, '#') + 1, strlen($n));

            $filex = 'c/' . $idx . '/name.sm';
            if (file_exists($filex)) {
                $name_use = load_file_local($filex);
            }

            if (strpos($name_use, '#')) {
                $ano = substr($name_use, strpos($name_use, '#') + 1, strlen($name_use));
                $name_use = substr($name_use, 0, strpos($name_use, '#'));
            } else {
                $ano = '';
            }
            if ($xano != $ano) {
                $xano = $ano;

                if ($ed > 0) {
                    $sx .= '</tr>';
                }

                $sx .= '<tr><td>' . cr();
                $sx .= '' . $ano . '' . cr();
                $sx .= '</td></tr>' . cr();

                $sx .= '<tr>';
                $sx .= '<td style="background-color: #00ff00;">';
                $ed++;
            }
            $sx .= '<div style="width: 80px; text-align: center; height: 120px; border: 1px solid #808080; margin: 10px 10px; float: left;">';
            $sx .= '<a href="' . base_url(PATH . 'v/' . $idx) . '">';
            $sx .= $name_use;
            $sx .= '</a>';
            $sx .= '</div>';

        }
        $sx .= '</td>';
        $sx .= '</tr>';
        $sx .= '</table>';

        return ($sx);
    }

}
