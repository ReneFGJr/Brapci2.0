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

class ai
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

/******************************************************************** BUSCADOR */
Class ia_index
{
function search($txt)
    {
        $t = strtolower(ascii($txt));
        $t = troca($t,' ',';');
        $tl = splitx(';',$t);
        $rst = array();
        
        $f = array('Work.txt','authors.txt','Subject.txt');
        for ($r=0;$r < count($f);$r++)
            {
                $file = "_temp/".LIBRARY.'/'.$f[$r];
                if (file_exists($file))
                {
                $tx = file_get_contents($file);
                if (strpos($tx,$tl[0]) > 0)
                    {
                        $tt = explode(chr(10),$tx);
                        for ($y=0;$y < count($tt);$y++)
                        {
                            $ts = 0;
                            for ($z=0;$z < count($tl);$z++)
                                {
                                    if (strpos($tt[$y],$tl[$z]) > 0)
                                        {
                                            $ts++;
                                        }
                                }
                            if ($ts == count($tl))
                                {
                                    array_push($rst,round(substr($tt[$y],0,8)));
                                }
                        }
                    }
                }
            }
        return($rst);
    }
function export_index($class='',$id='')
    {
        switch($class)
            {
                case 'Person':
                $sx = '';
                $sx .= '<div class="container">';
                $sx .= '<div class="row">';
                $sx .= '<div class="'.bscol(12).'">';
                $sx .= 'Exporta lista de autores';
                $sx .= $this->index_author($id);
                $sx .= '</div>';
                $sx .= '</div>';
                $sx .= '</div>';
                break;

                default:
                $sx = '';
                $sx .= '<div class="container">';
                $sx .= '<div class="row">';
                $sx .= '<div class="'.bscol(12).'">';
                $sx .= 'Exporta lista de '.$class;
                $sx .= $this->index_class($id,$class);
                $sx .= '</div>';
                $sx .= '</div>';
                $sx .= '</div>';
            }
        return($sx);
    }

function export_search($class='')
    {
        $sx = 'Exporta texto para busca';
        $sx .= $this->search_author($class);
        $sx .= $this->search_class('Work');
        $sx .= $this->search_class('Subject');
        return($sx);
    }
function search_author($class)
    {
        $dir = '_temp';
        dircheck($dir);
        $dir = '_temp/'.LIBRARY.'/';
        dircheck($dir);
        $file = $dir.'/authors.txt';

        $CI = &get_instance();
        $rdf = new rdf;
        $f = $rdf -> find_class($class);
        $wh = '';
        $sql = "select 
                    N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                    N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n 
                        INNER JOIN rdf_data as RD1 ON RD1.d_r2 = C1.id_cc
                        INNER JOIN rdf_data as RD2 ON (RD1.d_r1 = RD2.d_r1) and (RD2.d_r2 > 0) and (RD2.d_p = 54) /* Expressao */
                        INNER JOIN rdf_data as RD3 ON (RD2.d_r2 = RD3.d_r1) and (RD3.d_p = 55) /* Manifestacao */
                        INNER JOIN find_item ON RD3.d_r2 = i_manitestation
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        WHERE i_library = '".LIBRARY."' AND C1.cc_class = " . $f . " ".$wh."
                        group by n_name, n_lang, id_cc, n_name_use, n_lang_use, id_cc_use 
                        order by n_name";
        $rlt = $CI -> db -> query($sql);
        $rlt = $rlt -> result_array();  
        $lx = '';
        for ($r=0;$r < count($rlt);$r++)
            {
                $line = $rlt[$r];
                $lx .= strzero($line['id_cc'],8);
                $lx .= ';';
                $lx .= strtolower(ascii($line['n_name']));
                $lx .= cr();
            } 
        file_put_contents($file,$lx);
        return(message($class.' exported to '.$file,1));
    }

function search_class($class)
    {
        $dir = '_temp';
        dircheck($dir);
        $dir = '_temp/'.LIBRARY.'/';
        dircheck($dir);
        $file = $dir.$class.'.txt';

        $CI = &get_instance();
        $rdf = new rdf;
        $f = $rdf -> find_class($class);
        $wh = '';
        switch($class)
            {
                case 'Subject':  
                $prop = 119; 
                /* Melhorar para recuperar as remissivas */       
                $sql = "
                    SELECT n_name, id_cc, i_manitestation 
                    FROM `find_item`
                    inner join rdf_data ON i_manitestation = d_r1 and d_p = $prop
                    inner join rdf_concept ON d_r2 = id_cc
                    inner join rdf_name ON id_n = cc_pref_term
                    where i_library = '".LIBRARY."' 
                    group by n_name, id_cc, i_manitestation
                    order by n_name, id_cc, i_manitestation                   
                ";
                break;

                case 'Work':            
                $sql = "
                select n_name, id_cc, i_manitestation
                    FROM rdf_data as R1
                    INNER JOIN rdf_data as R2 ON (R1.d_r2 = R2.d_r1) and (R2.d_p = 55) /* Manifestation */
                    INNER JOIN find_item ON R2.d_r2 = i_manitestation
                    INNER JOIN rdf_concept ON id_cc = R1.d_r1
                    INNER JOIN rdf_name ON cc_pref_term = id_n
                    where R1.d_p = 54  /* 54 - Expressão */
                    AND i_library = '".LIBRARY."'
                    group by n_name, id_cc, i_manitestation
                    order by n_name, id_cc, i_manitestation
                ";
                break;

                default:
                    
                break;
            }
        $rlt = $CI -> db -> query($sql);
        $rlt = $rlt -> result_array();  
        $lx = '';
        for ($r=0;$r < count($rlt);$r++)
            {
                $line = $rlt[$r];
                $lx .= strzero($line['i_manitestation'],8);
                $lx .= ';';
                $lx .= strtolower(ascii($line['n_name']));
                $lx .= cr();
            } 
        file_put_contents($file,$lx);
        return(message($class.' exported to '.$file,1));
    }    

function index_author($id='')
    {
        if (strlen($id)==0) { $id = 65; }
        $rdf = new rdf;
        $sx = $this->export_author_index_list($id);
        return($sx);
    }

function index_class($id='',$class)
    {
        if (strlen($id)==0) { $id = 65; }
        $rdf = new rdf;
        $sx = $this->export_class_index_list($id,$class);
        return($sx);
    } 

    function export_class_index_list($lt = 0, $class) {
        $sx = 'Exporting';
        $nouse = 0;
        $dir = 'application/views';
        dircheck($dir);
        $dir = 'application/views/index/';
        dircheck($dir);
        $dir = 'application/views/index/'.LIBRARY;
        dircheck($dir);
        $sx = '';
        if (($lt >= 65) and ($lt <= 90)) {
            $ltx = chr(round($lt));
            $txt = $this -> index_list_style_2($ltx, $class, 0);
            $file = $dir . '/'.strtolower($class).'_' . $ltx . '.php';
            $hdl = fopen($file, 'w+');
            fwrite($hdl, $txt);
            fclose($hdl);
            $sx .= bs_alert('success', msg('Export_author') . ' #' . $ltx . '<br>');
            $sx .= '<meta http-equiv="refresh" content="3;' . base_url(PATH . 'admin/index/'.($class).'/' . ($lt + 1)) . '">';
            $sx .= $lt;
        }
        return ($sx);
    }       

    function export_author_index_list($lt = 0, $class = 'Person') {
        $sx = 'Exporting';
        $nouse = 0;
        $dir = 'application/views';
        dircheck($dir);
        $dir = 'application/views/index/';
        dircheck($dir);
        $dir = 'application/views/index/'.LIBRARY;
        dircheck($dir);
        $sx = '';
        if (($lt >= 65) and ($lt <= 90)) {
            $ltx = chr(round($lt));
            $txt = $this -> index_list_style_2($ltx, 'Person', 0);
            $file = $dir . '/authors_' . $ltx . '.php';
            $hdl = fopen($file, 'w+');
            fwrite($hdl, $txt);
            fclose($hdl);
            $sx .= bs_alert('success', msg('Export_author') . ' #' . $ltx . '<br>');
            $sx .= '<meta http-equiv="refresh" content="3;' . base_url(PATH . 'admin/index/author_index/' . ($lt + 1)) . '">';
            $sx .= $lt;
        }
        return ($sx);
    } 

    function index_list_style_2($lt = 'G', $class, $nouse = 0) {
        $rdf = new rdf;
        $CI = &get_instance();
        $f = $rdf -> find_class($class);
        //$this -> check_language();
        $wh = '';
        if ($nouse == 1) {
            $wh .= " and C1.cc_use = 0 ";
        }
        if (strlen($lt) > 0) {
            $wh .= " and (N1.n_name like '$lt%') ";
        }
        if ($class=="Person")
        {
        $sql = "select 
                    N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                    N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n 
                        INNER JOIN rdf_data as RD1 ON RD1.d_r2 = C1.id_cc
                        INNER JOIN rdf_data as RD2 ON (RD1.d_r1 = RD2.d_r1) and (RD2.d_r2 > 0) and (RD2.d_p = 54) /* Expressao */
                        INNER JOIN rdf_data as RD3 ON (RD2.d_r2 = RD3.d_r1) and (RD3.d_p = 55) /* Manifestacao */
                        INNER JOIN find_item ON RD3.d_r2 = i_manitestation
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        WHERE i_library = '".LIBRARY."' AND C1.cc_class = " . $f . " ".$wh."
                        group by n_name, n_lang, id_cc,n_name_use, n_lang_use, id_cc_use 
                        order by n_name";
        } else {
            $sql = "SELECT 
                    N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc                    
                    FROM find_item
                    INNER JOIN rdf_data as RD1 on i_manitestation = d_r1 and d_p = $f
                    INNER JOIN rdf_concept as C1 ON id_cc = d_r2
                    INNER JOIN rdf_name as N1 ON id_n = cc_pref_term
                    WHERE i_library = '".LIBRARY."' $wh
                    group by n_name, n_lang, id_cc
                    ORDER BY cc_pref_term ASC";

            //echo '<pre>'.$sql.'</pre>';
        }
        $rlt = $CI -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $l = '';
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idx = $line['id_cc'];
            $name_use = trim($line['n_name']);

            $link = '<a href="' . base_url(PATH . 'v/' . $line['id_cc']) . '" style="font-size: 85%; color: #505050;">';
            $linka = '</a>';

            $xl = substr(UpperCaseSql(strip_tags($name_use)), 0, 1);
            if ($xl != $l) {
                if ($l != '') {
                    $sx .= '</ul>';
                    $sx .= '</div>';
                    $sx .= '</div>';
                }
                $linkx = '<a name="' . $xl . '" tag="' . $xl . '"></a>';
                $sx .= '<div class="row"><div class="col-md-1 text-right">';
                $sx .= '<h1 style="font-size: 500%;">' . $xl . '</h1></div>';
                $sx .= '<div class="col-md-11">';
                $sx .= '<ul style="list-style: none; columns: 300px 4; column-gap: 0;">';
                $l = $xl;
            }

            $name = $link . $name_use . $linka . ' <sup style="font-size: 70%;"></sup>';
            $sx .= '<li>' . $name . '</li>' . cr();
        }
        $sx .= '</ul>';
        $sx .= '</div></div>';

        if (count($rlt) == 0)
        {
            $sx = '';
        }
        return ($sx);
    }       
}