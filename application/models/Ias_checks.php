<?php
class ias_checks extends CI_Model
{
    function article_duplicate()
        {
        $rdf = new rdf;
        $sql = 
            "
            SELECT * from (
            SELECT n_name, count(*) as total, d_literal
            FROM rdf_concept
            inner join rdf_data ON d_r1 = id_cc
            inner join rdf_name ON id_n = d_literal
            where d_p = 17 and d_literal > 0
            group by n_name, d_literal
            ) as tabela
            where total > 1
            order by total 
            ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        
        for ($r=0;$r < count($rlt);$r++)
            {
                $line = $rlt[$r];
                $idl = $line['d_literal'];

                $sql = "SELECT * FROM rdf_data where d_literal = $idl";
                $rrr = $this->db->query($sql);
                $rrr = $rrr->result_array();
                $max = 0;
                $fl = array();
                $sx = '';
                for ($y=0;$y < count($rrr);$y++)
                    {
                        $ln = $rrr[$y];
                        $fx = 'c/'.$ln['d_r1'].'/name.ABNT';
                        if (file_exists($fx))
                        {
                            $fl[$y] = file_get_contents($fx);
                        }

                        $file = 'c/'.$ln['d_r1'].'/name.nm';
                        if (file_exists($file))
                            {
                                $t = file_get_contents($file);
                                $sx .= $t;
                            }
                        if ($ln['d_r1'] > $max)
                            {
                                $max = $ln['d_r1'];
                            }                            
                    }
                //$rdf->remove_concept($max);

                echo $sx;
                echo '<pre>';
                print_r($fl);
                echo '</pre>';
                echo '<meta http-equiv="refresh" content="30">';
                exit;                 
            }
        }
}