<?php
class frbr extends CI_model {
    function api_thesa($id=0)
        {
            
        }
        
    function author_check_method_1($p=0)
        {
            $f = $this->frbr_core->find_class('Person');
            $sql = "select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                           N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
                            FROM rdf_concept as C1
                            INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                            LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                            LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                            where C1.cc_class = " . $f . " and c1.cc_use = 0
                            ORDER BY N1.n_name";            
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $n2 = '';
            $n0 = '';
            $i2 = 0;
            $sx = '';
            $m = 0;
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $n0 = trim($line['n_name']);
                    $n1 = trim($line['n_name']);
                    $n1 = troca($n1,' de ',' ');
                    $n1 = troca($n1,' da ',' ');
                    $n1 = troca($n1,' do ',' ');
                    $n1 = troca($n1,' dos ',' ');
                    $nf = substr($n1,strlen($n1)-3,3);
                    if (($nf == ' de') or ($nf == ' da') or ($nf == ' do') or ($nf == ' dos'))
                        {
                            $n1 = trim(substr($n1,0,strlen($n1)-3));
                        }
                    $n1 = trim($n1);
                    $i1 = $line['id_cc'];

                    if ($n1 == $n2)
                        {
                            $m++;
                            $sx .= '<br>'.$n1.'('.$i1.')';
                            $sx .=  '--'.$n2.'('.$i2.')';
                            $sx .=  ' --> <b><font color="green">Igual</font></b>'; 
                            $sql = "update rdf_concept set cc_use = $i2 where id_cc = $i1";
                            $rrr = $this->db->query($sql);                                      
                        }                    
                    $n2 = $n1;
                    $i2 = $i1;
                }

                if ($m==0)
                    {
                        $sx = msg('No_changes');
                    }
                return($sx);
        }        
    function author_check_method_2($p=0)
        {
            $sql = "SELECT * FROM rdf_concept as R1
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        INNER JOIN rdf_class ON cc_class = id_c
                        INNER JOIN rdf_data ON R1.id_cc = d_r2
                        where R1.cc_use > 0 and c_class = 'Person' ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '';
            $m=0;
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $ida = $line['id_cc'];
                    $idt = $line['cc_use'];
                    $sx .= '<br>'.$line['n_name'];
                    
                    $sql = "update rdf_data set 
                                d_o = ".$line['id_d'].",
                                d_r2 = $idt
                                where id_d = ".$line['id_d'];
                    $rlt2 = $this->db->query($sql);
                    $m++;
                }
                if ($m==0)
                    {
                        $sx = msg('No_changes');
                    }                
            return($sx);            
        }    
    function author_check_remissive($p=0,$p2=0)
        {
            $p2 = round($p2);
            switch($p2)
                {
                case '0':
                    $sx = '<h1>Phase '.romano(1).'</h1>';
                    $sx .= $this->author_check_method_1($p);
                    $sx .= '<meta http-equiv="refresh" content="10;'.base_url(PATH.'tools/remissive/1').'">';
                    $sx .= '<br><br>wait phase '.romano(2);
                    break;
                case '1':
                    $sx = '<h1>Phase '.romano(2).'</h1>';
                    $sx .= $this->author_check_method_2($p);
                    $sx .= '<meta http-equiv="refresh" content="5;'.base_url(PATH.'tools/remissive/2').'">';
                    break;                
                default:
                    $sx = '<h1>FIM</h1>';
                    $sx .= date("Y-m-d H:i:s");
                    break;
                }
            $sx = '<div class="row"><div class="col-md-12">'.$sx.'</div></div>';
            return($sx);
                        
            $sql = "";

                            
        }
        
    function vv($id) {
        $this -> load -> model("frbr_core");
        return ($this -> frbr_core -> vv($id));
    }

    function show_v($i) {
        $filename = 'c/' . $i . '/name.oai';
        if (file_exists($filename)) {
            $t = load_file_local($filename);
        } else {
            $t = msg('not_registred') . ' - ' . $i;
        }
        return ($t);
    }

    function show_article($id) {
        $tela = '';
        $data = $this -> frbr_core -> le_data($id);
        $article = $dados['article'] = $data;
        $dados['social'] = $this->nets->twitter($data);
        $dados['social'] .= $this->nets->facebook($data);
        $dados['social'] .= $this->nets->google($data);
        $dados['social'] .= $this->nets->linked($data);
        $dados['social'] .= $this->nets->selected($data);
		
		$dados['cited'] = $this->nets->howcited($data);  

        $tela .= $this -> load -> view('brapci/view/article', $dados, true);
            
        //$tela .= $this -> frbr_core -> view_data($id);
        return ($tela);
    }
    
    function show_subject($id) {
        $this->load->model("thesa_api");
        $tela = '';
        $data = $this -> frbr_core -> le_data($id);
           
        $tela .= $this -> frbr_core -> view_data($id);
        $tela .= $this -> thesa_api -> update_thesa($data);
        return ($tela);
    }
    

    function article_create($dt) {
        $name = $dt['li_identifier'].'#'.strzero($dt['id_jnl'],5);
        $idf = $this -> frbr_core -> rdf_concept_create('Article', $name, '');
        /******************************************************************************/
        $prop = 'hasIssueOf';
        $this -> frbr_core -> set_propriety($dt['issue_uri'], $prop, $idf, 0);

        /***************************************************** SECTION ****************/
        if (isset($dt['issue']['section'])) {

            $section = trim($dt['issue']['section']);
			
            $id_section = $this -> frbr_core -> find($section, 'ArticleSection');

            $section_id = $dt['li_setSpec'];
            $term = $this -> frbr_core -> frbr_name($section_id);
            $this -> frbr_core -> set_propriety($id_section, $prop, 0, $term);

            /* ASSOCIA ARTIGO A SESSÃO */
            $prop = 'hasSectionOf';
            $this -> frbr_core -> set_propriety($idf, $prop, $id_section, 0);
        }
        /***************************************************** IDENTIFIER ****************/
        $prop = 'hasId';
        if (isset($dt['li_identifier'])) {
            $name = $dt['li_identifier'];
            $term = $this -> frbr_core -> frbr_name($name, '');
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }

        /***************************************************** JOURNAL ****************/
        $jnl = 'jnl:' . $dt['id_jnl'];
        $idj = $this -> frbr_core -> find($jnl);
        $prop = 'isPubishIn';
        $this -> frbr_core -> set_propriety($idf, $prop, $idj, 0);

        /****************************************************** SUBJECT *************/
        $prop = 'hasSubject';
        $tt = $dt['subject'];

        for ($r = 0; $r < count($tt); $r++) {
            $name = $tt[$r];
            $lang = substr($name, strpos($name, '@') + 1, strlen($name));
            $name = substr($name, 0, strpos($name, '@'));

            /* LANGUAGE */
            $lang = $this -> frbr_core -> language($lang);

            /* TERMO */
            $name = ucase($name);
            $name2 = $name;
            if ($lang == 'pt-BR') {
                $name2 = convert($name);
            }

            $idterm = $this -> frbr_core -> rdf_concept_create('Subject', $name2, '', $lang);
            $this -> frbr_core -> set_propriety($idf, $prop, $idterm, 0);
            if ($name2 != $name) {

                $prop2 = 'altLabel';
                $term = $this -> frbr_core -> frbr_name($name);
                $this -> frbr_core -> set_propriety($idterm, $prop2, 0, $term);
            }
        }

        /******************************************************************************/
        $prop = 'hasTitle';
        $tt = $dt['title'];
        for ($r = 0; $r < count($tt); $r++) {
            $line = $tt[$r];
            $title = $tt[$r]['title'];
            $lang = $tt[$r]['lang'];
            $term = $this -> frbr_core -> frbr_name($title, $lang);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        /******************************************************************************/
        $prop = 'hasAbstract';
        for ($r = 0; $r < count($dt['abstract']); $r++) {
            $title = $dt['abstract'][$r]['descript'];
            $lang = $dt['abstract'][$r]['lang'];
            if (strlen($title) > 10) {
                $term = $this -> frbr_core -> frbr_name($title, $lang);
                $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
            }
        }
        
        /**************************************************************** PAGINATION */
        $prop = 'hasPageStart';
        if ((isset($dt['issue']['start_page'])) and (round($dt['issue']['start_page']) > 0)) {
            $name = $dt['issue']['start_page'];
            $id_date = $this -> frbr_core -> rdf_concept_create('Number', $name, '');
            $this -> frbr_core -> set_propriety($idf, $prop, $id_date, 0);
        }
        $prop = 'hasPageEnd';
        if ((isset($dt['issue']['end_page'])) and (round($dt['issue']['start_page']) > 0)) {
            $name = $dt['issue']['end_page'];
            $id_date = $this -> frbr_core -> rdf_concept_create('Number', $name, '');
            $this -> frbr_core -> set_propriety($idf, $prop, $id_date, 0);
        }
        /*********************************************************************** DATE */
        $prop = 'dateOfAvailability';
        if (isset($dt['date'])) {
            for ($r = 0; $r < count($dt['date']); $r++) {
                $name = $dt['date'][$r];
                $id_date = $this -> frbr_core -> rdf_concept_create('Date', $name, '');
                $this -> frbr_core -> set_propriety($idf, $prop, $id_date, 0);
            }
        }

        /******************************************************************************/
        $prop = 'hasUrl';
        for ($r = 0; $r < count($dt['relation']); $r++) {
            $title = $dt['relation'][$r];
            $term = $this -> frbr_core -> frbr_name($title);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        /******************************************************************************/
        $prop = 'hasSource';
        for ($r = 0; $r < count($dt['source']); $r++) {
            $title = $dt['source'][$r]['name'];
            $lang = $dt['source'][$r]['lang'];
            $term = $this -> frbr_core -> frbr_name($title, $lang);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        /******************************************************************************/
        $prop = 'hasRegisterId';
        for ($r = 0; $r < count($dt['identifier']); $r++) {
            $title = $dt['identifier'][$r];
            $term = $this -> frbr_core -> frbr_name($title);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        /*
         //******************************************************************************/
        $this -> frbr_core -> check_language();
        return ($idf);
    }

    function issue($dt) {
        if (strlen(trim($dt['jnl_name_abrev'])) > 0) {
            $nm = trim($dt['jnl_name_abrev']);
        } else {
            $nm = trim($dt['jnl_name']);
        }

        $iss = $dt['issue'];
        /*******************************************/
        $issue = $iss['issue_id'];
		
        if (isset($iss['vol'])) {
            if (strlen($iss['vol']) > 0) { $nm .= ', v.' . $iss['vol'];
            }
        }
        if (isset($iss['nr'])) {
            if (strlen($iss['nr']) > 0) { $nm .= ', n.' . $iss['nr'];
            }
        }
        if (isset($iss['year'])) {
            if (strlen($iss['year']) > 0) { $nm .= ', ' . $iss['year'];
            }
        }
		
        /************** ISSUE id **********************************************/
        $name = 'ISSUE:' . UpperCaseSql($dt['issue']['issue_id']);
        $idf = $this -> frbr_core -> rdf_concept_create('Issue', $name, '');
        /* Label */
        $name = ($nm);
        $nm = convert($nm);
        $prop = 'altLabel';
        $term = $this -> frbr_core -> frbr_name($name);
        $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);

        /******************************************************************************/
        $jnl = 'jnl:' . $dt['id_jnl'];
        $jnl = $this -> frbr_core -> find($jnl);
        $prop = 'hasIssue';
        $this -> frbr_core -> set_propriety($idf, $prop, $jnl, 0);
        
        /*************** source **********************/
        if (isset($iss['sourcer'])) {
            $prop = 'altLabel';
            $term = $this -> frbr_core -> frbr_name($iss['sourcer']);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);            
        }
        
        /*************** Year ************************/
        if (isset($iss['year'])) {
            $prop = 'dateOfPublication';
            $ano = $this -> frbr_core -> rdf_concept_create('Date', $iss['year'], '');
            $this -> frbr_core -> set_propriety($idf, $prop, $ano, 0);
        }
        if (isset($iss['vol']) and (strlen(trim($iss['vol'])) > 0)) {
            $prop = 'hasPublicationVolume';
            $tem = $this -> frbr_core -> rdf_concept_create('PublicationVolume', 'v. ' . $iss['vol'], '');
            $this -> frbr_core -> set_propriety($idf, $prop, $tem, 0);
            //$term = $this -> frbr_core -> frbr_name('v. ' . $iss['vol']);
            //$this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        if (isset($iss['nr']) and (strlen(trim($iss['nr'])) > 0)) {
            $prop = 'hasPublicationNumber';
            $tem = $this -> frbr_core -> rdf_concept_create('PublicationNumber', 'n. ' . $iss['nr'], '');
            $this -> frbr_core -> set_propriety($idf, $prop, $tem, 0);
            //$prop = 'hasPublicationNumber';
            //$term = $this -> frbr_core -> frbr_name('n. ' . $iss['nr']);
            //$this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        $this->export->export_Issue_Single($idf);
        return ($idf);
    }

    function journal($dt) {
        $name = $dt['jnl_name'];
        if (strlen($name) > 0) {
            $idf = $this -> frbr_core -> rdf_concept_create('Journal', $name, '');
            $email = trim($dt['adminEmail']);
            if (strlen($email) > 0) {
                $term = $this -> frbr_core -> frbr_name($email);
                $this -> frbr_core -> set_propriety($idf, 'hasEmail', 0, $term);
            }
            $url = trim($dt['jnl_url']);
            if (strlen($url) > 0) {
                $term = $this -> frbr_core -> frbr_name($url);
                $this -> frbr_core -> set_propriety($idf, 'hasUrl', 0, $term);
            }
            $issn = trim($dt['jnl_issn']);
            if (strlen($issn) > 0) {
                $term = $this -> frbr_core -> frbr_name($issn);
                $this -> frbr_core -> set_propriety($idf, 'hasISSN', 0, $term);
            }
            $jnl = 'jnl:' . $dt['id_jnl'];
            $term = $this -> frbr_core -> frbr_name($jnl);
            $this -> frbr_core -> set_propriety($idf, 'hasIdRegister', 0, $term);
        }
        return (1);
    }

    function frad($dt = array(), $type = '') {
        if (count($dt) > 0) {
            $aff = '';
            if (isset($dt['name'])) {
                $aff = $dt['aff'];
                $name = $dt['name'];
                $email = $dt['email'];
                //echo '<h1>=>'.$aff.'</h1>';

                if (strlen($name) > 0) {
                    $idf = $this -> frbr_core -> rdf_concept_create('Person', $name, '');
                    if (strlen($email) > 0) {
                        $term = $this -> frbr_core -> frbr_name($email);
                        $this -> frbr_core -> set_propriety($idf, 'hasEmail', 0, $term);
                    }
                }

                if (strlen($aff) > 0) {
                    $id_aff = $this -> frbr_core -> rdf_concept_create('Corporate Body', $aff, '');
                    $this -> frbr_core -> set_propriety($idf, 'affiliatedWith', $id_aff, 0);
                }

            }
            return ($idf);
        }
    }

}

require ("Frbr_core.php");
?>
