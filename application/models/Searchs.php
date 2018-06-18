<?php
class searchs extends CI_Model
    {
        function recover_reg($art,$t)
            {
                $t = substr($t,strpos($t,']')+1,strlen($t));
                $te = splitx(';',$t);
                for ($r=0;$r < count($te);$r++)
                    {
                        if (isset($art[$te[$r]]))
                            {
                                $art[$te[$r]] = $art[$te[$r]] + 1;
                            } else {
                                $art[$te[$r]] = 1;
                            }
                    }
                return($art);
            }
        function s($n,$t='')
            {
                echo '=name=>'.$n;
                $fl = 'c/search_subject.search';
                if (is_file($fl))
                    {
                        $f = load_file_local($fl);
                        $ln = splitx('¢',$f);
                        $te = lowercasesql($n);
                        $sx = '';
                        /* method 1 */
                        $art = array();
                        
                        for ($r=0;$r < count($ln);$r++)
                            {
                                if (strpos($ln[$r],$te) > 0)
                                    {
                                        $art = $this->recover_reg($art,$ln[$r]);
                                    }   
                            }
                        $sx = '<div class="row result">';
                        foreach ($art as $key => $value) {
                            $sx .= '<div class="col-12" style="margin-bottom: 15px;">';
                            $sx .= '<input type="checkbox"> ';
                            $sx .= '<a href="'.base_url(PATH.'v/'.$key).'" target="_new'.$key.'" class="refs">';
                            $sx .= $this->frbr->show_v($key);
                            $sx .= '</a>';
                            $sx .= '</div>';    
                        }
                    } else {
                        $sx = '
                                <div class="alert alert-warning" role="alert">
                                  ERRO #1001! The Search index file not Found
                                </div>';
                    }
                return($sx);
            }
        function convert($t)
            {
                $t .= ' ';
                /**** PT-BR ****/
                $a = array(
                    ' as'=>' a',
                    ' os'=>' o',
                    'aces'=>'ace',
                    'ais'=>'al',
                    'aos'=>'ao',
                    'ares'=>'ar',
                    'ubes'=>'ube',
                    'bens'=>'bem',
                    'blemas'=>'blema',
                    'boas'=>'boa',
                    'cas'=>'ca',
                    'cias'=>'cia',
                    'cies'=>'cie',
                    'cios'=>'cio',
                    'chas'=>'cha',                    
                    'cos'=>'co',
                    'coes'=>'cao',
                    'dados'=>'dado',
                    'des'=>'de',
                    'dios'=>'dio',
                    'dos'=>'do',
                    'das'=>'da',
                    'dias'=>'dia',
                    'nios'=>'nio',
                    'dores'=>'dor',
                    'eias'=>'eia',
                    'eios'=>'eio',
                    'fias'=>'fia',
                    'fins'=>'fim',
                    'fis'=>'fil',
                    'gens'=>'gem',
                    'gias'=>'gia',
                    'gios'=>'gio',
                    'guas'=>'gua',
                    'gos'=>'go',                    
                    'iais'=>'ial',
                    'ioes'=>'iao',
                    'ices'=>'ice',
                    'jos'=>'jo',
                    'leis'=>'lei',
                    'lhos'=>'lho',
                    'las'=>'la',
                    'le'=>'le',
                    'los'=>'lo',
                    'lores'=>'lor',
                    'mas'=>'me',
                    'mes'=>'me',
                    'mias'=>'mia',
                    'mos'=>'mo',
                    'mulas'=>'mula',
                    'nas'=>'na',
                    'nes'=>'ne',
                    'nos'=>'no',
                    'nais'=>'nal',
                    'nias'=>'ina',
                    'pas'=>'pa',
                    'pes'=>'pe',
                    'pios'=>'pio',
                    'pos'=>'po',
                    'quais'=>'qual',
                    'ques'=>'que',                    
                    'ras'=>'ra',
                    'res'=>'re',
                    'ros'=>'ro',
                    'rais'=>'ral',
                    'reas'=>'rea',
                    'rias'=>'ria',
                    'rios'=>'rio',
                    'rois'=>'rol',
                    'ros'=>'ro',
                    'roes'=>'rao',
                    'soes'=>'sao',
                    'sas'=>'sa',                    
                    'ses'=>'se',
                    'seus'=>'sel',                    
                    'sos'=>'so',
                    'soas'=>'soa',
                    'tas'=>'ta',
                    'tes'=>'te',
                    'tens'=>'tem',
                    'tins'=>'tim',
                    'tios'=>'tio',
                    'tras'=>'tra',
                    'toes'=>'tao',
                    'tos'=>'to',                     
                    'uais'=>'ual',
                    'uias'=>'uia',
                    'uns'=>'um',
                    'vas'=>'va',
                    'ves'=>'ve',
                    'vis'=>'vil',
                    'vos'=>'vo',
                    'xos'=>'xo',
                    'xoes'=>'xao',                   
                    'zes'=>'z',
                    );
                foreach ($a as $key => $value) {
                    $t = troca($t,$key.' ',$value.' ');    
                }                    

                while (strpos($t,'  '))
                    {
                        $t = troca($t,'  ',' ');
                    }

                $t = trim($t);                                
                return($t);
            }                  
    }
?>
