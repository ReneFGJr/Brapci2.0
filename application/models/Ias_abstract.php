<?php

class ias_abstract extends CI_Model
{
    function neuro_nlp($txtf, $data)
    {
        $sx = '<hr><b>Resumo e Palavras-chave</b>';
        $ln = $this->ias->to_line($txtf,1);

        $rspt = $this->abstract($ln, 'pt');
        $rsen = $this->abstract($ln, 'en');

        /*****************************************************/
        $sx = '<hr>'.$rspt['abstract'];
        $sx .= '<br><b>Palvaras-chave</b>: ';
        $key = '';
        for ($r=0;$r < count($rspt['keywords']);$r++)
            {
                $key .= $rspt['keywords'][$r]. '. ';
            }
        $sx .= $key;
        /****************************************************/
        $sx .= '<hr>'.$rsen['abstract'];
        $sx .= '<br><b>Keyword</b>: ';
        $key = '';
        for ($r=0;$r < count($rsen['keywords']);$r++)
            {
                $key .= $rsen['keywords'][$r]. '. ';
            }
        $sx .= $key;
        return($sx);
    }

    function abstract($ln, $lang)
    {
        $lg = array();

        switch ($lang) {
            case 'pt':
                $lg = array('RESUMO','Resumo');
                $lq = array('PALAVRAS-CHAVE','Palavras-chave');
                break;
            case 'en':
                $lg = array('ABSTRACT','Abstract');
                $lq = array('KEYWORDS','Keywords');
                break;
        }

        /* Zera texto */
        $ini = 0;
        $fim = 0;
        /* Inicio do Resumo */
        for ($t=0;$t < count($lg);$t++)
        {
        $term = $lg[$t];
        for ($r=0;$r < count($ln);$r++)
            {
                $termr = substr($ln[$r],0,strlen($term));
                if ($termr == $term) { $ini = $r; $r = count($ln); }
            }
        }

        /* Inicio das Palavras-chaves */
        for ($t=0;$t < count($lq);$t++)
        {        
        $term = $lq[$t];
        for ($r=$ini;$r < count($ln);$r++)
            {
                $terml = substr($ln[$r],0,strlen($term));
                if ($terml == $term) 
                    { 
                        $fim = $r; 
                        $ln[$r] = troca($ln[$r],$term,'');
                        $r = count($ln); 
                    }

            }
        }
        /* resumo */
        $txt = '';
        if (($ini > 0) and ($fim > 0))
        {
        for ($r=$ini;$r < $fim;$r++)
            {
                $txt .= $ln[$r].' ';
            }
        } else {
            $txt .= 'OPS, ERRO DE RESUMO '.$ini.'-'.$fim;
        }

        $rsp = array();
        $rsp['abstract'] = $txt;
        $rsp['lang'] = $lang;

        /************** Identifica Keywords */
        $key = '';
        for ($r=$fim;$r < ($fim+15);$r++)
            {
                if (isset($ln[$r]))
                {
                $key .= ' '.trim($ln[$r]);
                }
            }
        $key = trim($key);

        /***************************************** Separa as palavras */
        $key = troca($key,':',';');
        $key = troca($key,'.',';');
        $pkeys = splitx(';',$key);
        $keys = array();

        for ($r=0;$r < count($pkeys);$r++)
            {
                $k = trim($pkeys[$r]);
                $ok = 1;
                if (strpos($k,':')) { $ok = 0; }
                if (strpos($k,'/')) { $ok = 0; }
                if (strlen($k) > 50) { $ok = 0; }
                if (strtolower(substr($k,0,8)) == 'recebido') { $ok = 0; }
                if (strtolower(substr($k,0,8)) == 'originai') { $ok = 0; }
                
                if ($ok == 1)
                    {
                        echo '<br>---->'.$pkeys[$r];
                        array_push($keys,$pkeys[$r]);
                    } else {
                        
                        break;
                        //$r = $r + 1000;
                    }                
            }
        $rsp['keywords'] = $keys;       

        return ($rsp);
    }
}
