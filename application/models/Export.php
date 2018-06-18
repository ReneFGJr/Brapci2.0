<?php
class export extends CI_Model {

    function export_Article($pg = 0) {
        $class = 'Article';
        $sz = 10;
        $f = $this -> frbr_core -> find_class($class);
        $sql = "select n_name, id_cc, n_lang, cc_use
                        FROM rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        where cc_class = " . $f . " and n_lang = 'pt-BR'
                        ORDER BY id_cc
                        LIMIT $sz OFFSET " . ($pg * $sz) . "    ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        dircheck('c');
        $sx = '';

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $file = 'c/' . $line['id_cc'] . '/name.nm';
            $file2 = 'c/' . $line['id_cc'] . '/name.oai';
            $txt = trim($line['n_name']);
            $idx = trim($line['id_cc']);

            $dt = $this -> frbr_core -> le_data($idx);

            $aut = '';
            $aut2 = '';
            $tit = '';
            $sor = '';
            $vnr = '';
            $ano = '';
            $sc = '';
            $link = '';
            $link_issue = '';
            $link_source = '';
            $link_sc = '';
            $linka = '</a>';
            for ($q = 0; $q < count($dt); $q++) {
                $l = $dt[$q];
                $type = trim($l['c_class']);
                switch($type) {
                    case 'hasSectionOf':
                        $link_sc = $this->frbr_core->link($dt[$q]);                    
                        $sc = $dt[$q]['n_name'].'</a>';
                        break;
                    case 'hasIssueOf' :
                        $issue = $l['d_r1'];
                        $link_issue = $this->frbr_core->link(array('d_r2'=>$issue,'c_class'=>'hasIssueOf'));
                        $di = $this -> frbr_core -> le_data($issue);
                        for ($y = 0; $y < count($di); $y++) {
                            $tq = trim($di[$y]['c_class']);
                            if ($tq == 'hasVolumeNumber') {
                                if (strlen($vnr) > 0) {
                                    $vnr .= ', ';
                                }
                                $vnr .= trim($di[$y]['n_name']);
                            }
                            if ($tq == 'dateOfPublication') {                                
                                $ano = $di[$y]['n_name'];
                            }
                        }
                        break;
                    case 'isPubishIn' :
                        if (strlen($sor) == 0) {
                            $link_source = $this->frbr_core->link($l);
                            $sor = $link.trim($l['n_name']);
                        }
                        break;
                    case 'hasAuthor' :
                        if (strlen($aut) > 0) {
                            $aut .= '; ';
                            $aut2 .= '; ';
                        }
                        $link = $this->frbr_core->link($l);
                        $aut .= $l['n_name'];
                        $aut2 .= $link.$l['n_name'].'</a>';
                        break;
                    case 'hasTitle' :
                        
                        if (strlen($tit) == 0) {
                             $link_work = $this->frbr_core->link($l);
                             $tit = $link.$l['n_name'];
                        }
                        break;
                }
            }
            $txt = trim(trim($aut) . '. ' . $tit . '. <b>' . $sor . '</b>, ' . $vnr .', ' . $ano . '.');
            $sx .= '<li><tt>' . $txt . '</tt></li>';
            dircheck('c/' . $idx);
            if (strlen($txt) > 0) {
                /******************************/
                $f = fopen($file, 'w+');
                fwrite($f, $txt);
                fclose($f);
            }
            
            $txt2 = $link_work.'<b>'.$tit.'</b></a><br>';
            $txt2 .= '<i>'.trim($aut2) . '</i><br>';
            $txt2 .= '' . $link_source.$sor .$linka . ', ';
            $txt2 .= $link_issue. $vnr . ', ' . $ano . $linka.'. ('.$sc.')';            
            $sx .= '<li><tt>' . $txt2 . '</tt></li>';
            
            dircheck('c/' . $idx);
            if (strlen($txt) > 0) {
                /******************************/
                $f = fopen($file2, 'w+');
                fwrite($f, $txt2);
                fclose($f);
            }


        }
        if (count($rlt) > 0) {
            $sx .= '<meta http-equiv="refresh" content="1;' . base_url(PATH . 'export/article/' . ($pg + 1)) . '">';
        }

        $sx = '<ul>' . $sx . '</ul>';
        return ($sx);

    }

    function export_subject_reverse($pg = 0) {
        $this->load->model('searchs');
        $class = 'Subject';
        $sz = 5000000000;
        $f = $this -> frbr_core -> find_class($class);
        $sql = "select n_name, id_cc, n_lang, cc_use, d_r1, d_r2
                        FROM rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        INNER JOIN rdf_data ON id_cc = d_r2
                        where cc_class = " . $f . " and n_lang = 'pt-BR'
                        ORDER BY n_name
                        LIMIT $sz OFFSET " . ($pg * $sz) . "    ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $tx = '';
        $to = '';
        $tt = '';
        $ti = 0;
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $term = $line['n_name'];
            $term = lowercasesql($term);
            $to = $this->searchs->convert($term);
            if ($to != $term)
                {
                    //echo '<br>'.$term.'=>'.$to;
                    $term = $to;
                }
            $tr = '['.$term.']';
            $id = $line['d_r1'];
            if ($tx != $tr) {
                /*******************************************/
                if ($ti > 0) {
                    $ss = $tx . $tt;
                    $sx .= $ss.'¢';
                }
                $tx = $tr;
                $tt = '';
                $ti = 0;
            }

            $ti++;
            if (strlen($tt) > 0) { $tt .= ';'; }
            $tt .= $id;
        }
        if ($ti > 0) {
            $ss = $tx . $tt;
            $sx .= $ss.'¢';
        }
        dircheck('c');
        $fl = 'c/search_subject.search';
        $f = fopen($fl,'w+');
        fwrite($f,$sx);
        fclose($f);
        $mss = '<h1>Export Reverse Index</h1><br><br>
                <div class="alert alert-success" role="alert">
                  Success! Export Reverse Index
                </div>';     
        return('<div class="col-12">'.$mss.'<pre>'.$sx.'</pre></div>');
    }

    function export_subject($pg = 0) {
        $class = 'Subject';
        $sz = 100;
        $f = $this -> frbr_core -> find_class($class);
        $sql = "select n_name, id_cc, n_lang, cc_use
                        FROM rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        where cc_class = " . $f . " and n_lang = 'pt-BR'
                        ORDER BY id_cc
                        LIMIT $sz OFFSET " . ($pg * $sz) . "    ";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        dircheck('c');
        $tt = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $file = 'c/' . $line['id_cc'] . '/name.nm';
            $txt = trim($line['n_name']);
            $idx = trim($line['id_cc']);
            dircheck('c/' . $idx);
            if ((!file_exists($file)) and (strlen($txt) > 0)) {
                /******************************/
                $f = fopen($file, 'w+');
                fwrite($f, $txt);
                fclose($f);
                $tt .= $line['id_cc'] . ':' . $txt . cr();
            }
        }
        $sx = '';
        if (count($rlt) > 0) {
            $sx .= '<meta http-equiv="refresh" content="30;' . base_url(PATH . 'export/' . ($pg + 1)) . '">';
        }

        $sx .= '<pre>' . $tt . '</pre>';
        return ($sx);
    }

}
?>
