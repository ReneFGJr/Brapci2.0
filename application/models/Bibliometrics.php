<?php
class bibliometrics extends CI_model {
    var $file_name = '';  
    var $pqs = array();  
    function index($ac)
    {
        $tela = '<h3>'.msg($ac).'</h3>';
        $tela .= '<p>'.msg($ac.'_info').'</p>';
        $dd1 = get("dd1");
        $dd2 = get("dd2");
        switch($ac) {
            case 'basket_cited':
                if ((strlen($dd1) == 0)) {
                    $tela .= $this -> bibliometrics -> form_1();
                } else {
                    $rst = $this->basket_cited($dd1, $dd2);
                    $tela .= $this -> bibliometrics -> form_1();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= '<textarea class="form-control" style="height: 300px;">' . $rst . '</textarea>';
                }            
                
            break;
            case 'half_live':
                if (strlen($dd1) == 0) {
                    $tela .= $this -> bibliometrics -> form_1();
                } else {
                    $rst = $this -> bibliometrics -> half_live($dd1);
                    $tela .= $this -> bibliometrics -> form_1();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= $rst;
                }            
            break;            
            case 'cited_analyse':
                if (strlen($dd1) == 0) {
                    $tela .= $this -> bibliometrics -> form_1();
                } else {
                    $rst = $this -> bibliometrics -> cited_analyse($dd1);
                    $tela .= $this -> bibliometrics -> form_1();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= $rst;
                }            
            break;
            case 'csv_to_net' :
                if (!(isset($_FILES['userfile']['tmp_name']))) {
                    $tela .= $this -> bibliometrics -> form_file(msg($ac));
                } else {
                    $txt = $this -> bibliometrics -> readfile($_FILES['userfile']['tmp_name']);
                    $rst = $this -> bibliometrics -> csv_to_net($txt);
                    $this -> bibliometrics -> download_file($rst, '.net');
                    exit;
                    return ('');
                }
            break;
            case 'net_to_gephi' :
                if (!(isset($_FILES['userfile']['tmp_name']))) {
                    $tela .= $this -> bibliometrics -> form_file(msg($ac));
                } else {
                    $txt = $this -> bibliometrics -> readfile($_FILES['userfile']['tmp_name']);
                    $rst = $this -> bibliometrics -> net_to_gephi($txt);   
                    exit;             
                    return ('');
                }
            break;        
            case 'csv_to_matrix' :
                if (!(isset($_FILES['userfile']['tmp_name']))) {
                    $tela .= $this -> bibliometrics -> form_file(msg($ac));
                } else {
                    $txt = $this -> bibliometrics -> readfile($_FILES['userfile']['tmp_name']);
                    $rst = $this -> bibliometrics -> csv_to_matrix_ocorrencia($txt);
                    $this -> bibliometrics -> download_file($rst);
                    exit;
                    return ('');
                }
            break;
            case 'csv_to_matrix_ocorrencia' :
                if (!(isset($_FILES['userfile']['tmp_name']))) {
                    $tela .= $this -> bibliometrics -> form_file(msg($ac));
                } else {
                    $txt = $this -> bibliometrics -> readfile($_FILES['userfile']['tmp_name']);
                    $rst = $this -> bibliometrics -> csv_to_matrix_ocorrencia($txt);
                    $this -> bibliometrics -> download_file($rst);
                    return ('');
                }
            break;                
            case 'semicolon_to_list' :
                if (strlen($dd1) == 0) {
                    $tela .= $this -> bibliometrics -> form_1();
                } else {
                    $rst = $this -> bibliometrics -> semicolon_to_list($dd1);
                    $tela .= $this -> bibliometrics -> form_1();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= '<textarea class="form-control" style="height: 300px;">' . $rst . '</textarea>';
                }
            break;
            case 'remove_tags':
                if ((strlen($dd1) == 0) or (strlen($dd1) == 0)) {
                    $tela .= $this -> bibliometrics -> form_1();
                } else {
                    $rst = $this -> bibliometrics -> remove_tags($dd1, $dd2);
                    $tela .= $this -> bibliometrics -> form_1();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= '<textarea class="form-control" style="height: 300px;">' . $rst . '</textarea>';
                }            
            break;
            case 'change_to' :
                if ((strlen($dd1) == 0) or (strlen($dd2) == 0)) {
                    $tela .= $this -> bibliometrics -> form_2();
                } else {
                    $rst = $this -> bibliometrics -> change_text_to($dd1, $dd2);
                    $tela .= $this -> bibliometrics -> form_2();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= '<textarea class="form-control" style="height: 300px;">' . $rst . '</textarea>';
                }
            break;

            case 'list_row' :
                if ((strlen($dd1) == 0) or (strlen($dd2) == 0)) {
                    $tela .= $this -> bibliometrics -> form_1();
                } else {
                    $rst = $this -> bibliometrics -> list_row($dd1, $dd2);
                    $tela .= $this -> bibliometrics -> form_1();
                    $tela .= '<h4>' . msg('result') . '</h4>';
                    $tela .= '<textarea class="form-control" style="height: 300px;">' . $rst . '</textarea>';
                }
            break;
            
            default :
            $tela = $this -> bibliometrics -> tools_menu();
        }
        return($tela);            
    }
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
        $sx .= '<a href="'.base_url(PATH.'bibliometric/net_to_gephi').'">';
        $sx .= msg('net_to_gephi');
        $sx .= '</a>';
        $sx .= '</li>';        
        
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/csv_to_matrix').'">';
        $sx .= msg('csv_to_matrix');
        $sx .= '</a>';
        $sx .= '</li>';    
        
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/csv_to_matrix_ocorrencia').'">';
        $sx .= msg('csv_to_matrix_ocorrencia');
        $sx .= '</a>';
        $sx .= '</li>';

        $sx .= '<li>'.msg('Text_process').'</li>';

        $sx .= '<ul>';
        
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/change_to').'">';
        $sx .= msg('change_to');
        $sx .= '</a>';
        $sx .= '</li>'; 
        
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/remove_tags').'">';
        $sx .= msg('remove_tags');
        $sx .= '</a>';
        $sx .= '</li>'; 

        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/list_row').'">';
        $sx .= msg('list_row');
        $sx .= '</a>';
        $sx .= '</li>';          

        $sx .= '</ul>';
        
        $sx .= '<li>';
        $sx .= '<b>'.msg('citation_index').'</b>';
        $sx .= '<ul>';
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/cited_analyse').'">';
        $sx .= msg('cited_analyse');
        $sx .= '</a>';
        $sx .= '</li>';
        /* Half Live */
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/half_live').'">';
        $sx .= msg('half_live');
        $sx .= '</a>';
        $sx .= '</li>';

        /* Half Live */
        $sx .= '<li>';
        $sx .= '<a href="'.base_url(PATH.'bibliometric/basket_cited').'">';
        $sx .= msg('basket_cited');
        $sx .= '</a>';
        $sx .= '</li>';        
        
        $sx .= '</ul>';
        $sx .= '</li>';                       
        
        $sx .= '</ul>';
        $sx .= '</div>';
        $sx .= '</div>';
        return($sx);
    }
    function basket_cited($d1,$d2)
        {
            /****/
            $dd1 = troca($d1,chr(13),';');
            $dd1 = splitx(';',$dd1);            
            $wh2 = '';
            for ($r=0;$r < count($dd1);$r++)
                {
                    $t = $dd1[$r];
                    if (strlen($wh2) > 0) { $wh2 .= ' OR '; }
                    $wh2 .= " (ca_text like '%$t,%') ";
                }
            $this->load->model("Cited");
            $ob = $this->bs->sels();
            $wh = '';            
            for ($r=0;$r < count($ob);$r++)
                {
                    if (strlen($wh) > 0) { $wh .= ' or '; }
                    $wh .= '( ca_rdf = '.$ob[$r].') ';
                }
                
            $sql = "SELECT * FROM ".$this->Cited->base."cited_article where ($wh) and ($wh2) ORDER BY ca_text";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();            
            return($rlt);
        }
    function half_live($d1)
    {
        $sx = troca($d1,';','.,');
        $sx = troca($sx,chr(13),';');
        $sx = troca($sx,chr(10),';');
        $ln = splitx(';',$sx);
        $rst = '';
        $mr = '';
        for ($y=1900;$y < date("Y")+1;$y++)
        {
            for ($r=0;$r < count($ln);$r++)
            {
                $lns = $ln[$r];
                if (strpos($lns,(string)$y) > 0)
                {
                    $ln[$r] = "";
                    $rst .= $y.cr();
                    if (strlen($mr) > 0) { $mr .= ', '; }
                    $mr .= ''.$y.'';
                }                            
            }
        }
        $rsn = '';
        for ($r=0;$r < count($ln);$r++)
        {
            if (strlen($ln[$r]) > 0)
                {
                    $rsn .= $ln[$r].cr();
                }
        }
        $rt = '<table class="table">'.cr();
        $rt .= '<tr><td width="20%">'.msg('Years').'</td>
        <td><textarea style="widtH: 100%; height: 400px;" name="year" id="years">'.$rst.'</textarea></td></tr>';
        $rt .= '<tr><td width="20%">'.msg('R Script').'</td>';
        $rt .= '<td>years <- c('.$mr.')</td>';
        $rt .= '</tr>';
        $rt .= '<tr><td width="20%">'.msg('Not Identify').'</td>
        <td><textarea style="widtH: 100%; height: 400px;" name="year" id="years">'.$rsn.'</textarea></td></tr>';
        $rt .= '</table>';
        return($rt);
    }
    function remove_tags($d1)
    {
        $d1 = troca($d1,' [','<');
        $d1 = troca($d1,'] ','>');
        $d1 = troca($d1,'[','<');
        $d1 = troca($d1,']','>');
        $d1 = strip_tags($d1);
        return($d1);
    }

    function list_row($d1)
        {
            $ln = troca($d2,chr(13),';');
            $ln = troca($ln,chr(10),'');
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
            if (!isset($m[1]))
                {
                    $d1 = '[erro] linha: '.$r.' => '.$m[0];
                } else {
                    $d1 = troca($d1,$m[0],$m[1]);
                }            
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
    
    function net_to_gephi($txt)
    {
        $sx = '';
        $sx .= '<pre>';
        $sx .= $txt;
        $sx .= '</pre>';
        
        $ln = troca($txt,chr(13),'#');
        $ln = splitx('#',$ln);
        
        $nodes = array();
        $edges = array();
        $n = 0;
        $e = 0;
        for ($r=0;$r < count($ln);$r++)
        {
            $cmd = substr($ln[$r],0,2);
            if (($n == 1) and ($cmd != '*E'))
            {
                $nr = substr($ln[$r],0,strpos($ln[$r],' '));
                $name = substr($ln[$r],strlen($nr)+2,1000);
                $char = ' ';
                if (strpos($name,'"') > 0) { $char = '"'; }
                $name = substr($name,0,strpos($name,$char));
                array_push($nodes,array($nr,$name));
            }
            
            if ($e == 1)
            {
                $ll = troca($ln[$r],' ',';'); 
                $lla = splitx(';',$ll);
                if (count($lla) == 3)
                {
                    array_push($edges,$lla);
                }
            }
            if ($cmd == '*V')
            {
                $n = 1;
                $e = 0;
            }
            if ($cmd == '*E')
            {
                $n = 0;
                $e = 1;
            }                    
        }
        $sx = '';            
        $sx .= 'graph'.cr();
        $sx .= '['.cr();
        $sx .= '    Creator "Brapci '.date("Y-m-d H:i:s").'"'.cr();
        for ($r=0;$r < count($nodes);$r++)
        {
            $sx .= '    node'.cr();
            $sx .= '    ['.cr();
            $sx .= '        id '.$nodes[$r][0].cr();
            $sx .= '        label "'.$nodes[$r][1].'"'.cr();
            $sx .= '    ]'.cr();
        }
        $sx .= '********** FILE2 ************'.cr();
        $sx .= 'Source,Target,Type,Weight'.cr();
        for ($r=0;$r < count($edges);$r++)
        {
            $sx .= '    edge'.cr();
            $sx .= '    ['.cr();
            $sx .= "        source ".$edges[$r][0].cr();
            $sx .= "        target ".$edges[$r][1].cr();
            $sx .= "        value ".$edges[$r][2].cr();
            $sx .= '    ]'.cr();
        }
        $this -> bibliometrics -> download_file($sx, '.gml');
        return($sx);
    }    
    
    function csv_to_net($txt)
    {
        set_time_limit(3600);
        $txt = $this->trata($txt);
        $txt = troca($txt,'.,',';');
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
    
    
    
    function csv_to_matrix_ocorrencia($txt)
    {
        $txt = $this->trata($txt);
        $txt = troca($txt,'.,',';');
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
            
            $ax = array();
            $ai = array();
            for ($z=0;$z < count($au);$z++)
            {
                $nn = $au[$z];
                if (!isset($ax[$nn]))
                {
                    array_push($ai,$nn);
                    $ax[$nn] = $nn;
                }
            }
            
            $au = $ai;
            
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
        'rvice', 'bus','line','use','ies','face','sis','ens');
        
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
        'asica','nomia','dulo','orma','nal','ormas','nais','egal','sito');
        
        $es = array(' le',' la',' lo',' los', 'ción','cion','del','sión','sion',
        'gía','ana','ura','ueva','eva','fía','blografía','ales',
        'ctos','datos','tos','cación','ipción','ctura','sidad','dad',
        'culos','bros','itas','ó','tión',' por','esos','bros','bro','guo',
        ' en','ínea','ajo','brar');
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
    
    function cited_analyse($txt)
    {
        $ln = troca($txt,';','.,');
        $ln = troca($ln,chr(13),';');
        $ln = troca($ln,chr(10),';');
        $lns = splitx(';',$ln);
        $sx = '<ol>';
        $years = array();
        $source = array('JOURNAL'=>0,'BOOK'=>0,'PROCEEDINGS'=>0, 'TD'=>0,'SITE'=>0,'LAW'=>0,'NC'=>0);
        
        for ($r=0;$r < count($lns);$r++)
        {
            $sx .= '<li>';
            $sx .= $lns[$r];
            /**************************************** ANO ********/
            $year = $this->recover_year($lns[$r]);
            if (sonumero($year) == $year)
            {
                if (isset($years[$year]))
                {
                    $years[$year] = $years[$year] + 1;
                } else {
                    $years[$year] = 1;    
                }
            }            
            $sx .= '<br>'.$year;
            
            /**************************************** ARTICLE *****/
            $tp = 'NC';
            if ($this->is_law($lns[$r])) { $tp = 'LAW'; }
            else
            {
                if ($this->is_tese_dissertacao($lns[$r]))
                {
                    $tp = 'TD'; 
                } else {
                    if ($this->is_journal($lns[$r])) 
                    { 
                        $tp = 'JOURNAL'; 
                    }
                    else 
                    {
                        if ($this->is_proceedings($lns[$r])) 
                        { 
                            $tp = 'PROCEEDINGS'; 
                        } 
                        else
                        {
                            if ($this->is_http($lns[$r])) 
                            { 
                                $tp = 'SITE'; 
                            } 
                            else
                            {
                                if ($this->is_book($lns[$r])) { $tp = 'BOOK'; }
                            }
                        }
                    }
                }
            }
            
            /******************************** PROCESSA FONTS **************/
            if (isset($source[$tp]))
            { $source[$tp] = $source[$tp] + 1;} 
            else 
            { $source[$tp] = 1; }
            
            $sx .= ' - '.$tp;
            $sx .= '</li>';
        }
        $sx .= '</ol>';
        
        $sa = '<div class="row">';
        
        $sa .= '<div class="col-md-2 text-center">';
        $sa .= msg('Cites').'<br>';
        $sa .= '<span style="font-size: 250%">'.count($lns).'</span>';
        $sa .= '<br/><span class="font-size: 70%;">'.msg('cites').'</span>';
        $sa .= '</div>';
        
        /***************** Half Live *****************/        
        $sa .= '<div class="col-md-2 text-center">';
        $sa .= msg('Half Live').'-'.date("Y").'<br>';
        $sa .= '<span style="font-size: 250%">'.$this->halflive($years).'</span>';
        $sa .= '<br/><span class="font-size: 70%;">anos</span>';
        $sa .= '</div>';
        
        $sa .= '</div>';
        $sa .= '<div class="row">';
        /***************** Sources *****************/ 
        foreach ($source as $key => $value) {
            $sa .= '<div class="col-md-2 text-center">';
            $sa .= msg($key).'<br>';
            $sa .= '<span style="font-size: 250%">'.$value.'</span>';
            $sa .= '<br/><span class="font-size: 70%;">itens</span>';
            $sa .= '</div>';        
        }            
        
        $sa .= '</row>';
        
        return($sa.$sx);
    }
    
    function is_http($txt)
    {
        $bc = array('Disponível em');
        $ct = 0;
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $ct++;
            }                    
        }            
        if ($ct > 0)
        {
            return(1);    
        } else {
            return(0);
        }            
    }
    
    function is_book($txt)
    {
        $bc = array(': ');
        $ct = 0;
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $ct++;
            }                    
        }            
        if ($ct > 0)
        {
            return(1);    
        } else {
            return(0);
        }            
    }   
    
    function is_proceedings($txt)
    {
        $bc = array('Anais...','Actas...','Proceedings...','Proceedings [...]');
        $ct = 0;
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $ct++;
            }                    
        }            
        if ($ct > 0)
        {
            return(1);    
        } else {
            return(0);
        }            
    }
    
    function is_tese_dissertacao($txt)
    {
        $bc = array(' Tese',' Dissertação','Tesis','(Doctorado)');
        $ct = 0;
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $ct++;
            }                    
        }            
        if ($ct > 0)
        {
            return(1);    
        } else {
            return(0);
        }            
    }
    
    function is_law($txt)
    {
        $bc = array('Lei n.');
        $ct = 0;
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $ct++;
            }                    
        }            
        if ($ct > 0)
        {
            return(1);    
        } else {
            return(0);
        }
    }
    
    function is_journal($txt)
    {
        $txt = substr($txt,strlen($txt)/2,strlen($txt));
        $bc = array(' V.',' Vol.',' v.',' n.',' nr.');
        $ct = 0;
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $ct++;
            }                    
        }            
        if ($ct > 0)
        {
            return(1);    
        } else {
            return(0);
        }
    }
    
    function halflive($dt,$year='')
    {
        if (strlen($year) == 0)
        {
            $year = date("Y");
        }
        
        $tot = 0;
        $tov = 0;
        foreach ($dt as $y => $v) {
            $tot = $tot+($y*$v);
            $tov = $tov+$v;
        }
        $hl = ((int)($tot/$tov*10))/10;
        $hl = round(($year - $hl)*10)/10;
        return($hl);
    }
    
    function recover_year($txt)
    {
        $bc = array('Acesso em','Disponível em','DOI:','Disponible en:','Acceso en:');
        for ($r=0;$r < count($bc);$r++)
        {
            if (strpos($txt,$bc[$r]))
            {
                $txt = substr($txt,0,strpos($txt,$bc[$r]));
            }                    
        }
        
        /************************* so anos */
        $nr2 = sonumero($txt);
        $ok = strlen($nr2);
        while ($ok >= 3)
        {
            $nr = (int)substr($nr2,$ok-4,4);
            //echo $nr.'<br>';
            if (($nr > 1900) and ($nr <= (date("Y")+1)))
            {
                return($nr);
            }
            $ok--;
        }
        $nr = '<span class="alert">ERRO</span>';
        return($nr);
    }     
    
    /******************************** Painel Bibliometrico */       
    function metrics_basket($tp='')
    {
        $this->load->helper('R');
        $ob = $this->bs->sels();
        /* Ano de publicação */
        /* Titulos dos periódicos */
        /* Assuntos */
        /* Autores */
        $sx = '';
        $sx .= $this->bibliotetric_authors($ob,$tp);
        $sx .= $this->bibliotetric_year($ob,$tp);
        $sx .= $this->bibliotetric_journal($ob,$tp);
        //$sx .= $this->bibliometrics_ref($ob,$tp);
        return($sx);
    }

    function is_pq($id)
        {
            $pqs = $this->pqs;
            if (count($pqs) == 0)
            {
                $this->load->model("Pqs");
                $this->pqs = $this->Pqs->lista();            
            }
            echo '==>'.$id;
            return(0);
        }

    function bibliometrics_ref($ob)
        {
            $this->load->model('cited');
            $ref = $this->cited->refs_group($ob);
            return($ref);
        }

    function bibliotetric_authors($ob,$tp)
        {
            $pqs = array();
            $limit = get("limit");
            if (strlen($limit) == 0)
                {
                    $limit = 20;
                }

            $au = array();
            $aaa = array();
            $csv = 'id,author,value,percente,acumulate'.cr();
            $ip = troca(ip(),'.','_');            
            $pq = get("pq");
            $case = '';
            if (strlen($pq) > 0)
                {
                    $this->load->model('Pqs');
                    $case = 'pq';
                }

            for ($r=0;$r < count($ob);$r++)
                {
                    
                switch($case)
                    {
                        /* PQ */
                        case 'pq':
                                $lst = array();
                                $rst = $this->metadata($ob[$r],'AU');
                                foreach ($rst as $aaa1=>$aaa2)
                                    {
                                        array_push($lst,$aaa1);
                                    }
                                $aaa = 'c/'.$ob[$r].'/author.json';
                                if (file_exists($aaa))
                                {
                                $aaa = file_get_contents($aaa);
                                $aaa = (array)json_decode($aaa);
                                $nr = 0;
                                foreach ($aaa as $aa2=>$aa3)
                                    {
                                        $pq = $this->Pqs->is_pq($aa2); 
                                        if ($pq == 1)
                                            {
                                            $key = $lst[$nr];
                                            if (isset($au[$key]))
                                                {
                                                    $au[$key] = $au[$key] + 1;
                                                } else {
                                                    $au[$key] = 1;
                                                }
                                            }
                                        $nr++;
                                    }
                                }
                                break;
                        default:                        
                            $nr = 0;
                            $rst = $this->metadata($ob[$r],'AU');
                            foreach($rst as $key => $value)
                                {
                                    /**************************************************************/
                                    if (isset($au[$key]))
                                        {
                                            $au[$key] = $au[$key] + 1;
                                        } else {
                                            $au[$key] = 1;
                                        }
                                }
                        }
                }

            /*********** Ordenar */
            $aut = array();
            foreach($au as $key => $value)
                {
                    array_push($aut,strzero($value,4).$key);
                }
            rsort($aut);

            /*********** Ordenar */
            $au = array();
            $tot = 0;
            $totn = 0;
            foreach($aut as $key => $value)
                {
                    $name = substr($value,4,strlen($value));
                    $vlr = round(substr($value,0,4));
                    $au[$name] = $vlr;
                    $totn++;
                    $tot = $tot + $vlr;
                }

            $nr = 0;
            $acm = 0;
            $last = 0;

            $out = 0;
            $vout = 0;
            $sx = '';

            $R = new R;
            $sc = '';
            $sc .= $R->library('ggplot2');
            $sc .= $R->datasets($au,1,10);
            $sc .= $R->fcn('authors_productions');
            $sc .= $R->image(1);
            $R->exec($sc);

            $sx .= '<div class="'.bscol(12).'">';
            $sx .= $R->html_image($R->img);            
            $sx .= '</div>';

            $sx .= '<div class="'.bscol(12).'">';
            $sx .= $R->show_script($sc,0);
            $sx .= '</div>';

            $sx .= '<div class="'.bscol(12).'">';
            $sx .= '<span class="legend">Tabela 1 - Autores mais produtivos na temática (n='.count($aut).')</span>';
            $sx .= '<table width="100%">';
            $sx .= '<tr>';
            $sx .= '<td align="center"><b>#</b></td>';
            $sx .= '<td><b>'.msg('name').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('freq.').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('perc.').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('acum.').'</b></td>';
            $sx .= '</tr>'.cr();
            foreach($au as $key => $value)
                {
                    $nr++;
                    $per = $value/$tot;
                    $acm = $acm + $per;

                    if (($nr <= $limit) or (($nr > $limit) and ($value >= $last)))
                    {
                        $sx .= '<tr>';
                        $sx .= '<td with="5%" align="center">'.$nr.'</td>';
                        $sx .= '<td with="65%">'.$key.'</td>';
                        $sx .= '<td with="10%" align="center">'.$value.'</td>';
                        $sx .= '<td with="10%" align="center">'.number_format($per*100,1,',','.').'%</td>';
                        $sx .= '<td with="10%" align="center">'.number_format($acm*100,1,',','.').'%</td>';
                        $sx .= '</tr>';
                        $last = $value;
                    } else {
                        $out++;
                        $vout = $vout + $value;
                    }
                    $csv .= $nr.',"'.$key.'",'.$value.',';
                    $csv .= '"'.number_format($per*100,1,',','.').'%",';
                    $csv .= '"'.number_format($acm*100,1,',','.').'%"';
                    $csv .= cr();
                }
            if ($out > 0)
                {
                    $sx .= '<tr>';
                    $sx .= '<td></td>';
                    $sx .= '<td>Outros ('.$out.')</td>';
                    $sx .= '<td align="center">'.number_format($vout,0,',','.').'</td>';
                    $sx .= '<td align="center">'.number_format($vout/$tot*100,1,',','.').'%</td>';
                    $sx .= '<td align="center">'.number_format($tot/$tot*100,1,',','.').'%</td>';
                    $sx .= '</tr>';
                }

/*********************** total */
                    $sx .= '<tr>';
                    $sx .= '<td></td>';
                    $sx .= '<td><b>Total ('.count($au).')</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot,0,',','.').'</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot/$tot*100,1,',','.').'%</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot/$tot*100,1,',','.').'%</b></td>';
                    $sx .= '</tr>';

            $sx .= '</table>';
            $sx .= '</div>';
            /* Salva CSV */
            $file_csv = '_temp/author_'.$ip.date("His").'.csv';
            file_put_contents($file_csv,utf8_decode($csv));
            $sx .= '<div class="'.bscol(12).'">';
            $sx .= '<br><br><a href="'.base_url($file_csv).'" class="btn btn-outline-primary">Export .CSV Authors</a>';
            $sx .= '</div>';
            return($sx);
        }

   function bibliotetric_journal($ob)
        {
            $limit = get("limit");
            if (strlen($limit) == 0)
                {
                    $limit = 99;
                }

            $au = array();
            $csv = 'id,journal,value,percente,acumulate'.cr();
            $ip = troca(ip(),'.','_');
            $file_csv = '_temp/journal_'.$ip.date("His").'.csv';
            $tot = 0;
            $min = 9999;
            $max = 0;
            $jnl = array();
            for ($r=0;$r < count($ob);$r++)
                {
                    $rst = $this->metadata($ob[$r],'T2');
                    foreach($rst as $key => $value)
                        {
                            if (isset($jnl[$key]))
                                {
                                    $jnl[$key] = $jnl[$key] + 1;
                                } else {
                                    $jnl[$key] = 1;
                                }
                        }
                    $tot++;
                }

            /*********** Ordenar */
            arsort($jnl);

            $R = new R;
            $sc = '';
            $sc .= $R->library('ggplot2');
            $sc .= $R->datasets($jnl,0,15);
            $sc .= $R->fcn('journals');
            $sc .= $R->image(2);
            $R->exec($sc);            

            $nr = 0;
            $acm = 0;
            $last = 0;

            $out = 0;
            $vout = 0;
            $sx = '';

            $sx .= '<div class="'.bscol(12).'">';
            $sx .= $R->html_image($R->img);            
            $sx .= '</div>';

            $sx .= '<div class="'.bscol(12).'">';
            $sx .= $R->show_script($sc,1);
            $sx .= '</div>';            
            
            $sx .= '<span class="legend">Tabela 1 - Local de Publicação (n='.($tot).')</span>';
            $sx .= '<table width="100%">';
            $sx .= '<tr>';
            $sx .= '<td align="center"><b>#</b></td>';
            $sx .= '<td><b>'.msg('journal').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('freq.').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('perc.').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('acum.').'</b></td>';
            $sx .= '</tr>'.cr();
            
            foreach($jnl as $key => $value)
                {
                    $nr++;
                    $per = $value/$tot;
                    $acm = $acm + $per;

                    $sx .= '<tr>';
                    $sx .= '<td with="5%" align="center">'.$nr.'</td>';
                    $sx .= '<td with="65%">'.$key.'</td>';
                    $sx .= '<td with="10%" align="center">'.$value.'</td>';
                    $sx .= '<td with="10%" align="center">'.number_format($per*100,1,',','.').'%</td>';
                    $sx .= '<td with="10%" align="center">'.number_format($acm*100,1,',','.').'%</td>';
                    $sx .= '</tr>';
                    $nr++;
 
                    $csv .= $nr.',"'.$key.'",'.$value.',';
                    $csv .= '"'.number_format($per*100,1,',','.').'%",';
                    $csv .= '"'.number_format($acm*100,1,',','.').'%"';
                    $csv .= cr();
                }
            if ($out > 0)
                {
                    $sx .= '<tr>';
                    $sx .= '<td></td>';
                    $sx .= '<td>Outros ('.$out.')</td>';
                    $sx .= '<td align="center">'.number_format($vout,0,',','.').'</td>';
                    $sx .= '<td align="center">'.number_format($vout/$tot*100,1,',','.').'%</td>';
                    $sx .= '<td align="center">'.number_format($tot/$tot*100,1,',','.').'%</td>';
                    $sx .= '</tr>';
                }

/*********************** total */
                    $sx .= '<tr>';
                    $sx .= '<td></td>';
                    $sx .= '<td><b>Total ('.count($au).')</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot,0,',','.').'</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot/$tot*100,1,',','.').'%</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot/$tot*100,1,',','.').'%</b></td>';
                    $sx .= '</tr>';

            $sx .= '</table>';
            /* Salva CSV */
            file_put_contents($file_csv,utf8_decode($csv));
            $sx .= '<br><br><a href="'.base_url($file_csv).'" class="btn btn-outline-primary">Export .CSV Journal</a>';
            return($sx);
        }        

    function bibliotetric_year($ob)
        {
            $limit = get("limit");
            if (strlen($limit) == 0)
                {
                    $limit = 99;
                }

            $au = array();
            $csv = 'id,author,value,percente,acumulate'.cr();
            $ip = troca(ip(),'.','_');
            $file_csv = '_temp/year_'.$ip.date("His").'.csv';
            $tot = 0;
            $min = 9999;
            $max = 0;
            for ($r=0;$r < count($ob);$r++)
                {
                    $rst = $this->metadata($ob[$r],'PY');
                    foreach($rst as $key => $value)
                        {
                            $xkey = round($key);
                            if ($xkey > 1950)
                            {
                                if (isset($au[$key]))
                                    {
                                        $au[$key] = $au[$key] + 1;
                                    } else {
                                        $au[$key] = 1;
                                    }
                            }
                        }
                    
                    if ($xkey > 1950)
                    {
                        if ($xkey < $min) { $min = $xkey; }
                        if ($xkey > $max) { $max = $xkey; }
                    }
                    $tot++;
                }

            /*********** Ordenar */
            ksort($au);

            $nr = 0;
            $acm = 0;
            $last = 0;

            $out = 0;
            $vout = 0;
            $sx = '';
            $sx .= '<span class="legend">Tabela 1 - Produção por ano ('.$min.'-'.$max.') (n='.($tot).')</span>';
            $sx .= '<table width="100%">';
            $sx .= '<tr>';
            $sx .= '<td align="center"><b>#</b></td>';
            $sx .= '<td><b>'.msg('name').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('freq.').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('perc.').'</b></td>';
            $sx .= '<td align="center"><b>'.msg('acum.').'</b></td>';
            $sx .= '</tr>'.cr();
            $ano = round($min);
            foreach($au as $key => $value)
                {
                    $nr++;
                    $per = $value/$tot;
                    $acm = $acm + $per;

                    while ($key > $ano)
                        {
                            $sx .= '<tr>';
                            $sx .= '<td with="5%" align="center">'.$nr.'</td>';
                            $sx .= '<td with="65%">'.$ano.'</td>';
                            $sx .= '<td with="10%" align="center">0</td>';
                            $sx .= '<td with="10%" align="center">'.number_format($per*100,1,',','.').'%</td>';
                            $sx .= '<td with="10%" align="center">'.number_format($acm*100,1,',','.').'%</td>';
                            //$sx .= '<td with="10%" align="center">'..'</td>';
                            $sx .= '</tr>';              
                            $ano++;
                            $nr++;
                            if ($ano > 2099) { exit; }              
                        }

                    $sx .= '<tr>';
                    $sx .= '<td with="5%" align="center">'.$nr.'</td>';
                    $sx .= '<td with="65%">'.$key.'</td>';
                    $sx .= '<td with="10%" align="center">'.$value.'</td>';
                    $sx .= '<td with="10%" align="center">'.number_format($per*100,1,',','.').'%</td>';
                    $sx .= '<td with="10%" align="center">'.number_format($acm*100,1,',','.').'%</td>';
                    $sx .= '</tr>';
                    $last = $value;

                    $xkey = round($key);
                    if ($xkey > 1900)
                        {
                            $ano = $key+1;
                        }                    

                    $csv .= $nr.',"'.$key.'",'.$value.',';
                    $csv .= '"'.number_format($per*100,1,',','.').'%",';
                    $csv .= '"'.number_format($acm*100,1,',','.').'%"';
                    $csv .= cr();
                }
            if ($out > 0)
                {
                    $sx .= '<tr>';
                    $sx .= '<td></td>';
                    $sx .= '<td>Outros ('.$out.')</td>';
                    $sx .= '<td align="center">'.number_format($vout,0,',','.').'</td>';
                    $sx .= '<td align="center">'.number_format($vout/$tot*100,1,',','.').'%</td>';
                    $sx .= '<td align="center">'.number_format($tot/$tot*100,1,',','.').'%</td>';
                    $sx .= '</tr>';
                }

/*********************** total */
                    $sx .= '<tr>';
                    $sx .= '<td></td>';
                    $sx .= '<td><b>Total ('.count($au).')</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot,0,',','.').'</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot/$tot*100,1,',','.').'%</b></td>';
                    $sx .= '<td align="center"><b>'.number_format($tot/$tot*100,1,',','.').'%</b></td>';
                    $sx .= '</tr>';

            $sx .= '</table>';
            /* Salva CSV */
            file_put_contents($file_csv,utf8_decode($csv));
            $sx .= '<br><br><a href="'.base_url($file_csv).'" class="btn btn-outline-primary">Export .CSV Years</a>';
            return($sx);
        }


    function metadata($id,$meta)
        {
            $rst = array();
            $file = 'c/'.$id.'/name.ABNT';
            if (file_exists($file))
            {
                $txt = file_get_contents($file);
                $ln = explode(chr(10), $txt);
                for ($r=0;$r < count($ln);$r++)
                    {
                        if (substr($ln[$r],0,2) == $meta)
                            {
                                $name = trim(substr($ln[$r],4,strlen($ln[$r])));
                                $rst[$name] = 1;
                            }
                    }
            } else {
                echo "OPS = ".$id.'<br>';
            }
            return($rst);
        }
}
?>
