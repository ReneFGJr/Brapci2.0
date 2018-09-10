<?php
class export extends CI_Model {

    function export_Article_Single($idx) {
        $dt = $this -> frbr_core -> le_data($idx);
        $file = 'c/' . $idx . '/name.nm';
        $file2 = 'c/' . $idx . '/name.oai';
        $file3 = 'c/' . $idx . '/name.ABNT';

        /************** zera dados ****/
        $sx = '';
        $aut = '';
        $aut2 = '';
        $tit = '';
        $sor = '';
        $nr = '';
        $vr = '';
        $ano = '';
        $sc = '';
        $link = '';
        $link_issue = '';
        $link_source = '';
        $link_work = '';
        $link_sc = '';
        $linka = '</a>';
        $txt = '';
        $pagi = '';
        $pagf = '';
        $rwork = 'TY - JOUR'.cr();
        $rwork = 'DB - BRAPCI'.cr();
        $rwork = 'UR - '.base_url(PATH.'v/'.$idx).cr();
        

        /************* recurepa dados ****/
        for ($q = 0; $q < count($dt); $q++) {
            $l = $dt[$q];
            $type = trim($l['c_class']);
            //echo $type.'=>'.$l['n_name'].'<hr>';
            //print_r($l);
            switch($type) {
                case 'hasPageStart':
                    $pagi = trim($l['n_name']);
                    break;
                case 'hasPageEnd':
                    $pagf = trim($l['n_name']);
                    break;                    
                case 'hasAbstract':
                    $rwork .= 'AB - '.troca($l['n_name'],chr(13),'').cr();
                    break;                
                case 'hasSubject':
                    $rwork .= 'KW - '.$l['n_name'].cr();
                    break;
                case 'hasSectionOf' :
                    $link_sc = $this -> frbr_core -> link($dt[$q]);
                    $sc = $dt[$q]['n_name'] . '</a>';
                    $rwork .= 'M3 - '.$dt[$q]['n_name'].cr();
                    break;
                case 'hasIssueOf' :
                    $issue = $l['d_r1'];
                    $link_issue = $this -> frbr_core -> link(array('d_r2' => $issue, 'c_class' => 'hasIssueOf'));
                    $di = $this -> frbr_core -> le_data($issue);
                    for ($y = 0; $y < count($di); $y++) {
                        $tq = trim($di[$y]['c_class']);
                        if ($tq == 'hasPublicationNumber') {
                            if (strlen($nr) > 0) {
                                $nr .= ', ';
                            }
                            $nr .= trim($di[$y]['n_name']);
                        }
                        if ($tq == 'hasPublicationVolume') {
                            if (strlen($nr) > 0) {
                                $vr .= ', ';
                            }
                            $vr .= trim($di[$y]['n_name']);
                        }
                        if ($tq == 'dateOfPublication') {
                            $ano = $di[$y]['n_name'];
                        }
                    }
                    $filex = 'c/'.$issue.'/name.rfe';
                    if (file_exists($filex))
                        {
                            $rwork .= load_file_local($filex);
                        }
                    break;
                case 'isPubishIn' :
                    if (strlen($sor) == 0) {
                        $link = $this -> frbr_core -> link($l);
                        $sor = $link . trim($l['n_name']) . '</a>';
                        $rwork .= 'T2 - '.$l['n_name'].cr();
                    }
                    break;
                case 'hasAuthor' :
                    if (strlen($aut) > 0) {
                        $aut .= '; ';
                        $aut2 .= '; ';
                    }
                    $link = $this -> frbr_core -> link($l);
                    $aut .= $l['n_name'];
                    $aut2 .= $link . $l['n_name'] . '</a>';
                    $rwork .= 'AU - '.$l['n_name'].cr();
                    break;
                case 'hasTitle' :
                    if (strlen($tit) == 0) {
                        $link = $this -> frbr_core -> link($l);
                        $tit = $link . $l['n_name'].'</a>';
                        $rwork .= 'TI - '.$l['n_name'].cr();
                    }
                    break;
            }
        }
        $pages = '';
        if (strlen($pagi.$pagf) > 0)
            {
                if (strlen($pagf) > 0)
                    {
                        $pages = ', p. '.$pagi.'-'.$pagf;
                    } else {
                        $pages = ', p. '.$pagi;
                    }
            }
        $txt = trim(trim($aut) . '. ' . $tit . '. <b>' . $sor . '</b>, ' . $nr . $vr . $pages .', ' . $ano . '.');
        $sx .= $txt;
        dircheck('c/' . $idx);
        if (strlen($txt) > 0) {
            /******************************/
            $f = fopen($file, 'w+');
            fwrite($f, $txt);
            fclose($f);
        }

        //echo '<pre>'.$rwork.'</pre>';
        //exit;

        $txt2 = $link_work . '<b>' . $tit . '</b></a><br>';
        $txt2 .= '<i>' . trim($aut2) . '</i><br>';
        $txt2 .= '' . $link_source . $sor . $linka . ', ';
        $txt2 .= $link_issue . $nr . $vr . $pages .', ' . $ano . $linka . '. (' . $sc . ')';

        dircheck('c/' . $idx);
        if (strlen($txt) > 0) {
            /******************************/
            $f = fopen($file2, 'w+');
            fwrite($f, $txt2);
            fclose($f);
        }

        if (strlen($rwork) > 0) {
            /******************************/
            $f = fopen($file3, 'w+');
            fwrite($f, $rwork);
            fclose($f);
        }
        return ($sx);
    }

    function export_Issue_Single($idx) {
        $dt = $this -> frbr_core -> le_data($idx);
        $file = 'c/' . $idx . '/name.nm';
        $file_sm = 'c/' . $idx . '/name.sm';
        $file2 = 'c/' . $idx . '/name.oai';
        $file3 = 'c/' . $idx . '/name.rfe';

        /************** zera dados ****/
        $sx = '';
        $aut = '';
        $aut2 = '';
        $tit = '';
        $sor = '';
        $num = '';
        $vol = '';
        $ano = '';
        $sc = '';
        $link = '';
        $link_issue = '';
        $link_source = '';
        $link_work = '';
        $link_sc = '';
        $linka = '</a>';
        $txt = '';
        $rwork = '';
        $txt_sm = '';

        /************* recurepa dados ****/
        for ($q = 0; $q < count($dt); $q++) {
            $l = $dt[$q];
            $type = trim($l['c_class']);
            //echo $type.'=>'.$l['n_name'].'<hr>';
            switch($type) {
                case 'hasIssue':
                    //$rwork .= 'SO - '.troca($l['n_name'],chr(13),'').cr();
                    $sor = $l['n_name'];
                    break; 
                case 'dateOfPublication':
                    if (strlen($ano) == 0)
                        {
                            $ano = $l['n_name'];
                            $rwork .= 'PY - '.troca($l['n_name'],chr(13),'').cr();
                        }
                    break;                                   
                case 'hasPublicationVolume':
                       $rwork .= 'VL - '.troca($l['n_name'],'v. ','').cr();    
                       $vol = $l['n_name'];
                       break;    
                case 'hasPublicationNumber':
                        $rwork .= 'IS - '.troca($l['n_name'],'n. ','').cr();
                        if (strlen($num) == 0)
                            {
                                $num = $l['n_name'];
                            }
                    break;                                   
            }
        }
        $txt = '<b>'.$sor.'</b>, ';
        if (strlen($num) > 0)
            {
                $txt .= $num.', ';
                $txt_sm .= $num.', ';
            }
        if (strlen($vol) > 0)
            {
                $txt .= $vol.', ';
                $txt_sm .= $vol.'';
            }
        if (strlen($ano) > 0)
            {
                $txt .= $ano;
                $txt_sm .= '<br>'.$ano;
            }                    
        $txt .= '.';
        $txt = troca($txt,', .','.');
                        
        dircheck('c/' . $idx);
        if (strlen($txt) > 0) {
            /******************************/
            $f = fopen($file, 'w+');
            fwrite($f, $txt);
            fclose($f);
        }
        if (strlen($txt) > 0) {
            /******************************/
            $f = fopen($file_sm, 'w+');
            fwrite($f, $txt_sm.'#'.$ano);
            fclose($f);
        }        
        if (strlen($rwork) > 0) {
            /******************************/
            $f = fopen($file3, 'w+');
            fwrite($f, $rwork);
            fclose($f);
        }        
        return ($txt);
    }

    function export_Issue($pg = 0) {
        $class = 'Issue';
        $sz = 50;
        $f = $this -> frbr_core -> find_class($class);
        $sql = "select id_cc, cc_use
                        FROM rdf_concept 
                        where cc_class = " . $f . " 
                        ORDER BY id_cc
                        LIMIT $sz OFFSET " . ($pg * $sz) . "    ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        dircheck('c');
        $sx = '<ul>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idx = $line['id_cc'];
            /*************************** EXPORTAR ****************/
            $sx .= '<li>' . $this -> export_Issue_Single($idx) . '</li>' . cr();
            /*****************************************************/
        }
        $sx .= '</ul>' . cr();

        if (count($rlt) > 0) {
            $sx .= '<meta http-equiv="refresh" content="1;' . base_url(PATH . 'export/issue/' . ($pg + 1)) . '">';
        }
        $sx = '<ul>' . $sx . '</ul>';
        return ($sx);

    }

    function export_Article($pg = 0) {
        $class = 'Article';
        $sz = 50;
        $f = $this -> frbr_core -> find_class($class);
        $sql = "select id_cc, cc_use
                        FROM rdf_concept 
                        where cc_class = " . $f . " 
                        ORDER BY id_cc
                        LIMIT $sz OFFSET " . ($pg * $sz) . "    ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        dircheck('c');
        $sx = '<ul>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idx = $line['id_cc'];
            /*************************** EXPORTAR ****************/
            $sx .= '<li>' . $this -> export_Article_Single($idx) . '</li>' . cr();
            /*****************************************************/
        }
        $sx .= '</ul>' . cr();

        if (count($rlt) > 0) {
            $sx .= '<meta http-equiv="refresh" content="1;' . base_url(PATH . 'export/article/' . ($pg + 1)) . '">';
        }
        $sx = '<ul>' . $sx . '</ul>';
        return ($sx);

    }

    function export_subject_reverse($pg = 0) {
        $this -> load -> model('searchs');
        $class = 'Subject';
        $f1 = $this -> frbr_core -> find_class('Subject');
        $f2 = $this -> frbr_core -> find_class('Word');

        $sz = 5000000000;
        $P1 = $this -> frbr_core -> find_class('prefLabel');
        $P2 = $this -> frbr_core -> find_class('altLabel');
        $P3 = $this -> frbr_core -> find_class('hiddenLabel');

        $sql = "SELECT trim(n_name) as n_name, id_cc, n_lang, cc_use, d1.d_r1 as d_r1
                    FROM rdf_data as d1
                    INNER JOIN rdf_name ON d_literal = id_n 
                    INNER JOIN rdf_concept on d_r1 = id_cc 
                    where ((cc_class = " . $f1 . ") or (cc_class = " . $f2 . ")) 
                        AND ((d1.d_p) = $P1 or (d1.d_p = $P2) or (d1.d_p = $P3))
                        
                    ORDER BY n_name 
                        LIMIT $sz OFFSET " . ($pg * $sz) . "";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $tx = '';
        $to = '';
        $tt = '';
        $ti = 0;
        $sx = '';
        $st = '';
        $i = 0;
        $dir = 'c';
        dircheck($dir);

        for ($r = 0; $r < count($rlt); $r++) {
            /************************** TERM */
            $line = $rlt[$r];
            $term = $line['n_name'];
            $term = lowercasesql($term);
            $term = convert($term);
            $tr = '[' . trim($term) . ']';

            /* ID do conceito / termo */
            $id = $line['id_cc'];

            /* termo diferente */
            if ($tx != $tr) {
                /*******************************************/
                if ($ti > 0) {
                    $ss = $tx . $tt;
                    $sx .= $ss . '¢';
                    $i++;
                    $st .= ($i) . '. ' . $ss . cr();
                }
                $tx = $tr;
                $tt = '';
                $ti = 0;
            }

            $ti++;
            if (strlen($tt) > 0) { $tt .= ';';
            }
            $tt .= $id;
        }
        if ($ti > 0) {
            $ss = $tx . $tt;
			$st .= ($i) . '. ' . $ss . cr();
            $sx .= $ss . '¢';
        }
        dircheck('c');
        $fl = 'c/search_subject.search';
        $f = fopen($fl, 'w+');
        fwrite($f, $sx);
        fclose($f);
        $mss = '<h1>Export Reverse Index</h1><br><br>
                <div class="alert alert-success" role="alert">
                  Success! Export Reverse Index
                </div>';
        return ('<div class="col-12">' . $mss . '<pre>' . $st . '</pre></div>');
    }

    function export_subject($pg = 0) {
        $sz = 15000000  ;
        $offset = $pg * $sz;
        $f1 = $this -> frbr_core -> find_class('Subject');
        $f2 = $this -> frbr_core -> find_class('Word');
        $sql = "select distinct n_name, id_cc, n_lang, cc_use, d_r1
                        FROM rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        INNER JOIN rdf_data ON (id_cc = d_r2) AND (d_r2 > 0)
                        where (cc_class = " . $f1 . " or cc_class = " . $f2 . ")
                        
                        ORDER BY id_cc
                        LIMIT $sz OFFSET $offset
                        ";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        dircheck('c');
        $tt = '';
        $idc = 0;
        $art = array();
        $name = '';
        $txt = '';

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $tx = trim($line['id_cc']);

            if ($tt != $tx) {
                if ((count($art) > 0) and ($idc > 0)) {
                    $txt .= $name . ' (' . count($art) . ') - ' . $idc;
                    $txt .= $this -> save_file_index($idc, $art) . cr();
                }
                $art = array();
                $idc = $line['id_cc'];
                $name = $line['n_name'];
                $tt = $tx;
            }
            array_push($art, $line['d_r1']);
        }
        if ((count($art) > 0) and ($idc > 0)) {
            $txt .= $name . ' (' . count($art) . ') - ' . $idc;
            $txt .= $this -> save_file_index($idc, $art) . cr();
        }
        if (count($rlt) > 0) {
            $txt .= '<meta http-equiv="refresh" content="3;' . base_url(PATH . 'export/subject/' . ($pg + 1)) . '">';
        }

        $sx = '<pre>' . $txt . '</pre>';
        return ($sx);
    }

    function save_file_index($id, $c) {
        $txt = '';
        for ($r = 0; $r < count($c); $r++) {
            $txt .= $c[$r] . ';';
        }

        $file = 'c/' . $id . '/works.nm';
        dircheck('c/' . $id);
        /******************************/
        $f = fopen($file, 'w+');
        fwrite($f, $txt);
        fclose($f);

        if (strlen($txt) > 0) {
            $sx = ' <font color="green">Saved</font>';
        } else {
            $sx = ' <font color="red">Not saved</font>';
        }
        return ($sx);
    }

}
?>
