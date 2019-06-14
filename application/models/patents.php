<?php
class patents extends CI_model {

    function le($id)
        {
            $sql = "select * from patent where id_p = $id";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $line = $rlt[0];            
            return($line);
        }

    function view($id) 
        {
            $line = $this->le($id);
            $sx = '<table class="table">';
            for ($r=0;$r < 10;$r++)
                {
                    $sx .= '<td width="10%">';
                }
            $sx .= '<tr>';
            $sx .= '<td colspan=2>';
            $sx .= $line['p_nr'];
            $sx .= '</td>';
            $sx .= '<td colspan=8>';
            $sx .= '<b>'.$line['p_title'].'<b>';
            $sx .= '</td>';
            $sx .= '</tr>';
            $sx .= '</table>';
            return($sx);
        }

    function import() {
        $sx = '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();
        $sx .= '<h3>Harvesting</h3>';
        $sx .= $this -> process();
        $sx .= '</div>';
        $sx .= '</div>';
        return ($sx);
    }

    function issue($dta) {
        $year = $dta['year'];
        $num = $dta['num'];
        $jid = $dta['jid'];
        $pub = $dta['pusblished'];
        $year = $dta['year'];

        $sql = "select * from patent_issue
                        where issue_source = $jid
                            AND issue_year = '$year'
                            AND issue_number = '$num'
            ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into patent_issue
                                (issue_source, issue_year, issue_number, issue_published)
                                values
                                ('$jid','$year','$num','$pub')";
            $rlti = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        $id_issue = $rlt[0]['id_issue'];
    }

    function method_inpi($file) {
        $cnt = file_get_contents($file);
        $cnt = troca($cnt, '-', '_');
        $xml = simplexml_load_string($cnt);

        $rst = $this -> xml_read($xml, '');
        if (isset($rst['numero'])) {
            $id = $rst['numero'];
            $dt['year'] = substr(sonumero($rst['dataPublicacao']), 4, 4);
            $dt['num'] = sonumero($rst['numero']);
            $dt['vol'] = '';
            $dt['pusblished'] = brtos($rst['dataPublicacao']);
            $dt['jid'] = '1';
            $issue = $this -> issue($dt);
            $data = $dt['pusblished'];

        } else {
            return ("ERROR");
        }

        $is = $xml;
        /* despachos */
        $debug = 0;
        foreach ($is as $key => $value) {
            $p = array();
            $p['section'] = (string)$value -> codigo;
            $p['section_title'] = (string)$value -> titulo;

            /************************************************************* PROCESSO PATENTE *****/
            if ($debug == 1) { echo '<br>Processo Patente';
            }
            $pp = $value -> processo_patente;
            $num = $this -> xml_read($pp -> numero);

            $p['patent_nr'] = troca($num[0], '_', '-');
            if (isset($num['kindcode'])) {
                $p['patent_nr_kindcode'] = (string)$num['kindcode'];
            }
            $p['patent_nr_inid'] = (string)$num['inid'];

            /* Título da Patente */
            if ($debug == 1) { echo '<br>Título da Patente';
            }
            if (isset($pp -> titulo)) {
                $dt = $this -> xml_read($pp -> titulo);
                $p['patent_titulo'] = troca($dt[0], '_', '-');
                $p['patent_titulo_inid'] = $dt['inid'];
            }

            /* Data do depósito */
            if ($debug == 1) { echo '<br>Data depósito';
            }
            if (isset($pp -> data_deposito)) {
                $dt = $this -> xml_read($pp -> data_deposito);
                $p['patent_nr_deposit_date'] = $dt[0];
                $p['patent_nr_deposit_date_inid'] = $dt['inid'];
            }

            /* Data fase nacional */
            if ($debug == 1) { echo '<br>Data fase nacional';
            }
            if (isset($pp -> data_fase_nacional)) {
                $dt = $this -> xml_read($pp -> data_fase_nacional);
                $p['patent_fase_nacional'] = $dt[0];
                $p['patent_fase_nacional_inid'] = $dt['inid'];
            }

            /* Pedido Internacional */
            if ($debug == 1) { echo '<br>Pedido Internacional';
            }
            if (isset($pp -> pedido_internacional)) {
                $dt = $this -> xml_read($pp -> pedido_internacional);
                $p['pedido_internacional_inid'] = $dt['inid'];
                $p['pedido_internacional_numero_pct'] = (string)$pp -> pedido_internacional -> numero_pct;
                $p['pedido_internacional_numero_pct_data'] = (string)$pp -> pedido_internacional -> data_pct;
            }

            /* Publicação Internacional */
            if ($debug == 1) { echo '<br>Publicação Internacional';
            }
            if (isset($pp -> publicacao_internacional)) {
                $dt = $this -> xml_read($pp -> publicacao_internacional);
                $p['publicacao_internacional_inid'] = $dt['inid'];
                $p['publicacao_internacional_numero_ompi'] = (string)$pp -> publicacao_internacional -> numero_ompi;
                $p['publicacao_internacional_numero_ompi_data'] = (string)$pp -> publicacao_internacional -> data_ompi;
            }

            /* Classificação Internacional */
            $classes = array();
            if ($debug == 1) { echo '<br>Classificação Internacional';
            }
            if (isset($pp -> classificacao_internacional_lista)) {
                $dt = $pp -> classificacao_internacional_lista -> classificacao_internacional;
                for ($q = 0; $q < count($dt); $q++) {
                    $dta = $this -> xml_read($dt[$q]);
                    $dtt = array();
                    $dtt['cip_inid'] = $dta['inid'];
                    $dtt['cip_seq'] = $dta['sequencia'];
                    $dtt['cip_ano'] = $dta['ano'];
                    $dtt['cip_classe'] = (string)$dt[$q][0];
                    array_push($classes, $dtt);
                }
                $p['classificacao_internacional'] = $classes;
            }

            /** prioridade-unionista-lista */
            if ($debug == 1) { echo '<br>Prioridade Unionista ';
            }
            if (isset($pp -> prioridade_unionista_lista)) {
                $dt = $pp -> prioridade_unionista_lista;

                for ($q = 0; $q < count($dt -> prioridade_unionista); $q++) {

                    $dtx = $dt -> prioridade_unionista[$q];
                    $dta = $this -> xml_read($dtx);

                    $dtt = array();
                    $dtt['prior_inid'] = $dta['inid'];
                    $dtt['prior_seq'] = $dta['sequencia'];

                    $dta = $this -> xml_read($dt -> prioridade_unionista[$q] -> sigla_pais);
                    $dtt['prior_sigla_pais'] = $dta[0];
                    $dtt['prior_sigla_pais_inid'] = $dta['inid'];

                    $dta = $this -> xml_read($dt -> prioridade_unionista[$q] -> numero_prioridade);
                    $dtt['prior_numero_prioridade'] = $dta[0];
                    $dtt['prior_numero_prioridade_inid'] = $dta['inid'];

                    $dta = $this -> xml_read($dt -> prioridade_unionista[$q] -> data_prioridade);
                    $dtt['prior_data_prioridade'] = $dta[0];
                    $dtt['prior_data_prioridade_inid'] = $dta['inid'];
                    $p['prioridade_unionista'][$q] = $dtt;
                }
            }

            /* Divisao Pedido */
            if ($debug == 1) { echo '<br>Divisao Pedido';
            }
            if (isset($pp_divisao_pedido)) {
                $dt = $this -> xml_read($pp -> divisao_pedido);
                $p['patent_nr_divisao_pedido_inid'] = $dt['inid'];
                $dt = $this -> xml_read($pp -> divisao_pedido -> data_deposito);
                $p['patent_nr_divisao_pedido_deposito_data'] = $dt[0];
                $dt = $this -> xml_read($pp -> divisao_pedido -> numero);
                $p['patent_nr_divisao_pedido_numero'] = $dt[0];
            }

            /* titulares */
            if ($debug == 1) { echo '<br>Titulares';
            }
            $titular = array();
            if (isset($value -> processo_patente -> titular_lista)) {
                $dt = $value -> processo_patente -> titular_lista;
                for ($r = 0; $r < count($dt); $r++) {
                    $dd = $dt[$r];
                    $titular[$r]['nome'] = (string)$dd -> titular -> nome_completo;
                    if (isset($titular[$r]['nome_pais'])) {
                        $titular[$r]['nome_pais'] = (string)$dd -> titular -> endereco -> pais -> sigla;
                    }
                    if (isset($dd -> titular -> endereco -> uf)) {
                        $titular[$r]['nome_endereco_uf'] = (string)$dd -> titular -> endereco -> uf;
                    }
                }
            }
            $p['titular'] = $titular;

            /* Inventores */
            if ($debug == 1) { echo '<br>Inventor';
            }
            $inventor = array();
            if (isset($value -> processo_patente -> inventor_lista)) {
                $dt = $value -> processo_patente -> inventor_lista;
                for ($r = 0; $r < count($dt); $r++) {
                    $dd = $dt[$r];
                    $inventor[$r]['nome'] = (string)$dd -> inventor -> nome_completo;
                    //$titular[$r]['nome_pais'] = (string)$dd -> inventor -> endereco -> pais -> sigla;
                }
            }
            $p['inventor'] = $inventor;

            /* comentarios */
            if (isset($value -> comentario)) {
                $dt = $this -> xml_read($value -> comentario);
                $p['comentario'] = $dt[0];
                $p['comentario_indi'] = $dt['inid'];
            }
            echo $this -> process($p, $data);
        }
        return ("");
    }

    function harvest_patent($id) {
        /*********** pdf ************************************************/
        $file = '_repository_patent/inpi/pdf/Patent-' . strzero($id, 5) . 'pdf';
        if (!file_exists($file)) {
            $url = "http://revistas.inpi.gov.br/pdf/Patentes" . $id . ".pdf";
            $rcn = file_get_contents($url);

            /* Save */
            $rsc = fopen($file, 'w+');
            fwrite($rsc, $rcn);
            fclose($rsc);
        }

        /*********** pdf ************************************************/
        $file = '_repository_patent/inpi/zip/Patent-' . strzero($id, 5) . '.zip';
        if (!file_exists($file)) {
            $url = "http://revistas.inpi.gov.br/txt/P" . $id . ".zip";
            $rcn = file_get_contents($url);

            /* Save */

            $rsc = fopen($file, 'w+');
            fwrite($rsc, $rcn);
            fclose($rsc);
        }

        $zip = new ZipArchive;
        if ($zip -> open($file) === TRUE) {
            $zip -> extractTo($file = '_repository_patent/inpi/txt');
            $zip -> close();
            echo 'UNZIP ' . $file . ' success!' . cr();
        } else {
            echo 'failed';
        }

        return ("");
    }

    function check_diretory() {
        $dir = '_repository_patent';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $dir .= '/inpi';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $dira = $dir . '/pdf';
        if (!is_dir($dira)) {
            mkdir($dira);
        }

        $dira = $dir . '/zip';
        if (!is_dir($dira)) {
            mkdir($dira);
        }
    }

    function harvesting() {

        $id = 2527;

        $this -> check_diretory();
        $this -> harvest_patent($id);

        $sql = "select * from patent_source limit 1";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line = $rlt[0];

        $method = $line['ps_method'];
        switch($method) {
            case 'INPI' :
                $file = '_repository_patent/inpi/txt/Patente_' . $id . '.xml';
                $sx = $this -> method_inpi($file);
                return ($sx);
                break;
        }
        return ("KO");
    }

    function repository_list() {

    }

    public function xml_read($x, $vl = '') {
        $v = array();
        if (strlen($vl) == 0) {
            $sr = $x;
        } else {
            $sr = $x -> vl;
        }

        foreach ($sr as $key => $value) {
            $vlr = trim((string)$value);
            if (strlen($vlr) > 0) {
                array_push($v, (string)$value);
            }
            /******************* atributes *************/
        }
        foreach ($sr->attributes() as $a => $b) {
            $v[$a] = (string)$b;
        }
        return ($v);
    }

    function history($data, $d, $id) {
        if (isset($d['comentario']) and (strlen($d['comentario']) > 0)) {
            $cot = troca($d['comentario'],"'",'´');
            $sec = $d['section'];
            $data = substr($data, 0, 4) . '-' . substr($data, 4, 2) . '-' . substr($data, 6, 2);
            $sql = "select * from patent_history 
                                    where h_patent = $id 
                                        and h_section = '$sec' 
                                        and h_comment = '$cot' 
                                        and h_data = '$data'";
            echo $sql;
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            if (count($rlt) == 0) {
                $sql = "insert into patent_history 
                                (h_patent, h_section, h_comment, h_data, h_method)
                                values
                                ('$id','$sec','$cot','$data','INPI')";
                $rlt = $this -> db -> query($sql);
            }
        }
        return (1);
    }

    function process($d, $data) {
        $sx = '';
        if (isset($d['patent_nr'])) {
            $id = $this -> patent($d);
            $sx .= cr() . $d['patent_nr'] . ' process (' . $id . ')<br>';

            /************************ History ******************/
            $this -> history($data, $d, $id);

            echo '<pre>';
            print_r($d);
            //exit ;

            /****************** Titulares **********************/
            $tit = $d['titular'];
            for ($r = 0; $r < count($tit); $r++) {
                $line = $tit[$r];
                $pais = '';
                $estado = '';
                $name = $line['nome'];
                if (isset($line['pais'])) { $pais = $line['pais'];
                }
                if (isset($line['estado'])) { $estado = $line['pais'];
                }
                $ida = $this -> agent($name, $pais, $estado);
            }
            /****************** Autores **********************/
            $tit = $d['titular'];
            for ($r = 0; $r < count($tit); $r++) {
                $line = $tit[$r];
                $pais = '';
                $estado = '';
                $name = $line['nome'];
                if (isset($line['pais'])) { $pais = $line['pais'];
                }
                if (isset($line['estado'])) { $estado = $line['pais'];
                }
                $ida = $this -> agent($name, $pais, $estado);
            }
            /****************** Inventor **********************/
            $tit = $d['inventor'];
            for ($r = 0; $r < count($tit); $r++) {
                $line = $tit[$r];
                print_r($line);
                echo '<hr>';
                $pais = '';
                $estado = '';
                $name = $line['nome'];
                if (isset($line['pais'])) { $pais = $line['pais'];
                }
                if (isset($line['estado'])) { $estado = $line['pais'];
                }
                $ida = $this -> agent($name, $pais, $estado);
            }
        }
        return ($sx);
    }

    function agent($name = '', $country = '', $state = '') {
        $name = troca($name, "'", '´');
        $sql = "select * from patent_agent where pa_nome = '$name'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into patent_agent
                            (pa_nome, pa_pais, pa_estado)
                            value
                            ('$name','$country','$state')";
            $rlti = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        $line = $rlt[0];
        $id = $line['id_pa'];
        return ($id);
    }

    function patent($d) {
        /* VARIAVEIS */
        $pat_nr = troca($d['patent_nr'], ' ', '');
        $pat_dd = '0000-00-00';
        $pat_title = '';

        if (isset($d['patent_nr_deposit_date'])) {
            $pat_dd = brtos($d['patent_nr_deposit_date']);
        }
        if (isset($d['patent_titulo'])) {
            $pat_title = utf8_decode($d['patent_titulo']);
            $pat_title = strtolower($pat_title);
            $pat_title = troca($pat_title, '"', '');
            $pat_title = troca($pat_title, "'", '´');
            $pat_title = strtoupper(substr($pat_title, 0, 1)) . substr($pat_title, 1, strlen($pat_title));
            $pat_title = utf8_encode($pat_title);
        }

        /**************** recupera patent ****************************/
        $sql = "select * from patent where p_nr = '" . $pat_nr . "'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into patent
                                (p_nr, p_dt_deposito)
                                value
                                ('$pat_nr', '$pat_dd')";
            $rlti = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        $line = $rlt[0];
        $idp = $line['id_p'];

        /* UPDATES */
        $set = '';
        if ((strlen($line['p_title']) == 0) and ($pat_title != '')) {
            if (strlen($set) > 0) { $set .= ', ';
            }
            $set .= 'p_title = "' . $pat_title . '" ';
        }

        if ((strlen($line['p_dt_deposito']) == '0000-00-00') and ($pat_dd != '0000-00-00')) {
            if (strlen($set) > 0) { $set .= ', ';
            }
            $set .= 'p_dt_deposito = "' . $pat_dd . '" ';
        }

        /********************************** SALVA NO BANCO DE DADOS ************/
        if (strlen($set) > 0) {
            $sqli = "update patent set " . $set . " where id_p = " . $idp;
            $rlti = $this -> db -> query($sqli);
            echo $sqli . cr() . '<br>';
        }
        return ($idp);
    }

}
?>