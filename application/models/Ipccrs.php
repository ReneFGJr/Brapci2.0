<?php

class ipccrs extends CI_model {

    var $base = 'brapci_icr.';
    var $baseCited = 'brapci_cited.';
    var $jnl = 0;
    var $jnlrdf = 0;
    var $j = array();

    function index($d1, $d2, $d3, $d4)
    {
        $sx = '';
        switch ($d1) {
            case 'dataset':

            break;

            case 'ipccr_jnl_list':
                $sx .= '<div class="' . bscol(4) . '">';
                $sx .= $this->icr_logo();
                $sx .= '</div>';
                $sx .= '<div class="' . bscol(8) . '">';
                $sx .= $this->jnl_list($d2, $d3);
                $sx .= '</div>';
            break;

            case 'author':
                $this->load->model('cited');
                $this->load->model('ias');
                $this->load->model('ias_cited');
                $this->load->model('frad');
                $this->load->model('frbr_core');
                $this->load->model('api_brapci');
                $sx .= $this -> frbr_core-> person_show($d2);
                $sx .= $this->show_citation_by_author($d2,$d3);
                break;

            case 'jnl':
                $this->load->helper("highcharts");
                $sx .= '<div class="' . bscol(4) . '">';
                $sx .= $this->icr_logo();

                if (perfil("#ADM"))
                    {
                        $sx .= '<a href="'.base_url(PATH.'ipccr/calc_jnl/'.$d2).'" class="btn btn-outline-primary">'.msg('calc_jnl').'</a>';
                    }

                $sx .= '</div>';
                $sx .= '<div class="' . bscol(8) . '">';
                $sx .= $this->ipccr_jnl($d2, $d3);
                $sx .= '</div>';

                $sx .= '<div class="' . bscol(12) . '">';
                $data = array();
                $data['type'] = 'bar';
                $data['LEG_HOR'] = 'Seções de publicação dos trabalhos';
                $sx .= $this->show_indicador('sections',$d2,$data);

                $data['type'] = 'column';
                $data['LEG_HOR'] = 'Número de trabalhos publicados por ano';
                $sx .= $this->show_indicador('year',$d2,$data);

                $data['type'] = 'column';
                $data['LEG_HOR'] = 'Identidade de Citações';
                $sx .= $this->show_citation_idententify($d2,$d3,$d4);                    

                $data['type'] = 'column';
                $data['LEG_HOR'] = 'Identidade de Citações';
                $sx .= '<textarea class="form-control" style="height: 500px;">';
                $sx .= $this->acoplamento_citacoes($d2,$d3,$d4);
                $sx .= '</textarea>';

                $data['type'] = 'column';
                $data['LEG_HOR'] = 'Citações';
                $sx .= $this->show_citation($d2);                
                $sx .= '</div>';
            break;

            case 'calc_jnl':
                $this->calc_jnl($d2, $d3);
                redirect(base_url(PATH.'ipccr/jnl/'.$d2));
                break;

            default:
                $sx .= '<div class="' . bscol(4) . '">';
                $sx .= $this->icr_logo();
                $sx .= '</div>';
                $sx .= '<div class="' . bscol(8) . '">';
                $sx .= $this->menu();
                $sx .= '</div>';
        }
        return ($sx);
    }

    function ipccr($v=0)
        {
            $sx = '<div class="">';
            $sx .= '<a href="'.base_url(PATH.'ipccr/author/'.$v).'">';
            $sx .= '<img src="'.base_url('img/icon/icone_cited.png').'">';
            $sx .= msg('Quote');
            $sx .= '</a>';
            $sx .= '</div>';
            return($sx);
        }

    function show_citation_types($jnl,$ini,$fim)
        {
            $sql = "
            SELECT count(*) as total, ca_year_origem, ca_tipo, ct_name
                FROM `cited_article`
                INNER JOIN cited_type ON ca_tipo = id_ct 
                where ca_journal_origem = 2 and ca_year_origem > 1900
                group by ca_year_origem, ca_tipo, ct_name
                order by ca_year_origem, ca_tipo, ct_name
                ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

        }

    function acoplamento_citacoes($jnl,$ini,$fim)
        {
            if (strlen($ini) == 0) { $ini = 2003; }
            if (strlen($fim) == 0) { $fim = 2020; }
            $limit = 50;
            $wh = '';
            $wh = " AND ((ca_year_origem >= $ini) AND (ca_year_origem <= $fim))";
            $sql = "select cj_name, ca_rdf
                        FROM ".$this->baseCited."cited_article
                        INNER JOIN ".$this->baseCited."cited_journal ON ca_journal = id_cj
                        where (ca_journal_origem = $jnl)
                        $wh
                        group by cj_name, ca_rdf
                        ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '';
            $idx = 0;
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $cj = UpperCaseSql($line['cj_name']);
                    $id = $line['ca_rdf'];
                    if ($id != $idx)
                        {
                            $sx .= chr(10);
                            $idx = $id;
                        }
                    $sx .= $cj.';';
                }
            return($sx);
        }

    function show_citation_by_author($ida)
        {
                $sx = $this->show_citation($ida,'author');
                return($sx);    
        }

    function show_citation_idententify($jnl,$ini,$fim)
        {
            if (strlen($ini) == 0) { $ini = 2005; }
            if (strlen($fim) == 0) { $fim = 2008; }
            $limit = 50;
            $wh = '';
            $wh = " AND ((ca_year_origem >= $ini) AND (ca_year_origem <= $fim))";
            
            $sx = '<h1>'.msg('Identidade de Citação').' '.$ini.'-'.$fim.'</h1>';
            $sql = "select count(*) as total 
                        FROM ".$this->baseCited."cited_article
                        INNER JOIN ".$this->baseCited."cited_journal ON ca_journal = id_cj
                        where (ca_journal_origem = $jnl)
                        $wh
                        order by total desc";
                        echo $sql;
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $gtotal = $rlt[0]['total'];
            if ($gtotal == 0)
                {
                    $sx .= message('Sem dados de citação',3);
                    return($sx);
                }

            $sql = "select cj_name, count(*) as total 
                        FROM ".$this->baseCited."cited_article
                        INNER JOIN ".$this->baseCited."cited_journal ON ca_journal = id_cj
                        where ca_journal_origem = $jnl
                        $wh
                        GROUP BY cj_name
                        order by total desc";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx .= '<table width="100%">';
            $other = 0;
            $tot = 0;
            $tot_other = 0;
            $pos = 0;
            $xtotal = 0;
            $show = 1;
            $perc_acum = 0;            
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $total = $line['total'];
                    if ($xtotal != $total)
                        {
                            $pos++;
                            $xtotal = $total;
                            $mpos = $pos;
                        } else {
                            $mpos = '';
                        }
                    /* Regras */
                    if ($r >= $limit)
                        {
                            if ($mpos != '')
                                {
                                    $show = 0;
                                }
                        }
                    if ($show == 1) 
                    {
                        $per = $line['total'] / $gtotal;
                        $perc_acum = $perc_acum + $per;
                        $sx .= '<tr>';
                        $sx .= '<td align="center">'.($mpos).'</td>';
                        $sx .= '<td>'.nbr_author($line['cj_name'],7).'</td>';
                        $sx .= '<td align="center">'.$line['total'].'</td>';
                        $sx .= '<td>'.number_format($per * 100,1).'%</td>';
                        $sx .= '<td>'.number_format($perc_acum * 100,1).'%</td>';
                        $sx .= '</tr>';
                    } else {
                        $other++;
                        $tot_other = $tot_other + $line['total'];
                        $per = $line['total'] / $gtotal;
                        $perc_acum = $perc_acum + $per;
                    }
                    $tot = $tot + $line['total'];
                }
            $sx .= '<tr><td colspan=2>'.msg('Other_journals').' - '.$other.' '.msg('journals').'</td><td align="center">'.$tot_other.'</td>';
            $per = $tot_other / $gtotal;
            $sx .= '<td>'.number_format($per * 100,1).'%</td>';
            $sx .= '<td>'.number_format($perc_acum * 100,1).'%</td>';
            $sx .= '</tr>';

            $sx .= '<tr><td colspan=2><b>'.msg('Total').'</b></td><td align="center"><b>'.$tot.'</b></td></tr>';
            $sx .= '</table>';
            return($sx);
        }
    
    function citation_by_author($ida)
        {
                $rdf = new rdf;
                $dt = $rdf->le_data($ida);
                $ob = $rdf->extract_id($dt,'hasAuthor',$ida);
                return($ob);
        }

    function show_citation($id=0,$type='jnl')
        {
            $ida = $id;
            $auth = array();
            $rlt = $this->cited->citation($id,$type);
            $sx = '';
            $sx .= '<div class="'.bscol(11).'">';
            $sx .= '<h3>Total de citações - '.count($rlt).'</h3>';
            $sx .= '<ul>';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $sx .= '<li>';
                    $sx .= $line['ca_text'];
                    $sx .= ' '.$this->cited->cited_type($line,0);
                    $sx .= $this->link_article($line);
                    $sx .= '</li>';
                    $auth = $this->add_authors($line['ca_text'],$auth);
                }
            $sx .= '</ul>';

            $au = array();
            foreach($auth as $author=>$total)
                {
                    if ($total > 3)
                        {
                            array_push($au,strzero($total,5).$author);
                        }
                }
            sort($au);

            $sz = '';
            foreach($au as $ida=>$nome)
                {
                    $x = '<li>'.substr($nome,5,strlen($nome)).' - (';
                    $x .= round(substr($nome,0,5));
                    $x .= ')</li>';
                    $sz = $x . $sz;
                }     
            $sz = ' <div class="'.bscol(11).'">
                    <h4>Autores com cinco ou mais citações</h4>
                    <ol>'.$sz.'</ol>
                    </div>';
            $sz .= '<div class="'.bscol(1).'">';
            $sz .= 'Datasets<br/>';

            $lk['service'] = 'cited/author/';
            $lk['format'] = 'csv';
            $lk['id'] = $id;
            
            $link = $this->api_brapci->link($lk);
            $sz .= $link.'<img src="'.base_url('img/icon/icon_dataset.png').'" class="img-fluid">'.'</a>';
            $sz .= '</div>';

            $sx = $sz . $sx;

            return($sx);
        }

    function add_authors($txt,$auth)
        {
            $a = $this->ias_cited->cited_analyse($txt);
            for ($r=0;$r < count($a);$r++)
                {
                    $w = $a[$r];
                    if (isset($auth[$w]))
                        {
                            $auth[$w] = $auth[$w] + 1;
                        } else {
                            $auth[$w] = 1;
                        }
                }
            return($auth);
        }
    
    function link_article($l)
        {
            $sx = '<a href="'.base_url(PATH.'/v/'.$l['ca_rdf']).'" target="_new'.$l['ca_rdf'].'">[A]</a>';
            return($sx);
        }

    function show_indicador($ind,$jnl,$data = array())
        {
            $limit = '';
            if (isset($data['limit'])) { $limit = ' limit '.$data['limit'] ; }
            $sql = "select ind_descricao as descr, ind_valor as valor
                        from ".$this->base."icr_indicadores
                        where ind_jnl = $jnl and ind_indicador = '$ind'
                        order by ind_descricao 
                        $limit";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            $hc = new highcharts;
            
            $data['DATA'] = array();
            $data['CATS'] = array();
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    array_push($data['DATA'],$line['valor']);
                    array_push($data['CATS'],$line['descr']);
                }
            
            $sx = $hc->grapho($data);
            return($sx);
        }
    function ipccr_jnl($id,$d2)
        {
            $this->load->model('sources');
            $this->load->model('oai_pmh');
            $this->load->model('frbr_core');
            $this->load->model('frbr');

            $sx = '';
            $sx .= $this->sources->info($id);

            return($sx);
        }
    function le_jnl($id)
        {
            $j = $this->sources->le($id);
            $this->jnlrdf = $j['jnl_frbr'];
            $this->jnl = $j['id_jnl'];   
            $this->j = $j;
            return(1);         
        }

    function calc_jnl($id)
    {
        $this->load->model('sources');
        $this->load->model('oai_pmh');
        $this->load->model('frbr_core');
        $this->load->model('frbr');

        $this->le_jnl($id);    
        $jnlrdf = $this->jnlrdf;

        $sx = '';
        $sx .= '<div class="' . bscol(6) . '">';
        $sx .= $this->sources->info($id);
        $sx .= '</div>';

        $sx .= '<div class="' . bscol(6) . '">';
        $sx .= 'Calculando';
        $corpus = $this->jnl_articles($id);
        /* Artigos por ano */
        $sx .= $this->iccr_p1($corpus, $id, $jnlrdf);
        $sx .= '</div>';
        return ($sx);
    }
    function iccr_p1($c, $jnl, $jnlrdf)
    {
        $sx = '';
        $da1 = array();
        $dano = array();
        $dautor = array();
        $dsec = array();
        $de = array();
        /* Seções não indexadas */
        $no_sections = array(            
        'APRESENTACAO'=>0,
        'EDITORIAL'=>0,
        'EDICAO ANTERIORE'=>0,
        'EXPEDIENTE' => 0,
        'NORMA DA REVISTA ACB'=>0,
        'NORMA DE PUBLICACAO'=>0,
        'NORMA EDITORIAL'=>0,
        'NORMA PARA PUBLICACAO'=>0,        
        'PRATICA EDITORIAL'=>0   
        );
        
        /* Calcula producao */
        for ($r = 0; $r < count($c); $r++) {
            $file = 'c/' . $c[$r] . '/name.ABNT';
            if (file_exists($file)) {
                $t = file_get_contents($file);
                $ln = explode(chr(10), $t);

                /* Recupera secao da publicaçao */
                $type = substr($t, strpos($t, 'M3 - '), 100);
                $type = trim(UpperCaseSql(trim(substr($type, 5, strpos($type, chr(10)) - 5))));
                
                /* Verifica se indexa */
                if (!isset($no_sections[$type])) {
                if (isset($dsec[$type]))
                    {
                        $dsec[$type] = $dsec[$type] + 1;
                    } else {
                        $dsec[$type] = 1;
                    }
                /* Calcula os indicadores */

                    for ($l = 0; $l < count($ln); $l++) {
                        $cm = substr($ln[$l], 0, 2);
                        $v = trim(substr($ln[$l], 5, 100));
                        switch ($cm) {
                            case 'PY':
                                if (isset($dano[$v])) {
                                    $dano[$v] = $dano[$v] + 1;
                                } else {
                                    $dano[$v] = 1;
                                }
                                break;
                        }
                    }
                }
            } else {
                array_push($de, $c[$r]);
                echo '<h1>ERRO</h1>';
                exit;
            }
        }

        /* Indicador de Seção */
        $this->indicador_save($dano,'year');

        /* Indicador de Seção */
        $this->indicador_save($dsec,'sections');
        return ($sx);
    }
    function indicador_save($dt,$ind='')
        {
            $jnl = $this->jnl;
            $jnlrdf = $this->jnlrdf;
            $sql = "delete from " . $this->base . "icr_indicadores
                                where ind_jnl = $jnl
                                and ind_indicador = '$ind'";
            $this->db->query($sql);            

            foreach($dt as $desc=>$valor)
                {
                    $sql = "insert into " . $this->base . "icr_indicadores
                                (ind_indicador, ind_descricao, ind_valor, 
                                    ind_jnl, ind_jnlrdf)
                                values
                                ('$ind','$desc','$valor',
                                $jnl,$jnlrdf)";
                    $this->db->query($sql); 
                }
        }

    function jnl_articles($id)
    {
        $dt = $this->sources->le($id);
        $idjnl = $dt['jnl_frbr'];
        $rdf = new rdf;
        $data = $rdf->le_data($idjnl);
        $da = array();
        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            if ($line['c_class'] == 'isPubishIn') {
                array_push($da, $line['d_r1']);
            }
        }
        return ($da);
    }


    function jnl_list($d2, $d3)
    {
        $this->load->model('sources');
        $sx = $this->sources->list_sources_link(base_url(PATH . 'ipccr/jnl/'));
        return ($sx);
    }

    function menu()
    {
        $its = array('admin_calc_icr');
        $its = array('ipccr_jnl_list');
        $sx = '<h3>Menu</h3>';
        $sx .= '<ul>';
        for ($r = 0; $r < count($its); $r++) {
            $sx .= '<li>';
            $sx .= '<a href="' . base_url(PATH . 'ipccr/' . $its[$r]) . '">' . msg($its[$r]) . '</a>';
            $sx .= '</li>';
        }
        $sx .= '</ul>';
        return ($sx);
    }

    /* LOGO */
    function icr_logo($tp = 0)
    {
        switch ($tp) {
            default:
                $sx = '<div style="border: 1px solid #202090; padding: 5px; font-family: Tahoma; Verdana; Arial;">';
                $sx .= '<div style="font-size: 250%; background: #5050F0; width: 200px 75px; padding: 1px 20px; color: white;"><b>I P C C R</b></div>';
                $sx .= 'Índices de Produção, Colaboração e Citações das Revistas';
                $sx .= '</div>';
                break;
        }
        return ($sx);
    }
}
