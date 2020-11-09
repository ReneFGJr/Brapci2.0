<?php
/**
* CodeIgniter Form Helpers
*
* @package     Artificial Inteligence
* @subpackage  AI
* @category    NLP
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/CodIgniter
* @version     v0.20.10.22
* @link    https://github.com/klawdyo/Inflector-BR/blob/master/src/Inflector.php
*/

class ia
{
    /* execessões */
    var $exceptions = array(
        'ou'=>'ou',
        'cidadão' => 'cidadãos',
        'mão' => 'mãos',
        'qualquer' => 'quaisquer',
        'campus' => 'campi',
        'lápis' => 'lápis',
        'ônibus' => 'ônibus',
        'país' => 'país',
        'anais' => 'anais',
        'coronavírus'=>'coronavírus',
        'vírus'=>'vírus',
        'grátis'=>'grátis',
        'mas' => 'mas',
        'locus' => 'locus',
        'lattos' => 'lattos',
        'de'=>'de',
        'é'=>'são',
        'tem'=>'têm',
        'você'=>'vocês',
        'se'=>'se',
        'ser'=>'serem',
        'por'=>'por',
        '-se'=>'-se',
        'refere'=>'referem',
        'encontra'=>'encontram',
        'que'=>'que',
        'em'=>'em',
        'Brasil'=>'Brasil',
        'e'=>'e',
        'sem'=>'sem',
        'a'=>'aos',        
    );
    
    /***************** regras */
    var $rules = array(
        //singular     plural
        'ão' => 'ões',
        'ês' => 'eses',
        'm' => 'ns',
        'l' => 'is',
        'r' => 'res',
        'x' => 'xes',
        'z' => 'zes',
    );
    
    function nlp_inflector($txt='',$tp='S')
    {
        $pre = array('(','[','”','#','"','-');
        $pos = array(')',']','}',',','.','?','!',';');
        $sig = array_merge($pre,$pos);
        for ($r=0;$r < count($sig);$r++)
        {
            $txt = troca($txt,$sig[$r],' '.$sig[$r].' ');
        }
        $txt = troca($txt,chr(13),' #cr# ');
        $txt = troca($txt,chr(10),' #ln# ');
        $txt = troca($txt,' ',';');
        $wd = splitx(';',$txt);
        
        $txt = '';
        for ($r=0;$r < count($wd);$r++)
        {
            $wrd = $wd[$r];
            /* Verifica se é pontuação */
            if ((in_array($wrd,$sig)) or (sonumero($wrd) == $wrd) or (trim($wrd) == '') or (substr(trim($wrd),0,1) == '#'))
            {
                $w = $wrd;
            } 
            else 
            {
                if ($tp == 'S')
                {
                    /************* Convert para o Singular */
                    $w = $this->nlp_word_singular($wrd);
                } else {
                    /************* Convert para o Plural */
                    $w = $this->nlp_word_plural($wrd);
                }                
            }
            $txt .= $w . ' ';
        }
        
        for ($r=0;$r < count($pre);$r++)
        {
            $txt = troca($txt,' '.$pre[$r].' ',' '.$pre[$r]);
        }        
        for ($r=0;$r < count($pos);$r++)
        {
            $txt = troca($txt,' '.$pos[$r].' ',$pos[$r].' ');
        }        
        $txt = troca($txt,'#ln#',chr(10));
        $txt = troca($txt,'#cr#',chr(13));
        return($txt);
    }
    
    /******************************************************************************* PLURAL */
    function nlp_word_plural($word)
    {
        //Pertence a alguma exceção?
        if (array_key_exists($word, $this->exceptions)) {
            return $this->exceptions[$word];
        } //Não pertence a nenhuma exceção. Mas tem alguma regra?
        else {
            foreach ($this->rules as $singular => $plural) {
                if (preg_match("({$singular}$)", $word)) {
                    return preg_replace("({$singular}$)", $plural, $word);
                }
            }
        }
        
        //Não pertence às exceções, nem às regras.
        //Se não terminar com "s", adiciono um.
        if (substr($word, -1) !== 's') {
            return $word.'s';
        } else {
            return $word;
        }
    }    
    
    /******************************************************************************* SINGULAR */
    function nlp_word_singular($word='')
    {   
        //Pertence às exceções?
        if (in_array($word, $this->exceptions)) 
        {
            $invert = array_flip($this->exceptions);
            return $invert[$word];
        } //Não é exceção.. Mas pertence a alguma regra?
        else 
        {
            foreach ($this->rules as $singular => $plural) 
            {
                if (preg_match("({$plural}$)", $word)) 
                {
                    return preg_replace("({$plural}$)", $singular, $word);
                }
            }
        }
        
        //Não é exceção.. Mas pertence a alguma regra?
        {
            foreach ($this->rules as $singular => $plural) 
            {
                if (preg_match("({$plural}$)", $word)) 
                {
                    return preg_replace("({$plural}$)", $singular, $word);
                }
            }
        }
        
        //Nem é exceção, nem tem regra definida. 
        //Apaga a última somente se for um "s" no final
        if (substr($word, -1) == 's') 
        {
            return substr($word, 0, -1);
        } else {
            return $word;
        }
    }
}