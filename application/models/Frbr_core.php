<?php
class frbr_core extends CI_model {
    var $limit = 20;
    
    function prefTerm_chage($c = '', $t1 = '') {
        $sql = "select * from rdf_concept
                    INNER JOIN rdf_name on id_n = cc_pref_term  
                    where id_cc = $c ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) == 1)
            {
                $line = $rlt[0];
                if ($line['cc_pref_term'] != $t1)
                    {
                        $prop = $this->frbr_core->find_class("prefLabel");
                        $lit = $line['cc_pref_term'];
                        /****************** UPDATE *******************/
                        $sql = "update rdf_data set d_literal = $t1 where d_p = $prop AND d_r1 = ".$c;
                        $rrr = $this->db->query($sql);
                        
                        $sql = "update rdf_concept set cc_pref_term = $t1 where id_cc = ".$c;
                        $rrr = $this->db->query($sql);

                        /****************** SET HIDDEN ***************/
                        $prop = 'altLabel';
                        $this->frbr_core->set_propriety($c, $prop, 0, $lit); 
                        return(1);
                    } else {
                        return(0);
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
    
    function prefTerm($data)
        {
            for ($r=0;$r < count($data);$r++)
                {
                    $line = $data[$r];
                    $prop = $line['c_class'];
                    if ($prop == 'prefLabel')
                        {
                            $name = trim($line['n_name']).'@'.trim($line['n_lang']);
                            return($name);
                        }
                }
            return("");
        }
    
    function find($n, $prop = '') {
        $sql = "select d_r1, c_class, d_r2, n_name from rdf_name
                        INNER JOIN rdf_data on d_literal = id_n 
                        INNER JOIN rdf_class ON d_p = id_c
                        where n_name like '%" . $n . "%'";
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

    function index_list($lt = '', $class = 'Person') {
        $f = $this -> find_class($class);
        $this -> check_language();

        $sql = "select * from rdf_concept 
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        where cc_class = " . $f . " 
                        ORDER BY n_name";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<div class="col"><div class="col-12">';
        $sx .= '<h5>' . msg('total_subject') . ' ' . number_format(count($rlt), 0, ',', '.') . ' ' . msg('registers') . '</h5>';
        $sx .= '<ul>';
        $l = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $xl = substr(UpperCaseSql($line['n_name']), 0, 1);
            if ($xl != $l) {
                $sx .= '<h4>' . $xl . '</h4>';
                $l = $xl;
            }
            $link = '<a href="' . base_url(PATH . 'v/' . $line['id_cc']) . '">';
            $name = $link . $line['n_name'] . '</a> <sup style="font-size: 70%;">(' . $line['n_lang'] . ')</sup>';
            $sx .= '<li>' . $name . '</li>' . cr();
        }
        $sx .= '<ul>';
        $sx .= '</div></div>';
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
    	$this->load->model('indexer');
        $n = trim($n);
		$n = $this->utf8_detect($n);
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
        $sql = "select * from rdf_data 
                    WHERE 
                    ((d_r1 = $r1 AND d_r2 = $r2)
                        OR
                     (d_r1 = $r2 AND d_r2 = $r1))
                    AND d_p = $pr 
                    AND d_literal = $lit ";
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
    }

    function person_show($id) {
        $data = array();
        $sx = '';

        $data = $this -> le($id);
        $data['person'] = $this -> le_data($id);
        $data['id'] = $id;

        $sx = $this -> load -> view('find/view/person', $data, true);
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
            $sx .= '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return ($sx);
    }

    function mostra_dados($n, $l = '', $line) {
        $la = '';
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
        if ((lowercase(substr($n, 0, 4)) == 'oai:')) {
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
        if (count($data) == 0) {
            $this -> load -> view('error', $data);
        } else {
            $tela = '';
            if (strlen($data['n_name']) > 0) {
                $tela .= '<div class="row result">';
                $tela .= '<div class="col-md-12">';
                $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="middle">';
                $linkca = '</a>';
                $tela .= '<h2>' . $linkc . $data['n_name'] . $linkca . '</h2>';
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
                case 'Article' :
                    $tela = $this-> frbr->show_article($id);
                    $tela .= $this -> view_data($id);
                    break;                
                case 'Issue' :
                    $tela .= $this -> view_data($id);
                    break;
                case 'Subject' :
                    $tela .= $this-> frbr->show_Subject($id);
                    break;                    
                case 'Corporate Body' :
                    $tela .= $this -> view_data($id);
                    break;
                case 'Person' :
                    $tela = $this -> person_show($id);
                    $tela .= $this -> view_data($id);
                    break;
                case 'Journal' :
                    $tela = $this -> person_show($id);
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
                $compl = "', cc_origin = '$orign' ";
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

    function le_data($id) {
        $cp = 'd_r2, d_r1, c_order, c_class, id_d, n_name, n_lang';
        $cp_reverse = 'd_r2 as d_r1, d_r1 as d_r2, c_order, c_class, id_d, n_name, n_lang';
        $sql = "select $cp from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r2 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r1 = $id and d_r2 > 0";
        $sql .= ' union ';
        $sql .= "select $cp_reverse from rdf_data as rdata
                        INNER JOIN rdf_class as prop ON d_p = prop.id_c 
                        INNER JOIN rdf_concept ON d_r1 = id_cc 
                        INNER JOIN rdf_name on cc_pref_term = id_n
                        WHERE d_r2 = $id and d_r1 > 0";
        $sql .= ' union ';
        $sql .= "select $cp from rdf_data as rdata
                        LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
                        LEFT JOIN rdf_concept ON d_r2 = id_cc 
                        LEFT JOIN rdf_name on d_literal = id_n
                        WHERE d_r1 = $id and d_r2 = 0";
        $sql .= " order by c_order, c_class, n_lang desc, id_d";

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
                    $sx .= '<a href="' . base_url('index.php/main/v/' . $idm) . '" class="small">';
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
                        $sx .= '<a href="' . base_url('index.php/main/v/' . $idm) . '" class="small">';
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

	function utf8_detect($n)
		{
			$type = mb_detect_encoding($n, "auto");
			if ($type != 'UTF-8')
				{
					return($n);
				}
			/************* UTF8 **************/				
			$utf = 0;
			$conv = 0;
			for ($r=0;$r < strlen($n);$r++)
				{
					$c = substr($n,$r,1);
					$co = ord($c);
					if (($co > 195) and ($utf == 0))
						{
							$conv = 1;
						}
					if ($co == 195) { $utf = 1; }
					//echo '<br>'.$c.'=>'.$co;
				}
			if ($conv == 1)
				{
					$n = utf8_encode($n);
				}
			return($n);
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
