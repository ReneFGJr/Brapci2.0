<?php
defined("BASEPATH") or exit("No direct script access allowed");

/**
 * CodeIgniter Form Helpers
 *
 * @package     CodeIgniter
 * @subpackage  IA
 * @category    IA-Checks
 * @author      Rene F. Gabriel Junior <renefgj@gmail.com>
 * @link        http://www.sisdoc.com.br/CodIgniter
 * @version     v0.21.02.16
 */

class ias_checks extends CI_Model
{
    function article_exclude_section()
    {
        $sx = '';
        $sx .= '<h1>Removendo a seção "Resumo de Artigos"</h1>';
        $at = array('Resumo de Artigo Científico', 'Resenha de Livros/artigos/mídia');

        for ($q = 0; $q < count($at); $q++) {
            $t = $at[$q];
            $rdf = new rdf;
            $idn = $rdf->rdf_name($t);
            $sql = "select * from rdf_concept where cc_pref_term = $idn";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $idc = $rlt[0]['id_cc'];

            $sql = "select * from rdf_data
                            where d_r2 = $idc
                            limit 20";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $idd = $line['d_r1'];
                $sx .= 'Item: ' . $line['d_r1'] . ' removed<br>';
                $rdf->remove_concept($idd);
            }
        }
        if (strlen($sx) > 0) {
            $sx .= '<meta http-equiv="refresh" content="3">';
        } else {
            $sx .= 'FIM da verificação';
        }

        return ($sx);
    }

    function article_duplicate($d1 = '', $d2 = '', $d3 = '')
    {

        /*************************************** processamentos */
        $rdf = new rdf;
        switch ($d2) {
            case 'YES_ANO':
                $this->load->model("export");
                $class = $rdf->find_class('hasTitle');
                $fx = 'c/' . $d1 . '/name.ABNT';
                $txt = file_get_contents($fx);
                $v1 = $this->recupera_dados($txt, 'TI');
                if (strlen($v1) < 3) {
                    $rdf->remove_concept($d1);
                    $sx .= '<meta http-equiv="refresh" content="5">';
                    return ($sx);
                }
                if (strpos(' ' . $v1, 'Prefácio') > 0) {
                    $vl = $this->recupera_dados($txt, 'IS');
                    $vn = $this->recupera_dados($txt, 'IL');
                    if ($vl > 0) {
                        $v1 .= ', v.' . $vl;
                    }
                    if ($vn > 0) {
                        $vn .= ', n.' . $vn;
                    }
                } else {
                    $v1 .= ' <sup>*</sup>';
                }

                $idn = $rdf->rdf_name($v1);
                $sql = "update rdf_data
                                set d_literal = $idn
                                where d_r1 = $d1
                                and d_p = $class
                                AND d_literal = $d3";
                $rlt = $this->db->query($sql);
                $this->export->export_Issue_Single($d1);
                redirect(base_url(PATH . 'ia/check'));
                exit;
                break;
        }
        $prop = 17;
        $sql =
            "
            SELECT * from (
            SELECT n_name, count(*) as total, d_literal
            FROM rdf_concept
            inner join rdf_data ON d_r1 = id_cc
            inner join rdf_name ON id_n = d_literal
            where d_p = $prop and d_literal > 0
            group by n_name, d_literal
            ) as tabela
            where total > 1
            order by total
            limit 1
            ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '';

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idl = $line['d_literal'];

            $sql = "SELECT * FROM rdf_data
                            INNER JOIN rdf_name ON d_literal = id_n
                            where d_literal = $idl
                            and d_p = $prop
                            limit 2";
            $rrr = $this->db->query($sql);
            $rrr = $rrr->result_array();
            $max = 0;
            $sx .= '<h4>Term:' . $rrr[0]['n_name'] . '</h4>';
            $fl = array();
            $sx = '';

            $n = array();
            $n[0] = 0;
            $n[3] = 0;
            $n[4] = 0;

            for ($y = 0; $y < count($rrr); $y++) {
                $ln = $rrr[$y];
                $fx = 'c/' . $ln['d_r1'] . '/name.ABNT';
                if (file_exists($fx)) {
                    array_push($fl, file_get_contents($fx));
                    $n[3] =  $n[3] + 1;
                } else {
                    $sx .=  ' ERRO NO arquivo ' . $fx . '<hr>';
                }

                $file = 'c/' . $ln['d_r1'] . '/name.nm';
                if (file_exists($file)) {
                    $t = file_get_contents($file);
                    $sx .= $t . '<hr>';
                }

                if ($ln['d_r1'] > $max) {
                    $max = $ln['d_r1'];
                }
            }
            /********************** Arquivo não existe */
            $sx .=  '<h2>Fase I</h2>';
            $ano = 0;
            if (($n[3] > 0) and (count($fl) > 0)) {
                $sx .= '<h4>Fase Ia</h4>';
                /********************** Anos da publicação */
                $a1 = sround($this->recupera_dados($fl[0], 'PY'));
                $a2 = sround($this->recupera_dados($fl[1], 'PY'));
                $ano = $a2;
                if ($a1 == $a2) {
                    $n[0] = 1;
                } else {
                    $n[0] = 0;
                }


                /********************* Primeiro autor */
                $v1 = $this->recupera_dados($fl[0], 'AU');
                $v2 = $this->recupera_dados($fl[1], 'AU');
                $n[1] = ($v1 == $v2);

                /********************* Primeiro autor */
                $IS1 = $this->recupera_dados($fl[0], 'IS');
                $IS2 = $this->recupera_dados($fl[1], 'IS');
                $n[11] = ($IS1 == $IS2);

                /********************* Sessão */
                $v1 = $this->recupera_dados($fl[0], 'M3');
                $v2 = $this->recupera_dados($fl[1], 'M3');
                $n[10] = ($v1);
                $n[11] = ($v2);

                /********************* Journal ********/
                $v1 = $this->recupera_dados($fl[0], 'T2');
                $v2 = $this->recupera_dados($fl[1], 'T2');
                $n[2] = (int)($v1 == $v2);
                $sx .= '<h4>Fase Ib</h4>';
                if ($this->neuro($n) == 1) {
                    $sx .= '<p style="color: red">Fase Ic - Remove</p>';
                    $rdf->remove_concept($max);
                    $sx .= '<meta http-equiv="refresh" content="2">';
                } else {
                    $sx .= '<h4>Fase Id - Neuro Not Equal</h4>';
                    if ($n[0] == 0) {
                        if ($n[2] == 1) {
                            $sx .= '<a href="' . base_url(PATH . 'ia/check/' . $max . '/YES_ANO/' . $ln['d_literal']) . '" class="btn btn-outline-primary">' . msg('ias_change_name') . '</a>';
                            $sx .= '<meta http-equiv="refresh" content="5; URL=' . base_url(PATH . 'ia/check/' . $max . '/YES_ANO/' . $ln['d_literal']) . '"/>';
                        } else {
                            /* Checar regra */
                            //$sx .= '<a href="'.base_url(PATH.'ia/check/'.$max.'/YES_ANO/'.$ln['d_literal']).'" class="btn btn-outline-primary">'.msg('ias_change_name').'</a>';
                            $sx .= 'Revista diferente ' . $line['d_literal'] . '-' . $max;
                            $sx .= '<meta http-equiv="refresh" content="5; URL=' . base_url(PATH . 'ia/check/' . $max . '/YES_ANO/' . $ln['d_literal']) . '"/>';
                        }
                    } else {
                        $sx .= 'Ano igual - ' . $a1 . '-' . $a2;
                        if ($n[2] == 1) {
                            $sx .= '<p style="color: red">Fase Ic - Remove</p>';
                            $rdf->remove_concept($max);
                            $sx .= '<meta http-equiv="refresh" content="5">';
                        } else {
                            $sx .= 'Revista Diferente';
                            $sx .= '<meta http-equiv="refresh" content="5; URL=' . base_url(PATH . 'ia/check/' . $max . '/YES_ANO/' . $ln['d_literal']) . '"/>';
                        }
                    }
                }
            } else {
                $sx .= 'NOT FOUND';
            }
        }
        $sx = '<div class="row"><div class="col-md-12">' . $sx . '</div></div>';
        return ($sx);
    }

    function neuro($n)
    {
        $rs = 0;
        if ($n[3] == 1) {
            return (1);
        }

        if (($n[0] == 1) and ($n[1] == 1) and ($n[2] == 1)) {
            $rs = 1;
        }
        return ($rs);
    }

    function recupera_dados($txt, $tp)
    {

        switch ($tp) {
            case 'PY':
                $t = substr($txt, strpos($txt, $tp . ' - ') + 5, 4);
                break;

            case 'TI':
                $t = substr($txt, strpos($txt, $tp . ' - ') + 5, strlen($txt));
                $t = substr($t, 0, strpos($t, chr(10)));
                $t = substr($t, 0, strpos($t, chr(13)));
                break;

            default:
                $t = substr($txt, strpos($txt, $tp . ' - ') + 5, strlen($txt));
                $t = substr($t, 0, strpos($t, chr(10)));
                $t = substr($t, 0, strpos($t, chr(13)));
        }
        return ($t);
    }
}
