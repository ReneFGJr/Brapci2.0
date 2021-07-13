<?php
class pqs extends CI_Model  {
    var $table = 'brapci_pq.bolsistas';
    var $table_bolsas = 'brapci_pq.bolsas';
    var $table_bolsas_tipo = 'brapci_pq.modalidades';

    function index($d1,$d2,$d3,$d4)
    {
        $sx = '<h1>'.msg("BasePQ").'</h1>';
        switch($d1)
        {
            case 'edit':
                $sx .= $this->edit($d2);
            break;

            default: 
            $sx = $this->row_pq($d2,$d3);
        }
        return($sx);
    }

    function le($id)
        {
            $sql = "select * from ".$this->table." where id_bs = ".$id;
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) > 0)
                {
                    return($rlt[0]);
                }
        }

    function edit($id)
        {
            $form = new form;
            $form->id = $id;
            $cp = array();
            array_push($cp,array('$H8','id_bs','id_bs',false,false));
            array_push($cp,array('$S100','bs_nome','bs_nome',True,True));
            array_push($cp,array('$I8','bs_rdf_id','bs_rdf_id',True,True));
            array_push($cp,array('$S100','bs_lattes','bs_lattes',False,True));
            

            $sx = $form->editar($cp,$this->table);
            $line = $this->le($id);

            $sx .= '<a href="'.base_url(PATH.'?q='.$line['bs_nome'].'&type=2').'" target="_new'.$line['id_bs'].'">Busca ...</a>';
            if ($form->saved > 0)
                {
                    redirect(base_url(PATH.'pq'));
                }
            return($sx);
        }
    function row_pq($d1='',$d2='')
    {
        $sx = '<h1>Bolsistas PQ</h1>';
        $sql = "select bs_rdf_id, bs_nome, id_bs, bs_lattes, count(*) as bolsas,
            min(bs_start) as bs_start,
            max(bs_finish) as bs_finish
         from ".$this->table." 
        inner join ".$this->table_bolsas." ON bb_person = id_bs
        inner join ".$this->table_bolsas_tipo." ON bs_tipo = id_mod
        group by bs_rdf_id, bs_nome, id_bs, bs_lattes
        order by bs_nome
        ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $tot = 0;
        $sx .= '<table class="table">';
        $sx .= '<tr><th>Pesquisador</th><th>Bolsa</th><th>Início</th><th>Fim</th><th>IES</th></tr>';
        for ($r=0;$r < count($rlt);$r++)
        {
            $tot++;
            $line = $rlt[$r];
            $id_rdf = $line['bs_rdf_id'];
            if ($id_rdf > 0)
            {
                $linkrdf = '<a href="'.base_url(PATH.'v/'.$id_rdf).'" target="_new">';
                $linkrdfa = '</a>';
            } else {
                $linkrdf = '';
                $linkrdfa = '';
            }
            $link = '<a href="'.$line['bs_lattes'].'" target="_new">Lattes</a>';
            $sx .= '<tr>';
            $sx .= '<td>';
            $sx .= $linkrdf.$line['bs_nome'].$linkrdfa;
            $sx .= '</td>';
            
            $sx .= '<td align="center">'.$line['bolsas'].'</td>';
            
            $sx .= '<td class="text-center">'.stodbr($line['bs_start']).'</td>';
            $sx .= '<td class="text-center">'.stodbr($line['bs_finish']).'</td>';
            
            //$sx .= '<td>'.$line['BS_IES'].'</td>';
            $sx .= '<td>'.'<a href="'.base_url(PATH.'pq/edit/'.$line['id_bs']).'">[ed]</a></td>';
            $sx .= '</tr>';
        }
        $sx .= '<tr><td colspan=10>Total '.$tot.'</td></tr>';
        $sx .= '</table>';
        return($sx);
    }
}