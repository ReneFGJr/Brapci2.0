<?php
defined("BASEPATH") or exit("No direct script access allowed");

/**
 * CodeIgniter Form Helpers
 *
 * @package     CodeIgniter
 * @subpackage  IA
 * @category    IA-Cited
 * @author      Rene F. Gabriel Junior <renefgj@gmail.com>
 * @link        http://www.sisdoc.com.br/CodIgniter
 * @version     v0.21.02.16
 */

class ias_cited extends CI_Model
{
    var $cities = array();
    var $base = 'brapci_cited.';

    function index($d1, $d2, $d3)
    {
        switch ($d1) {
            case 'stem':
                $sx = $this->nlp2();
                break;
            case 'ed':
                $sx = $this->ed_cited($d2);
                break;
            case 'journal':
                $sx = $this->journal_ed($d1, $d2);
                break;
            case 'status':
                $sx = $this->journal_status();
                break;
            case 'import_journals':
                $sx = $this->journal_import();
                break;
            case 'process':
                $sx = $this->nlp_cited($d1, $d2);
                break;
            default:
                $action = array('status','process','import_journals','stem');
                $sx = '';
                $sx .= '<h2>'.msg("Cited").'</h2>';
                $sx .= '<ul>';
                for ($r=0;$r < count($action);$r++)
                    {
                        $link = '<a href="'.base_url(PATH.'ia/cited/'.$action[$r]).'">';
                        $linka = '</a>';
                        $sx .= '<li>'.$link.$action[$r].$linka.'</li>';
                    }
                $sx .= '</ul>';
                
        }
        return ($sx);
    }

    /******************************************************************/
    function nlp2()
        {
            $sql = "select * from ".$this->base."cited_article 
                        where ca_tipo = 1 and ca_journal = 0
                        limit 1";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $line = $rlt[0];
            $id = $line['id_ca'];
            $txt = $line['ca_text'];

            $this->load->helper('nlp');
            $nlp = new nlp;
            $sx = '<ul>';
            $w = array('Médicos','Médicas','Médicina');
            $w = explode(' ',$txt);
            for ($r=0;$r < count($w);$r++)
                {
                    $txt = $w[$r];
                    $sx .= '<li>'.$nlp->stem($txt).'</li>';
                }
            $sx .= '</ul>';            
            return($sx);
        }
    function journal_import()
        {
            $sx = '';
            $form = new form;
            $cp = array();
            array_push($cp,array('$H8','','',false,false));
            array_push($cp,array('$A1','',msg('Journal Import'),false,false));
            array_push($cp,array('$T80:10','',msg('import'),true,true));
            $sx = $form->editar($cp,'');

            if ($form->saved > 0)
                {
                    $txt = get("dd2");
                    $txt = troca($txt,';',',');
                    $txt = troca($txt,chr(13),';');
                    $txt = troca($txt,chr(10),';');
                    $ln = splitx(';',$txt);

                    for ($r=0;$r < count($ln);$r++)
                        {
                            $l = $ln[$r];
                            if (strpos($l,'/') > 0) { $l = substr($l,0,strpos($l,'/')); }
                            
                            $la = ascii($l);
                            $la = UpperCase($la);
                            $sql = "select * from ".$this->base."cited_journal
                                        where cj_name_asc = '$la' ";
                            $rlt = $this->db->query($sql);
                            $rlt = $rlt->result_array();
                            $sx .= '<br>'.$l;
                            if (count($rlt) == 0)
                                {
                                    $sx .= ' <b><span style="color: green">NEW</span></b>';
                                    $sql = "insert into  ".$this->base."cited_journal 
                                                (cj_name, cj_issn, cj_qualis)
                                                values
                                                ('$la','','')";
                                    $this->db->query($sql);
                                } else {
                                    $sx .= ' <b><span style="color: gray">PASS</span></b>';
                                }
                        }
                }
                $this->journal_update();
            return($sx);
        }
    function journals()
    {
        $jnl = array();
        $file = '_ia/domain_journals.sw';
        $exe = file_get_contents($file);
        eval($exe);
        return ($jnl);
    }

    function journal_status()
    {   
        $sx = '';
        $sql =  "select count(*) as toDO from " . $this->base . "cited_article where ca_tipo = 1 and ca_journal > 0 ";
        $sql .= " UNION ";
        $sql .= "select count(*) as toDO from " . $this->base . "cited_article where ca_tipo = 1 and ca_journal = 0 ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $line = $rlt;
        $sx .= '<table width="400" style="border: 1px solid #000000">
                <tr class="text-center"><th>Feito</th><th>Para fazer</th></tr>
                <tr class="text-center">
                <td>'.$line[0]['toDO'].'</td>
                <td>'.$line[1]['toDO'].'</td>
                </tr>
                </table>';
                
        /******************************************************** Periódicos */
        $sql = "select * from (
                    select ca_journal, cj_name, count(*) as total 
                    from " . $this->base . "cited_article
                    inner join " . $this->base . "cited_journal ON ca_journal = id_cj
                    where ca_tipo = 1
                    group by ca_journal, cj_name
                    ) as tabela
                    order by total desc, cj_name";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx .= '<table width="100%">';
        $sx .= '<tr class="text-center"><th width="10%">Citações</th><th>Revista</th></tr>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<tr style="border-bottom: 1px #808080 solid;">';

            $sx .= '<td align="center">';
            $sx .= $line['total'];
            $sx .= '</td>';

            $sx .= '<td>';
            $sx .= nbr_author($line['cj_name'], 7);
            $sx .= '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return ($sx);
    }
    function journal_update()
    {
        $sql = "select * from " . $this->base . "cited_journal 
                    where cj_name_asc = ''";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $n = $line['cj_name'];
            $n = ascii($n);
            $n = UpperCase($n);
            if (strpos($n, '(') > 0) {
                $n = substr($n, 0, strpos($n, '('));
            }
            $idcj = $line['id_cj'];
            $sql = "update " . $this->base . "cited_journal ";
            $sql .= " set ";
            $sql .= " cj_name_asc = '$n' ";
            $sql .= " where id_cj = $idcj";
            $this->db->query($sql);
        }
    }
    function where($d1, $t = 0)
    {
        if (strlen($d1) > 0) {
            $j = troca($d1, ' ', ';');
            $j = splitx(';', $j . ';');
            $wh = '';
            for ($r = 0; $r < (count($j) - $t); $r++) {
                $name = UpperCase(ascii($j[$r]));

                if ((strlen($name) > 2) and ($name != 'AND')) {
                    if (strlen($wh) > 0) {
                        $wh .= ' AND ';
                    }
                    $wh .= " (cj_name_asc like '%$name%') ";
                }
            }
            if ($wh == '') {
                $wh = '1=1';
            }
            /* SQL */
            $sql = "select * from " . $this->base . "cited_journal 
                where (" . $wh . ") and (cj_name <> '') and (cj_use = 0)
                ";
            $sql .= " order by cj_name";
            $sql .= " limit 150 ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            return ($rlt);
        }
    }
    function journal_ed($d1)
    {
        $d1 = get("dd1");


        $op = '0:--Nova';
        $t = 0;
        $rlt = array();
        while ((count($rlt) == 0) and ($t < 4)) {
            $rlt = $this->where($d1, $t);
            $t++;
        }
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $name = trim($line['cj_name']);
            $name = troca($name, ':', '-');
            $name = troca($name, '&', ' ');
            $op .= '&' . $line['id_cj'] . ':' . $name;
        }

        /* POST */
        if (strlen(get("dd1")) == 0) {
            $_POST['dd1'] = $d1;
            $_GET['dd1'] = $d1;
        }


        $cp = array();
        array_push($cp, array('$H', '', '', false, false));
        array_push($cp, array('$S80', 'cj_name', msg('cj_name'), True, True));
        array_push($cp, array('$B8', '', msg('include'), False, True));
        array_push($cp, array('$M', '', nbr_author($d1, 7), False, True));
        array_push($cp, array('$O ' . $op, 'cj_use', msg('cj_use'), True, True));
        array_push($cp, array('$S40', 'cj_place_text', msg('cj_place_text'), False, True));
        array_push($cp, array('$S10', 'cj_qualis', msg('cj_qualis'), False, True));
        array_push($cp, array('$S40', 'cj_issn', msg('cj_issn'), False, True));
        array_push($cp, array('$HV', 'cj_name_asc', '', False, True));

        $form = new form;
        $form->id = 0;
        $sx = $form->editar($cp, $this->base . 'cited_journal');
        if ($form->saved > 0) {
            $this->journal_update();
            $sx .= '<script>wclose();</script>';
        }
        return ($sx);
    }
    function export_journals()
    {
        $sql = "select * from " . $this->base . "cited_journal";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();

        $dt = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $s = strzero(strlen(trim($line['cj_name_asc'])), 3);
            if ($line['cj_use'] == 0) {
                $s .= ';' . $line['id_cj'] . ';';
            } else {
                $s .= ';' . $line['cj_use'] . ';';
            }
            $s .= trim($line['cj_name_asc']);
            array_push($dt, $s);
        }
        asort($dt);
        $sx = '';
        foreach ($dt as $id => $t) {
            $ln = splitx(';', $t);
            $t = ascii($ln[2]);
            $t = lowercase($t);
            $sx .= '$jnl[\'' . $t . '\'] = ' . $ln[1] . ';' . cr();
        }
        $file = '_ia/domain_journals.sw';
        file_put_contents($file, $sx);
    }
    function ed_cited($id)
    {
        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', 'id_ca', '', false, false));
        array_push($cp, array('$T80:5', 'ca_text', msg('ca_text'), false, false));
        $sql = "select * from " . $this->base . "cited_type";
        array_push($cp, array('$Q id_ct:ct_name:' . $sql, 'ca_tipo', msg('ca_tipo'), True, false));
        array_push($cp, array('$B8', '', msg('save'), false, false));
        $form->id = $id;
        $sx = $form->editar($cp, $this->base . 'cited_article');
        if ($form->saved > 0) {
            $sx .= '<script>wclose();</script>';
        }
        return ($sx);
    }

    function link($dt)
    {
        $link = '<span onclick="newxy(\'' . base_url(PATH . 'ia/cited/ed/' . $dt['id_ca']) . '\',800,600);" style="cursor: pointer;">';
        return ($link);
    }

    function nlp_cited($d1, $d2)
    {
        $d2 = round($d2);
        echo '==>' . $d1 . '-->' . $d2;
        $this->export_journals();
        $sx = '<h3>Process Cited</h3>';
        $sql = "select * from " . $this->base . "cited_article
                    where ca_tipo = 1 and ca_journal = 0
                    order by ca_text 
                    limit 5
                    offset $d2";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();

        if (count($rlt) == 0) {
            $sx = 'FIM';
            return ($sx);
        }
        $lock = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = $this->link($line);
            $linka = '</span>';

            $t = $link . $line['ca_text'] . $linka . ' - ' . $line['id_ca'];
            $idj = $this->nlp_jounal($line['ca_text']);
            if ($idj[0] > 0) {
                $sql = "update " . $this->base . "cited_article set
                                    ca_journal = $idj[0]
                                    where id_ca = " . $line['id_ca'];
                $this->db->query($sql);
                $cor = '<span style="color: blue">';
                $lock++;
            } else {
                $cor = '<span style="color: red">';
            }
            $sx .= $t . $cor . $idj[1] . '</span><hr>';
        }
        /* Pula somente se atualizou algum registro */
        if ($lock >= 0) {
            $sx .= '<META http-equiv="refresh" content="15;URL=' . base_url(PATH . 'ia/cited/process/' . (round($d2) + 4)) . '">';
        }
        return ($sx);
    }
    function nlp_jounal($t)
    {
        $sx = '';
        $jnl = $this->journals();
        $t = str_replace(array(',', '.', '!', '?','(',')'), ';', $t);
        $t = splitx(";", $t);

        for ($r = (count($t) - 1); $r >= 0; $r--) {
            $tt = trim($t[$r]);
            $tt = ascii($tt);
            $tt = lowercase($tt);
            $tt = troca($tt, '&', '');
            $tt = troca($tt, '<', '$lt;');
            $tt = troca($tt, '>', '$gt;');
            $link = '<span onclick="newxy(\'' . base_url(PATH . 'ia/cited/journal/?dd1=' . $tt) . '\',800,600);" style="cursor: pointer;">';
            $sx = '[' . $link . nbr_author($tt, 7) . '</span>]; ' . $sx;
            if (strlen($tt) > 0) {
                if (isset($jnl[$tt])) {
                    return (array($jnl[$tt], ''));
                }
            }
        }
        if (strlen($sx) > 0) {
            $sx = '<br>' . $sx;
        }
        return (array(0, $sx));
    }

    function neuro_type_source($txt, $id = 0)
    {
        $n = array();
        $n[0] = $this->tem_nr_vl($txt);
        $n[1] = $this->tem_dois_pontos($txt);
        $n[2] = $this->disponível_em($txt);
        $n[3] = $this->e_um_evento($txt);
        $n[4] = $this->tem_in($txt);
        $n[5] = $this->tem_organizador($txt);
        $n[6] = $this->e_uma_tese($txt);
        $n[7] = $this->e_uma_dissertacao($txt);
        $n[8] =  $this->e_um_tcc($txt);
        $n[9] = $this->tem_cidade($txt);
        $n[10] =  $this->e_uma_lei($txt);

        $sx = 0;

        /********************************* Regras 
         * 1 - Journal
         * 5 - Event
         * 6 - Tese
         * 7 - Dissetação
         * 8 - TCC
            /* 20 - Lei
         */
        /***************** LEI  */
        if (($n[0] == 0) and ($n[10] == 1)) {
            return (20);
        }

        /***************** Analisa outros */
        if (
            ($n[0] == 1) /* Tem numero ou volume */
            and ($n[5] != 1) /* Não tem editor, organizador */
        ) {
            /* Journal */
            return (1);
        }

        /************** Outros Tipo ********/
        else {
            if ($n[3] == 1) {
                /* Evento */
                return (5);
            } else {
                /****************************** É livro ou capítulo */
                $book = $this->is_book($n);
                $tede = $this->is_tede($n);
                if (($book > 0) and ($tede == 0)) {
                    return ($book);
                } else
                /************ Literatura Cinzenta */
                {
                    if ($tede) {
                        return (6 + $n[6] + $n[7] * 2 + $n[8] * 3);
                    }
                }
            }
        }
        if ($this->is_link($n)) {
            return (15);
        }

        if (perfil("#ADM")) {
            echo $txt;
            echo '<br>';
            echo $this->ias->show_sensores($n);
            echo '<hr>';
        }
        return ($sx);
    }
    function is_book($n)
    {
        if ($n[9] == 1) /* Tem local */ {
            if ($n[4] == 1) {
                return (3);
            } else {
                return (2);
            }
        }
    }
    function is_tede($n)
    {
        if (($n[6] == 1)
            or ($n[7] == 1)
            or ($n[8] == 1)
        ) {
            return (1);
        }
        return (0);
    }
    function is_link($n)
    {
        if ($n[2] == 1) /* Tem Link Acesso */ {
            if (($n[0] + $n[4] + $n[9]) == 0) {
                return (1);
            }
        }
        return (0);
    }
    function e_uma_lei($txt)
    {
        $a = array(' Lei nº ', 'Decreto nº', '. Lei');
        return ($this->locate($txt, $a));
    }
    function e_um_tcc($txt)
    {
        $a = array(' Trabalho de conclusão de curso ');
        return ($this->locate($txt, $a));
    }
    function e_uma_tese($txt)
    {
        $a = array(' Tese ', '(Doutorado', ' Thesis ', 'Tese (Doutorado', '(Tese de doutoramento', '(Tese de Doutoramento');
        return ($this->locate($txt, $a));
    }
    function e_uma_dissertacao($txt)
    {
        $a = array(' Dissertação ', '(Mestrado');
        return ($this->locate($txt, $a));
    }
    function tem_in($txt)
    {
        $a = array(' In: ');
        return ($this->locate($txt, $a));
    }
    function tem_organizador($txt)
    {
        $a = array('(Org.)', '(Ed.)', '(Coord.)');
        return ($this->locate($txt, $a));
    }
    function disponível_em($txt)
    {
        $a = array(
            'Available:', 'Available in:',
            'Disponível em', 'Disponível:', 'Disponívelem',
            'Acesso em:', 'Access in:'
        );
        return ($this->locate($txt, $a));
    }
    function tem_dois_pontos($txt)
    {
        $txt = $this->remove($txt, array('Disponível', 'Disponivel', 'Acesso:', 'Acesso em:', 'Access in:'));
        $a = array(': ');
        return ($this->locate($txt, $a));
    }
    function tem_nr_vl($txt)
    {
        $a = array(', v.', ', V.', ', n.');
        return ($this->locate($txt, $a));
    }
    function e_um_evento($txt)
    {
        $a = array('Anais...', 'Anais…', 'Proceedings…', 'Proceedings...', 'Anais eletrônicos...', 'Anais [...]', 'Actas...', 'Actas…');
        return ($this->locate($txt, $a));
    }
    function tem_cidade($txt)
    {
        if (count($this->cities) == 0) {
            $file = '_ia/domain_places.txt';
            $dt = $this->ias->file_get_subdomain($file);
            $this->cities = $dt;
        }
        $dt = $this->cities;

        /* recupera arquivo */
        $txt = ascii($txt);
        $txt = strtolower($txt);
        $txt = str_replace(array(',', '?', ':', ' :', ' -', ' –'), '.', $txt);
        $txt = str_replace(array('['), '', $txt);

        for ($r = 0; $r < count($dt); $r++) {
            $city = $dt[$r];
            //echo '<br>==>'.$city.'=>'.strpos($txt,'. '.$city);
            if (
                (strpos($txt, '. ' . $city))
            ) {
                return (1);
            }
        }
        return (0);
    }

    function locate($txt, $a)
    {
        for ($r = 0; $r < count($a); $r++) {
            if (strpos($txt, $a[$r])) {
                return (1);
            }
        }
        return (0);
    }
    function remove($txt, $a)
    {
        for ($r = 0; $r < count($a); $r++) {
            if ($pos = strpos($txt, $a[$r])) {
                $txt = substr($txt, 0, $pos);
            }
        }
        return ($txt);
    }
    /********************************************************************* */
    function neuro_cited($txt, $data)
    {
        //echo '<pre>'.$txt.'</pre>';

        $terms = array(
            'REFERÊNCIAS (ESTILO <SECAOSEMNUM>)',
            'REFERÊNCIAS BIBLIOGRÁFICAS',
            'Referências Bibliográficas',
            'Referências Bibliográficas',
            'Referências bibliográficas',
            'REFERÊNCIAS',
            'REFERENCIAS',
            'REFERENCES',
            'Referências',
            'References',
            'Reférences',
            'Referencias',
            'BIBLIOGRAFIA',
            'Referëncias',
            'Références',
        );
        $ref = '';

        $txt = troca($txt, chr(13), chr(10));
        for ($r = 0; $r < count($terms); $r++) {
            $pos = strpos($txt, $terms[$r] . chr(10));
            if ($pos > 0) {
                while ($pos > 0) {
                    $ref = substr($txt, $pos, strlen($txt));
                    $txt = $ref;
                    $pos = strpos($txt, $terms[$r] . chr(10));
                }
            }
        }

        /************************************** Ve se termina */
        for ($r = 0; $r < count($data['title']); $r++) {
            $tit = $data['title'][$r];
            $tite = $this->ias->split_word($tit, ' ', 7);
            $pos = strpos($ref, $tite);
            if ($pos > 0) {
                $ref = substr($ref, 0, $pos);
            }
        }
        /************************************** Ve se termina com o título em inglês */
        for ($r = 0; $r < count($data['title']); $r++) {
            $tit = $data['title'][$r];
            $tite = $this->ias->split_word($tit, ' ', 7);
            $pos = strpos($ref, $tite);
            if ($pos > 0) {
                $ref = substr($ref, 0, $pos);
            }
        }
        /************************************** Ve se termina com abstract */
        if (
            ($pos = strpos($ref, 'Abstract:'))
            or ($pos = strpos($ref, 'Abstract' . chr(10)))
            or ($pos = strpos($ref, 'ABSTRACT' . chr(10)))
            or ($pos = strpos($ref, 'Resumo:'))
        ) {
            $ref = substr($ref, 0, $pos);
            while (substr($ref, strlen($ref) - 1, 1) != '.') {
                $ref = substr($ref, 0, strlen($ref) - 1);
            }
        }
        /********************************* Remove so numero */
        $ref = $this->ias->to_line($ref);
        $txt = '';
        for ($rr = 1; $rr < count($ref); $rr++) {
            $ln = $ref[$rr];
            if ($this->ias->sonumero($ln, false) == 1) {
                /* Nada */
            } else {
                $txt .= $ref[$rr] . chr(10) . chr(13);
            }
        }
        $ref = $this->process($txt, $data);
        return ($ref);
    }

    function process($txt, $data)
    {
        $sx = '<ol>';
        $ln = $this->ias->to_line($txt);
        $tb = '';
        $tn = '';
        $rsp = '';
        $err = 0;
        $nl = 0; /* Nova linha anterior */
        $cx = 0; /* Terminoi em caixa alta a linha anterior */
        $last_collon = 0; /* Termina em dois pontos*/
        for ($r = 0; $r < count($ln); $r++) {
            $t = $ln[$r];
            //$tb .= $t.';';
            $n = array();
            /* Tem ano no texto */
            $n[0] = $this->ias->tem_ano($t);

            /* Tudo em caixa alta */
            $n[1] = $this->ias->caixa_alta($t);

            /* Primeira Palavra em caixa alta */
            $n[2] = $this->ias->caixa_alta_palavra($t);

            /* So numero */
            $n[3] = $this->ias->sonumero($t);

            /* Tamanho mínimo */
            $n[4] = $this->ias->linesize($t, 25);

            /* registro anterior termina com ano */
            $n[6] = $this->ias->termina_com_ano($tn);
            $tn = $t;

            /* termina com caixa alta */
            $n[7] = $this->ias->termina_com_caixa_alta($tn);
            $tn = $t;

            /* termina com caixa alta */
            $n[10] = $last_collon;
            $n[11] = $this->ias->termina_com_caracter($tn, ':');
            $last_collon = $n[11];
            $tn = $t;

            /* registro anterior termina com caixa alta */
            $n[8] = $cx;
            $cx = $n[7];

            /* registro com titulação do autor */
            $n[9] = $this->ias->tem_titulacao($tn);
            if ($n[9] == 1) {
                $r = count($ln) + 1;
                $t = '';
            }


            /* Nova linha no registro anterior */
            $n[5] = $nl;
            $nl = $this->rede_neuro($n);

            if ($nl == 1) {
                $rsp .= cr();
            }
            $t = troca($t, '.,', ';');
            $rsp .= troca($t, '.,', ';') . ' ';

            $sx .= '<li>';
            $sx .= '<tt>' . $t . '</tt>';
            $sx .= '<br/>';
            $nrn = $this->rede_neuro($n);
            $tb .=  $nrn . ';';

            $sx .= '[' . $nrn . '] ' . $this->ias->show_sensores($n);
            $tb .= $this->ias->show_sensores($n, 'T') . cr();
            $sx .= '</li>';
        }
        $sx .= '</ol>';

        /************************************************** Busca por ____ */
        if (strpos($rsp, '___') > 0) {
            $ln = $this->ias->to_line($rsp);
            $last = '';
            $rsp = '';
            for ($r = 0; $r < count($ln); $r++) {
                $l = trim($ln[$r]);
                if (substr($l, 0, 2) == '__') {
                    $ln[$r] = $this->author_citado($ln[$r], $last);
                }
                $last = $ln[$r];
                $rsp .= $ln[$r] . cr();
            }
        }
        $sx = '<hr><b>Referências</b>
            <form action="' . base_url(PATH . 'ia/nlp/save/cited/' . $data['id']) . '" method="post">
            <textarea name="dd1" class="form-control" rows=15>' . $rsp . '</textarea>
            <input type="submit" value="salvar referências" name="action">
            </form>';
        return ($sx);
    }

    function author_citado($n, $l)
    {
        $nnl = substr($l, 0, strpos($l, '.'));

        $n = troca($n, '_ ', '_.');
        $n = troca($n, '_;', '_.');
        $n = troca($n, '_,', '_.');

        $n = troca($n, '__.', $nnl . '.');
        $n = troca($n, '_', '');
        return ($n);
    }
    function rede_neuro($n)
    {
        $rs = 0;
        /* Primeira maiusculoa */
        if (($n[2] == 1) and ($n[10] == 0)) {
            /* so numero */
            if ($n[3] == 0) {
                /* anterior novo linha */
                if ((($n[5] == 0) or ($n[6] == 1)) and ($n[8] == 0)) {
                    $rs = 1;
                }
            }
        }
        return ($rs);
    }
}
