<?php

class ias_abstract extends CI_Model
{
    function neuro_nlp($txtf, $data)
    {
        $sx = '<hr><b>Resumo e Palavras-chave</b>';
        $ln = $this->ias->to_line($txtf);

        //$rsp = $this->abstract($ln, 'pt');
        $rsp = $this->abstract($ln, 'en');
        $sx = $rsp['abstract'];
        $sx .= '<br><b>Palvaras-chave</b>: ';
        $key = '';
        for ($r=0;$r < count($rsp['keywords']);$r++)
            {
                $key .= $rsp['keywords'][$r]. '. ';
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

        $key = trim($ln[$fim].' '.$ln[$fim+1]);
    
        $key = troca($key,':',' ');
        $key = troca($key,'.',';');
        $keys = splitx(';',$key);

        $rsp['keywords'] = $keys;

        return ($rsp);
    }
    function keywords($txtf)
    {
        $a = array('PALAVRAS-CHAVE', 'KEYWORDS');
    }
}
