<?php
class bibliometrics extends CI_model {
    var $file_name = '';    
    function tools_menu()
        {
            $sx = '';
            $sx .= '<div class="row">';
            $sx .= '<div class="col-12">';
            $sx .= '<h4>'.msg('bibliometric_menu').'</h4>';
            $sx .= '<ul>';

            $sx .= '<li>';
            $sx .= '<a href="'.base_url(PATH.'bibliometric/semicolon_to_list').'">';
            $sx .= msg('semicolon_to_list');
            $sx .= '</a>';
            $sx .= '</li>';

            $sx .= '<li>';
            $sx .= '<a href="'.base_url(PATH.'bibliometric/csv_to_net').'">';
            $sx .= msg('csv_to_net');
            $sx .= '</a>';
            $sx .= '</li>';
            
            $sx .= '<li>';
            $sx .= '<a href="'.base_url(PATH.'bibliometric/csv_to_matrix').'">';
            $sx .= msg('csv_to_matrix');
            $sx .= '</a>';
            $sx .= '</li>';            

            $sx .= '<li>';
            $sx .= '<a href="'.base_url(PATH.'bibliometric/change_to').'">';
            $sx .= msg('change_to');
            $sx .= '</a>';
            $sx .= '</li>'; 

            $sx .= '</ul>';
            $sx .= '</div>';
            $sx .= '</div>';
            return($sx);
        }
    function change_text_to($d1,$d2)
        {
            $ln = troca($d2,chr(13),';');
            $ln = troca($ln,chr(10),'');
            
            $lns = splitx(';',$ln);
            for ($r=0;$r < count($lns);$r++)
                {
                    $ln = troca($lns[$r],'=>',';');
                    $m = splitx(';',$ln);
                    $d1 = troca($d1,$m[0],$m[1]);
                }
            return($d1);
        }
    function form_1()
        {
            $form = new form;
            $cp = array();
            array_push($cp,array('$H8','','',False,False));
            array_push($cp,array('$T80:10','',msg('text_to_process'),True,True));
            array_push($cp,array('$B8','',msg('process'),False,True));
            $sx = $form->editar($cp,'');
            return($sx);
        }
    function form_2()
        {
            $form = new form;
            $cp = array();
            array_push($cp,array('$H8','','',False,False));
            array_push($cp,array('$T80:10','',msg('text_to_process'),True,True));
            array_push($cp,array('$T80:10','',msg('text_to_change_to'),True,True));
            array_push($cp,array('$B8','',msg('process'),False,True));
            $sx = $form->editar($cp,'');
            return($sx);
        }        
        function form_file($title='')
            {
                if (strlen($title) > 0)
                    {
                        $sx = '<h4>'.$title.'</h4>'.cr();
                    } else {
                        $sx = '';
                    }
                $sx .= '
                    <form action="" method="post" enctype="multipart/form-data">
                        <!-- MAX_FILE_SIZE deve preceder o campo input -->
                        <!-- O Nome do elemento input determina o nome da array $_FILES -->
                        Enviar esse arquivo: <input name="userfile" type="file" />
                        <br><br>
                        <input type="submit" value="Enviar arquivo" class="btn btn-primary" />
                    </form>               
                ';                
                if (isset($_FILES['userfile']['tmp_name']))
                {
                    $file_name = $_FILES['userfile']['tmp_name'];
                    if (file_exists(($file_name)))
                    {
                        $this->file_name = $file_name;
                    }
                }
                return($sx);
            }        

        
        
    function semicolon_to_list($txt) {
        for ($r=1;$r < 32;$r++)
            {
                $t = troca($txt, chr($r), ';');
            }
        while (strpos($t,';;'))
            {
                $t = troca($t,';;',';');
            }
        for ($r = 0; $r < 31; $r++) {
            $t = troca($txt, chr($r), '');
        }
        $te = splitx(';', $t);
        $sx = '';
        asort($te);
        foreach ($te as $key => $value) {          
            if (strlen($value) > 0) {
                $id = $this->detect_language($value);
                $sx .= $value . $id.cr();
            }
        }
        return ($sx);
    }

        function readfile($file='')
            {
                $tx = '';
                
                if ($file == '') { $file = $this->file_name; }
                if (is_file($file))
                {
                    $fl = fopen($file,'r+');
                    
                    while (!feof($fl))
                        {
                            $tx .= fread($fl,1024);
                        }
                    fclose($fl);
                }
                return($tx);
            }

        function csv_to_net($txt)
            {
                $txt = $this->trata($txt);
                $txt = troca($txt,';','£');
                $txt = troca($txt,chr(10),';');
                $txt = troca($txt,chr(13),';');
                $lns = splitx(';',$txt);
                
                $nx = array();
                $ns = array();
                $nf = array();
                $nz = array();
                for ($r=0;$r < count($lns);$r++)
                    {
                        $mn = $lns[$r];
                        $mn = troca($mn,'£',';');
                        $au = splitx(';',$mn.';');
                        
                        for ($a=0;$a < count($au);$a++)
                            {
                                if (get("dd1")=='1')
                                    {
                                        $mm = nbr_autor($au[$a],5);     
                                    } else {
                                        $mm = $au[$a];
                                    }                               

                                $mm = troca($mm,',','');
                                $mm = troca($mm,'. ','');
                                $mm = troca($mm,'.','');
                                $au[$a] = $mm;                              
                            }                       
                        
                        for ($a=0;$a < count($au);$a++)
                            {
                                $mm = $au[$a];
                                if (isset($ns[$mm]))
                                    {
                                        $ns[$mm] = $ns[$mm] + 1;
                                    } else {
                                        $ns[$mm] = 1;
                                        array_push($nf,$mm);
                                    }
                                /* monta matriz */
                                if ($a == 0)
                                    {
                                        /**************** Primeiro Autor **********************/
                                        if (isset($nx[$au[0]][$au[0]]))
                                            {
                                                $nx[$au[0]][$au[0]] = $nx[$au[0]][$au[0]] + 1;
                                            } else {
                                                $nx[$au[0]][$au[0]] = 1;
                                            }
                                    } else {
                                        /*************** Outros autores ***********************/
                                        for ($b=0;$b < $a;$b++)
                                        {
                                        $ma = $au[$b];
                                        if (isset($nx[$ma][$mm]))
                                            {
                                                $nx[$ma][$mm] = $nx[$ma][$mm] + 1;
                                                $nx[$mm][$ma] = $nx[$mm][$ma] + 1;
                                            } else {
                                                $nx[$ma][$mm] = 1;
                                                $nx[$mm][$ma] = 1;
                                            }
                                        }                                                           
                                    }                               
                            }
                        
                    }
                sort($nf);
                /*  matriz */
                $sx = '*Vertices '.count($nf).cr();
                $max = 10;
                foreach ($nf as $key => $val1) {
                    if ($ns[$val1] > $max)
                        {
                            $max = $ns[$val1]; 
                        }
                }
                foreach ($nf as $key => $val1) {
                    $n1 = number_format($ns[$val1]/$max*10,4);
                    //$sx .= ($key+1).' "'.$val1.'" '.$n1.' '.$ns[$val1].' '.$ns[$val1].' '.cr();
                    $sx .= ($key+1).' "'.$val1.'" ellipse x_fact '.$n1.' y_fact '.$n1.' fos 1 ic LightYellow lc Blue '.cr();
                }

                $sx .= '*Edges'.cr();
                
                foreach ($nf as $key1 => $val1) {
                    foreach ($nf as $key2 => $val2 ) {
                        if ($val1 < $val2)
                        {
                            if (isset($nx[$val1][$val2]))
                            {
                                if (isset($nx[$val1][$val2]))
                                    {
                                        $tot = $nx[$val1][$val2];
                                    } else {
                                        $tot = 0;
                                    }
                                $sx .= ($key1+1).' '.($key2+1).' '.$tot.cr();
                            } else {
                                
                            }
                        }
                    }
                }

                return($sx);
            }

        function csv_to_matrix($txt)
            {
                $txt = $this->trata($txt);
                $txt = troca($txt,';','£');
                $txt = troca($txt,chr(10),';');
                $txt = troca($txt,chr(13),';');
                $lns = splitx(';',$txt);
                
                $nx = array();
                $ns = array();
                $nf = array();
                
                for ($r=0;$r < count($lns);$r++)
                    {
                        $mn = $lns[$r];
                        $mn = troca($mn,'£',';');
                        $au = splitx(';',$mn.';');
                        
                        for ($a=0;$a < count($au);$a++)
                            {
                                if (get("dd1")=='1')
                                    {
                                        $mm = nbr_autor($au[$a],5);     
                                    } else {
                                        $mm = $au[$a];
                                    }
                                
                                $mm = troca($mm,',','');
                                $mm = troca($mm,'. ','');
                                $mm = troca($mm,'.','');
                                $au[$a] = $mm;                              
                            }
                        
                        for ($a=0;$a < count($au);$a++)
                            {
                                $mm = $au[$a];                              
                                if (isset($ns[$mm]))
                                    {
                                        $ns[$mm] = $ns[$mm] + 1;
                                    } else {
                                        $ns[$mm] = 1;
                                        array_push($nf,$mm);
                                    }

                                /* monta matriz */
                                if ($a == 0)
                                    {
                                        /**************** Primeiro Autor **********************/
                                        if (isset($nx[$au[0]][$au[0]]))
                                            {
                                                $nx[$au[0]][$au[0]] = $nx[$au[0]][$au[0]] + 1;
                                            } else {
                                                $nx[$au[0]][$au[0]] = 1;
                                            }
                                    } else {
                                        /*************** Outros autores ***********************/
                                        for ($b=0;$b < $a;$b++)
                                        {
                                        $ma = $au[$b];
                                                                            
                                        if (isset($nx[$ma][$mm]))
                                            {
                                                $nx[$ma][$mm] = $nx[$ma][$mm] + 1;
                                                $nx[$mm][$ma] = $nx[$mm][$ma] + 1;
                                            } else {
                                                $nx[$ma][$mm] = 1;
                                                $nx[$mm][$ma] = 1;
                                            }
                                        }                                                           
                                    }                               
                            }
                        
                    }
                
                /*  matriz */
                $sx = '#;';
                foreach ($nf as $key => $val1) {
                    $sx .= ''.$val1.';';
                }
                $sx .= ''.cr();
                foreach ($nf as $key => $val1) {
                    $sx .= ''.$val1.';';
                    foreach ($nf as $key2 => $val2 ) {
                        if ((isset($nx[$val1][$val2])) AND ($val1 != $val2))
                        {
                            $sx .= ''.$nx[$val1][$val2].';';
                        } else {
                            $sx .= '0;';
                        }
                    }
                    $sx .= ''.cr();
                }
                $sx  .= '';
                return($sx);
            }
        function detect_language($t)
            {
                $t = ' '.lowercase($t).' ';
                $t = troca($t,'.',' ');
                $t = troca($t,'?',' ');
                $t = troca($t,'-',' ');
                $t = troca($t,'(',' ');
                $en = array(' and',' or','nals','mic','ion','ing','rds','rd',
                            'ics','k','y','t','d','der','lt','cs','lex','nal','ian','rch',
                            'hic','ase','ials','stem','eval','ms','ure','sm','al','an',' for','c',
                            'ate','ile','bases','databases','ions','rian','ph','ives', 'cles',
                            'hers','ks','ts','ds','brazil','like','class','ools','rcis','lente',
                            'ange','ex','ors','w','ws','ship','ires','nces','role',
                            'rvice', 'bus','line','use','ies','face','sis','ens'
                            );
                            
                $pt = array('ário','afia','mana','ção','são',' de',' da',' do',
                            'gia','lica','ico', 'logo',' para','tica',
                            'sil','mia',' vro','rvo','sal', 'nto','rie','ria',
                            'fía', 'ana','ura','cia','ica','eca','ada','ato', 'nual',
                            'iva','ao','ivo','ado','ulo','ias','cas','ilos','cos',
                            'ografía','asse','tador','tudo','vro','esso','nha','arte',
                            'mbuco','lona','ilia','grafo','usca','doro','chas',
                            'ense','nema','ismo','dade','mbia','ito','trole','urso',
                            'reito','lista','ino', 'rior','ista','stão','mídia',
                            'exto','des','ador','dores','mbio','lei','uais','meio','obra',
                            'rara','quisa','ursos','anta','ina','ida','ída', 'cola',
                            'ema','uro','ano','ião','iao','nio','nia','rede','todos','grafia',
                            'asica','nomia','dulo','orma','nal','ormas','nais','egal','sito'
                            );
                            
                $es = array(' le',' la',' lo',' los', 'ción','cion','del','sión','sion',
                            'gía','ana','ura','ueva','eva','fía','blografía','ales',
                            'ctos','datos','tos','cación','ipción','ctura','sidad','dad',
                            'culos','bros','itas','ó','tión',' por','esos','bros','bro','guo',
                            ' en','ínea','ajo','brar'
                            );
                $pen = 0;
                $ppt = 0;
                $pes = 0;
                /* ingles */
                for ($r=0;$r < count($en);$r++)
                    {
                        if (strpos($t,$en[$r].' ')) { $pen++; }
                    }
                /* PORTUGUES */
                for ($r=0;$r < count($pt);$r++)
                    {
                        if (strpos($t,$pt[$r].' ')) { $ppt++; }
                    }
                /* ESPANHOL */                     
                for ($r=0;$r < count($es);$r++)
                    {
                        if (strpos($t,$es[$r].' ')) { $pes++; }
                    } 
                    
                    
                $sx = '==>EN='.$pen.'==PT='.$ppt.'<br>';
                if (($ppt >= $pen) and ($ppt >= $pes))
                    {
                        $sx = " (PT); en:$pen, pt:$ppt, es:$pes"; 
                    }
                if (($pen > $ppt) and ($pen > $pes))
                    {
                        $sx = " (EN); en:$pen, pt:$ppt, es:$pes"; 
                    }
                if (($pes > $ppt) and ($pes >= $pen))
                    {
                        $sx = " (ES); en:$pen, pt:$ppt, es:$pes"; 
                    } 
                if (($pen == 0) and ($ppt == 0) and ($pes == 0))
                    {
                        $sx = '(??);(---'.$t.'---)';
                    }
                return(';'.$sx);
            }
        function trata($txt)
            {
                $txt = troca($txt,'; ',';');
                $txt = troca($txt,'"','');
                for ($r=0;$r < 32;$r++)
                    {
                        if (($r != 13) and ($r != 10))
                            {
                                $txt = troca($txt,chr($r),' ');
                            }       
                    }
                
                $t  = array('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ã','õ','Â','Ô','ä','ë','ï','ö','ü','Ä','Ë','Ï','Ö','Ü','â','ê','î','ô','û','Â','Ê','Ô','Û','ç','Ç');
                $tr = array('a','e','i','o','u','A','E','I','O','U','a','o','A','O','a','e','i','o','u','A','E','I','O','U','a','e','i','o','u','A','E','O','U','c','C');
                for ($r=0;$r < count($tr);$r++)
                    {
                        $t1 = $t[$r];
                        $t2 = $tr[$r];
                        $txt = troca($txt,$t1,$t2);
                        $txt = troca($txt,utf8_decode($t1),$t2);        
                    }
                for ($r=128;$r < 255;$r++)
                    {
                        $txt = troca($txt,chr($r),' ');     
                    }
                $txt = troca($txt,'  ',' ');
                return($txt);
            }
        function download_file($txt,$type='.csv')
            {
                $arquivo = 'brapci_'.date("Ymd_His").$type;
                //header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                //header ("Pragma: no-cache");
                header ("Content-type: application/x-msexcel");
                header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );       
                echo $txt;
            }
}
?>
