<?php
class frbr_core extends CI_model {
    var $limit = 20;

    function transfRemissive($id, $idp) {
        $prop = $this -> find_class("altLabel");
        $sql = "update rdf_data set d_r1 = $idp where d_r1 = $id and d_p = $prop";
        $this -> db -> query($sql);

        return (1);
    }

    function equivalentClass($id, $idp) {
        $prop = 'equivalentClass';
        $this -> frbr_core -> set_propriety($id, $prop, $idp, 0);

        /* Atualiza remissiva */
        $sql = "update rdf_concept set cc_use = $idp where id_cc = $id";
        $rlt = $this -> db -> query($sql);

        /* Transfere todas as remissivas e relações para o termo principal */
        $sql = "update ????";
        return (1);
    }

    function prefTerm_chage($c = '', $t1 = '') {
        $sql = "select * from rdf_concept
                    INNER JOIN rdf_name on id_n = cc_pref_term  
                    where id_cc = $c ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 1) {
            $line = $rlt[0];
            if ($line['cc_pref_term'] != $t1) {
                $prop = $this -> frbr_core -> find_class("prefLabel");
                $lit = $line['cc_pref_term'];
                /****************** UPDATE *******************/
                $sql = "update rdf_data set d_literal = $t1 where d_p = $prop AND d_r1 = " . $c;
                $rrr = $this -> db -> query($sql);

                $sql = "update rdf_concept set cc_pref_term = $t1 where id_cc = " . $c;
                $rrr = $this -> db -> query($sql);

                /****************** SET HIDDEN ***************/
                $prop = 'altLabel';
                $this -> frbr_core -> set_propriety($c, $prop, 0, $lit);
                return (1);
            } else {
                return (0);
            }
        }
        return (0);
    }

    function link($line, $tp = 1) {
        $id = $line['d_r2'];
        if ($id == 0) {
            $id = $line['d_r1'];
        }
        $link = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="' . $line['c_class'] . '">';
        return ($link);
    }

    function prefTerm($data) {
        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            $prop = $line['c_class'];
            if ($prop == 'prefLabel') {
                $name = trim($line['n_name']) . '@' . trim($line['n_lang']);
                return ($name);
            }
        }
        return ("");
    }

    function find($n, $prop = '', $equal = 1) {
        /* EQUAL */
        $wh = "(n_name like '%" . $n . "%')";
        if ($equal == 1) {
            $wh = "(n_name = '" . $n . "')";
        } else {
            if (perfil("#ADM")) {
                echo "** ALERT - use like in " . $n . ' ********<br>';
            }
        }

        /* PROPRIETY */
        if (strlen($prop) > 0) {
            $class = $this -> find_class($prop);
            $wh .= "and ((d_p = $class) or (cc_class = $class))";
        } else {
            $wh .= '';
        }

        $sql = "select d_r1, c_class, d_r2, n_name from rdf_name
                        INNER JOIN rdf_data on d_literal = id_n 
                        INNER JOIN rdf_class ON d_p = id_c
                        INNER JOIN rdf_concept ON id_cc = d_r1
                        where $wh";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line['d_r1']);
        }
        return (0);
    }

    function language($lang) {
        switch($lang) {
            case 'por' :
                $lang = 'pt-BR';
                break;
            case 'pt_BR' :
                $lang = 'pt-BR';
                break;
            case 'eng' :
                $lang = 'en';
                break;
            case 'en-US' :
                $lang = 'en';
                break;
        }
        return ($lang);
    }

    function check_language() {
        $sql = "update rdf_name set n_lang = 'pt-BR' where n_lang = 'pt_BR'";
        $rlt = $this -> db -> query($sql);
    }

    function index_list($lt = '', $class = 'Person', $nouse = 0) {
        $f = $this -> find_class($class);
        $this -> check_language();
        $wh = '';
        if ($nouse == 1) {
            $wh .= " and C1.cc_use = 0 ";
        }

        $sql = "select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                       N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        where C1.cc_class = " . $f . " $wh 
                        ORDER BY N1.n_name";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<div class="col"><div class="col-12">';
        $sx .= '<h5>' . msg('total_subject') . ' ' . number_format(count($rlt), 0, ',', '.') . ' ' . msg('registers') . '</h5>';
        $sx .= '<ul>';
        $l = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idx = $line['id_cc'];
            $name_use = trim($line['n_name']);

            $filex = 'c/' . $idx . '/name.nm';
            if (file_exists($filex)) {
                $name_use = load_file_local($filex);
            }

            $link = '<a href="' . base_url(PATH . 'v/' . $line['id_cc']) . '">';
            $linka = '</a>';
            if ($line['id_cc_use'] > 0) {
                $link = '';
                $linka = '';
                $x2 = ucase($line['n_name_use']);
                $link_use = '<a href="' . base_url(PATH . 'v/' . $line['id_cc_use']) . '">';
                $name_use = ' <i>use</i> ' . $link_use . $x2 . '</a>';
            }

            if ($line['id_cc_use'] == 0) {
                $xl = substr(UpperCaseSql(strip_tags($name_use)), 0, 1);
                if ($xl != $l) {
                    $sx .= '<h4>' . $xl . '</h4>';
                    $l = $xl;
                }
                $name = $link . $name_use . $linka . ' <sup style="font-size: 70%;">(' . $line['n_lang'] . ')</sup>';
                $sx .= '<li>' . $name . '</li>' . cr();
            }
        }
        $sx .= '</div></div>';
        return ($sx);
    }

    function index_list_2($lt = '', $class = 'Person', $nouse = 0) {
        $dir = 'application/views/brapci/index';
        $sx = '';
        if (strlen($lt) == 0) {
            $lt = 'A';
        }
        if ($class == "Person") {
            $file = $dir . '/authors_' . $lt . '.php';
            $file2 = 'brapci/index/authors_' . $lt;
            if (file_exists($file)) {
                $sx .= $this -> load -> view($file2, null, True);
            } else {
                $sx .= bs_alert('danger', "Index not found " . $file);
            }
        }
        if ($class == "Subject") {
            $file = $dir . '/subject_' . $lt . '.php';
            $file2 = 'brapci/index/subject_' . $lt;
            if (file_exists($file)) {
                $sx .= $this -> load -> view($file2, null, True);
            } else {
                $sx .= bs_alert('danger', "Index not found " . $file);
            }
        }
        return ($sx);
    }

    function index_list_style_2($lt = 'G', $class = 'Person', $nouse = 0) {
        $f = $this -> find_class($class);
        $this -> check_language();
        $wh = '';
        if ($nouse == 1) {
            $wh .= " and C1.cc_use = 0 ";
        }
        if (strlen($lt) > 0) {
            $wh .= " and (N1.n_name like '$lt%') ";
        }

        $sql = "select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                       N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        where C1.cc_class = " . $f . " $wh  and C1.cc_use = 0                        
                        ORDER BY N1.n_name";

        $rlt = $this -> db -> query($sql);
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
        $sx .= '<div class="row"><div class="col-md-12">';
        $sx .= '<b>' . msg('total_subject') . ' ' . number_format(count($rlt), 0, ',', '.') . ' ' . msg('registers') . '</b>';
        $sx .= '</div></div>';
        return ($sx);
    }

    function index_list_3($lt = 'G', $class = 'Person', $nouse = 0) {
        $f = $this -> find_class($class);
        $this -> check_language();
        $wh = '';
        if ($nouse == 1) {
            $wh .= " and C1.cc_use = 0 ";
        }
        if (strlen($lt) > 0) {
            $wh .= " and (N1.n_name like '$lt%') ";
        }

        $sql = "select length(trim(N1.n_name)) as sz,
                        N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                       N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        where C1.cc_class = " . $f . " $wh  and C1.cc_use = 0                        
                        ORDER BY sz desc";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $l = '';
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idx = $line['id_cc'];
            $name_use = trim($line['n_name']);
            $name_use = troca($name_use,'"','');
            $name_use = troca($name_use,'[','');
            $name_use = troca($name_use,']','');
            $name_use = troca($name_use,'(','');
            $name_use = troca($name_use,')','');
            $name_use = troca($name_use,'|','');
            $name_use = troca($name_use,'{','');
            $name_use = troca($name_use,'}','');
            try {
                $name_use = iconv('UTF-8','ISO-8859-1',$name_use);    
            } catch (\Exception $e) {
                echo 'Erro em:'.$name_use.'<br>';
            }           
            
            $name_use = troca($name_use,'¿','');
            $name_use = troca($name_use,'¼','');            
            $name_use = trim(LowerCaseSQL($name_use));

            if (strlen($name_use) > 0)
                    {
                        $sx .= $name_use . ' {'.$idx.'}'.cr();
                    }
        }
        return ($sx);
    }    

    /***  FIND CLASS **/
    function find_class($class) {
        $sql = "select * from rdf_class
                        WHERE c_class = '$class' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            echo '<h1>Ops, ' . $class . ' não localizada';
            exit ;
        }
        $line = $rlt[0];
        return ($line['id_c']);
    }

    /******************************************************************* RDF NAME ***/
    function frbr_name($n = '', $lang = 'pt-BR', $new = 1) {
        
        if (is_array($n))
            {
                return(0);
            }
        $this -> load -> model('indexer');
        $n = trim($n);
        $n = $this -> utf8_detect($n);
        $lang = trim($lang);
        $lang = troca($lang, '@', '');
        if (strlen($lang) > 5) { $lang = substr($lang, 0, 5);
        }
        $n = troca($n, "'", "´");
        $n = troca($n, "  ", " ");
        if (strlen($n) == 0) {
            return (0);
        }
        /***************************************************************** LANGUAGE */
        $lang = $this -> language($lang);
        $md5 = md5(trim($n));
        $dt['title'] = $n;

        /************ BUSCA NOMES **************************************/
        $sql = "select * from rdf_name where (n_name = '" . $n . "') or (n_md5 = '$md5')";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into rdf_name (n_name, n_lang, n_md5) values ('$n','$lang','$md5')";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }
        $line = $rlt[0];
        return ($line['id_n']);
    }

    /**************************************************************************** SET PROPRIETY *****/
    function set_propriety($r1, $prop, $r2, $lit = 0) {
        /********* propriedade com o prefixo ***************/
        if (strpos($prop, ':')) {
            $prop = substr($prop, strpos($prop, ':') + 1, strlen($prop));
        }
        /*********************** recupera propriedade ID ***/
        $pr = $this -> find_class($prop);
        $sql = "select * from (
                    select * from rdf_data 
                        WHERE 
                    (d_p = $pr and d_literal = $lit)
                    ) as table1
                where
                    ((d_r1 = $r1 AND d_r2 = $r2)
                        OR
                    (d_r1 = $r2 AND d_r2 = $r1))";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sql = "insert into rdf_data
                        (d_r1, d_p, d_r2, d_literal)
                        values
                        ('$r1','$pr','$r2',$lit)";
            $rlt = $this -> db -> query($sql);
        } else {

        }
        return (true);
    }

    function person_show($id) {
        $data = array();
        $sx = '';

        $data = $this -> le($id);
        $data['person'] = $this -> le_data($id);
        $data['use'] = $this -> le_remissiva($id);
        $data['id'] = $id;

        $sx = $this -> load -> view('find/view/person', $data, true);
        return ($sx);
    }

    function journal_show($id) {
        $this->load->model("sources");
        $this->load->model("oai_pmh");
        $data = array();
        $sx = '';

        $data = $this -> le($id);
        $data['person'] = $this -> le_data($id);
        $data['use'] = $this -> le_remissiva($id);
        $data['source'] = $this -> sources->le_frbr($data['id_cc']);
        $data['id'] = $id;

        $sx = $this -> load -> view('find/view/journal', $data, true);
        $sx .= $this -> sources -> show_issues($data['source']['id_jnl']);
        return ($sx);
    }

    function corporate_show($id) {
        $data = array();
        $sx = '';

        $data = $this -> le($id);
        $data['person'] = $this -> le_data($id);
        $data['use'] = $this -> le_remissiva($id);
        $data['id'] = $id;

        $sx = $this -> load -> view('find/view/corporate', $data, true);
        return ($sx);
    }

    function view_data($id) {
        $data = $this -> le_data($id);
        $sx = '<table class="table">';
        $sx .= '<tr>';
        $sx .= '<th width="20%" style="text-align: right;">' . msg('propriety') . '</th>';
        $sx .= '<th width="80%">' . msg('value') . '</th>';
        $sx .= '</tr>';
        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            $line['id'] = $id;
            $link = '';
            if ($line['d_r2'] > 0) {
                $link = '<a href="' . base_url(PATH . 'v/' . $line['d_r2']) . '">';
                if ($line['d_r2'] == $id) {
                    $link = '<a href="' . base_url(PATH . 'v/' . $line['d_r1']) . '">';
                }
            }
            $sx .= '<tr>';
            $sx .= '<td align="right" valign="top">';
            $sx .= '<i>' . msg(trim($line['c_class'])) . '</i>';
            $sx .= '</td>';
            $sx .= '<td>';
            /********* INVERT ********/
            if (($line['d_r1'] == $id) and ($line['d_r2'] != 0)) {
                $idv = $line['d_r2'];
                $line['d_r2'] = $line['d_r1'];
                $line['d_r1'] = $idv;
            }
            $sx .= $this -> mostra_dados($line['n_name'], $link, $line);
            $sx .= ' <sup>(' . $line['n_lang'] . ')</sup>';
            $sx .= ' <sup>' . $line['rule'] . '</sup>';
            $sx .= '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return ($sx);
    }

    function mostra_dados($n, $l = '', $line) {
        $la = '';
        $idx = $line['d_r2'];
        if ($idx == $line['id']) {
            $idx = $line['d_r1'];
        }
        $filex = 'c/' . $idx . '/name.nm';
        if (file_exists($filex) and ($idx > 0)) {
            //print_r($line);
            //echo '<hr>';
            $n = load_file_local($filex);
        }

        /****************** HTTP *********/
        if ((lowercase(substr($n, 0, 4)) == 'http') and (strlen($l) == 0)) {
            $l = '<a href="' . $n . '" target="new_' . date("mis") . '" title="' . msg('Link:') . $n . '">';
        }
        /****************** DOI *********/
        if ((lowercase(substr($n, 0, 3)) == '10.') and (strlen($l) == 0) and (strpos($n, '/') > 0)) {
            $l = '<a href="http://dx.doi.org/' . $n . '" target="new_' . date("mis") . '" title="DOI Link' . $n . '">';
            $n = 'DOI: ' . $n;
        }
        /****************** OAI *********/
        if (((lowercase(substr($n, 0, 4)) == 'oai:')) and ($line['c_class'] != 'hasID')) {
            $n = $this -> frbr -> show_v($line['d_r1']);
            $l = '<a href="' . base_url(PATH . 'v/' . $line['d_r1']) . '" target="new_' . date("mis") . '" title="View Article" class="result">';
        }

        if (strlen($l) > 0) {
            $la = '</a>';
        }

        return ($l . $n . $la);
    }

    function vv($id) {
        $data = $this -> le($id);
        if ($data['cc_use'] > 0)
            {
                redirect(base_url(PATH.'//v//'.$data['cc_use']));
                exit;
            }
        $tela = '';
        if (count($data) == 0) {
            $this -> load -> view('error', $data);
        } else {
            $tela = '';
            if (strlen($data['n_name']) > 0) {
                $tela .= '<div class="row result">';
                $tela .= '<div class="col-md-12">';
                $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="middle">';
                $linkca = '</a>';
                $tela .= '' . $linkc . $data['n_name'] . $linkca . '</h2>';
                $tela .= '</div>';
                $tela .= '</div>';
            }

            /******** line #2 ***********/
            $tela .= '<div class="row">';
            $tela .= '<div class="col-md-11">';
            $tela .= '<h5>' . msg('Class') . ': ' . $data['c_class'] . '</h5>';
            $tela .= '</div>';
            $tela .= '<div class="col-md-1 text-right">';
            if (perfil("#ADMIN")) {
                $tela .= '<a href="' . base_url(PATH . 'a/' . $id) . '" class="btn btn-secondary">' . msg('edit') . '</a>';
            }
            $tela .= '</div>';
            $tela .= '</div>';

            $tela .= '<hr>';
            $class = trim($data['c_class']);

            switch ($class) {
                case 'Patent' :
                    $tela = $this -> frbr -> show_patent($id);
                    $tela .= $this -> view_data($id);
                    break;
                case 'Article' :
                    $tela = $this -> frbr -> show_article($id);
                    $tela .= $this -> cited->show_ref($id);
                    $tela .= $this -> view_data($id);
                    
                    break;
                case 'Issue' :
                    if (perfil("#ADM"))
                    {
                        $tela .= $this -> frbr -> import_csv_issue($id);
                    }
                    $tela .= $this -> frbr -> show_issue($id);
                    $tela .= $this -> view_data($id);
                    break;
                case 'Subject' :
                    $tela .= $this -> frbr -> show_Subject($id);
                    break;
                case 'CorporateBody' :
                    $tela = $this -> corporate_show($id);
                    $tela .= $this -> view_data($id);
                    break;
                case 'Person' :
                    $tela = $this -> person_show($id);
                    $data = $this -> frad -> production($id);

                    //$tela .= $this->load->view("brapci/cloud_tags_2",$data,true);
                    //$tela .= $this -> load -> view("brapci/cloud_tags", $data, true);

                    $tela .= $this -> load -> view("brapci/cloud_tags_3", $data, true);

                    $tela .= '<div class="col-md-8">';
                    $tela .= $this -> view_data($id);
                    $tela .= '</div>';

                    $tela .= '<div class="col-md-4">';
                    $tela .= $data['authors'];
                    $tela .= '</div>' . cr();

                    $tela .= '</div>' . cr();
                    $tela .= $this -> genero -> update($id);
                    break;
                case 'Journal' :
                    $tela = $this -> journal_show($id);
                    $tela .= $this -> view_data($id);
                    break;
                default :
                    $tela .= $this -> view_data($id);
                    $tela .= $this -> related($id);
                    break;
            }
        }
        return ($tela);
    }

    /*****************************************************************  RDF CONCEPT **/
    function rdf_concept_create($class, $term, $orign = '', $lang = 'pt-BR') {
        $cl = $this -> find_class($class);
        $term = $this -> frbr_name($term, $lang);

        $dt = date("Y/m/d H:i:s");
        $date = date("Y-m-d");
        /*********** checar se não existe um termo já iserido *********************/
        $sql = "select * from rdf_concept 
                        WHERE 
                        cc_class = $cl AND cc_pref_term = $term ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sqli = "insert into rdf_concept
                            (cc_class, cc_pref_term, cc_created, cc_origin, cc_update)
                            VALUES
                            ($cl, $term,'$dt','$orign','$date')";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
        }

        /**************** pref Term ****************************************************/
        $line = $rlt[0];
        $r1 = $line['id_cc'];
        $this -> set_propriety($r1, 'prefLabel', 0, $term);
        return ($r1);
    }

    function rdf_concept($term, $class, $orign = '') {

        /**** recupera codigo da classe *******************/
        $cl = $this -> find_class($class);
        $dt = date("Y/m/d H:i:s");
        if ($term == 0) {
            $sql = "select * from rdf_concept
                     WHERE cc_class = $cl and cc_created = '$dt'
                     ORDER BY id_cc";
        } else {
            if (strlen($orign) > 0) {
                $sql = "select * from rdf_concept
                        WHERE cc_class = $cl and (cc_pref_term = $term or cc_origin = '$orign')";
            } else {
                $sql = "select * from rdf_concept
                        WHERE cc_class = $cl and (cc_pref_term = $term)";
            }
        }
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $id = 0;
        $date = date("Y-m-d");

        if (count($rlt) == 0) {

            $sqli = "insert into rdf_concept
                            (cc_class, cc_pref_term, cc_created, cc_origin, cc_update)
                            VALUES
                            ($cl,$term,'$dt','$orign', '$date')";
            $rlt = $this -> db -> query($sqli);
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $id = $rlt[0]['id_cc'];
        } else {
            $id = $rlt[0]['id_cc'];
            $line = $rlt[0];
            $compl = '';
            if ((strlen($orign) > 0) and ((strlen(trim($line['cc_origin'])) == 0) or ($line['cc_origin'] == 'ERRO:'))) {
                $compl = ", cc_origin = '$orign' ";
            }
            $sql = "update rdf_concept set cc_status = 1, cc_update = '$date' $compl where id_cc = " . $line['id_cc'];
            $rlt = $this -> db -> query($sql);
        }
        return ($id);
    }

    /******************************************************************** LE ************/
    function le($id) {
        $sql = "select * from rdf_concept 
                    INNER JOIN rdf_class ON cc_class = id_c
                    LEFT JOIN rdf_name ON cc_pref_term = id_n
                        WHERE id_cc = $id";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                return ($line);
            }
        } else {
            return ( array());
        }
    }

    function le_remissiva($id) {
        $sql = "SELECT * FROM rdf_concept as R1
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        where R1.cc_use = $id";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function le_data($id, $prop = '') {
        if (strlen($prop) > 0) {
            $wh = " AND (c_class = '$prop')";
        } else {
            $wh = '';
        }
        $cp = 'd_r2, d_r1, c_order, c_class, id_d, n_name, n_lang';
        $cp_reverse = 'd_r2 as d_r1, d_r1 as d_r2, c_order, c_class, id_d, n_name, n_lang';
        $sql = "select $cp,1 as rule from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r2 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r1 = $id and d_r2 > 0 " . $wh . cr() . cr();
        $sql .= ' union ' . cr() . cr();
        /* TRABALHOS */
        $sql .= "select $cp_reverse,2 as rule from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r1 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r2 = $id and d_r1 > 0 " . $wh . cr() . cr();
        $sql .= ' union ' . cr() . cr();
        $sql .= "select $cp,3 as rule from rdf_data as rdata
                        LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                        LEFT JOIN rdf_concept ON d_r2 = id_cc 
                        LEFT JOIN rdf_name on d_literal = id_n
                        WHERE d_r1 = $id and d_r2 = 0 " . $wh . cr() . cr();

        /* USE */
        $prop = $this -> frbr_core -> find_class("equivalentClass");
        $sqll = "SELECT * FROM rdf_data where (d_r2 = $id or d_r1 = $id) and d_p = $prop";

        //$sqll = "select * from rdf_concept where (cc_use = $id) and (id_cc <> cc_use)";
        $rrr = $this -> db -> query($sqll);
        $rrr = $rrr -> result_array();
        for ($r = 0; $r < count($rrr); $r++) {
            $line = $rrr[$r];
            $iduse = $line['d_r1'];
            if ($iduse == $id) {
                $iduse = $line['d_r2'];
            }
            $sql .= ' union ' . cr() . cr();
            $sql .= "select $cp_reverse, " . (10 + $r) . " as rule from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r1 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r2 = $iduse and d_r1 > 0 and d_p <> $prop" . cr() . cr();
        }
        $sql .= " order by c_order, c_class, rule, n_lang desc, id_d";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function person_work($id) {
        $r = array();
        $sql = "select d_r1, d_p, d_r2 from rdf_data 
                    where (d_r1 = $id or d_r2 = $id)
                       AND NOT (d_r1 = 0 OR d_r2 = 0)
                ORDER BY d_r1, d_p, d_r2";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $wk = array();
        $ww = array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $p = $line['d_p'];
            $r1 = $line['d_r1'];
            if ($r1 != $id) {
                if (!isset($ww[$r1])) {
                    array_push($wk, $r1);
                }
            }
            $r1 = $line['d_r2'];
            if ($r1 != $id) {
                if (!isset($ww[$r1])) {
                    array_push($wk, $r1);
                }
            }
        }
        return ($wk);
    }

    /********************************************************************************** RELATED **/
    /*********************************************************************************************/
    /*********************************************************************************************/
    /*********************************************************************************************/
    function related($id) {
        $pg = round('0' . get("pg"));
        $limit = $this -> limit;
        $offset = $limit * $pg;
        /******************************************** by manifestation ********/
        $cl1 = $this -> find_class('isEmbodiedIn');
        $cl2 = $this -> find_class('isRealizedThrough');

        /** div **/
        $sx = '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();

        $sql = "SELECT dd3.d_r1 as w, count(*) as mn FROM `rdf_data` as dd1 
                    left JOIN rdf_data as dd2 ON dd1.d_r1 = dd2.d_r2 
                    left JOIN rdf_data as dd3 ON dd2.d_r1 = dd3.d_r2 
                    LEFT JOIN rdf_class ON dd2.d_p = id_c
                where dd1.d_r2 = $id and dd2.d_p = 88 and dd3.d_p = 37
                group by w";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $pags = $this -> pagination($rlt);
        for ($r = 0; $r < count($rlt); $r++) {
            if (($r >= $offset) and ($r < ($offset + $limit))) {
                $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">' . cr();
                $line = $rlt[$r];
                $idm = $line['w'];
                $sx .= $this -> show_manifestation_by_works($idm, 0, 0);
                if ($line['mn'] > 1) {
                    $sx .= '<br>';
                    $sx .= '<a href="' . base_url(PATH . 'v/' . $idm) . '" class="small">';
                    $sx .= '<span style="color:red"><i>' . msg('see_others_editions') . '</i></span>';
                    $sx .= '</a>' . cr();
                }
                $sx .= '</div>';
            }
        }

        /******************************************** by expression ***********/
        if (count($rlt) == 0) {
            $sql = "SELECT dd2.d_r1 as w, count(*) as mn FROM `rdf_data` as dd1 
                    left JOIN rdf_data as dd2 ON dd1.d_r1 = dd2.d_r2 
                    /* left JOIN rdf_data as dd3 ON dd2.d_r1 = dd3.d_r2 */ 
                    LEFT JOIN rdf_class ON dd2.d_p = id_c
                where dd1.d_r2 = $id and dd2.d_p = 37
                group by w ";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $pags = $this -> pagination($rlt);
            for ($r = 0; $r < count($rlt); $r++) {
                if (($r >= $offset) and ($r < ($offset + $limit))) {
                    $sx .= '<div class="col-lg-2 col-md-4 col-xs-3 col-sm-6 text-center" style="line-height: 80%; margin-top: 40px;">' . cr();
                    $line = $rlt[$r];
                    $idm = $line['w'];
                    $sx .= $this -> show_manifestation_by_works($idm, 0, 0);
                    if ($line['mn'] > 1) {
                        $sx .= '<br>';
                        $sx .= '<a href="' . base_url(PATH . 'v/' . $idm) . '" class="small">';
                        $sx .= '<span style="color:red"><i>' . msg('see_others_editions') . '</i></span>';
                        $sx .= '</a>' . cr();
                    }
                    $sx .= '</div>';
                }
            }
        }

        /** div **/
        $sx .= '</div>';
        $sx .= '</div>';

        return ($pags . $sx);
    }

    function pagination($t) {
        $pg = round('0' . get("pg"));
        $t = count($t);
        $l = $this -> limit;
        /***************************** math *************/
        if ($l == 0) {
            return ('');
        }
        $p = ($t / $l);
        $p = (int)$p;
        if (($t / $l) > $p) { $p++;
        }

        $sx = '<div class="container">' . cr();
        $sx .= '<div class="row">' . cr();
        $sx .= '  
            <nav aria-label="Page navigation example">
              <ul class="pagination">' . cr();
        $ds = 'disabled';
        if ($pg > 0) { $ds = '';
        }
        $sx .= '<li class="page-item ' . $ds . '"><a class="page-link" href="?pg=' . ($pg - 1) . '">Previous</a></li>' . cr();
        for ($r = 0; $r < $p; $r++) {
            $ac = '';
            if ($pg == $r) { $ac = 'active';
            }
            $sx .= '<li class="page-item ' . $ac . '"><a class="page-link " href="?pg=' . $r . '">' . ($r + 1) . '</a></li>' . cr();
        }
        $ps = 'disabled';
        if ($pg < ($p - 1)) { $ps = '';
        }
        $sx .= '<li class="page-item ' . $ps . '"><a class="page-link " href="?pg=' . ($pg + 1) . '">Next</a></li>' . cr();
        $sx .= '</ul></nav>' . cr();
        $sx .= '</div>';
        $sx .= '</div>';
        return ($sx);
    }

    function show_class($wk) {
        $sx = '';
        $ss = '<h5>' . msg('hasColaboration') . '</h5><ul>';
        $wks = array();
        for ($r = 0; $r < count($wk); $r++) {
            $id = $wk[$r];

            $data = $this -> le_data($id);
            for ($z = 0; $z < count($data); $z++) {
                $line = $data[$z];
                $cl = $line['c_class'];
                $vl = $line['n_name'];
                $id1 = $line['d_r1'];
                $id2 = $line['d_r2'];
                //echo '<br>===>'.$cl;
                switch ($cl) {
                    case 'hasTitle' :
                        $link = '<a href="' . base_url(PATH . 'v/' . $id1) . '">';
                        array_push($wks, $id1);
                        break;
                    case 'hasAuthor' :
                        $link = '<a href="' . base_url(PATH . 'v/' . $id2) . '">';
                        $ss .= '<li>' . $link . $vl . ' (' . ($cl) . ')</a></li>' . cr();
                        break;
                    default :
                }
            }
        }
        $ss .= '</ul>';
        //$sx .= '<div class="row img-person" >' . cr();
        for ($r = 0; $r < count($wks); $r++) {
            $wk = $wks[$r];
            $sx .= '<div class="col-md-2 text-center" style="line-height: 80%; margin-top: 40px;">';
            //$sx .= $this -> show_manifestation_by_works($wk);
            $sx .= '</div>';
        }
        //$sx .= '</div>' . cr();

        return ($sx);
    }

    function utf8_detect($n) {
        $type = mb_detect_encoding($n, "auto");
        if ($type != 'UTF-8') {
            return ($n);
        }
        /************* UTF8 **************/
        $utf = 0;
        $conv = 0;
        for ($r = 0; $r < strlen($n); $r++) {
            $c = substr($n, $r, 1);
            $co = ord($c);
            if (($co > 195) and ($utf == 0)) {
                $conv = 1;
            }
            if ($co == 195) { $utf = 1;
            }
            //echo '<br>'.$c.'=>'.$co;
        }
        if ($conv == 1) {
            $n = utf8_encode($n);
        }
        return ($n);
    }

    function form($id, $dt) {
        $class = $dt['cc_class'];
        $sx = '';
        /* complementos */
        switch($class) {
            default :
                $cp = 'n_name, cpt.id_cc as idcc, d_p as prop, id_d';
                $sqla = "select $cp from rdf_data as rdata
                                INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                                INNER JOIN rdf_concept as cpt ON d_r2 = id_cc 
                                INNER JOIN rdf_name on cc_pref_term = id_n
                                WHERE d_r1 = $id and d_r2 > 0";
                $sqla .= ' union ';
                $sqla .= "select $cp from rdf_data as rdata
                                LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                                LEFT JOIN rdf_concept as cpt ON d_r2 = id_cc 
                                LEFT JOIN rdf_name on d_literal = id_n
                                WHERE d_r1 = $id and d_r2 = 0";
                /*****************/
                $sql = "select * from rdf_form_class
                            INNER JOIN rdf_class ON id_c = sc_propriety
                            LEFT JOIN (" . $sqla . ") as table1 ON id_c = prop 
                        where sc_class = $class 
                        order by sc_ord, id_sc, c_order";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                $sx .= '<table width="100%" cellpadding=5>';
                $js = '';
                $xcap = '';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $cap = msg($line['c_class']);
                    $link = '<a href="#" id="action_' . trim($line['c_class']) . '" data-toggle="modal" data-target=".bs-example-modal-lg">';
                    $link = '<a href="#" id="action_' . trim($line['c_class']) . '">';
                    $linka = '</a>';
                    $sx .= '<tr>';
                    $sx .= '<td width="25%" align="right">';
                    if ($xcap != $cap) {
                        $sx .= '<nobr><i>' . msg($line['c_class']) . '</i></nobr>';
                        $sx .= '<td width="1%">' . $link . '[+]' . $linka . '</td>';
                        $xcap = $cap;

                    } else {
                        $sx .= '&nbsp;';
                        $sx .= '<td>-</td>';
                    }
                    $sx .= '</td>';
                    $sx .= '<td style="border-bottom: 1px solid #808080;">';
                    if (strlen($line['n_name']) > 0) {
                        $linkc = '<a href="' . base_url(PATH . 'v/' . $line['idcc']) . '" class="middle">';
                        $linkca = '</a>';
                        $sx .= $linkc . $line['n_name'] . $linkca;
                        $link = ' <span id="ex' . $line['id_d'] . '" onclick="exclude(' . $line['id_d'] . ');" style="cursor: pointer;">';
                        $sx .= $link . '<font style="color: red;" title="Excluir lancamento">[X]</font>' . $linka;
                        $sx .= '</span>';
                    }
                    $sx .= '</td>';
                    $sx .= '</tr>';
                    $js .= 'jQuery("#action_' . trim($line['c_class']) . '").click(function() 
                      {
                          carrega("' . trim($line['c_class']) . '");
                          jQuery("#dialog").modal("show"); 
                      });' . cr();
                }
                $sx .= '</table>';
                break;
        }
        $sx .= '<script>
                    ' . $js . '
                    function carrega($id)
                    {
                        jQuery.ajax({
                          url: "' . base_url(PATH . 'ajax/') . '"+$id+"/"+' . $id . ',
                          context: document.body
                        })  .done(function( html ) {
                            jQuery( "#model_texto" ).html( html );
                        });
                    }                    
                </script>';
        $sx .= '<div id="dialog" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                              <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" id="model_texto">
                                </div>
                              </div>
                            </div>';
        $sx .= $this -> load -> view('modal/modal_exclude', null, true);
        return ($sx);
    }

    function show($r) {
        if (strlen($r) == 0) {
            return ('');
        }
        $sx = '';
        $sql = "select * from rdf_concept 
                        INNER JOIN rdf_class as prop ON cc_class = id_c
                        WHERE id_cc = " . $r;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        /****************************************** return if empty */
        if (count($rlt) == 0) {
            return ('');
        }
        /**************************************************** show **/
        $line = $rlt[0];
        $sx .= '<h3>class:' . $line['c_class'] . '</h3>';

        $cp = '*';
        $sql = "select $cp from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r2 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r1 = $r and d_r2 > 0";
        $sql .= ' union ';
        $sql .= "select $cp from rdf_data as rdata
                        LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                        LEFT JOIN rdf_concept ON d_r2 = id_cc 
                        LEFT JOIN rdf_name on d_literal = id_n
                        WHERE d_r1 = $r and d_r2 = 0";
        $sql .= " order by c_order, c_class";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $sx .= '<table width="100%" cellpadding=5>' . cr();
        $sx .= '<tr><th width=20%" class="text-right">propriety</th><th>value</th></tr>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $id = $line['id_cc'];
            $link = '<a href="' . base_url(PATH . 'a/' . $line['id_cc']) . '">';
            $linka = '</a>';
            if (strlen($line['id_cc']) == 0) {
                $link = '';
                $linka = '';
            }
            $sx .= '<tr>';
            $sx .= '<td class="text-right" style="font-size: 60%;">';
            $sx .= msg($line['c_class']);
            $sx .= '</td>';

            $sx .= '<td>';
            $sx .= $link . $line['n_name'] . $linka;
            $sx .= ' ';

            $link = '<span id="ex' . $line['id_d'] . '" onclick="exclude(' . $line['id_d'] . ');" style="cursor: pointer;">';
            $sx .= $link . '<font style="color: red;" title="Excluir lancamento">[X]</font>' . $linka;
            $sx .= '</span>';

            /********************* prefer */
            if ($line['c_class'] == 'altLabel') {
                $link = '<span id="ep' . $line['id_d'] . '" onclick="setPrefTerm(' . $line['id_d'] . ',' . $line['id_n'] . ');" style="cursor: pointer;">';
                $sx .= $link . '<font style="color: red;" title="Definir como preferencial">[pref]</font>' . $linka;
                $sx .= '</span>';
            }

            $sx .= '</td>';

            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        $sx .= $this -> load -> view('modal/modal_exclude', null, true);
        $sx .= $this -> load -> view('modal/modal_set_prefterm', null, true);
        return ($sx);
    }

    function Model($path = '', $id = 0, $dt = '') {
        $sx = '
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>            
            ';
        $sx = '
                 <div class="modal-header" >                    
                    <h4 class="modal-title" id="myModalLabel">Modal - ' . $path . '</h4>
                    <button type="button" class="close text-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  </div>
                  <div class="modal-body">';

        $sx .= $this -> form_ajax($path, $id, $dt);

        $sx .= '
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" style="display: none;" id="save">Incluir</button>
                    <button type="button" class="btn btn-primary" id="submt" disabled>Salvar</button>
                  </div>                  
            ';
        return ($sx);
    }

    function form_ajax($path, $id, $dt=array()) {
        $tela = '';
        $sql = "select cl2.c_class as rg from rdf_class as cl1
                        LEFT JOIN rdf_form_class ON sc_propriety = cl1.id_c
                        LEFT JOIN rdf_class as cl2 ON cl2.id_c = sc_range
                        WHERE cl1.c_class = '" . $path . "' and cl1.c_type = 'P' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $type = (string)$path;
        if (!is_array($dt))
        {
            $dt = array();
        }
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $type = $line['rg'];

        }
        /**********************************************************************************/
        $dt['type'] = $type;
        //echo '==>'.$type;
        switch($type) {
            case 'ISBN' :
                $tela .= $this -> cas_flex($path, $id, $dt);
                break;
            case 'Work' :
                $tela .= $this -> cas_flex($path, $id, $dt);
                break;
            case 'Pages' :
                $tela .= $this -> cas_flex($path, $id, $dt);
                break;
            case 'Image' :
                $tela .= $this -> upload_image($path, $id, $dt);
                break;
            default :
                $dt['type'] = $type;
                $tela .= $this -> cas_ajax($path, $id, $dt);
                break;
        }
        return ($tela);
    }

    function data_exclude($id) {
        $sql = "select * from rdf_data where id_d = " . $id;
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            if ($line['d_r1'] > 0) {
                $sql = "update rdf_data set
                                d_r1 = " . ((-1) * $line['d_r1']) . " ,
                                d_r2 = " . ((-1) * $line['d_r2']) . " ,
                                d_p  = " . ((-1) * $line['d_p']) . " 
                                where id_d = " . $line['id_d'];
                $rlt = $this -> db -> query($sql);
            }
        }
    }

    function ajax2($path, $id, $type = '') {
        $tela = '<select name="dd51" id="dd51" size=5 class="form-control" onchange="change();">' . cr();
        $vlr = get("q");
        if (strlen($vlr) < 1) {
            $tela .= '<option></option>' . cr();
        } else {
            $vlr = troca($vlr, ' ', ';');
            $v = splitx(';', $vlr);
            $wh = '';
            for ($r = 0; $r < count($v); $r++) {
                if ($r > 0) {
                    $wh .= ' and ';
                }
                $wh .= "(n_name like '%" . $v[$r] . "%') ";
            }
            /* RANGE ***************************************************************/
            if (strlen($type) > 0) {
                $wh2 = '';
                $ww = $this -> frbr_core -> find_class($type);
                $wh2 = ' (cc_class = ' . $ww . ') ';

                $sql = "select * FROM rdf_class
                                        WHERE c_class_main = $ww";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $wh2 .= ' OR (cc_class = ' . $line['id_c'] . ') ';
                }
                $wh2 = ' AND (' . $wh2 . ')';
            } else {
                $wh2 = '';
            }
            /***********************************************************************/
            if (strlen($wh) > 0) {
                $sql = "select * from rdf_name
                                    INNER JOIN rdf_data ON id_n = d_literal
                                    INNER JOIN rdf_concept ON d_r1 = id_cc
                                    INNER JOIN rdf_class ON id_c = d_p 
                                    WHERE ($wh) and (n_name <> '') $wh2 
                                    LIMIT 50";
                $rlt = $this -> db -> query($sql);
                $rlt = $rlt -> result_array();

                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $tela .= '<option value="' . $line['id_cc'] . '">' . $line['n_name'] . '</option>' . cr();
                }
            }
        }

        $tela .= '</select>' . cr();
        $tela .= '  <script>                 
                        function change()
                            {
                                jQuery("#submt").removeAttr("disabled");
                            }
                            
                        jQuery("#submt").attr("disabled","disabled");
                    </script>';
        return ($tela);
    }

    function cas_ajax($path, $id, $dt = array()) {
        if (!isset($dt['label1'])) { $dt['label1'] = 'Nome do autor';
        }

        /* */
        $type = '';
        if (isset($dt['type'])) {
            $type = $dt['type'];
        }
        $tela = '';
        $tela .= '<span style="font-size: 75%">filtro do [' . $dt['label1'] . ']</span><br>';
        $tela .= '<input type="text" id="dd50" name="dd50" class="form-control">';
        $tela .= '<span style="font-size: 75%">selecione o [' . $dt['label1'] . ']</span><br>';
        $tela .= '<div id="dd51a"><select class="form-control" size=5 name="dd51" id="dd51"></select></div>';
        $tela .= '
                    <script>
                        /************ keyup *****************/
                        jQuery("#dd50").keyup(function() 
                            {
                                var $key = jQuery("#dd50").val();
                                
                                $.ajax({
                                    type: "POST",
                                    url: "' . base_url(PATH . 'ajax/ajax2/' . $path . '/' . $id . '/' . $type) . '",
                                    data:"q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                    }
                                });                                            
                            });
                         /************ submit ***************/
                         jQuery("#submt").click(function() {
                            var $key = jQuery("#dd51").val();
                            $.ajax({
                                    type: "POST",
                                    url: "' . base_url(PATH . 'ajax/ajax3/' . $path . '/' . $id) . '",
                                    data: "q="+$key,
                                    success: function(data){
                                        $("#dd51a").html(data);
                                    }
                                });                           
                            /*
                            jQuery("#dialog").modal("toggle");
                            */
                         });
                    </script>';
        return ($tela);
    }

    function le_class($id) {
        $sql = "select * from rdf_class
                        WHERE c_class = '" . $id . "'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line);
        } else {
            $line = array();
            return ($line);
        }
    }

    function data_classes($d) {
        $id = $this -> find_class($d);
        $sql = "select * from rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_N
                        WHERE cc_class = $id
                        ORDER BY n_name ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function inport_rdf($t, $class = '') {
        if (strlen($class) == 0) {
            echo 'Classe não definida na importação';
            retur('');
        }
        $ln = $t;
        $ln = troca($ln, ';', ':.');
        $ln = troca($ln, chr(13), ';');
        $ln = troca($ln, chr(10), ';');
        $lns = splitx(';', $ln);
        for ($r = 0; $r < count($lns); $r++) {
            $ln = $lns[$r];
            $ln = troca($ln, chr(9), ';');

            $l = splitx(';', $ln);
            if (count($l) == 3) {
                $prop = $l[1];
                $term = $l[2];
                $resource = $l[0];
                if ($prop == 'skosxl:is_synonymous') {
                    $prop = 'skos:altLabel';
                }
                if ($prop == 'skosxl:literalForm') {
                    $prop = 'skos:altLabel';
                }
                if ($prop == 'skosxl:isSingular') {
                    $prop = 'skos:altLabel';
                }
                switch($prop) {
                    case 'skos:prefLabel' :
                        $item = $this -> frbr_name($term);
                        $p_id = $this -> rdf_concept($item, $class, $resource);
                        $this -> set_propriety($p_id, $prop, 0, $item);
                        break;
                    default :
                        $item = $this -> frbr_name($term);
                        $p_id = $this -> rdf_concept_find_id($resource);
                        if ($p_id > 0) {
                            $this -> set_propriety($p_id, $prop, 0, $item);
                        }
                        break;
                }
            }
        }
        echo '<span style="color: #0000ff">Fim da importação</span>';
    }

    function rdf_concept_find_id($r) {
        $id = 0;
        $sql = "select * from rdf_concept where cc_origin = '$r'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line['id_cc']);
        }
        return ($id);
    }

    function form_class() {
        $admin = perfil("#ADM");

        $cp = 'id_sc, sc_ativo, sc_ord, tb1.c_class as c1, tb2.c_class as c2, tb3.c_class as c3';
        $sql = "select $cp from rdf_form_class 
                        INNER JOIN rdf_class as tb1 ON sc_class = tb1.id_c
                        INNER JOIN rdf_class as tb2 ON sc_propriety = tb2.id_c
                        LEFT JOIN rdf_class as tb3 ON sc_range = tb3.id_c
                        order by c1, sc_ord, c2, c3
                        ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        $link = '<a href="#" class="btn btn-secondary" onclick="newwin(\'' . base_url(PATH . 'pop_config/forms/') . '\',800,600);">Novo registro</a>';
        $sx .= '<br>' . $link;
        $sx .= '<table width="100%">' . cr();
        $sx .= '<tr style="border-bottom: 2px solid #505050;">
                        <th width="30%">' . msg('resource') . '</th>
                        <th width="35%">' . msg('propriety') . '</th>
                        <th width="30%">' . msg('range') . '</th>
                        <th width="5%">' . msg('ed') . '</th>';
        if ($admin == 1) {
            $sx .= '            <th>ac</th>' . cr();
        }
        $sx .= '            </tr>' . cr();
        $x = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $st = $line['sc_ativo'];
            if ($st == '1') {
                $st = '';
                $sta = '';
            } else {
                $st = '<s>';
                $sta = '</s>';
            }

            if ($x == $line['c1']) {
                $sx .= '<tr style="border-top: 1px solid #a0a0a0;">';
                $sx .= '<td></td>';
            } else {
                $sx .= '<tr style="border-top: 3px solid #a0a0a0; ' . $st . '">';
                $x = $line['c1'];
                $sx .= '<td><b>' . $st . $line['c1'] . $sta . '</b></td>';
            }

            $sx .= '<td>' . $st . msg($line['c2']) . ' (' . $line['c2'] . ')' . $sta . '</td>';
            $sx .= '<td>' . $st . msg($line['c3']) . $sta . '</td>';
            $sx .= '<td align="center">' . $st . ($line['sc_ord']) . $sta . '</td>';
            if ($admin == 1) {
                $link = '<a href="#" onclick="newwin(\'' . base_url(PATH . 'pop_config/forms/' . $line['id_sc']) . '\',800,600);">[ed]</a>';
                $sx .= '<td align="center">' . $link . '</td>';
            }
            $sx .= '</tr>' . cr();
        }
        $sx .= '</table>';
        return ($sx);
    }

    function form_class_ed($id) {
        $form = new form;
        $form -> id = $id;
        $cp = array();
        array_push($cp, array('$H8', 'id_sc', '', false, true));

        $sqlc = "select * from rdf_class where c_type = 'C'";
        $sqlp = "select * from rdf_class where c_type = 'P'";
        array_push($cp, array('$Q id_c:c_class:' . $sqlc, 'sc_class', msg('resource'), true, true));
        array_push($cp, array('$Q id_c:c_class:' . $sqlp, 'sc_propriety', msg('propriety'), true, true));
        array_push($cp, array('$Q id_c:c_class:' . $sqlc, 'sc_range', msg('range'), true, true));
        array_push($cp, array('$[1:99]', 'sc_ord', msg('ordem'), true, true));
        array_push($cp, array('$O 1:Ativo&0:Inativo', 'sc_ativo', msg('ativo'), true, true));

        $tela = $form -> editar($cp, 'rdf_form_class');

        if ($form -> saved) {
            $tela .= '
                        <script>
                            window.opener.location.reload();
                            close();
                        </script>
                        ';
        }
        return ($tela);
    }

    function classes_lista() {
        /**************** class *************************/
        $sql = "select * from rdf_class where c_type = 'C' order by c_type, c_class";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        $tp = '';
        $lg = array('C' => 'Classe', 'P' => 'Propriedade');
        $sx .= '<div class="row">';
        $sx .= '<div class="col-md-1">';
        $sx .= '<b>' . $lg['C'] . '</b>';
        $sx .= '</div>';

        $sx .= '<div class="col-md-5">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $link = '<a href="' . base_url(PATH . 'vocabulary_ed/' . $line['id_c']) . '">';

            $sx .= msg($line['c_class']);
            $sx .= ' (' . $link . $line['c_class'] . '</a>' . ')';
            $sx .= '<br>';
        }
        $sx .= '</div>';

        /**************** propriety **********************/
        $sql = "select * from rdf_class where c_type = 'P' order by c_type, c_class";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx .= '<div class="col-md-1">';
        $sx .= '<b>' . $lg['P'] . '</b>';
        $sx .= '</div>';

        $sx .= '<div class="col-md-5">';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $xtp = $line['c_type'];
            $link = '<a href="' . base_url(PATH . 'vocabulary_ed/' . $line['id_c']) . '">';
            $sx .= msg($line['c_class']);
            $sx .= ' (' . $link . $line['c_class'] . '</a>' . ')';
            $sx .= '<br>';
        }
        $sx .= '</div>';
        $sx .= '</div>';
        return ($sx);
    }

}

function person_work($id) {
    $r = array();
    $sql = "select d_r1, d_p, d_r2 from rdf_data 
                    where (d_r1 = $id or d_r2 = $id)
                       AND NOT (d_r1 = 0 OR d_r2 = 0)
                ORDER BY d_r1, d_p, d_r2";
    $rlt = $this -> db -> query($sql);
    $rlt = $rlt -> result_array();

    $wk = array();
    $ww = array();
    for ($r = 0; $r < count($rlt); $r++) {
        $line = $rlt[$r];
        $p = $line['d_p'];
        $r1 = $line['d_r1'];
        if ($r1 != $id) {
            if (!isset($ww[$r1])) {
                array_push($wk, $r1);
            }
        }
        $r1 = $line['d_r2'];
        if ($r1 != $id) {
            if (!isset($ww[$r1])) {
                array_push($wk, $r1);
            }
        }
    }
    return ($wk);
}
?>
