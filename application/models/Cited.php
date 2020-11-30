<?php
class Cited extends CI_Model 
{
        var $base = 'brapci_cited.';
    function show_ref($id)
        {
            $sql = "select * from ".$this->base.'cited_article 
                    where ca_rdf = '.round($id).'
                    order by ca_ordem';
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            $sx = '<h4>'.msg('References').'</h4>';
            $sx .= '<ul>';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $l = $rlt[$r];
                    $sx .= '<li>'.$l['ca_text'].'</li>';
                }
            $sx .= '</ul>';
            return($sx);
        }
    function save_ref($id)
        {
            $ref = get("dd1");
            $ref = $this->ias->to_line($ref);
            if (count($ref) > 0)
                {
                    $this->delete_ref($id);
                    for ($item=0;$item < count($ref);$item++)
                        {
                            $l = $ref[$item];
                            $this->save_ref_item($l,$id,$item+1);
                        }                    
                }
            redirect(base_url(PATH.'v/'.$id));
        }
    function delete_ref($id)
        {
            $sql = "delete from ".$this->base.'cited_article where ca_rdf = '.round($id);
            $this->db->query($sql);
        }
    function save_ref_item($l,$id,$item)
        {
            $sql = "insert into ".$this->base."cited_article ";
            $sql .= "(ca_rdf, ca_journal, ca_year,
                       	ca_vol, ca_nr, ca_pag,
                        ca_tipo, ca_text, ca_status,
                        ca_ordem)";
            $sql .= " values ";
            $sql .= "($id,0,0,
                        '','','',
                        0,'$l',0,
                        $item)";
            $rlt = $this->db->query($sql);
        }        
}