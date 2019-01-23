<?php
class export extends CI_Model {

    function export_Article_Single($idx) {
        $dt = $this -> frbr_core -> le_data($idx);

        $file = 'c/' . $idx . '/name.nm';
        $file2 = 'c/' . $idx . '/name.oai';
        $file3 = 'c/' . $idx . '/name.ABNT';
        $file_dc = 'c/' . $idx . '/name.dc';
        $file_xls = 'c/' . $idx . '/name.xls';

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
        $rwork = 'TY - JOUR' . cr();
        $rwork = 'DB - BRAPCI' . cr();
        $rwork = 'UR - ' . base_url(PATH . 'v/' . $idx) . cr();
        $subj = '';
        $source = '';
        $title = '';
        $abstract = '';
        /* Doblin Core */
        $dc = '';

        /************* recurepa dados ****/
        for ($q = 0; $q < count($dt); $q++) {
            $l = $dt[$q];
            $type = trim($l['c_class']);
            //echo $type.'=>'.$l['n_name'].'<hr>';
            //print_r($l);
            switch($type) {
                case 'hasPageStart' :
                    $pagi = trim($l['n_name']);
                    break;
                case 'hasPageEnd' :
                    $pagf = trim($l['n_name']);
                    break;
                case 'hasAbstract' :
                    $rwork .= 'AB - ' . troca($l['n_name'], chr(13), '') . cr();
                    $dc .= '<meta name="DC.Description"  xml:lang="' . troca($l['n_lang'], chr(13), '') . '" content="' . troca($l['n_name'], chr(13), '') . '"/>' . cr();
                    $abstract .= troca($l['n_name'], chr(13), '').'@'.troca($l['n_lang'], chr(13), '').cr();
                    break;
                case 'hasSubject' :
                    $rwork .= 'KW - ' . $l['n_name'] . cr();
                    $dc .= '<meta name="DC.Subject" xml:lang="' . $l['n_lang'] . '" content="' . $l['n_name'] . '"/>' . cr();
                    $dc .= '<meta name="citation_keywords" xml:lang="' . $l['n_lang'] . '" content="' . $l['n_name'] . '"/>' . cr();
                    if (strlen($subj) > 0)
                        { $subj .= '; '; }
                    $subj .= trim($l['n_name']);
                    break;
                case 'hasSectionOf' :
                    $link_sc = $this -> frbr_core -> link($dt[$q]);
                    $sc = $dt[$q]['n_name'] . '</a>';
                    $rwork .= 'M3 - ' . $dt[$q]['n_name'] . cr();
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
                    $filex = 'c/' . $issue . '/name.rfe';
                    if (file_exists($filex)) {
                        $rwork .= load_file_local($filex);
                    }
                    break;
                case 'isPubishIn' :
                    if (strlen($sor) == 0) {
                        $link = $this -> frbr_core -> link($l);
                        $sor = $link . trim($l['n_name']) . '</a>';
                        $source = trim($l['n_name']);
                        $rwork .= 'T2 - ' . $l['n_name'] . cr();
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
                    $rwork .= 'AU - ' . $l['n_name'] . cr();
                    $dc .= '<meta name="DC.Creator.PersonalName" content="' . $l['n_name'] . '"/>' . cr();
                    break;
                case 'hasTitle' :
                    if (strlen($tit) == 0) {
                        $link = $this -> frbr_core -> link($l);
                        $tit = $link . $l['n_name'] . '</a>';
                        if (strlen($title) > 0) { $title .= cr(); }
                        $title .= $l['n_name'].'@'.$l['n_lang'];
                        $rwork .= 'TI - ' . $l['n_name'] . cr();
                    }
                    break;
            }
        }
        $pages = '';
        if (strlen($pagi . $pagf) > 0) {
            if (strlen($pagf) > 0) {
                $pages = ', p. ' . $pagi . '-' . $pagf;
            } else {
                $pages = ', p. ' . $pagi;
            }
        }
        $txt = trim(trim($aut) . '. ' . $tit . '. <b>' . $sor . '</b>, ' . $nr . $vr . $pages . ', ' . $ano . '.');
        $sx .= $txt;
        dircheck('c/' . $idx);
        if (strlen($txt) > 0) {
            /******************************/
            $f = fopen($file, 'w+');
            fwrite($f, $txt);
            fclose($f);
        }

        /************************************************************************************/
        /* DOBLIN CORE **********************************************************************/

        //echo '<pre>'.$rwork.'</pre>';
        //exit;

        $txt2 = $link_work . '<b>' . $tit . '</b></a><br>';
        $txt2 .= '<i>' . trim($aut2) . '</i><br>';
        $txt2 .= '' . $link_source . $sor . $linka . ', ';
        $txt2 .= $link_issue . $nr . $vr . $pages . ', ' . $ano . $linka . '. (' . $sc . ')';
        
        $txt3 = '<tr>';
        $txt3 .= '<td>'.strip_tags($aut2).'</td>';
        $txt3 .= '<td>'.$title.'</td>';
        $txt3 .= '<td>'.$source.'</td>';
        $txt3 .= '<td>'.$nr.$vr.$pages.'</td>';
        $txt3 .= '<td>'.$ano.'</td>';
        $txt3 .= '<td>'.$sc.'</td>';
        $txt3 .= '<td>'.$subj.'</td>';
        $txt3 .= '<td>'.$abstract.'</td>';
        $txt3 .= '<td>'.$idx.'</td>';
        $txt3 .= '<td>'.base_url(PATH.'v/'.$idx).'</td>';

        dircheck('c/' . $idx);
        if (strlen($txt) > 0) {
            /******************************/
            $f = fopen($file2, 'w+');
            fwrite($f, $txt2);
            fclose($f);
        }

        if (strlen($txt3) > 0) {
            /******************************/
            $f = fopen($file_xls, 'w+');
            fwrite($f, $txt3);
            fclose($f);
        }

        if (strlen($rwork) > 0) {
            /******************************/
            $f = fopen($file3, 'w+');
            fwrite($f, $rwork);
            fclose($f);
        }

        if (strlen($dc) > 0) {
            /******************************/
            $f = fopen($file_dc, 'w+');
            fwrite($f, $dc);
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
                case 'hasIssue' :
                    //$rwork .= 'SO - '.troca($l['n_name'],chr(13),'').cr();
                    $sor = $l['n_name'];
                    break;
                case 'dateOfPublication' :
                    if (strlen($ano) == 0) {
                        $ano = $l['n_name'];
                        $rwork .= 'PY - ' . troca($l['n_name'], chr(13), '') . cr();
                    }
                    break;
                case 'hasPublicationVolume' :
                    $rwork .= 'VL - ' . troca($l['n_name'], 'v. ', '') . cr();
                    $vol = $l['n_name'];
                    break;
                case 'hasPublicationNumber' :
                    $rwork .= 'IS - ' . troca($l['n_name'], 'n. ', '') . cr();
                    if (strlen($num) == 0) {
                        $num = $l['n_name'];
                    }
                    break;
            }
        }
        $txt = '<b>' . $sor . '</b>, ';
        if (strlen($num) > 0) {
            $txt .= $num . ', ';
            $txt_sm .= $num . ', ';
        }
        if (strlen($vol) > 0) {
            $txt .= $vol . ', ';
            $txt_sm .= $vol . '';
        }
        if (strlen($ano) > 0) {
            $txt .= $ano;
            $txt_sm .= '<br>' . $ano;
        }
        $txt .= '.';
        $txt = troca($txt, ', .', '.');

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
            fwrite($f, $txt_sm . '#' . $ano);
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
        if ($pg == 0) {
            $this -> utf8_check();
        }
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
            $sx .= $this -> elasticsearch -> update($idx);

            /*****************************************************/
        }
        $sx .= '</ul>' . cr();

        if (count($rlt) > 0) {
            $sx .= '<meta http-equiv="refresh" content="2;' . base_url(PATH . 'export/article/' . ($pg + 1)) . '">';
        }
        $sx = '<ul>' . $sx . '</ul>';
        return ($sx);

    }

    function export_author_index_list($lt = 0, $class = 'Person') {
        $nouse = 0;
        $dir = 'application/views';
        dircheck($dir);
        $dir = 'application/views/brapci';
        dircheck($dir);
        $dir = 'application/views/brapci/index';
        dircheck($dir);
        $sx = '';
        if (($lt >= 65) and ($lt <= 90)) {
            $ltx = chr(round($lt));
            $txt = $this -> frbr_core -> index_list_style_2($ltx, 'Person', 0);
            $file = $dir . '/authors_' . $ltx . '.php';
            $hdl = fopen($file, 'w+');
            fwrite($hdl, $txt);
            fclose($hdl);
            $sx .= bs_alert('success', msg('Export_author') . ' #' . $ltx . '<br>');
            $sx .= '<meta http-equiv="refresh" content="3;' . base_url(PATH . 'export/index_authors/' . ($lt + 1)) . '">';
        }
        return ($sx);
    }

    function export_subject_index_list($lt = 0, $class = 'Person') {
        $nouse = 0;
        $dir = 'application/views';
        dircheck($dir);
        $dir = 'application/views/brapci';
        dircheck($dir);
        $dir = 'application/views/brapci/index';
        dircheck($dir);
        $sx = '';
        if (($lt >= 65) and ($lt <= 90)) {
            $ltx = chr(round($lt));
            $txt = $this -> frbr_core -> index_list_style_2($ltx, 'Subject', 0);
            $file = $dir . '/subject_' . $ltx . '.php';
            $hdl = fopen($file, 'w+');
            fwrite($hdl, $txt);
            fclose($hdl);
            $sx .= bs_alert('success', msg('Export_subject') . ' #' . $ltx . ' - ' . $file . '<br>');
            $sx .= '<meta http-equiv="refresh" content="3;' . base_url(PATH . 'export/subject/' . ($lt + 1)) . '">';
        }
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
        $sz = 15000000;
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

    function utf8_check() {
        $chk = array('Ã©', 'Ã§', 'Ã£', 'Ã³', 'Ãª', 'Ã¡');
        $wh = '';
        for ($r = 0; $r < count($chk); $r++) {
            if (strlen($wh) > 0) {
                $wh .= ' OR ';
            }
            $wh .= " (n_name like '%" . $chk[$r] . "%') ";
        }
        $sql = "select * from rdf_name where $wh limit 2000";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $l = $line['n_name'];
            $id = $line['id_n'];

            $ln = utf8_decode($l);

            $sql = "update rdf_name set n_name = '" . $ln . "' where id_n = " . $id;
            $rrr = $this -> db -> query($sql);

            echo '.';
        }
    }

    function collections_form() {
        $class = 'Collection';
        $f = $this -> frbr_core -> find_class($class);
        $sql = "select *
                        FROM rdf_concept
                        INNER JOIN rdf_name ON cc_pref_term = id_n 
                        where cc_class = " . $f . " 
                        ORDER BY id_cc
                        ";

        $f1 = $this -> frbr_core -> find_class('Collection');
        $f2 = $this -> frbr_core -> find_class('Journal');

        $sz = 5000000000;
        $P1 = $this -> frbr_core -> find_class('prefLabel');
        $P2 = $this -> frbr_core -> find_class('altLabel');
        $P3 = $this -> frbr_core -> find_class('hiddenLabel');

        $sql = "SELECT  trim(N1.n_name) as n_name, 
                        trim(N2.n_name) as n_name_2,
                        id_cc, N1.n_lang, cc_use, d1.d_r1 as d_r1
                    FROM rdf_data as d1
                    INNER JOIN rdf_name as N1 ON d_literal = N1.id_n 
                    INNER JOIN rdf_concept on d_r1 = id_cc
                    INNER JOIN rdf_name as N2 ON d_literal = N2.id_n 
                    where ((cc_class = " . $f1 . ") or (cc_class = " . $f2 . ")) 
                        AND ((d1.d_p) = $P1 or (d1.d_p = $P2) or (d1.d_p = $P3))
                    
                    ORDER BY N1.n_name";

        $sql = "select  N1.n_name as n_name_1,
                        N2.n_name as n_name_2,
                        C1.id_cc as id_cc_1,
                        C2.id_cc as id_cc_2  
                        FROM rdf_concept as C1                         
                        INNER JOIN rdf_data ON d_r2 = C1.id_cc                        
                        INNER JOIN rdf_concept as C2 ON d_r1 = C2.id_cc
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                        INNER JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n 
                        where C1.cc_class = " . $f . " 
                        ORDER BY n_name_1, n_name_2
                        ";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $sx = '<form method="post">'.cr();
        $sx .= '<ul style="list-style-type: none;">'.cr();
        $n = '';
        $sel = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $ic1 = $line['id_cc_1'];
            $ic2 = $line['id_cc_2'];
            /******************** Header *************/
            $nx = $line['n_name_1'];
            if ($nx != $n) {

                $sx .= '<h4>';
                $dt = 'name = "c' . $ic1 . '" id="c' . $ic1 . '" ';
                if ((isset($_SESSION['c' . $ic1])) and ($_SESSION['c' . $ic1] == '1')) {
                    $dt .= ' checked';
                }
                $sx .= '<input type="checkbox" ' . $dt . ' onchange="fcn'.$ic1.'(\'#c' . $ic1 . '\');"> ';
                $sx .= $line['n_name_1'];
                $sx .= '</h4>' . cr();
                $n = $nx;
            }
            $sx .= '<li style="margin-left: 20px;">';
            $dt = 'name = "a' . $ic2 . '" id="c' . $ic2 . '" ';
            if ((isset($_SESSION['c' . $ic2])) and ($_SESSION['c' . $ic2] == '1')) {
                  $dt .= ' checked';
            }
            //$sx .= '<input type="checkbox" ' . $dt . '> ';
            $sx .= $line['n_name_2'];
            $sx .= '</li>'.cr();

            if (!isset($sel[$ic1])) {
                $sel[$ic1] = array();
            }
            array_push($sel[$ic1], $ic2);
        }
        $sx .= '</ul>'.cr();
        $sx .= '<input type="submit" value="' . msg('submit') . '" class="btn btn-outline-primary">'.cr();
        $sx .= '</form>'.cr();

        /* JAVA */
        $sx .= cr().'<script>' . cr();
        foreach ($sel as $key => $value) {
            $sx .= 'function fcn'.$key.'($id) {'.cr();
            $v = '';
            for ($rx=0;$rx < count($value);$rx++)
                {
                    $d = $value[$rx];
                    $v .= $d.';';
                }
            $sx .= 'alert("'.$v.'");';                
            $sx .= '}'.cr();            
        }
        $sx .= '</script>' . cr();
        return ($sx);
    }

}
?>
