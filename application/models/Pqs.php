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
            default: 
            $sx = $this->row_pq($d2,$d3);
        }
        return($sx);
    }
    function row_pq($d1='',$d2='')
    {
        $sx = '<h1>Bolsistas PQ</h1>';
        $sql = "select * from ".$this->table." 
        inner join ".$this->table_bolsas." ON bb_person = id_bs
        inner join ".$this->table_bolsas_tipo." ON bs_tipo = id_mod
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
            
            $sx .= '<td>'.$line['mod_sigla'].$line['bs_nivel'].'</td>';
            
            $sx .= '<td class="text-center">'.stodbr($line['bs_start']).'</td>';
            $sx .= '<td class="text-center">'.stodbr($line['bs_finish']).'</td>';
            
            $sx .= '<td>'.$line['BS_IES'].'</td>';
            $sx .= '</tr>';
        }
        $sx .= '<tr><td colspan=10>Total '.$tot.'</td></tr>';
        $sx .= '</table>';
        return($sx);
    }
}