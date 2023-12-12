<?php
class sources extends CI_Model {
    var $table = 'source_source';

    function source_status_update($id,$sta)
        {
                $date = date("Y-m-d H:m:s");
                $sql = "update ".$this->table."
                            set
                            jnl_oai_status = '$sta',
                            jnl_oai_last_harvesting = '$date'
                            where id_jnl = $id";
                $rlt = $this -> db -> query($sql);
        }

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

        $filename = $dir . 'script/cron.txt.html';
        $sx .= '<li><h5>PDF to TEXT Status</h5>';
        $sx .= '<h6>' . $filename . '</h6>';
        if (file_exists($filename)) {
            $ttt = file_get_contents($filename);
            $ttt = troca($ttt, '<meta', '<mmmm');
            $sx .= '<pre style="color: blue;">' . mst($ttt) . '</pre>';
        } else {
            $sx .= '<pre style="color: red;">Status not found</pre>';
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
        $p = sround($p);

        $sql = "SELECT * FROM source_source
                    WHERE jnl_url_oai <> ''
                    and id_jnl > $p
                    and jnl_scielo = 0
                    and jnl_active = 1
                    order by id_jnl limit 1 ";
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
        $sx = '&nbsp;<a href="' . base_url(PATH . 'journals/harvesting/999999') . '">';
        $sx .= msg('button_harvesting_status');
        $sx .= '</a>&nbsp;|&nbsp;';
        return ($sx);
    }

    function button_harvesting_all() {
        $sx = '&nbsp;<a href="' . base_url(PATH . 'journals/harvesting') . '" >';
        $sx .= msg('harvesting_all');
        $sx .= '</a>&nbsp;|&nbsp;';
        return ($sx);
    }

    function edit($id)
        {
            $form = new form;
            $form->id = $id;
            $cp=$this->cp();
            $table = $this->table;
            $sx = $form->editar($cp,$table);
            if ($form->saved > 0)
                {
                    $sx = 'SAVED';
                }
            return($sx);
        }

    function cp($id = '') {
        $cp = array();
        array_push($cp, array('$H8', 'id_jnl', '', False, True));
        array_push($cp, array('$S100', 'jnl_name', msg('jnl_name'), False, True));
        array_push($cp, array('$S30', 'jnl_name_abrev', msg('jnl_name_abrev'), False, True));
        array_push($cp, array('$O 0:No&1:Yes', 'jnl_historic', msg('jnl_historic'), True, True));

        array_push($cp, array('$A', '', msg('jnl_oai_title'), False, True));
        array_push($cp, array('$S100', 'jnl_url', msg('jnl_url'), False, True));
        array_push($cp, array('$S100', 'jnl_url_oai', msg('jnl_url_oai'), False, True));
        array_push($cp, array('$S100', 'jnl_oai_set', msg('jnl_oai_set'), False, True));
        array_push($cp, array('$O 0:Não&1:Sim', 'jnl_scielo', msg('jnl_scielo'), True, True));

        array_push($cp, array('$A', '', msg('jnl_issn_title'), False, True));
        array_push($cp, array('$S30', 'jnl_issn', msg('jnl_issn'), False, True));
        array_push($cp, array('$S30', 'jnl_eissn', msg('jnl_eissn'), False, True));
        array_push($cp, array('$[1950-' . date("Y") . ']', 'jnl_ano_inicio', msg('jnl_ano_inicio'), False, True));
        array_push($cp, array('$[1950-' . date("Y") . ']', 'jnl_ano_final', msg('jnl_ano_final'), False, True));

        array_push($cp, array('$S100', 'jnl_oai_token', msg('jnl_oai_token'), False, True));
        array_push($cp, array('$HV', 'jnl_oai_last_harvesting', date("Y-m-d"), True, True));
        array_push($cp, array('$HV', 'jnl_cidade', '0', False, True));
        $op = 'JA:Revista Brasileira';
        $op .= '&JE:Revista Estrangeira';
        $op .= '&EV:Evento Brasileiro';
        array_push($cp, array('$O '.$op, 'jnl_collection', msg('journal_type'), True, True));
        $op = '1:' . msg('yes, with OAI');
        $op .= '&2:' . msg('yes, without OAI');
        $op .= '&3:' . msg('No, finished');
        $op .= '&0:' . msg('canceled');
        array_push($cp, array('$O 1:Yes&0:No', 'jnl_active', msg('active'), True, True));
        return ($cp);
    }

    function jnl_name($line) {
        if (count($line) == 0) {
            return ("");
        }
        if ($line['jnl_frbr'] > 0)
            {
                $link = '<a href="' . base_url(PATH . 'v/' . $line['jnl_frbr']) . '">';
            } else {
                $link = '<a href="' . base_url(PATH . 'jnl/' . $line['id_jnl']) . '">';
            }
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

    function le_frbr($id) {
        if (($id == 0) or ($id == '')) {
            return ( array());
        }
        $sql = "select * from " . $this -> table . " where jnl_frbr = " . $id;
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
        $sx = ' | ';
        $sx .= '<a href="#" onclick="newwin(\''.base_url(PATH.'issue/new/'.$id).'\',800,600);">' . msg("new_issue") . '</a> | ';
        return ($sx);
    }

    function button_new_sources($id = '') {
        $sx = ' | ';
        if (strlen($id) == 0) {
            $sx .= '&nbsp;<a href="' . base_url(PATH . 'jnl_edit') . '">' . msg("new_source") . '</a>&nbsp;|&nbsp;';
        } else {
            $sx .= '&nbsp;<a href="' . base_url(PATH . 'jnl_edit/' . $id) . '">' . msg("edit_source") . '</a>&nbsp;|&nbsp;';
        }

        return ($sx);
    }

    function list_sources_link($url='')
    {
        $sql = "select * from " . $this -> table . "
                            where jnl_active = 1
                            order by jnl_name
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<ul>';
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $link = '<a href="'.$url.$line['id_jnl'].'">';
            $linka = '</a>';
            $sx .= '<li>'.$link.$line['jnl_name'].$linka.'</li>';
        }

        $sx .= '</ul>';
        return($sx);
    }


    function list_sources() {
        $limit_dias = 10;
        $this->load->model("oai_pmh");
        $sql = "select * from " . $this -> table . "
                            where jnl_active = 1
                            order by jnl_collection, jnl_name
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        /**************************** MOUNT HTML ***********/
        $xlt = '';
        $xtp = '';
        $it = 0;
        $ii = 0;
        $sx = '<div class="col-12">' . CR;
        $sx .= '<table style="width: 100%">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $tp = $line['jnl_collection'];
            if ($tp != $xtp)
            {
                $sx .= '<tr><th colspan=5><br/>';
                $sx .= '<h1>'.msg('collection_'.$tp).'</h1>';
                $sx .= '</th></tr>';
                $xtp = $tp;
                $ii = 0;
            }
            $lt = substr(UpperCaseSql($line['jnl_name']),0,1);
            if ($lt != $xlt)
            {
                $xlt = $lt;
                $sx .= '<tr><th>';
                $sx .= '<h5>'.$lt.'</h5>';
                $sx .= '</th></tr>';
            }
            $it++;
            $sx .= '<tr style="border-top: 1px solid #ccc;">';
            $sx .= '<td></td>';
            $sx .= '<td>'.(++$ii).'.</td>';
            $sx .= '<td>' . $this -> jnl_name($line) .'</td>';
            $token = trim($line['jnl_oai_token']);
            if (strlen($token) > 0)
                {
                    $token = '<span title="Token: '.$token.'" class="btn-outline-danger radius5">&nbsp;K&nbsp;</span>';
                }
            $sx .= '<td>' . $token .'</td>';

            /**************** Data */
            $sx .= '<td>'.stodbr($line['jnl_oai_last_harvesting']).'</td>';
            $dias = dataDiff($line['jnl_oai_last_harvesting'],date("Y-m-d"));


            /*************** Status da Coleta */
            $code = $line['jnl_oai_status'];
            $hist = $line['jnl_historic'];

            if ($hist==0)
                {
                    $sx .= '<td class="text-center small">'.$dias.'</td>';
                } else {
                    $sx .= '<td class="text-center small"></td>';
                }


            if ($code > 200)
                {
                    $status = '<span style="color: red" title="'.$this->oai_pmh->erros($code).'"><b>ERRO</b></span>';
                } else {
                    if ($dias > $limit_dias)
                    {
                        $status = '<span style="color: orange"><b>OLD</b></span>';
                    } else {
                        $status = '<span style="color: green"><b>OK</b></span>';
                    }
                }

            if ($hist == 1)
                {
                    $status = '<span style="color: blue"><b>Histórica</b></span>';
                }

            $sx .= '<td align="center">'.$status.'</td>';

            /******************************/
            $to = $line['jnl_oai_to_harvesting'];
            $link = '<a href="'.base_url(PATH.'jnl/'.$line['id_jnl']).'">';
            $linka = '</a>';
            if ($to > 0)
                {

                    $sx .= '<td align="center"><span style="color: red">'.$link.'+'.$to.$linka.'</span></td>';
                } else {
                    $sx .= '<td align="center">'.$link.'-'.$linka.'</td>';
                }


            $sx .= '</tr>';
        }
        $sx .= '</table>';
        $sx .= '<br>Legenda:';
        $sx .= '<br><span style="color: blue"><b>OK</b></span> - Coletado normalmente.';
        $sx .= '<br><span style="color: orange"><b>OLD</b></span> - Coletado a mais de '.$limit_dias.' dias.';
        $sx .= '<br><span style="color: red"><b>ERRO</b></span> - Erro na Coletada.';

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
            if ($line['jnl_frbr'] == 0)
                {
                    $sx .= $this -> frbr -> journal_manual($line['id_jnl'],$line['jnl_frbr']);
                }
        } else {
            $sx .= '<br>';
            $sx .= $this -> frbr -> journal_manual($line['id_jnl'],$line['jnl_frbr']);
        }

        $sx .= '<br>WebSemantic (Brapci):'.$line['jnl_frbr'];


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
            if ($line['c_class'] == 'hasIssueProceeding') {
                $n = $line['n_name'] . '#' . $line['d_r2'];
                array_push($ar, $n);
            }
            if ($line['c_class'] == 'hasIssueProceedingOf') {
                $n = $line['n_name'] . '#' . $line['d_r2'];
                array_push($ar, $n);
            }
        }
        rsort($ar);

        $sx = '';
        $ed = 0;
        $xano = '';
        $sx = '<table class="table">';
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
                $sx .= '<td style="background-color: #f0f0f0;">';
                $ed++;
            }
            $sx .= '<a href="' . base_url(PATH . 'v/' . $idx) . '">';
            $sx .= '<div style="width: 120px; text-align: center; height: 60px; border: 1px solid #808080; margin: 1px 5px; float: left;">';
            $sx .= $name_use;
            $sx .= '</div>';
            $sx .= '</a>';

        }
        $sx .= '</td>';
        $sx .= '</tr>';
        $sx .= '</table>';

        return ($sx);
    }

}
