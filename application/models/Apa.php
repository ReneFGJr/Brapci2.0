<?php
class apa extends CI_Model {
    function ABNTtoAPA($h = '') {

        $hn = troca($h, ';', '..');
        $hn = troca($hn, ' ', ';');
        $hn = splitx(';', $hn);
        $i = 0;
        $ano = 0;
        for ($r = 0; $r < count($hn); $r++) {
            $n = substr($hn[$r], 0, 1);
            $m = lowercase(substr($hn[$r], 0, 1));
            //echo '<br>'.$n.'==>'.$m;
            if (($n == $m) and ($i == 0)) { $i = $r - 2;
            }
            /********************* ANO ******/
            $anox = round(sonumero($hn[$r]));
            if (($anox > 1900) and ($anox < 2020)) {
                $ano = $anox;
            }

        }
        $ref = '';
        for ($r = 0; $r <= $i; $r++) {
            $n = $hn[$r];
            if (!strpos($n, ',')) {

                $n = substr($n, 0, 1) . '. ';
            } else {
                $n = substr($n,0,1).lowercase(substr($n,1,strlen($n)));
                if ($r > 0) { $n = ', ' . $n;
                }
                $n .= ' ';
            }
            $ref .= $n;
        }
        
        /*******************************************************/
        $comp = '';
        for ($r=($i+1);$r < count($hn);$r++)
            {
                if ($hn[$r] == $ano)
                    {
                        $hn[$r] = '';
                    }
                if ($hn[$r] == $ano.'.')
                    {
                        $hn[$r] = '';
                    }                    
                switch($hn[$r])
                    {
                    case 'v.':
                        $hn[$r] = '';
                        $hn[$r+1] = troca($hn[$r+1],',','(');
                        break;                        
                    case 'n.':
                        $hn[$r] = '';
                        $hn[$r+1] = troca($hn[$r+1],',','),');
                        $comp = troca($comp,'( ','(');
                        break;                        
                    case 'p.':
                        $hn[$r] = '';
                        break;
                    default:
                        $hn[$r] = $hn[$r] . ' ';                        
                    }
                if ($r > ($i+1))
                    {
                        $comp .= lowercase($hn[$r]);
                    } else {
                        $comp .= $hn[$r];        
                    }
                
            }
        $comp = trim($comp);
        $n = substr($comp,strlen($comp)-1,1);
        $comp = troca($comp,'n.','');
        $comp = troca($comp,'. ,','.,');
        $comp = troca($comp,'( ','(');
        echo '===>'.$n; 
        if ( $n == ',') 
            {
                $comp = substr($comp,0,strlen($comp)-1).'.';
            }
        $ref .= '('.$ano.'). ';
        return($ref.$comp);
    }

}
?>
