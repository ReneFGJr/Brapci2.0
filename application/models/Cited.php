<?php
class Cited extends CI_Model 
{
    var $base = 'brapci_cited.';
    var $baseCited = 'brapci_cited.';

    function zera()
        {
            $sql = "update ".$this->base."cited_article set ca_status = 0, ca_tipo = 0 WHERE ca_status <> 0";
            $this->db->query($sql);
        }

    function citation_by_author($ida,$type='')
        {
                $rdf = new rdf;
                $dt = $rdf->le_data($ida);
                $ob = $rdf->extract_id($dt,'hasAuthor',$ida);
                return($ob);
        }
    
    function api_citation($suba)
        {
            $id = get("id");
            if ($id <= 0)
                {
                    return($this->api_brapci->error('id not informed'));
                }
            switch($suba)
                {
                    case 'author':
                        $this->load->model('ias');
                        $this->load->model('ias_cited');
                        $dt = $this->citation($id,'author');
                    break;

                    default:
                        $dt = $this->citation($id,'author');
                    break;
                }            
            return($dt);
        }        

    function citation($id=0,$type='jnl')
        {
            $cp = 'ca_rdf, ca_year, ca_journal, cj_name_asc as cj_name, ';
            $cp .= 'ca_year_origem, ca_vol, ct_type, ca_tipo, ca_status, id_ca, ';
            $cp .= 'ca_nr, ca_pag, ';
            $cp .= ' "" as 1st, "" as 2nd, "" as 3th, ';
            $cp .= 'ca_text, jnl_name, jnl_name_abrev, ';
            $cp .= 'concat(\''.base_url(PATH.'v/').'\',jnl_frbr) as jnl_url';
            //$cp = '*';
            switch($type)
                {
                    case 'author':
                    $pb = $this->citation_by_author($id);
                    $wh = '';
                    for ($r=0;$r < count($pb);$r++)
                        {
                            if (strlen($wh) > 0)
                                {
                                    $wh .= ' OR ';
                                }
                            $wh .= '(ca_rdf = '.$pb[$r].') ';
                        }
                        $sql = "select $cp from ".$this->baseCited."cited_article
                            left join ".$this->baseCited."cited_journal ON ca_journal = id_cj 
                            left join source_source ON ca_journal_origem = id_jnl 
                            left join ".$this->baseCited."cited_type ON id_ct = ca_tipo
                            where ($wh) or (1=2)
                            order by ca_text, ca_year desc";
                    break;

                    default:
                    $sql = "select $cp from ".$this->baseCited."cited_article
                        inner join ".$this->baseCited."cited_journal ON ca_journal = id_cj 
                        left join source_source ON ca_journal_origem = id_jnl 
                        left join ".$this->baseCited."cited_type ON id_ct = ca_tipo
                        where cj_journal = $jnl
                        order by ca_year desc, ca_text";
                    break;
                }
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $apos = array('1st','2nd','3th');
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $a = array();
                    $auth = $this->add_authors($line['ca_text'],$a);
                    $id = 0;
                    foreach($auth as $name=>$total)
                        {
                            if (($id <= 2) and (strlen($name) > 0))
                                {
                                    $fld = $apos[$id];
                                    $line[$fld] = $name;
                                    $id++;
                                }
                        }
                    $rlt[$r] = $line;
                    /*
                    echo '<pre>';
                    print_r($line);
                    echo '<br>';
                    */
                }
            return($rlt);
        }        

    function show_ref($id)
        {
            //$this->zera();
            $this->load->model('ias');
            $this->load->model('ias_cited');
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
                    if (perfil("#ADM"))
                    {
                        $st = '';
                        if ($l['ca_tipo'] == 99) { $st = ' style="color: red;"'; }
                        $link = '<span onclick="newxy(\''.base_url(PATH.'ia/cited/ed/'.$l['id_ca']).'?nocab=true\',800,600);" style="cursor: pointer;">';
                        $linka = '</span>';
                        $txt = trim($l['ca_text']);
                        if (strlen($txt) == 0)
                            { $txt = msg('erro'); }
                        $sx .= '<li '.$st.'>'.$link.$txt.$linka;
                        $sx .= ' '.$this->cited_type($l);
                        $sx .= '</li>';        
                    } else {
                        $sx .= '<li>'.$l['ca_text'];
                    }
                    $sx .= '</li>';
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

    function cited_type($l)
        {
            $type = $l['ca_tipo'];
            $status = $l['ca_status'];
            $id = $l['id_ca'];

            if (($type == 0) and ($status == 0))
            {
                $type = $this->ias_cited->neuro_type_source($l['ca_text'],$l['id_ca']);
                if ($type != 0)
                    {
                        $this->cited_type_update($id,1,$type);
                    }
            }
            $sx = $this->type($type);

            return($sx);
        }

    function type($type)
        {
            switch($type)
                {
                    case '1':
                    $sx = '<span class="btn-primary radius5">&nbsp;'.msg('journal').'&nbsp;</span>';
                    break;

                    case '2':
                    $sx = '<span class="btn-danger radius5">&nbsp;'.msg('book').'&nbsp;</span>';
                    break;                    

                    case '3':
                    $sx = '<span class="btn-danger radius5">&nbsp;'.msg('book.cap').'&nbsp;</span>';
                    break;     

                    case '5':
                    $sx = '<span class="btn-success radius5">&nbsp;'.msg('events').'&nbsp;</span>';
                    break;                                   

                    case '7':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('these').'&nbsp;</span>';
                    break; 

                    case '8':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('dissertation').'&nbsp;</span>';
                    break; 

                    case '9':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('TCC').'&nbsp;</span>';
                    break;

                    case '15':
                        $sx = '<span class="btn-info radius5">&nbsp;'.msg('LINK').'&nbsp;</span>';
                        break;                     

                    case '20':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('LAW').'&nbsp;</span>';
                    break;  

                    case '21':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('REPORT').'&nbsp;</span>';
                    break;  

                    case '22':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('STANDARD').'&nbsp;</span>';
                    break;                                                                              

                    case '29':
                    $sx = '<span class="btn-warning radius5">&nbsp;'.msg('INTERVIEW').'&nbsp;</span>';
                    break;                                                            

                    case '30':
                    $sx = '<span class="btn-primary radius5">&nbsp;'.msg('SOFTWARE').'&nbsp;</span>';
                    break;                                                            

                    case '31':
                    $sx = '<span class="btn-primary radius5">&nbsp;'.msg('PATENT').'&nbsp;</span>';
                    break;                                                            

                    default:
                    $sx = '<span class="btn-secondary radius5">&nbsp;'.msg('none').$type.'&nbsp;</span>';
                }
            return($sx);
        }

    function cited_type_update($id,$status,$type)
        {
            $date = date("Y-m-d");
            $sql = "update ".$this->base."cited_article
                    set ca_tipo = $type,
                    ca_status = $status,
                    ca_update_at = '$date'
                    where id_ca = $id";
            $this->db->query($sql);
            return(1);
        }

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
}