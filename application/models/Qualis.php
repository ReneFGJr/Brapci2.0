<?php
class qualis extends CI_model {
       
    function journal_row()
        {
        $sx = '';

        /* Formulario */
        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$A', '', msg('qualis_list'), false, true));

        $sql = "select * from qualis.qualis_area order by area_nome";
        array_push($cp, array('$Q id_area:area_nome:' . $sql, '', msg('qualis_area'), true, true));

        $sql = "select * from qualis.qualis order by q_ano desc";
        array_push($cp, array('$Q id_q:q_descricao:' . $sql, '', msg('qualis_collection'), true, true));

        array_push($cp, array('$B', '', msg('show'), false, true));
        $form = new form;
        $sx = '<div class="col-md-12">' . $form -> editar($cp, '') . '</div>';
        
        
        if ($form->saved > 0)
            {
                $d['title'] = 'ROW';
                $sx = $this->collection_row(get("dd2"),get("dd3"));
            }
        
        $d['content'] =$sx;
        return($d);            
        }
    function collection_row($a,$c)
        {
            $sql = "SELECT * FROM qualis.qualis_collection_area
                    INNER JOIN qualis.qualis_area ON qca_area = id_area
                    INNER JOIN qualis.qualis ON qca_collection = id_q
                    INNER JOIN qualis.journals ON qca_journal = id_j
                    LEFT JOIN source_source ON (j_issn = jnl_issn) or (j_issn = jnl_eissn)
                    where qca_area = $a
                    AND qca_collection = $c
                    ORDER BY qca_qualis, j_name";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '';
            $qx = '';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $q = $line['qca_qualis'];
                    if ($qx != $q)
                        {
                            $sx .= '<div class="col-md-12" style="border-bottom: 1px solid #C0C0C0;">';
                            $sx .= '<h3>';
                            $sx .= 'Qualis '.$line['qca_qualis'];
                            $sx .= '</h3>';
                            $sx .= '</div>';
                            $qx = $q;
                        }
                    $link = '<a href="'.base_url(PATH.$line['id_j'].'/journal').'" style="color: #404040;">';
                    $linka = '</a>';
                    
                    $sx .= '<div class="col-md-2 text-center" style="border-bottom: 1px solid #C0C0C0;">';
                    $sx .= $link.$line['j_issn'].$linka;
                    $sx .= '</div>';

                    $sx .= '<div class="col-md-6" style="border-bottom: 1px solid #C0C0C0;">';
                    $sx .= $link.$line['j_name'].$linka;
                    $sx .= '</div>';
                    
                    $sx .= '<div class="col-md-2" style="border-bottom: 1px solid #C0C0C0;">';
                    $sx .= $line['j_country'];
                    $sx .= '</div>';                    
                    
                    $sx .= '<div class="col-md-1" style="border-bottom: 1px solid #C0C0C0;">';
                    $sx .= $line['qca_qualis'];
                    $sx .= '</div>'; 
                    
                    if ($line['jnl_frbr'] > 0)                                       
                        {
                            $sx .= '<div class="col-md-1" style="border-bottom: 1px solid #C0C0C0;">';
                            $sx .= 'Brapci';
                            $sx .= '</div>'; 
                        } else {
                            $sx .= '<div class="col-md-1" style="border-bottom: 1px solid #C0C0C0;">';
                            $sx .= '&nbsp;';
                            $sx .= '</div>';                            
                        }
                }
            return($sx);
        }
    function le_areas($id)
        {
            $sql = "SELECT * FROM qualis.qualis_collection_area
                        inner join qualis.qualis_area ON qca_area = id_area
                        inner join qualis.qualis ON qca_collection = id_q
                        where qca_journal = $id
                        order by q_ano desc";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            return($rlt);
        }
    
    function le($id)
        {
            $sql = "select * from qualis.journals where id_j = $id ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $line = $rlt[0];
            $line['qualis'] = $this->le_areas($id);
            return($line);
        }
    function journal_show($id)
        {
            $sx = '';
            $d = $this->le($id);
            $sx .= '<div class="col-md-12">';
            $sx .= $d['j_issn'];
            $sx .= '</div>';
            
            $sx .= $this->lista_qualis($d);
            
            $data['title'] = $d['j_name'];
            $data['content'] = $sx;
            return($data);
        }
    function lista_qualis($rlt)
        {
            $sx = '';
            $rlt = $rlt['qualis'];
            $sx .= '<div class="col-md-5">';
            $sx .= '<b>'.msg('Collection').'</b>';
            $sx .= '</div>';
            $sx .= '<div class="col-md-6">';
            $sx .= '<b>'.msg('Area').'</b>';
            $sx .= '</div>';
            $sx .= '<div class="col-md-1">';
            $sx .= '<b>'.msg('Qualis').'</b>';
            $sx .= '</div>';
            for ($r=0;$r < count($rlt);$r++)
                {
                     $sx .= '<div class="col-md-5">';
                     $sx .= $rlt[$r]['q_descricao'];
                     $sx .= '</div>';           

                     $sx .= '<div class="col-md-6">';
                     $sx .= $rlt[$r]['area_nome'];
                     $sx .= '</div>';           

                     $sx .= '<div class="col-md-1">';
                     $sx .= $rlt[$r]['qca_qualis'];
                     $sx .= '</div>';           

                }
            return($sx);
        }
    function resume() {
        $sx = '<ul>';
        $sx .= '<li>' . '<a href="' . base_url(PATH . 'qualis/0/inport') . '">' . msg('qualis_inport') . '</a>';
        $sx .= '<li>' . '<a href="' . base_url(PATH . 'qualis/0/row') . '">' . msg('qualis_row') . '</a>';
        $sx .= '</ul>';
        return ($sx);
    }

    function inport() {
        $sx = '';

        /* Formulario */
        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$T80:10', '', msg('qualis_list'), true, true));

        $sql = "select * from qualis.qualis_area order by area_nome";
        array_push($cp, array('$Q id_area:area_nome:' . $sql, '', msg('qualis_area'), true, true));

        $sql = "select * from qualis.qualis order by q_ano desc";
        array_push($cp, array('$Q id_q:q_descricao:' . $sql, '', msg('qualis_collection'), true, true));

        array_push($cp, array('$B', '', msg('inport'), false, true));
        array_push($cp, array('$M', '', msg('O texto deve ter essa estrutura sem o cabeçalho (Header): ISSN;Nome da publicação;Qualis'), false, true));
        $form = new form;
        $sx = '<div class="col-md-12">' . $form -> editar($cp, '') . '</div>';

        if ($form -> saved > 0) {
            $sx = '<div class="col-md-12">';
            $sx .= $this -> inport_qualis(get("dd2"), get("dd1"), get("dd3"));
            $sx .= '</div>';
        }
        return ($sx);
    }

    function inport_qualis($area, $lista, $collection) {
        $l = troca($lista, ';', '.,');
        $l = troca($l, chr(13), ';');
        $l = troca($l, chr(10), ';');
        $l = troca($l, chr(9), '');
        $ln = splitx(';', $l);

        /* Importação da lista */
        $sx = '<ul>';
        for ($r = 0; $r < count($ln); $r++) {
            $l = $ln[$r];
            $l = troca($l, '.,', ';');
            $c = splitx(';', $l);

            $id_journal = $this -> journal($c[0], $c[1], '');
            
            $act = $this->qualis_update($id_journal,$area,$collection,$c[2]);
            $link = '<a href="'.base_url(PATH.'qualis/'.$id_journal.'/journal').'" target="_new_'.$id_journal.'" style="color: grey;">';
            $sx .= '<li>'.$link.$c[0].' '.$c[1].'</a> '.$act.' '.$c[2].'</li>';
        }
        $sx .= '</ul>';
        return($sx);
    }

    function qualis_update($j,$a,$c,$qualis)
        {
            $sql = "select * from qualis.qualis_collection_area 
                        where qca_collection = $c
                        AND qca_area = $a
                        AND qca_journal = $j";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) == 0)
                {
                    $sql = "insert into qualis.qualis_collection_area
                                (qca_collection, qca_area, qca_journal,
                                 qca_qualis)
                             values
                                ($c,$a,$j,
                                '$qualis')";
                    $rlt = $this->db->query($sql);
                    $sx = '<span style="color: green"><b>'.msg('insered').'</b></span>'.cr();
                } else {
                    $sx = '<span style="color: grey"><b>'.msg('none').'</b></span>'.cr();
                    if ($rlt[0]['qca_qualis'] != $qualis)
                        {
                            $sx = '<span style="color: blue"><b>'.msg('updated').'</b></span>'.cr();
                            $sql = "update qualis.qualis_collection_area
                                        set qca_qualis = '$qualis'
                                        where id_qca = ".$rlt[0]['id_qca'];
                            $rlt = $this->db->query($sql);
                        }
                }
            return($sx);
        }

    function journal($issn, $name, $country) {
        $name = troca($name,"'","´");
        $issn = $this->issnl($issn);
        $sql = "select * from qualis.journals  where j_issn = '$issn'";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) == 0)
            {
                $sqli = "insert into qualis.journals 
                        (j_issn, j_name, j_country, j_qualis)
                        values
                        ('$issn','$name','$country',1)";
                $rlti = $this->db->query($sqli);
                sleep(1);                        
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
            }
       $id = $rlt[0]['id_j'];
       return($id);
    }
    
    function issnl($issn)
        {
            $sql = "select * from qualis.issn_l where issn = '$issn'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) > 0)
                {
                    $issn = $rlt[0]['issn_l'];
                }
            return($issn);
        }

}
?>
