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

                $sql = "SELECT * FROM rdf_data 
                            where d_literal = $idl";
                $rrr = $this->db->query($sql);
                $rrr = $rrr->result_array();
                $max = 0;
                $fl = array();
                $sx = '';

                $n = array();
                $n[3] = 0;
                $n[4] = 0;

                for ($y=0;$y < count($rrr);$y++)
                    {
                        $ln = $rrr[$y];
                        $fx = 'c/'.$ln['d_r1'].'/name.ABNT';
                        echo '<br>'.$fx;
                        if (file_exists($fx))
                        {
                            array_push($fl,file_get_contents($fx));
                            $n[3] =  $n[3] + 1;
                            echo ' OK';
                        } else {
                            echo ' ERRO';
                        }

                        $file = 'c/'.$ln['d_r1'].'/name.nm';
                        if (file_exists($file))
                            {
                                $t = file_get_contents($file);
                                $sx .= $t.'<hr>';
                            }

                        if ($ln['d_r1'] > $max)
                            {
                                $max = $ln['d_r1'];
                            }                            
                    }
                /********************** Arquivo não existe */
                echo '<h2>Fase I</h2>';
                echo '<pre>';
                print_r($n);
                echo 'Arquivos: '.count($fl);
                echo '</pre>';
                if (($n[3] > 0) and (count($fl) > 0))
                {
                    /********************** Anos da publicação */
                    $v1 = $this->recupera_dados($fl[0],'PY');
                    $v2 = $this->recupera_dados($fl[1],'PY');
                    $n[0] = ($v1 == $v2);

                    /********************* Primeiro autor */
                    $v1 = $this->recupera_dados($fl[0],'AU');
                    $v2 = $this->recupera_dados($fl[1],'AU');
                    $n[1] = ($v1 == $v2);

                    /********************* Journal ********/
                    $v1 = $this->recupera_dados($fl[0],'T2');
                    $v2 = $this->recupera_dados($fl[1],'T2');
                    $n[2] = ($v1 == $v2);

                    print_r($n);
                    echo '<hr>';
                    echo '<pre>'.$fl[0].'</pre>';
                    if ($this->neuro($n) == 1)
                        {
                            $rdf->remove_concept($max);                    
                        }
                }
                echo '<hr>';
                echo '===>'.$max;
                echo '<hr>';
                echo $sx;
                
                echo '<meta http-equiv="refresh" content="2">';
                exit;                 
            }
        }

        function neuro($n)
            {
                $rs = 0;
                print_r($n);
                if ($n[3] == 1) { return(1); }

                if (($n[0] == 1) and ($n[1] == 1) and ($n[2] == 1))
                    {
                        $rs = 1;
                    }
                return($rs);
            }

        function recupera_dados($txt,$tp)
            {
                
                switch($tp)
                    {
                        case 'PY':
                        $t = substr($txt,strpos($txt,$tp.' - ')+5,4);
                        break;

                        default:
                        $t = '';
                    }
                return($t);
            }
}