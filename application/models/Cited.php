<?php
class Cited extends CI_Model 
{
    var $base = 'brapci_cited.';
    function show_icone($id)
        {
            $sx = '';
            $file = 'c/'.$id.'/cited.total'; 
            if (file_exists(($file)))
            {
                $total = file_get_contents($file);
            $sx = '
            <div class="infobox" style="width: 100px;">
                <div class="infobox_name" style="background-color: #e0e0ff; float: left; width: 70%; padding: 0px 5px;">
                '.msg("Refs").'
                </div>
                <div class="infobox_version text-center" style="float: left; background-color: #e0ffe0; width: 30%; padding: 0px 2px;">
                <a href="#CITED">'.$total.'</a>
                </div>
            </div>            
            ';
            }
            return($sx);
        }

    function show_ref($id)
        {
            $sx = '';
            $sql = "select * from ".$this->base.'cited_article 
                    where ca_rdf = '.round($id).'
                    order by ca_ordem';
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) > 0)
            {
            $sx = '<a name="CITED"></a>';
            $sx .= '<h4>'.msg('References').'</h4>';
            $sx .= '<ul>';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $l = $rlt[$r];
                    $sx .= '<li>'.$l['ca_text'].'</li>';
                }
            $sx .= '</ul>';
            }
            return($sx);
        }
        function export_citeds($id)
        {
            $file = 'c/'.$id.'/cited.'; 
            $sql = "
                    select * from ".$this->base."cited_article
                    where ca_rdf = $id 
                    order by ca_ordem"; 
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $tot = 0;
            $ref = "";
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $ref .= $line['ca_text'].cr();
                    $tot++;
                }

            file_put_contents($file.'nm',$ref);
            file_put_contents($file.'total',$tot);
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
            $this->export_citeds($id);
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