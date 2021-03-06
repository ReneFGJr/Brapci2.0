<?php
class patents extends CI_model {
    var $sz = 20;
    var $s = '1';
    var $wait = 0.9;

    function check($id=0)
    {
        $dd = get("dd1");
        switch ($dd)
            {
                default:
                $sx = $this->check_names($id);
                break;

                ### Checa duplicatas das patentes
                case '1':
                $sx = $this->check_duplicatas($id);
                break;
            }
        
        
        return($sx);
    }

    function check_duplicatas()
    {
        $sx = '';
        /**** Consulta SQL ********************/
        $sql = "select * from (
        select p_nrn, count(*) as total FROM patent.patent 
            where not p_nrn like '%*%'
            group by p_nrn
        ) as tabela
        where total > 1 and p_nrn <> ''
        limit 1000";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $np = $line['p_nrn'];            

            /**** Consulta SQL ********************/
            $sql = "select * from patent.patent 
                        where p_nrn = '".$np."'";
            echo cr().date("Ymd-H:i:s").' 1. '.$np.' ('.$r.'/'.count($rlt).') ##############';
            $xrlt = $this->db->query($sql);
            $xrlt = $xrlt->result_array();
            echo cr().date("Ymd-H:i:s").' 1. '.$np.' RESPONSE';
            $ids = array();
            $title = '';
            $abstract = '';
            for ($x=0;$x < count($xrlt);$x++)
            {
                $xline = $xrlt[$x];
                if (strlen($xline['p_title']) > 0)
                {
                    if (strlen($title) > 0)
                    {
                        if (strpos($title,'?') > 0)
                        {
                            $title = $xline['p_title'];    
                        }
                    } else {
                        $title = $xline['p_title'];
                    }                    
                }
                if (strlen($xline['p_resumo']) > 0)
                {
                    $abstract = $xline['p_resumo'];
                }
                array_push($ids,$xline['id_p']);  
                //$sx .= $xline['id_p'].' ';
            }
            
            $idi = $ids[0];
            for ($x=0;$x < count($ids);$x++)
            {
                echo cr().date("Ymd-H:i:s").' 1A. IDS '.$ids[$x]. ' ====';
                ob_flush();
                flush();

                $compl = '';
                if ($x > 0)
                {
                    $compl = ", p_nr = concat('*',p_nr), p_nrn = concat('*',p_nrn) ";
                    $sql = "update patent.patent_agent_relation set rl_patent = $idi where rl_patent = ".$ids[$x];
                    $xrlt = $this->db->query($sql);
                    echo cr().date("Ymd-H:i:s").' 2. ';

                    $sql = "update patent.patent_classification set c_patent = $idi where c_patent = ".$ids[$x];
                    $xrlt = $this->db->query($sql);
                    echo cr().date("Ymd-H:i:s").' 3. ';

                    $sql = "update patent.patent_despacho set pd_patent = $idi where pd_patent = ".$ids[$x];
                    $xrlt = $this->db->query($sql);
                    echo cr().date("Ymd-H:i:s").' 4. ';

                    $sql = "update patent.patent_pct set pct_patent = $idi where pct_patent = ".$ids[$x];
                    $xrlt = $this->db->query($sql);  
                    echo cr().date("Ymd-H:i:s").' 5. ';                                      

                    $sql = "update patent.patent_prioridade set prior_patent = $idi where prior_patent = ".$ids[$x];
                    $xrlt = $this->db->query($sql);  
                    echo cr().date("Ymd-H:i:s").' 6. ';                                      

                    $sql = "update patent.patent_pub set pub_patent = $idi where pub_patent = ".$ids[$x];
                    $xrlt = $this->db->query($sql);                                        
                    echo cr().date("Ymd-H:i:s").' 7. ';
                }
                $sql = "update patent.patent set p_title = '$title', p_resumo = '$abstract' $compl where id_p = ".$ids[$x];
                $xrlt = $this->db->query($sql);
                echo cr().date("Ymd-H:i:s").' 8. ';
            }
        }  

        if (count($rlt) > 0)
        {
            //$sx .= '<meta http-equiv="refresh" content="5; url='.base_url(PATH.'check/0?dd1=1').'">';
        } else {
            //$sx .= '<meta http-equiv="refresh" content="5; url='.base_url(PATH.'check/0?dd1=2').'">';
        }   
        exit; 
        return($sx);          
    }

    function check_names()
    {
        /**** Consulta SQL ********************/
        $sql = "select * from patent.patent where p_nrn like '%-%' limit 100";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<ul>';
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $nr = trim($line['p_nrn']);
            $c = array("-");
            for ($y = 0;$y < count($c);$y++)
            {
                if (strpos($nr,$c[$y]) > 0)
                {
                    $nr = substr($nr,0,strpos($nr,'-'));        
                }                
            }
            $sql = "update patent.patent set p_nrn = '".$nr."' where id_p = ".$line['id_p'];
            $rltq = $this->db->query($sql);
            $sx .= '<li>'.$nr.' <span style="color: green"><b>Updated</b></span></li>';
        }        
        $sx .= '</ul>';
        if (count($rlt) > 0)
        {
            $sx .= '<meta http-equiv="refresh" content="5; url='.base_url(PATH.'check/0').'">';
        } else {
            $sx .= '<meta http-equiv="refresh" content="5; url='.base_url(PATH.'check/0?dd1=1').'">';
        }
        
        return($sx);
    }
    function check_duplicate() {
        $sql = "select * from (
        SELECT count(*) as total, max(id_prior) as id_prior, prior_seq, prior_numero_prioridade, prior_sigla_pais, prior_patent 
        FROM patent.patent_prioridade
        group by prior_seq, prior_numero_prioridade, prior_sigla_pais, prior_patent
        ) as tabela 
        where total > 1 limit 500";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $wh = "";
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            if (strlen($wh) > 0) {
                $wh .= ' OR ';
            }
            $wh .= '(id_prior = ' . $line['id_prior'] . ')';
        }
        if (strlen($wh) > 0) {
            echo cr() . "Excluindo " . count($rlt) . " repeticoes de prioridade";
            ob_flush();
            flush();
            $sql = "delete from patent.patent_prioridade where " . $wh;
            $rlt = $this -> db -> query($sql);
        }
        /************ Classificaction *************/
        $sql = "select * from (
        SELECT count(*) as total, max(id_c) as id_c, c_patent, c_c, c_cod 
        FROM patent.patent_classification
        group by c_patent, c_c, c_cod
        ) as tabela
        where total > 1 limit 500";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $wh = "";
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            if (strlen($wh) > 0) {
                $wh .= ' OR ';
            }
            $wh .= '(id_c = ' . $line['id_c'] . ')';
        }
        if (strlen($wh) > 0) {
            echo cr() . "Excluindo " . count($rlt) . " repeticoes de classificacao";
            ob_flush();
            flush();
            $sql = "delete from patent.patent_classification where " . $wh;
            $rlt = $this -> db -> query($sql);
        }
        /********************************* autoria **************************/
        $sql = "select * from (
        SELECT count(*) as total, max(id_rl) as id_rl, rl_patent, rl_agent, rl_relation 
        FROM patent.patent_agent_relation
        group by rl_patent, rl_agent, rl_relation
        ) as tabela 
        where total > 1
        limit 500";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $wh = "";
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            if (strlen($wh) > 0) {
                $wh .= ' OR ';
            }
            $wh .= '(id_rl = ' . $line['id_rl'] . ')';
        }
        if (strlen($wh) > 0) {
            echo cr() . "Excluindo " . count($rlt) . " repeticoes de agentes";
            ob_flush();
            flush();
            $sql = "delete from patent.patent_agent_relation where " . $wh;
            $rlt = $this -> db -> query($sql);
        }
    }

    function harvesting($id = '') {
        $debug = 0;
        $t1 = date("d/m/Y H:i:s");

        $h = array('00', '02', '04', '06', '08', '12', '16', '20', '22');
        $auth = 0;
        for ($r = 0; $r < count($h); $r++) {
            if ($h[$r] == date("H")) { $auth = 1;
            }
        }
        if ($id == '1') { $auth = 1;
            $id = '';
        }

        if ($auth == 0) {
            return ("Hora não autorizada!");
        }
        /*************************/
        $method = 'NRML';

        /**************************/
        if (strlen($id) == 0) {
            $sql = "select * from patent.patent_source where ps_status = 1
            ORDER BY ps_last_harvesting
            limit 1";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            if (count($rlt) == 0) {
                echo cr() . "Nada para coletar";
            }
            $line = $rlt[0];
            $id = $line['ps_issue'] + 1;
            $ids = $line['id_ps'];
            $method = $line['ps_method'];
        }
        $this -> check_diretory();

        $ok = 0;
        switch($method) {
            case 'INPI' :
            echo "================================" . cr();
            echo $id . cr();
            echo "================================" . cr();

            $this -> harvest_patent($id);
            $file = '_repository_patent/inpi/txt/Patente_' . $id . '.xml2';
            if (file_exists($file)) {
                echo '# Method 1' . cr();
                $sx = $this -> method_inpi($file);
                $ok = 1;
            } else {
                $file = '_repository_patent/inpi/txt/P' . $id . '.txt';
                if (file_exists($file)) {
                    echo '# Method 2' . cr();
                    $sx = $this -> method_inpi_txt($file);
                    $ok = 1;
                } else {
                    echo '# Method 3' . cr();
                    $file = '_repository_patent/inpi/txt/P' . $id . '.TXT';
                    if (file_exists($file)) {
                        $sx = $this -> method_inpi_txt($file);
                        $ok = 1;
                    } else {
                        echo '-File not found-' . $file;
                    }
                }
            }
            break;
        }
        echo cr() . '#########FIM DO PROCESSO#############';
        if ($ok == 1) {
            $sql = "update patent.patent_source set 
            ps_last_harvesting = '" . date("Y-m-d") . "',
            ps_issue = ($id)
            where id_ps = $ids ";
            $rlt = $this -> db -> query($sql);
        }
        $t2 = date("d/m/Y H:i:s");
        echo cr() . $t1;
        echo cr() . $t2;
    }

    function relatorio($id = 0) {
        $sql = "select tp, ano, count(*) as total from ( SELECT substr(p_nr,3,2) as tp, substr(p_nr,5,4) as ano FROM `patent` where p_nr like 'BR%' ) as tabela group by tp, ano order by ano desc,tp";
        $sql = "SELECT count(*) as total FROM patent";
        $sql = "select * from (
        SELECT count(*) as total, max(id_prior) as max, prior_seq, prior_numero_prioridade, prior_sigla_pais, prior_patent FROM `patent_prioridade`
        group by prior_seq, prior_numero_prioridade, prior_sigla_pais, prior_patent
        ) as tabela 
        where total > 1";
    }

    function le($id) {
        $sql = "select * from patent.patent where id_p = $id";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line = $rlt[0];

        /* Relacao Agente */
        $sql = "select * from patent.patent_agent_relation
        INNER JOIN patent.patent_agent ON rl_agent = id_pa
        WHERE rl_patent = $id order by rl_relation desc, rl_seq";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line['relacao'] = $rlt;

        /* Relacao Agente */
        $sql = "select * from patent.patent_despacho
        INNER JOIN patent.patent_issue ON pd_issue = id_issue
        LEFT  JOIN patent.patent_section ON pd_section = ps_acronic
        WHERE pd_patent = $id 
        order by issue_number desc, pd_section";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line['despacho'] = $rlt;

        /* patent_classification */
        $sql = "select * from patent.patent_classification
        LEFT JOIN patent.patent_class ON cc_class = c_c
        WHERE c_patent = $id 
        order by c_c";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line['classificacao'] = $rlt;

        /* prioritaria */
        $sql = "select * from patent.patent_prioridade
        LEFT JOIN patent.patent_pais_sigla on prior_sigla_pais = ps_sigla
        WHERE prior_patent = $id 
        order by prior_seq";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $line['prioritario'] = $rlt;

        return ($line);
    }

    function view($id) {
        $line = $this -> le($id);
        //echo '<pre>';
        //print_r($line);
        //echo '</pre>';
        $sx = '<table class="table">';
        for ($r = 0; $r < 10; $r++) {
            $sx .= '<td width="10%">';
        }
        /* 021 */
        $sx .= '<tr class="small">';
        $sx .= '<td colspan=2 align="center">';
        $sx .= msg('nr_pedido');
        $sx .= ' (21)';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center">';
        $sx .= msg('data_deposito');
        $sx .= ' (22)';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center">';
        $sx .= msg('data_publicacao');
        $sx .= ' (43)';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center">';
        $sx .= msg('data_concessao');
        $sx .= ' (47)';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center">';
        $sx .= msg('p_fase_nacional');
        $sx .= ' (85)';
        $sx .= '</td>';
        $sx .= '</tr>';

        $sx .= '<tr>';
        $sx .= '<td colspan=2 align="center"  style="border: 1px solid #000000;">';
        $sx .= '<b>' . $line['p_nr'] . '</b>';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center"  style="border: 1px solid #000000;">';
        $sx .= '<b>' . stodbr($line['p_dt_deposito']) . '</b>';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center"  style="border: 1px solid #000000;">';
        $sx .= '<b>' . stodbr($line['p_dt_publicacao']) . '</b>';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center"  style="border: 1px solid #000000;">';
        $sx .= '<b>' . stodbr($line['p_dt_concessao']) . '</b>';
        $sx .= '</td>';

        $sx .= '<td colspan=2 align="center"  style="border: 1px solid #000000;">';
        $sx .= '<b>' . stodbr($line['p_fase_nacional']) . '</b>';
        $sx .= '</td>';

        $sx .= '</tr>';

        /* 022 e 054 */
        $sx .= '<tr class="small">';
        $sx .= '<td colspan=2 align="left">';
        $sx .= msg('patente_titulo_resumo');
        $sx .= ' (54)';
        $sx .= '</td>';
        $sx .= '</tr>';

        $sx .= '<tr>';
        $sx .= '<td colspan=10 style="border: 1px solid #000000;">';
        $sx .= '<span style="font-size: 24px;">';
        if (strlen($line['p_title']) == 0) {
            $sx .= '(sem informação)&nbsp;';
        } else {
            $sx .= '<b>' . strtoupper($line['p_title']) . '</b>';
        }
        $sx .= '</span>' . cr();

        if (strlen($line['p_resumo']) > 0) {
            $sx .= '<hr>' . '<p>' . $line['p_resumo'] . '</p>';
        }

        $sx .= '</td>';
        $sx .= '</tr>';

        /* 72 e 71 - Inventor e Depositante */
        $sx .= '<tr class="small">';
        $sx .= '<td colspan=10 align="left">';
        $sx .= ' ';

        $sx .= '</td>';
        $sx .= '</tr>';

        $sx .= '<tr>';
        $sx .= '<td colspan=5  style="border: 1px solid #000000;">';
        $inv = 0;
        $dep = 0;
        if (count($line['relacao']) > 0) {
            $sx .= '<h6>' . msg('nome_do_inventor') . '<sup>(72)</h6>' . cr();
        }
        $sx .= '<ol>';
        for ($r = 0; $r < count($line['relacao']); $r++) {
            $xline = $line['relacao'][$r];
            if ($xline['rl_relation'] == 'I') {
                $sx .= '<li><b>' . $xline['pa_nome'] . '</b> ';

                if (strlen($xline['pa_pais'])) {
                    $sx .= '(';
                    $sx .= $xline['pa_pais'];
                    if (strlen($xline['pa_estado']) > 0) {
                        $sx .= ', ' . $xline['pa_estado'];
                    }
                    $sx .= ')';
                }
                $sx .= '</li>';
            }
        }
        $sx .= '</ol>';

        if (count($line['relacao']) > 0) {
            $sx .= '<h6>' . msg('nome_do_titular') . '<sup>(73)</sup></h6>' . cr();
        }
        $sx .= '<ol>';
        for ($r = 0; $r < count($line['relacao']); $r++) {
            $xline = $line['relacao'][$r];
            if ($xline['rl_relation'] == 'T') {
                $sx .= '<li><b>' . $xline['pa_nome'] . '</b> ';

                if (strlen($xline['pa_pais'])) {
                    $sx .= '(';
                    $sx .= $xline['pa_pais'];
                    if (strlen($xline['pa_estado']) > 0) {
                        $sx .= ', ' . $xline['pa_estado'];
                    }
                    $sx .= ')';
                }
                $sx .= '</li>';
            }
        }
        $sx .= '</ol>';
        $sx .= '</td>';

        $sx .= '<td colspan=5  style="border: 1px solid #000000;">';
        $inv = 0;
        $dep = 0;
        if (count($line['relacao']) > 0) {
            $sx .= '<h6>' . msg('nome_do_depositante') . '<sup>(71)</h6></sup>' . cr();
        }
        $sx .= '<ol>';
        for ($r = 0; $r < count($line['relacao']); $r++) {
            $xline = $line['relacao'][$r];
            if ($xline['rl_relation'] == 'D') {
                $sx .= '<li><b>' . $xline['pa_nome'] . '</b> ';

                if (strlen($xline['pa_pais'])) {
                    $sx .= '(';
                    $sx .= $xline['pa_pais'];
                    if (strlen($xline['pa_estado']) > 0) {
                        $sx .= ', ' . $xline['pa_estado'];
                    }
                    $sx .= ')';
                }
                $sx .= '</li>';
            }
        }
        $sx .= '</ol>';

        if (count($line['relacao']) > 0) {
            $sx .= '<h6>' . msg('nome_do_procurador') . '<sup>(74)</sup></h6>' . cr();
        }
        $sx .= '<ol>';
        for ($r = 0; $r < count($line['relacao']); $r++) {
            $xline = $line['relacao'][$r];
            if ($xline['rl_relation'] == 'P') {
                $sx .= '<li><b>' . $xline['pa_nome'] . '</b> ';

                if (strlen($xline['pa_pais'])) {
                    $sx .= '(';
                    $sx .= $xline['pa_pais'];
                    if (strlen($xline['pa_estado']) > 0) {
                        $sx .= ', ' . $xline['pa_estado'];
                    }
                    $sx .= ')';
                }
                $sx .= '</li>';
            }
        }
        $sx .= '</ol>';

        $sx .= '</td>';
        $sx .= '</tr>';

        /*************** PRIORITARIO *********************************************/
        $desp = $line['prioritario'];
        $sx .= '<tr><td colspan=10><h3>' . msg('prioritario') . '</h3></td></tr>';

        $sx .= '<tr align="center">';
        $sx .= '<th colspan=2 align="center">' . msg('pct') . '</th>';
        $sx .= '<th colspan=2 align="center">' . msg('pct_data') . '</th>';
        $sx .= '<th colspan=2 align="center">' . msg('pub') . '</th>';
        $sx .= '<th colspan=2 align="center">' . msg('pub_data') . '</th>';
        $sx .= '<th colspan=2 align="center"></th>';
        $sx .= '</tr>';

        $sx .= '<tr align="center">';
        $sx .= '<td colspan=2" style="border: 1px solid #000000;">' . $line['p_pct'] . '</td>';
        $sx .= '<td colspan=2" style="border: 1px solid #000000;">' . stodbr($line['p_pct_data']) . '</td>';
        $sx .= '<td colspan=2" style="border: 1px solid #000000;">' . $line['p_pub'] . '</td>';
        $sx .= '<td colspan=2" style="border: 1px solid #000000;">' . stodbr($line['p_pub_data']) . '</td>';
        $sx .= '<td colspan=2" style="border: 1px solid #000000;">-</td>';
        $sx .= '</tr>';

        $sx .= '<tr align="center">';
        $sx .= '<th colspan=1 align="center">' . msg('prior_seq') . '</th>';
        $sx .= '<th colspan=3 align="center">' . msg('prior_sigla_pais') . '</th>';
        $sx .= '<th colspan=3 align="center">' . msg('prior_numero_prioridade') . '</th>';
        $sx .= '<th colspan=3 align="center">' . msg('prior_data_prioridade') . '</th>';

        for ($r = 0; $r < count($desp); $r++) {
            $xline = $desp[$r];
            $sx .= '<tr>';
            $sx .= '<td align="center" colspan=1 align="center"  style="border: 1px solid #000000;">';
            $sx .= $xline['prior_seq'];
            $sx .= '</td>';
            $sx .= '<td align="center" colspan=3  style="border: 1px solid #000000;">';
            $sx .= $xline['ps_nome'];
            $sx .= ' (' . $xline['prior_sigla_pais'] . ')';
            $sx .= '</td>';

            $sx .= '<td align="center" colspan=3  style="border: 1px solid #000000;">';
            $sx .= $xline['prior_numero_prioridade'];
            $sx .= '</td>';

            $sx .= '<td align="center" colspan=3 style="border: 1px solid #000000;">';
            $sx .= stodbr($xline['prior_data_prioridade']);
            $sx .= '</td>';
            $sx .= '</tr>';
        }

        /*************** CLASSIFICACAO *********************************************/
        $desp = $line['classificacao'];
        $sx .= '<tr><td colspan=10><h3>' . msg('classificacao') . '</h3></td></tr>';
        $sx .= '<tr align="center">';
        $sx .= '<th colspan=2>' . msg('class') . '</th>';
        $sx .= '<th colspan=8>' . msg('descricao') . '</th>';

        for ($r = 0; $r < count($desp); $r++) {
            $xline = $desp[$r];
            $link = '<a href="https://www.uspto.gov/web/patents/classification/cpc/html/cpc-' . $xline['c_class'] . '.html" target="_new_' . $xline['c_class'] . '"">';
            $linka = '</a>';
            $l1 = strzero($xline['cc_c4'], 4);
            $l2 = strzero($xline['cc_c5'], 2);
            $l2 .= '0000';
            $link = '<a href="http://ipc.inpi.gov.br/ipcpub?notion=scheme&version=20190101&symbol=' . $xline['c_class'] . $l1 . $l2 . '&menulang=pt&lang=pt&viewmode=f&fipcpc=no&showdeleted=yes&indexes=no&headings=yes&notes=yes&direction=o2n&initial=A&cwid=none&tree=no&searchmode=smart" target="_new_' . $xline['c_class'] . '"">';
            $sx .= '<tr>';
            $sx .= '<td align="center" colspan=2  style="border: 1px solid #000000;">';
            $sx .= $link . $xline['cc_class'] . $linka;
            $sx .= '</td>';
            $sx .= '<td align="center" colspan=8  style="border: 1px solid #000000;">';
            $sx .= $xline['cc_name'];
            $sx .= '</td>';
            $sx .= '</tr>';
        }

        /*************** DESPACHO */
        $desp = $line['despacho'];
        $sx .= '<tr><td colspan=10><h3>' . msg('despachos') . '</h3></td></tr>';
        $sx .= '<tr align="center">';
        $sx .= '<th colspan=1>' . msg('issue') . '</th>';
        $sx .= '<th colspan=1>' . msg('data') . '</th>';
        $sx .= '<th colspan=1>' . msg('despacho') . '</th>';
        $sx .= '<th colspan=7>' . msg('comentario') . '</th>';

        for ($r = 0; $r < count($desp); $r++) {
            $xline = $desp[$r];
            $sx .= '<tr>';
            $sx .= '<td align="center" colspan=1  style="border: 1px solid #000000;">';
            $sx .= $xline['issue_number'];
            $sx .= '</td>';
            $sx .= '<td align="center" colspan=1  style="border: 1px solid #000000;">';
            $sx .= stodbr($xline['issue_published']);
            $sx .= '</td>';
            $sx .= '<td colspan=1 align="center"  style="border: 1px solid #000000;">';
            $sx .= $xline['pd_section'];
            $sx .= '</td>';
            $sx .= '<td colspan=7  style="border: 1px solid #000000;">';
            if (strlen($xline['pd_comentario']) == 0) {
                $sx .= troca($xline['ps_name'], '_', '-');
            } else {
                $sx .= troca($xline['ps_name'], '_', '-');
                $sx .= '<br>' . '<span style="color: #0000ff">' . troca($xline['pd_comentario'], '_', '-') . '</span>';
            }

            $sx .= '</td>';
            $sx .= '</tr>';
        }

        $sx .= '</table>';
        return ($sx);
    }

    function issue($dta) {
        $year = $dta['year'];
        $num = $dta['num'];
        $jid = $dta['jid'];
        $pub = $dta['pusblished'];
        $year = $dta['year'];

        $sql = "select * from patent.patent_issue
        where issue_source = $jid
        AND issue_year = '$year'
        AND issue_number = '$num'
        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into patent.patent_issue
            (issue_source, issue_year, issue_number, issue_published)
            values
            ('$jid','$year','$num','$pub')";
            $rlti = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            sleep($this -> wait);
        }
        $id_issue = $rlt[0]['id_issue'];
        return ($id_issue);
    }

    function method_inpi_txt($file) {
        $debug = 0;

        $fn = fopen($file, "r");
        $l = 0;
        $cd = '';
        $p = array();
        $max = 0;
        $cmdo = '';
        while (!feof($fn)) {
            $l++;
            $result = fgets($fn);
            $result = utf8_encode($result);
            $result = troca($result, '~', '');
            $result = troca($result, '^', '');
            $result = troca($result, '$', '');
            $result = troca($result, 'ˆ', '');

            $rs = '';
            for ($r = 0; $r < strlen($result); $r++) {
                $o = ord($result[$r]);
                if (($o > 128) or ($o < 32)) {
                    $rs .= $result[$r];
                } else {
                    $rs .= $result[$r];
                }
            }
            $result = $rs;
            $ln = $result;
            $result = troca($result,"'","´");
            //echo '<br>' . $l . ' - ' . $result;

            /* ISSUE */
            if ($l == 1) {
                $tp = substr(trim($result), 0, 3);
                $nr = substr(trim($result), 0, strpos(trim($result), ' '));
                if ($nr == sonumero($nr)) {
                    $tp = 'NRS';
                }
                echo $result.cr();
                switch ($tp) {
                    case 'No ' :
                    $result = trim($result);                    
                    $numero = sonumero(substr($result,3,4));
                    $ano = round(substr($result, strlen($result) - 4, 4));
                    $dta = substr($result, strlen($result) - 10, 10);
                    $dt['year'] = $ano;
                    $dt['num'] = $numero;
                    $dt['vol'] = '';
                    $dt['pusblished'] = brtos($dta);
                    $dt['jid'] = '1';
                    break;
                    case 'NRS' :
                    $result = trim($result);
                    $numero = sonumero($nr);
                    $ano = round(substr($result, strlen($result) - 2, 2));
                    if ($ano > 90) { $ano = 1900 + $ano;
                    }
                    $dta = substr($result, strlen($result) - 8, 6) . strzero($ano, 4);
                    $dt['year'] = $ano;
                    $dt['num'] = $numero;
                    $dt['vol'] = '';
                    $dt['pusblished'] = brtos($dta);
                    $dt['jid'] = '1';
                    break;
                    case 'REV' :
                    $result = trim($result);
                    $numero = sonumero(substr($result, 0, strpos($result, 'COM')));
                    $ano = round(substr($result, strlen($result) - 2, 2));
                    if ($ano > 90) { $ano = 1900 + $ano;
                    }
                    $dta = substr($result, strlen($result) - 8, 6) . strzero($ano, 4);
                    $dt['year'] = $ano;
                    $dt['num'] = $numero;
                    $dt['vol'] = '';
                    $dt['pusblished'] = brtos($dta);
                    $dt['jid'] = '1';
                    break;
                    case 'RPI' :
                    $result = trim($result);
                    if (substr($result, 0, 7) == 'RPI No,') {
                        $numero = sonumero(substr($result, 0, 13));
                    } else {
                        $numero = sonumero(substr($result, 0, 9));
                    }

                    $ano = round(substr($result, strlen($result) - 2, 2));
                    if ($ano > 90) { $ano = 1900 + $ano;
                    }
                    $dta = substr($result, strlen($result) - 8, 6) . strzero($ano, 4);
                    $dt['year'] = $ano;
                    $dt['num'] = $numero;
                    $dt['vol'] = '';
                    $dt['pusblished'] = brtos($dta);
                    $dt['jid'] = '1';
                    break;                        
                    case 'Rpi' :
                    $result = trim($result);
                    if (substr($result, 0, 6) == 'Rpi No') {
                        $numero = sonumero(substr($result, 0, 13));
                    } else {
                        $numero = sonumero(substr($result, 0, 9));
                    }

                    $ano = round(substr($result, strlen($result) - 2, 2));
                    if ($ano > 90) { $ano = 1900 + $ano;
                    }
                    $dta = substr($result, strlen($result) - 8, 6) . strzero($ano, 4);
                    $dt['year'] = $ano;
                    $dt['num'] = $numero;
                    $dt['vol'] = '';
                    $dt['pusblished'] = brtos($dta);
                    $dt['jid'] = '1';
                    break;
                    case 'No.' :
                    $dt['year'] = substr($result, 15, 4);
                    $dt['num'] = sonumero(substr($result, 4, 4));
                    $dt['vol'] = '';
                    $dt['pusblished'] = brtos(substr($result, 9, 10));
                    $dt['jid'] = '1';
                    break;
                    default :
                    echo 'ERRO DE REGISTRO==>' . $tp;
                    echo cr();
                    echo cr();
                    echo cr();
                    exit ;
                }
                $issue = $this -> issue($dt);
            }

            $cmd = substr($ln, 0, 4);
            if ((substr($cmd, 0, 1) != '(') and (substr($cmdo, 0, 1) == '(')) {
                $result = $cmdo . ' ' . $result;
                $cmd = $cmdo;
            } else {
                $cmdo = $cmd;
            }

            //echo $cmdo . '===' . $result . cr();
            //if ($l > 30) {
            //print_r($p);
            //exit ;
            //}

            $v = trim(substr($result, 4, strlen($result)));
            switch($cmd) {
                case '(Cd)' :
                $max = 0;
                /* Processar linha */
                if (count($p) > 0) {
                    echo $this -> process($p, $issue) . ' (section) ' . $sect;
                    ob_flush();
                    flush();
                        //exit ;
                }
                /* Zera */
                $p = array();
                $sect = $v;
                $sect_title = '';

                if ($sect == 'Mi - Interposicao') {
                    $sect = "MI";
                    $sect_title = 'Mi - Interposicao';
                }
                if ($sect == 'Mi - Recurso - Decisao') {
                    $sect = "MR";
                    $sect_title = 'Mi - Recurso - Decisao';
                }

                if ($sect == 'PR - Nulidades') {
                    $sect = "PR";
                    $sect_title = 'Nulidades';
                }
                if ($sect == 'PR - Recursos - Decisoes') {
                    $sect = "RE";
                    $sect_title = 'Recursos - Decisoes';
                }
                if ($sect == 'PR - Recursos - Despachos') {
                    $sect = "RD";
                    $sect_title = 'PR - Recursos - Despachos';
                }
                if ($sect == 'MJ - Interposicao') {
                    $sect = "MJ";
                    $sect_title = 'MJ - Interposicao';
                }
                if ($sect == 'MJ - Recurso - Decisao') {
                    $sect = "MR";
                    $sect_title = 'MJ - Recurso - Decisao';
                }
                if ($sect == 'PR - Cancelamentos') {
                    $sect = "PC";
                    $sect_title = 'PR - Cancelamentos';
                }
                if ($sect == 'PR - Recursos - Decisões') {
                    $sect = "RC";
                    $sect_title = 'PR - Recursos - Decisões';
                }
                if ($sect == 'Mi - Interposições') {
                    $sect = 'Mt';
                    $sect_title = 'Mi - Interposições';
                }

                if (strlen($sect) > 5) {
                    $sect = substr($sect, 0, 2);
                    $sect_title = $sect;
                }
                $p['section'] = $sect;
                $p['section_title'] = $sect_title;
                $p['titular'] = array();
                $p['inventor'] = array();
                $p['depositante'] = array();
                $p['procurador'] = array();
                $p['prioridade_unionista'] = array();
                $p['classificacao_internacional'] = array();
                $p['comentario'] = '';
                break;
                case '(co)' :
                if (strlen($p['comentario']) > 0) { $p['comentario'] .= '<br>';
            }
            $p['comentario'] .= $v;
            $p['comentario_indi'] = 'Co';
            break;
            case '(De)' :
            if (strlen($p['comentario']) > 0) { $p['comentario'] .= '<br>';
        }
        $p['comentario'] .= $v;
        $p['comentario_indi'] = 'Co';
        break;
        case '(11)' :
        if ($max < 11) {
            if (strpos($v,'-') > 0)
            {
                $v = substr($v,0,strpos($v,'-'));
            }
            $p['patent_nr'] = $v;
            $max = 11;
        } else {
            $result = $cmdo . ' ' . $result;
            $cmd = $cmdo;                        
        }
        break;
        case '(21)' :
        if ($max < 11) {
            $p['patent_nr'] = $v;
            $max = 21;
        } else {
            $result = $cmdo . ' ' . $result;
            $cmd = $cmdo;                        
        }

        break;
        case '(22)' :
        if ($max < 22) {
            $dt = trim($v);
            if (strlen($dt) == 8) {
                $dt = substr($dt, 0, 6) . '19' . substr($dt, 6, 2);
            }
            $p['patent_nr_deposit_date'] = $dt;
            $max = 22;
        } else {
            $result = $cmdo . ' ' . $result;
            $cmd = $cmdo;                        
        }

        break;
        case '(30)' :
        if ($max < 30) {
            $dtt = array();
            $dtt['prior_inid'] = '30';
            $seqq = count($p['prioridade_unionista']);
            $dtt['prior_seq'] = $seqq + 1;
            if (substr($v, 8, 1) == ' ') {
                $dtt['prior_sigla_pais'] = substr($v, 9, 2);
                $dtt['prior_numero_prioridade'] = substr($v, 12, strlen($v));
                $dtt['prior_data_prioridade'] = substr($v, 0, 6) . '19' . substr($v, 6, 2);
                $p['prioridade_unionista'][$seqq] = $dtt;
            } else {
                $dtt['prior_sigla_pais'] = substr($v, 11, 2);
                $dtt['prior_numero_prioridade'] = substr($v, 14, strlen($v));
                $dtt['prior_data_prioridade'] = substr($v, 0, 10);
                $p['prioridade_unionista'][$seqq] = $dtt;
            }
            $max = 30;
        } else {
            $result = $cmdo . ' ' . $result;
            $cmd = $cmdo;                        
        }

        break;
        case '(51)' :
        if ($max < 51) {
            $dtt = array();
            for ($q = 0; $q < strlen($v); $q++) {
                if (substr($v, $q, 1) == '/') {
                    $ca = substr($v, $q, strlen($v));
                    $ca = strpos($ca,' ');
                    $v[$q + $ca] = ';';
                }
            }

            $v = troca($v, 'G08B 13,22', 'G08B 13/22');
            $v = troca($v, ',', ';');
            $cl = splitx(';', $v . ';');
            for ($rc = 0; $rc < count($cl); $rc++) {
                if (substr($cl[$rc],0,1) != '(')
                {
                    $seq = count($p['classificacao_internacional']);
                    $dtt = array();
                    $dtt['cip_inid'] = '51';
                    $dtt['cip_seq'] = $seq + 1;
                    $dtt['cip_ano'] = '2006.01';
                    $dtt['cip_classe'] = $this -> formatar_classe($cl[$rc]);
                    $this -> classes($cl[$rc], '', $dtt['cip_ano']);
                    $p['classificacao_internacional'][$seq] = $dtt;
                }
            }
            $max = 51;
        } else {
            $result = $cmdo . ' ' . $result;
            $cmd = $cmdo;                        
        }

        break;
        case '(54)' :
        if ($max < 54)
        {
            $v = troca($v, '"', '');
            $v = troca($v, "'", "");
            $p['patent_titulo'] = $v;
        } else {
            $result = $cmdo . ' ' . $result;
            $cmd = $cmdo;                        
        }
        
        break;
        case '(57)' :
        $v = troca($v, '"', '');
        $v = troca($v, "'", "");
        $p['patent_resumo'] = $v;
        break;
        case '(71)' :
        $a = array();
        $nn = $v . ';';
        $nn = troca($nn, ',', ';');
        $nm = splitx(';', $nn);
        for ($rq = 0; $rq < count($nm); $rq++) {
            $v = $nm[$rq];
            if (strpos($v, '(') > 0) {
                $name = substr($v, 0, strpos($v, '('));
                $pais = substr($v, strpos($v, '('), strlen($v));
                if (strpos(' '.$pais,'(') > 1)
                {                                    
                    $pais = substr($pais, strpos($pais, ')')+1, strlen($pais));
                    $name = substr($v, 0, strpos($v, ')')+1); 
                }
                $a['nome'] = $name;
                $a['pais'] = substr($pais, 1, 2);
                if (substr($pais, 3, 1) == '/') {
                    $a['nome_endereco_uf'] = substr($pais, 4, 2);
                } else {
                    $a['nome_endereco_uf'] = '';
                }
            } else {
                $a['nome'] = $v;
            }
            $seq = count($p['depositante']);
            $a['nome_seq'] = $seq + 1;
            $p['depositante'][$seq] = $a;
        }
        break;                
        case '(72)' :
        $a = array();
        $nn = $v . ';';
        $nn = troca($nn, ',', ';');
        $nm = splitx(';', $nn);
        for ($rq = 0; $rq < count($nm); $rq++) {
            $v = $nm[$rq];
            if (strpos($v, '(') > 0) {
                $name = substr($v, 0, strpos($v, '('));
                $pais = substr($v, strpos($v, '('), 10);
                $a['nome'] = $name;
                $a['pais'] = substr($pais, 1, 2);
                if (substr($pais, 3, 1) == '/') {
                    $a['nome_endereco_uf'] = substr($pais, 4, 2);
                } else {
                    $a['nome_endereco_uf'] = '';
                }
            } else {
                $a['nome'] = $v;
            }
            $seq = count($p['inventor']);
            $a['nome_seq'] = $seq + 1;
            $p['inventor'][$seq] = $a;
        }
        break;
        case '(73)' :
        $a = array();
        $name = $v;
        if (strpos($v, '(') > 0) {
            $name = substr($name, 0, strpos($v, '('));
            $pais = substr($v, strpos($v, '('), 10);
            $a['nome'] = $name;
            $a['pais'] = substr($pais, 1, 2);
            if (substr($pais, 3, 1) == '/') {
                $a['nome_endereco_uf'] = substr($pais, 4, 2);
            } else {
                $a['nome_endereco_uf'] = '';
            }
        } else {
            $a['nome'] = $v;
        }
        $seq = count($p['titular']);
        $a['nome_seq'] = $seq + 1;
        $p['titular'][$seq] = $a;
        break;

        case '(74)' :
        $a = array();
        $nn = $v . ';';
        $nn = troca($nn, ',', ';');
        $nm = splitx(';', $nn);
        for ($rq = 0; $rq < count($nm); $rq++) {
            $v = $nm[$rq];
            if (strpos($v, '(') > 0) {
                $name = substr($v, 0, strpos($v, '('));
                $pais = substr($v, strpos($v, '('), 10);
                $a['nome'] = $name;
                $a['pais'] = substr($pais, 1, 2);
                if (substr($pais, 3, 1) == '/') {
                    $a['nome_endereco_uf'] = substr($pais, 4, 2);
                } else {
                    $a['nome_endereco_uf'] = '';
                }
            } else {
                $a['nome'] = $v;
            }
            $seq = count($p['procurador']);
            $a['nome_seq'] = $seq + 1;
            $p['procurador'][$seq] = $a;
        }
        break;
        case '(85)' :
        $p['patent_fase_nacional'] = $v;
        break;
        case '(86)' :
        $v = trim($v);
        $p['pedido_internacional_numero_pct'] = trim(substr($v, 0, strpos($v,'/')));
        $p['pedido_internacional_numero_pct_data'] = substr($v, strlen($v) - 10, 10);
        break;
        case '(87)' :
        $p['publicacao_internacional_numero_ompi'] = trim(substr($v, 0, strlen($v) - 13));
        $p['publicacao_internacional_numero_ompi_data'] = substr($v, strlen($v) - 10, 10);
        break;
    }
}
/* Processar linha */
if (count($p) > 0) {
    echo $this -> process($p, $issue) . ' -> ' . $sect;
    ob_flush();
    flush();
            //exit ;
}

}

function method_inpi($file) {
    $debug = 0;
    $cnt = file_get_contents($file);
    $cnt = troca($cnt, '-', '_');
    $xml = simplexml_load_string($cnt);
    $sessions = $this -> sessions();
    $class = $this -> classes();

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

    foreach ($is as $key => $value) {
        $p = array();
        $p['section'] = trim((string)$value -> codigo);
        $p['section_title'] = (string)$value -> titulo;
        $sect = $p['section'];
        if (!isset($sessions[$sect])) {
            $this -> sessions($sect, $p['section_title']);
            $sessions[$sect] = $p['section_title'];
        }

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
    if (!isset($dt[0])) { $dt[0] = '';
}
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
        $cod = $dtt['cip_ano'];
        $c = $dtt['cip_classe'];
        if (!isset($class[$c])) {
            $this -> classes($c, '', $cod);
        }

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
        $seq = $this -> xml_read($dd -> titular);
        $titular[$r]['nome_seq'] = $seq['sequencia'];
        $titular[$r]['nome_inid'] = $seq['inid'];

        $titular[$r]['nome'] = (string)$dd -> titular -> nome_completo;
        if (isset($dd -> titular -> endereco -> pais -> sigla)) {
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
        $seq = $this -> xml_read($dd -> inventor);
        $inventor[$r]['nome_seq'] = $seq['sequencia'];
        $inventor[$r]['nome_inid'] = $seq['inid'];
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
echo $this -> process($p, $issue) . ' -> ' . $sect;
ob_flush();
flush();
}
return ("");
}

function harvest_patent($id) {
    /*********** ZIP ************************************************/
    $file = '_repository_patent/inpi/zip/Patent-' . strzero($id, 5) . '.zip';
    if (!file_exists($file)) {
        echo "OPS " . $file;
        exit ;
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

function formatar_classe($class) {
    $class = troca($class, "'", '');
    $class = troca($class, '  ', ' ');
    $class = troca($class, '  ', ' ');
    if (substr($class, 7, 1) == ',') { $class[7] = '/';
}
if (!substr($class, 4, 1) != ' ') {
    $class = substr($class, 0, 4) . ' ' . substr($class, 4, strlen($class));
}
return ($class);
}

function classes($class = '', $desc = '', $cod = '') {
    $class = $this -> formatar_classe($class);
    if (strlen(trim($class)) == 0) {
        $sql = "select * from patent.patent_class";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $secs = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $s = trim($line['cc_class']);
            $desc = trim($line['cc_name']);
            $secs[$s] = $desc;
        }
        return ($secs);
    } else {

        $sql = "select * from patent.patent_class where cc_class = '$class' and cc_cod = '$cod'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
    }
    if (count($rlt) == 0) {
        $ca1 = substr($class, 0, 1);
        $ca2 = substr($class, 1, 2);
        $ca3 = substr($class, 3, 1);
        $cs = trim(substr($class, 5, 10));
        $ca4 = substr($cs, 0, strpos($cs, '/'));
        $ca5 = substr($cs, strpos($cs, '/') + 1, strlen($cs));
        $sql = "insert into patent.patent_class
        (cc_name, cc_class, cc_description,cc_language,cc_cod,
        cc_c1,cc_c2,cc_c3,cc_c4,cc_c5)
        values
        ('$desc','$class','','pt','$cod',
        '$ca1','$ca2','$ca3','$ca4','$ca5')";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
        return ("");
    }
}

function sessions($sec = '', $desc = '') {
    if (strlen($sec) == 0) {
        $sql = "select * from patent.patent_section";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $secs = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $s = trim($line['ps_acronic']);
            $desc = trim($line['ps_name']);
            $secs[$s] = $desc;
        }
        return ($secs);
    } else {
        $sql = "select * from patent.patent_section where ps_acronic = '$sec'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
    }
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_section 
        (ps_name, ps_acronic, ps_description, ps_source, ps_active)
        values
        ('$desc','$sec','',1,1)";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
        return ("");
    }
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

function despacho($issue, $d, $id) {
    if (strlen($issue) == 0) {
        echo cr() . "<br>OPS, erro de ISSUE";
        exit ;
    }

    if (!isset($d['comentario'])) {
        $d['comentario'] = '';
    }

    $cot = troca($d['comentario'], "'", '');
    $cot = troca($cot, "\\", '-');
    $sec = $d['section'];
    $sql = "select * from patent.patent_despacho 
    where pd_patent = $id 
    and pd_section = '$sec' 
    and pd_comentario    = '$cot' 
    and pd_issue = '$issue'";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_despacho 
        (pd_patent, pd_section, pd_comentario, pd_issue, pd_method)
        values
        ('$id','$sec','$cot','$issue','INPI')";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
        return(1);
    }
    return (0);
}

function relacao_agente_patent($idp, $age, $relacao, $seq) {
    if (strlen($seq) == 0) {
        $seq = 1;
    }
    $sql = "select * from patent.patent_agent_relation 
    WHERE rl_patent = $idp
    AND rl_agent = $age
    AND rl_relation = '$relacao' ";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_agent_relation
        (rl_agent, rl_relation, rl_patent,rl_seq)
        values
        ('$age','$relacao','$idp',$seq)";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
    }
    return (1);
}

function pct($id, $pct, $pct_dt) {
    if ((sonumero($pct_dt) == '00000000') or (substr($pct_dt,0,4) != '-'))
    {
        if (strlen($pct_dt) == 6)
        {
            $pct_dt = '19'.substr($pct_dt,0,2).'-'.substr($pct_dt,2,2).'-'.substr($pct_dt,4,2);
        } else {
            $pct_dt = '0001-02-02';        
        }
        
    }        
    $sql = "select * from patent.patent_pct
    where pct_nr = '$pct'
    AND pct_data = '$pct_dt'
    AND pct_patent = $id";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_pct
        (pct_nr, pct_data, pct_patent)
        values
        ('$pct','$pct_dt','$id')";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
    }
    return (1);
}

function pub($id, $pct, $pct_dt) {
    if ((sonumero($pct_dt) == '00000000') or (substr($pct_dt,0,4) != '-'))
    {
        if (strlen($pct_dt) == 6)
        {
            $pct_dt = '19'.substr($pct_dt,0,2).'-'.substr($pct_dt,2,2).'-'.substr($pct_dt,4,2);
        } else {
            $pct_dt = '0001-02-02';        
        }
        
    }
    $sql = "select * from patent.patent_pub
    where pub_nr = '$pct'
    AND pub_data = '$pct_dt'
    AND pub_patent = $id";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_pub
        (pub_nr, pub_data, pub_patent)
        values
        ('$pct','$pct_dt','$id')";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
    }
    return (1);
}

function kindcode($id, $kc, $issue) {
    $sql = "select * from patent.patent_kindcode
    where pk_issue = '$issue'
    AND pk_code = '$kc'
    AND pk_patent = $id";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_kindcode
        (pk_issue, pk_code, pk_patent)
        values
        ('$issue','$kc','$id')";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
    }
    return (1);
}

function process($d, $issue) {
    $debug = 0;
    $sx = '';
    if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo Patente';
}
if (isset($d['patent_nr'])) {
    if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Número da Patent';
}
$id = $this -> patent($d);

$sx .= cr() . $d['patent_nr'] . ' process (' . $id . ')';

/************************ History ******************/
if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo despacho';
}
if ($this -> despacho($issue, $d, $id) == 0){
    echo ' * old *';
} else {
    echo ' * novo *';
    /****************** KINDCODE ******************/
    if (isset($d['patent_nr_kindcode'])) {
        $kind = $d['patent_nr_kindcode'];
        if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo KINDCODE';
    }
    $this -> kindcode($id, $kind, $issue);
}

/****************** PCT **********************/
if (isset($d['pedido_internacional_numero_pct'])) {
    $pct = $d['pedido_internacional_numero_pct'];
    $pct = troca($pct,"'","");
    $pct_dt = $d['pedido_internacional_numero_pct_data'];
    $pct_dt = brtos($pct_dt);
    if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo PCT';
}
$this -> pct($id, $pct, $pct_dt);
}

/****************** PUB **********************/
if (isset($d['publicacao_internacional_numero_ompi'])) {
    $pct = $d['publicacao_internacional_numero_ompi'];
    $pct = troca($pct,"'","");
    $pct_dt = $d['publicacao_internacional_numero_ompi_data'];
    if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo PUB';
}
$pct_dt = brtos($pct_dt);
$this -> pub($id, $pct, $pct_dt);
}

/****************** Titulares **********************/
$tit = $d['titular'];
for ($r = 0; $r < count($tit); $r++) {
    $line = $tit[$r];
    $pais = '';
    $estado = '';
    $name = $line['nome'] . ';';
    $name = troca($name, ',', ';');
    $seq = $line['nome_seq'];
    $name = splitx(';', $name);
    for ($a = 0; $a < count($name); $a++) {
        if (isset($line['nome_pais'])) { $pais = $line['nome_pais'];
    }
    if (isset($line['nome_endereco_uf'])) { $estado = $line['nome_endereco_uf'];
}
$ida = $this -> agent($name[$a], $pais, $estado);
if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo TITULARES';
}
$this -> relacao_agente_patent($id, $ida, 'T', $seq);
$seq++;
}
}
/****************** Inventor **********************/
$tit = $d['inventor'];
for ($r = 0; $r < count($tit); $r++) {
    $line = $tit[$r];
    $pais = '';
    $estado = '';
    $name = $line['nome'] . ';';
    $name = troca($name, ',', ';');
    $seq = $line['nome_seq'];
    $name = splitx(';', $name);
    for ($a = 0; $a < count($name); $a++) {
        if (isset($line['nome_pais'])) { $pais = $line['nome_pais'];
    }
    if (isset($line['nome_endereco_uf'])) { $estado = $line['nome_endereco_uf'];
}
$ida = $this -> agent($name[$a], $pais, $estado);
if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo TITULARES';
}
$this -> relacao_agente_patent($id, $ida, 'I', $seq);
$seq++;
}

}
/****************** Despachante **********************/
$tit = $d['procurador'];
for ($r = 0; $r < count($tit); $r++) {
    $line = $tit[$r];
    $pais = '';
    $estado = '';
    $name = $line['nome'] . ';';
    $name = troca($name, ',', ';');
    $seq = $line['nome_seq'];
    $name = splitx(';', $name);
    for ($a = 0; $a < count($name); $a++) {
        if (isset($line['nome_pais'])) { $pais = $line['nome_pais'];
    }
    if (isset($line['nome_endereco_uf'])) { $estado = $line['nome_endereco_uf'];
}
$ida = $this -> agent($name[$a], $pais, $estado);
if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo TITULARES';
}
$this -> relacao_agente_patent($id, $ida, 'P', $seq);
$seq++;
}

}
/****************** Escritorio **********************/
$tit = $d['depositante'];
for ($r = 0; $r < count($tit); $r++) {
    $line = $tit[$r];
    $pais = '';
    $estado = '';
    $name = $line['nome'];
    $seq = $line['nome_seq'];
    if (isset($line['pais'])) { $pais = $line['pais'];
}
if (isset($line['nome_endereco_uf'])) { $estado = $line['nome_endereco_uf'];
}
$ida = $this -> agent($name, $pais, $estado);
if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo Depositante';
}
$this -> relacao_agente_patent($id, $ida, 'D', $seq);
}

/****************** Classificacao **********************/
if (isset($d['classificacao_internacional'])) {
    $class = $d['classificacao_internacional'];
    for ($r = 0; $r < count($class); $r++) {
        $line = $class[$r];
        if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo Classificacao';
    }
    $this -> classification($id, $line);
}
}
/****************** Classificacao **********************/
if (isset($d['prioridade_unionista'])) {
    $class = $d['prioridade_unionista'];
    for ($s = 0; $s < count($class); $s++) {
        $line = $class[$s];
        if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Processo Unionista';
    }
    $this -> prioritario($id, $line);
}
}
}
}
if ($debug == 1) { echo '' . cr() . date("Y-m-d H:i:s B") . ' - Fim';
}
return ($sx);
}

function classification($id, $l) {
    $cl = $l['cip_classe'];
    $cl1 = troca($cl, ' ', ';');
    $c = splitx(';', $cl1);
    $c1 = $c[0];
    if (!isset($c[1])) {
        $c2 = '-1';
    } else {
        $c2 = $c[1];
    }

    $data = substr($l['cip_ano'], 0, 7);
    $seq = $l['cip_seq'];
    
    $sql = "select * from patent.patent_classification
    WHERE c_patent = $id
    AND c_class = '$c1'
    AND c_subclass = '$c2'";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_classification
        (c_patent, c_class, c_subclass, c_cod, c_seq, c_c)
        values
        ($id,'$c1','$c2','$data','$seq', '$cl')";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
    }
    return (1);
}

function agent($name = '', $country = '', $state = '') {
    $name = troca($name, "'", '');
    $name = troca($name, ".", ' ');
    $name = troca($name, "_", '-');
    $name = troca($name, "(", '');
    $name = troca($name, ")", '');
    $name = trim($name);
    $sql = "select * from patent.patent_agent where pa_nome = '$name'";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sqli = "insert into patent.patent_agent
        (pa_nome, pa_pais, pa_estado)
        values
        ('$name','$country','$state')";
        $rlti = $this -> db -> query($sqli);
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        sleep($this -> wait);
    }
    $line = $rlt[0];

    /******************* Update ***************************************/
    $set = '';
    if ((strlen($line['pa_pais']) == 0) and (strlen($country) > 0)) {
        $set = " pa_pais = '$country' ";
    }
    if ((strlen($line['pa_estado']) == 0) and (strlen($state) > 0)) {
        if (strlen($set) > 0) { $set .= ', ';
    }
    $set = " pa_estado = '$state' ";
}
if (strlen($set) > 0) {
    $sql = "update patent.patent_agent set $set where id_pa = " . $line['id_pa'];
    $rlti = $this -> db -> query($sql);
}

$id = $line['id_pa'];
return ($id);
}

function patent($d) {
    /* VARIAVEIS */
    $pat_nr = troca($d['patent_nr'], ' ', '');
    $pat_dd = '0000-00-00';
    $pat_title = '';
    $pct = '';
    $pct_data = '';
    $pub = '';
    $pub_data = '';
    $fase_nasc = '';

    if (isset($d['patent_fase_nacional'])) {
        $fase_nasc = brtos($d['patent_fase_nacional']);
    }

    if (isset($d['pedido_internacional_numero_pct'])) {
        $pct_data = brtos($d['pedido_internacional_numero_pct_data']);
        $pct = $d['pedido_internacional_numero_pct'];
    }
    if (isset($d['publicacao_internacional_numero_ompi'])) {
        $pub_data = brtos($d['publicacao_internacional_numero_ompi_data']);
        $pub = $d['publicacao_internacional_numero_ompi'];
        $pub = troca($pub,"'","");
    }
    if (isset($d['patent_nr_deposit_date'])) {
        $pat_dd = brtos($d['patent_nr_deposit_date']);
    }

    if (isset($d['patent_titulo'])) {
        $pat_title = utf8_decode($d['patent_titulo']);
        $pat_title = strtoupper((string)$pat_title);
        $pat_title = troca($pat_title, '"', '');
        $pat_title = troca($pat_title, "'", '');
        $pat_title = troca($pat_title, "/", '');
        $pat_title = troca($pat_title, chr(134), '');
        $pat_title = troca($pat_title, '\\', '');
        $pat_title = utf8_encode($pat_title);
    }
    if (!isset($d['patent_resumo'])) {
        $pat_resumo = '';
    } else {
        $pat_resumo = $d['patent_resumo'];
    }

    /**************** recupera patent ****************************/
    $sql = "select * from patent.patent where p_nr = '" . $pat_nr . "'";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sqli = "insert into patent.patent
        (p_nr,p_nrn, p_dt_deposito)
        values
        ('$pat_nr','$pat_nr', '$pat_dd')";
        $rlti = $this -> db -> query($sqli);
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        sleep($this -> wait);
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

if ((strlen($line['p_resumo']) == 0) and ($pat_resumo != '')) {
    if (strlen($set) > 0) { $set .= ', ';
}
$set .= 'p_resumo = "' . $pat_resumo . '" ';
}

if (substr($line['p_dt_deposito'], 0, 4) == '0000') {

    if (substr($pat_dd, 0, 4) != '0000') {
        if (strlen($set) > 0) { $set .= ', ';
    }
    $set .= 'p_dt_deposito = "' . $pat_dd . '" ';
}
}

if (strlen($line['p_pct'] == 0) and (strlen($pct) > 0)) {
    if (strlen($set) > 0) { $set .= ', ';
}
$set .= "p_pct = '$pct', p_pct_data = '$pct_data' ";
}
if (strlen($line['p_pub'] == 0) and (strlen($pub) > 0)) {
    if (strlen($set) > 0) { $set .= ', ';
}
$set .= "p_pub = '$pub', p_pub_data = '$pub_data' ";
}

if (substr($line['p_fase_nacional'], 0, 2) == '00') {

    if (substr($fase_nasc, 0, 4) != '0000') {
        if (strlen($set) > 0) { $set .= ', ';
    }
    $set .= 'p_fase_nacional = "' . $fase_nasc . '" ';
}
}

/********************************** SALVA NO BANCO DE DADOS ************/
if (strlen($set) > 0) {
    $sqli = "update patent.patent set " . $set . " where id_p = " . $idp;
            //echo '<span class="color: blue;">' . $sqli . '</span><br>';
    $rlti = $this -> db -> query($sqli);
}

return ($idp);
}

function prioritario($idp, $d) {
    $prioc = trim(troca($d['prior_numero_prioridade'], "'", ""));
    if (strpos($prioc, ';') > 0) {
        $prioc = strpos($prioc, 0, strpos($prioc, ';'));
    }
    $pais = trim((string)$d['prior_sigla_pais']);
    $seq = $d['prior_seq'];
    $data = brtos($d['prior_data_prioridade']);
    $sql = "select * from patent.patent_prioridade
    WHERE prior_patent = $idp
    AND prior_numero_prioridade = '$prioc'
    AND prior_sigla_pais = '$pais'";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        $sql = "insert into patent.patent_prioridade
        (prior_seq, prior_numero_prioridade, prior_sigla_pais, 
        prior_data_prioridade,   prior_patent)
        values
        ($seq,'$prioc','$pais',
        '$data',$idp)";
        $rlt = $this -> db -> query($sql);
        sleep($this -> wait);
    }
    return (1);
}

function s($n, $t = '') {
    $p = round(get("p"));
    if ($p == 0) { $p = 1;
    }
    $type = 'patent';
    $q = $this -> elasticsearch -> query($type, $n, $t);
        //$q = $this->ElasticSearch->query_all($n);
    if ((!isset($q['hits']['hits'])) AND (perfil("#ADM"))) {
            //echo '<pre>';
            //print_r($q);
            //echo '</pre>';
    }
    switch($t) {
        case '2' :
        $sql = "select * from patent.patent_agent where pa_nome like '%$n%' order by pa_nome";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<ul>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url(PATH . '/vi/' . $line['id_pa']) . '">';
            $sx .= '<li>' . $link . troca($line['pa_nome'], '_', '-') . '</a>' . '</li>';
        }
        $sx .= '</ul>';
        return ($sx);
    }

    /*************** history ***************/
    $data['q'] = $n;
    $data['type'] = $t;

    if (!isset($q['hits'])) {
        $total = 0;
        $data['total'] = $total;
        $this -> save_history($data);
        return ('Not found');
    } else {
        $total = $q['hits']['total'];

        /* History */
        $data['total'] = $total;
        $this -> save_history($data);
    }

    $rst = $q['hits']['hits'];

    $st = '';
    for ($r = 0; $r < count($rst); $r++) {
        $line = $rst[$r];
        $st .= $line['_id'] . ';';
    }

    $sx = '<div class="container"><div class="row">';
    $sx .= $this -> bs -> script_all($st);
    $sx .= '<div class="col-8">' . $this -> pages($n, $total) . '</div>' . cr();
    $sx .= '<div class="col-4">Total ' . $total . '</div>' . cr();
    $sx .= '</div></div>';

    /**************************************************** Busca Parte II *****************/
    $sx .= '<div class="row result">';

    $sz = $this -> sz;
    $ppi = (($p - 1) * $sz);
    $ppf = $total;
    for ($r = 0; $r < count($rst); $r++) {
        $key = $rst[$r]['_source']['article_id'];
        $jnl = $rst[$r]['_source']['id_jnl'];
        $year = $rst[$r]['_source']['year'];
        $img = 'img/cover/cover_issue_' . $jnl . '.jpg';
        if (!is_file($img)) {
            $img = 'img/cover/cover_issue_0.jpg';
        }
        $sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . $img . '" class="img-fluid"></div>';
        $sx .= '<div class="col-10 " style="margin-bottom: 15px;">';
        $sx .= $this -> bs -> checkbox($key);
        $sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
        $sx .= $this -> frbr -> show_v($key);
        $sx .= '</a>';
        $sx .= ' <sup>' . number_format($rst[$r]['_score'], 4) . '</sup>';
        $sx .= '</div>';
        $sx .= '<div class="col-1 ">' . $year . '</div>';
    }
    $sx .= '</div>';
    if ($total > 5) {
        $sx .= '<div class="container"><div class="row">';
        $sx .= '<div class="col-8">' . $this -> pages($n, $total) . '</div>' . cr();
        $sx .= '<div class="col-4">Total ' . $total . '</div>' . cr();
        $sx .= '</div></div>';
    }
    if ($total == 0) {
        $sx = '<div class="container"><div class="row">';
        $sx .= '<div class="col-12">';
        $sx .= bs_alert("warning", msg("not_match_to") . ' "<b>' . $n . '</b>"');
        $sx .= '</div>';
        $sx .= '</div></div>';
    }

    return ($sx);
    /*********************************************************************************/
}

function save_history($data) {
    if (get("h") == '1') {
        return ("");
    }

    $user = 0;
    $date = date("Y-m-d");
    $hour = date("H:i:s");
    $session = $this -> s;
    $ip = $_SERVER['REMOTE_ADDR'];

    if (isset($_SESSION['id_us'])) {
        $user = $_SESSION['id_us'];
    }
    $q = UpperCase($data['q']);
    $t = round($data['type']);
    $total = round($data['total']);
    $page = round(GET("p"));

    $sql = "select * from patent._search 
    where s_date = '$date' 
    and s_hour = '$hour'
    and s_query = '$q'
    and s_type = $t
    and s_user = $user
    and s_total = $total
    and s_session = s_session";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    if (count($rlt) == 0) {
        if (strlen($q) > 0) {
            $sql = "insert into patent._search (s_date, s_hour, s_query, s_type, s_user, s_total, s_session, s_ip) ";
            $sql .= " values ";
            $sql .= "('$date', '$hour', '$q',$t,$user,$total,$session,'$ip')";
            $this -> db -> query($sql);
            sleep($this -> wait);
        }
    }
    return ('');
}

function pages($n = 0, $t = 0) {
    $sz = $this -> sz;
    $pgs = ((int)($t / $sz) + 1);
    $q = get("q");
    $q = troca($q, '"', '¢');
    $link = base_url('index.php/res/?q=' . $q . '&type=' . get("type"));

    $p = round(get("p"));
    if ($p == 0) { $p = 1;
    }
    /********* PAGINA INICIAL ******************/
    $pgi = $p - 5;
    if ($pgi < 1) { $pgi = 1;
    }

    $pgf = ($pgi + 9);
    $pgm = ((int)($t / $sz) + 1);
    if ($pgf > $pgm) { $pgf = $pgm;
    }

    $sx = '<nav aria-label="Page navigation example">
    <ul class="pagination">';
    if ($pgi > 1) {
        $sx .= '    <li class="page-item"><a class="page-link" href="' . $link . '&p=' . ($pgi - 1) . '">&laquo;</a></li>' . cr();
    }

    for ($r = $pgi; $r <= $pgf; $r++) {
        $class = "";
        if ($r == $p) {
            $class = ' active ';
        }
        $sx .= '<li class="page-item ' . $class . '"><a class="page-link" href="' . $link . '&p=' . $r . '">' . $r . '</a></li>' . cr();
    }

    if ($pgf < $pgm) {
        $sx .= '<li class="page-item"><a class="page-link" href="' . $link . '&p=' . ($pgf + 1) . '">&raquo;</a></li>' . cr();
    }
    $sx .= '</ul></nav>' . cr();
    return ($sx);
}
function instituicao($id) {
    $sql = "select * from patent.patent_agent
    where id_pa = $id";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    
    $name = $rlt[0]['pa_nome'];

    $sx = '';
    $sx .= '<h2>'.$name.'</h2>';
    $th = 8;
    $sx .= $this->thesa_api->check_thesa($name,$th);
    return($sx);
}

function instituicao_list($id) {
    $sql = "select * from patent.patent_agent_relation
    INNER JOIN patent.patent ON id_p = rl_patent 
    where rl_agent = $id";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $sx = '';
    $sx .= '<table class="table">';
    $t = 0;
    for ($r = 0; $r < count($rlt); $r++) {
        $line = $rlt[$r];
        $t++;
        $sx .= '<tr>';
        $link = '<a href="' . base_url(PATH . '/v/' . $line['id_p']) . '">';
        $sx .= '<td>';
        $sx .= '<nobr>' . $link . $line['p_nrn'] . '</a></nobr>';
        $sx .= '</td>';

        $sx .= '<td>';
        $sx .= strtoupper($line['p_title']);
        $sx .= '</td>';

        $sx .= '</tr>';
    }
    $sx .= '<tr><td colspan=2>Total: ' . $t . '</td></tr>';
    $sx .= '</table>';
    return ($sx);
}

function zera() {
    echo "********* ZERAR **********";
    $sql = "TRUNCATE patent.patent";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_agent";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_agent_relation";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_class";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_classification";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_despacho";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_issue";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_kindcode";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_pct";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_prioridade";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_pub";
    $this -> db -> query($sql);
    $sql = "TRUNCATE patent.patent_section";
    $this -> db -> query($sql);
}

function summary() {
    $sx = '<h1>' . msg('summary') . '</h1>';
    $sql = "select count(*) as total,  
    max(issue_published) as issue_published,
    max(issue_created ) as issue_created                            
    from patent.patent_issue";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $line = $rlt[0];
    $sx .= '<ul>';
    $sx .= '<li>' . msg('last_proccess') . ': <b>' . stodbr($line['issue_created']) . ' ' . substr($line['issue_created'], 10, 9) . '</b></li>' . cr();
    $sx .= '<li>' . msg('issues_proccess') . ': <b>' . number_format($line['total'], 0, ',', '.') . '</b>' . ' (' . stodbr($line['issue_published']) . ')' . '</li>' . cr();

    $sql = "select count(*) as total from patent.patent";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $line = $rlt[0];
    $sx .= '<li>' . msg('patents_proccess') . ': <b>' . number_format($line['total'], 0, ',', '.') . '</b></li>' . cr();

    $sql = "select count(*) as total from patent.patent_agent";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $line = $rlt[0];
    $sx .= '<li>' . msg('agents_proccess') . ': <b>' . number_format($line['total'], 0, ',', '.') . '</b></li>' . cr();
    $sx .= '</ul>';

    $sx = '<div class="row"><div class="col-md-12">' . $sx . '</div></div>';
    return ($sx);
}
function rel($tipo=1)    
{   
    /*
    SELECT pd_section, ps_name, count(*) as total FROM `patent_despacho`
    INNER JOIN patent_issue on issue_source = pd_issue
    LEFT JOIN patent_section on pd_section = ps_acronic
    where issue_year = 2010
    group by pd_section, ps_name
    order by pd_section
    */

}
}
?>
