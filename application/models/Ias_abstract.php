<?php

class ias_abstract extends CI_Model
{
    function neuro_nlp($txtf, $data)
    {
        $sx = '<hr><b>Resumo e Palavras-chave</b>';
        $ln = $this->ias->to_line($txtf);

        $rsp = $this->abstract($ln, 'pt');
        $sx = $rsp['abstract'];
        
        return($sx);
    }

    function abstract($ln, $lang)
    {
        $lg = array();
        switch ($lang) {
            case 'pt':
                $lg = array('RESUMO');
                $lq = array('PALAVRAS-CHAVE');
                break;
            case 'en':
                $lg = array('ABSTRACT');
                break;
        }

        /* Zera texto */
        $ini = 0;
        $fim = 0;
        /* Inicio do Resumo */
        $term = $lg[0];
        for ($r=0;$r < count($ln);$r++)
            {
                $termr = substr($ln[$r],0,strlen($term));
                if ($termr == $term) { $ini = $r; $r = count($ln); }
            }

        /* Inicio das Palavras-chaves */
        $term = $lq[0];
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

        /* resumo */
        $txt = '<hr>';
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

        $keys = $ln[$fim];
        $key = troca($keys,':','');
        $key = troca($keys,';','.');
        $keys = explode('.',$key);
        $rsp['keywords'] = $keys;

        return ($rsp);
    }
    function keywords($txtf)
    {
        $a = array('PALAVRAS-CHAVE', 'KEYWORDS');
    }
}
