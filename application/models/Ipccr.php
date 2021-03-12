<?php
class ipccr extends CI_Model
{
    var $base = 'brapci_icr.';
    var $jnl = 0;
    var $jnlrdf = 0;
    var $j = array();

    function index($d1, $d2, $d3)
    {
        $sx = '';
        switch ($d1) {
            case 'ipccr_jnl_list':
                $sx .= '<div class="' . bscol(4) . '">';
                $sx .= $this->icr_logo();
                $sx .= '</div>';
                $sx .= '<div class="' . bscol(8) . '">';
                $sx .= $this->jnl_list($d2, $d3);
                $sx .= '</div>';
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
        print_r($this->j);

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
