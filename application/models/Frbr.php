<?php
class frbr extends CI_model {
    function api_thesa($id = 0) {

    }

    function author_check_method_1($p = 0) {
        $f = $this -> frbr_core -> find_class('Person');
        $sql = "select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                           N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
                            FROM rdf_concept as C1
                            INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                            LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                            LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                            where C1.cc_class = " . $f . " and C1.cc_use = 0
                            ORDER BY N1.n_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $n2 = '';
        $n0 = '';
        $i2 = 0;
        $sx = '';
        $m = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $n0 = trim($line['n_name']);
            $n1 = trim($line['n_name']);
            $n1 = troca($n1, ' de ', ' ');
            $n1 = troca($n1, ' da ', ' ');
            $n1 = troca($n1, ' do ', ' ');
            $n1 = troca($n1, ' dos ', ' ');
            $nf = substr($n1, strlen($n1) - 3, 3);
            if (($nf == ' de') or ($nf == ' da') or ($nf == ' do') or ($nf == ' dos')) {
                $n1 = trim(substr($n1, 0, strlen($n1) - 3));
            }
            $n1 = trim($n1);
            $i1 = $line['id_cc'];

            if ($n1 == $n2) {
                $m++;
                $sx .= '<br>' . $n1 . '(' . $i1 . ')';
                $sx .= '--' . $n2 . '(' . $i2 . ')';
                $sx .= ' --> <b><font color="green">Igual</font></b>';
                $sql = "update rdf_concept set cc_use = $i2 where id_cc = $i1";
                $rrr = $this -> db -> query($sql);
            }
            $n2 = $n1;
            $i2 = $i1;
        }

        if ($m == 0) {
            $sx = msg('No_changes');
        }
        return ($sx);
    }

    function author_check_method_2($p = 0, $class = "Person") {
        $sql = "SELECT * FROM rdf_concept as R1
                        INNER JOIN rdf_name ON cc_pref_term = id_n
                        INNER JOIN rdf_class ON cc_class = id_c
                        INNER JOIN rdf_data ON R1.id_cc = d_r2
                        where R1.cc_use > 0 and c_class = '$class' ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';
        $m = 0;
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $ida = $line['id_cc'];
            $idt = $line['cc_use'];
            $sx .= '<br>' . $line['n_name'];

            $sql = "update rdf_data set 
                                d_o = " . $line['id_d'] . ",
                                d_r2 = $idt,
                                d_update = 1
                                where id_d = " . $line['id_d'];
            $rlt2 = $this -> db -> query($sql);
            $m++;
        }
        if ($m == 0) {
            $sx = msg('No_changes');
        }
        return ($sx);
    }

    function author_check_remissive($p = 0, $p2 = 0) {
        $p2 = round($p2);
        switch($p2) {
            case '0' :
                $sx = '<h1>Phase ' . romano(1) . '</h1>';
                $sx .= $this -> author_check_method_1($p);
                $sx .= '<meta http-equiv="refresh" content="10;' . base_url(PATH . 'tools/remissive/1') . '">';
                $sx .= '<br><br>wait phase ' . romano(2);
                break;
            case '1' :
                $sx = '<h1>Phase ' . romano(2) . '</h1>';
                $sx .= $this -> author_check_method_2($p);
                $sx .= '<meta http-equiv="refresh" content="5;' . base_url(PATH . 'tools/remissive/2') . '">';
                break;
            case '2' :
                $sx = '<h1>Phase ' . romano(3) . '</h1>';
                $sx .= $this -> author_check_method_2($p, 'CorporateBody');
                $sx .= '<meta http-equiv="refresh" content="5;' . base_url(PATH . 'tools/remissive/3') . '">';
                break;
            default :
                $sx = '<h1>FIM</h1>';
                $sx .= date("Y-m-d H:i:s");
                break;
        }
        $sx = '<div class="row"><div class="col-md-12">' . $sx . '</div></div>';
        return ($sx);

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

    function show_patent($id) {
        $tela = '';
        $data = $this -> frbr_core -> le_data($id);
        $article = $dados['article'] = $data;

        $dados['social'] = $this -> nets -> twitter($data);
        $dados['social'] .= $this -> nets -> facebook($data);
        $dados['social'] .= $this -> nets -> google($data);
        $dados['social'] .= $this -> nets -> linked($data);
        $dados['social'] .= $this -> nets -> selected($data);

        $dados['cited'] = $this -> nets -> howcited($data);

        $tela .= $this -> load -> view('brapci/view/patent', $dados, true);

        //$tela .= $this -> frbr_core -> view_data($id);
        return ($tela);

    }
    
    function remove_concept($id) {
        $sql = "update rdf_data set
                                d_r1 = ((-1) * d_r1) ,
                                d_r2 = ((-1) * d_r2 ),
                                d_p  = ((-1) * d_p) 
                                where d_r1 = $id or d_r2 = $id";
        $rlt = $this -> db -> query($sql);
        redirect(base_url(PATH));
        return ("");
    }    
    
    function form_article($id)
        {
            $lg = 'pt-BR:Portugues&en:Inglês&es:Espanhol&fr:Francês';
            $form = new form;
            $cp = array();
            array_push($cp,array('$H8','','',false,true));    
            array_push($cp,array('$A4','',msg('Authors'),FALSE,true));        
            array_push($cp,array('$T80:8','',msg('hasAuthors'),true,true));
            array_push($cp,array('$M','',msg('hasAuthoresInfo'),false,false));
            
            array_push($cp,array('$A4','',msg('MainTitle'),FALSE,true));
            array_push($cp,array('$T80:4','',msg('hasTitle'),true,true));
            array_push($cp,array('$T80:8','',msg('hasAbstract1'),FALSE,true));
            array_push($cp,array('$S100','',msg('hasKeyword1'),FALSE,true));
            array_push($cp,array('$O '.$lg,'',msg('hasLanguage'),true,true));
            
            array_push($cp,array('$A4','',msg('SencondTitle'),FALSE,true));
            array_push($cp,array('$T80:4','',msg('hasTitle'),true,true));            
            array_push($cp,array('$T80:8','',msg('hasAbstract'),FALSE,true));
            array_push($cp,array('$S100','',msg('hasKeyword'),FALSE,true));
            array_push($cp,array('$O '.$lg,'',msg('hasLanguage'),true,true));
            
            array_push($cp,array('$A4','',msg('Pages'),FALSE,true));
            array_push($cp,array('$S20','',msg('pageStart'),FALSE,true));
            array_push($cp,array('$S20','',msg('pageStop'),FALSE,true));
            
            array_push($cp,array('$O jnl-artile:Artigo&jnl-editorial:Editorial','',msg('session'),true,true));
            
            array_push($cp,array('$B8','',msg('submit'),false,true));
            $tela = $form->editar($cp,'');
            
            if ($form->saved > 0)
                {
                    $ida = 0;
                    $title = LowerCase(get("dd5"));
                    $title = Uppercase(substr($title,0,1)).substr($title,1,strlen($title));
                    $title2 = LowerCase(get("dd10"));
                    $title2 = Uppercase(substr($title2,0,1)).substr($title2,1,strlen($title2));
                    
                    $aut = troca(get("dd2"),chr(13),';');
                    $aut = troca($aut,chr(10),';');
                    $aut = troca($aut,'  ',' ');
                    $aut = splitx(';',$aut);
                    for ($r=0;$r < count($aut);$r++)
                        {
                            $aut[$r] = nbr_autor($aut[$r],1);
                        }                                      
                        
                    /* ABSTRACT */
                    $abs1 = troca(get("dd6"),chr(10),'');
                    $abs1 = troca($abs1,chr(13),' ');
                    $abs1 = troca($abs1,'  ',' ');
                    $key1 = get("dd7");
                    $lng1 = get("dd8");
                    
                    $abs2 = troca(get("dd11"),chr(10),'');
                    $abs2 = troca($abs2,chr(13),' ');
                    $abs2 = troca($abs2,'  ',' ');
                    $key2 = get("dd12");
                    $lng2 = get("dd13");    
                    
                    /* KEYWORD */                
                    $pagi = get("dd15");
                    $pagf = get("dd16");
                    $session = get("dd17");
                    
                    /*********************ok **********/
                    
                    $artid = 'issue:'.$id.'-'.date("Ymdhis");   
                    //$artid = 'issue:'.$id.'-'.date("Ymdh"); /* remover depois do teste */
                                                         
                    $ida = $this->frbr_core->rdf_concept_create('Article',$artid);

                    $prop = 'hasId';
                    $idan = $this->frbr_core->frbr_name($artid);
                    $this->frbr_core->set_propriety($ida,$prop,0,$idan);                
                    
                    /********* Issue ***************************/
                    $prop = 'hasIssueOf';
                    $this->frbr_core->set_propriety($ida,$prop,$id,0);
                    
                    /*******************************************/
                    $prop = 'isPubishIn';
                    
                    /* Session **********************************/
                    $session = get("dd17");
                    
                    /********** Section *************************/                           
                    $class = 'ArticleSection'; 
                    $ids = $this->frbr_core->rdf_concept_create($class,$session);
                    
                    $prop = 'hasSectionOf';                     
                    $this->frbr_core->set_propriety($ida,$prop,$ids,0);
                            
                    /* SOURCE */
                    $prop = 'hasSource';
                    $dt = $this->frbr_core->le_data($id);
                    $source = 'não localizado - '.$pagi.'-'.$pagf;
                    $source_id = 0;
                    for ($r=0;$r < count($dt);$r++)
                        {
                            if ($dt[$r]['c_class']=='altLabel')
                                {
                                    $source = $dt[$r]['n_name'].'; '.$pagi.'-'.$pagf;                
                                }
                            if ($dt[$r]['c_class']=='hasIssue')
                                {
                                    $source_id = $dt[$r]['d_r2'];                
                                }                                
                        }
                    $prop = 'hasSource';
                    $idan = $this->frbr_core->frbr_name($source);
                    $this->frbr_core->set_propriety($ida,$prop,0,$idan);
                    
                    if ($source_id > 0)
                        {
                            $prop = 'isPubishIn';
                            $this->frbr_core->set_propriety($ida,$prop,$source_id,0);                
                        }
                        
    
                    
                    /********* Abstract 1 **********************/
                    if (strlen($title) > 0)
                        {
                            $prop = 'hasTitle';
                            $idan = $this->frbr_core->frbr_name($title,$lng1);
                            $this->frbr_core->set_propriety($ida,$prop,0,$idan);                            
                        }
                    if (strlen($title2) > 0)
                        {
                            $prop = 'hasTitleAlternative';
                            $idan = $this->frbr_core->frbr_name($title2,$lng2);
                            $this->frbr_core->set_propriety($ida,$prop,0,$idan);                            
                        }                    
                    
                    /********* Abstract 1 **********************/
                    if (strlen($abs1) > 0)
                        {
                            $prop = 'hasAbstract';
                            $idan = $this->frbr_core->frbr_name($abs1,$lng1);
                            $this->frbr_core->set_propriety($ida,$prop,0,$idan);                            
                        }
                    if (strlen($abs2) > 0)
                        {
                            $prop = 'hasAbstract';
                            $idan = $this->frbr_core->frbr_name($abs2,$lng2);
                            $this->frbr_core->set_propriety($ida,$prop,0,$idan);                            
                        } 
                    /******** Pages ******************************/
                    if (strlen($pagi) > 0)
                        {
                            $prop = 'hasPageStart';
                            $idp = $this->frbr_core->rdf_concept_create('Number',$pagi);
                            $this->frbr_core->set_propriety($ida,$prop,$idp,0);                            
                        }
                    if (strlen($pagf) > 0)
                        {
                            $prop = 'hasPageEnd';
                            $idp = $this->frbr_core->rdf_concept_create('Number',$pagf);
                            $this->frbr_core->set_propriety($ida,$prop,$idp,0);                            
                        }                                                               
                    /****** Key words *********************************/
                        $prop = 'hasSubject';
                        $kys = $key1;
                        $lng = $lng1;
                        $kys = troca($kys,'.',';');';';
                        $kys = troca($kys,',',';');';';
                        $kys = splitx(';',$kys);
                        for ($r=0;$r < count($kys);$r++)
                            {
                                $keyw = $kys[$r];
                                $idp = $this->frbr_core->rdf_concept_create('Subject',$keyw,'',$lng); 
                                $this->frbr_core->set_propriety($ida,$prop,$idp,0);  
                            }
                        $prop = 'hasSubject';
                        $kys = $key2;
                        $lng = $lng2;
                        $kys = troca($kys,'.',';');';';
                        $kys = troca($kys,',',';');';';
                        $kys = splitx(';',$kys);
                        for ($r=0;$r < count($kys);$r++)
                            {
                                $keyw = $kys[$r];
                                $idp = $this->frbr_core->rdf_concept_create('Subject',$keyw,'',$lng); 
                                $this->frbr_core->set_propriety($ida,$prop,$idp,0);  
                            }                            
                       /****** Author *********************************/
                        $prop = 'hasAuthor';
                        for ($r=0;$r < count($aut);$r++)
                            {
                                $author = $aut[$r];
                                $idp = $this->frbr_core->rdf_concept_create('Person',$author); 
                                $this->frbr_core->set_propriety($ida,$prop,$idp,0);  
                            }           
                       redirect(base_url(PATH.'a/'.$ida));                    
                }
            return($tela);
        }
    
    function show_issue($id)
        {
        $tela = '';
        $data = $this -> frbr_core -> le_data($id);
        $dados['issue'] = $data;
        $tela .= $this -> load -> view('brapci/view/issue', $dados, true);

        //$tela .= $this -> frbr_core -> view_data($id);
        return ($tela);            
        }

    function show_article($id) {
        $tela = '';
        $data = $this -> frbr_core -> le_data($id);
        $article = $dados['article'] = $data;
        $dados['social'] = $this -> nets -> twitter($data);
        $dados['social'] .= $this -> nets -> facebook($data);
        $dados['social'] .= $this -> nets -> google($data);
        $dados['social'] .= $this -> nets -> linked($data);
        $dados['social'] .= $this -> nets -> selected($data);

        $dados['cited'] = $this -> nets -> howcited($data);
        $this->handle->hdl_register($id,'http://www.brapci.inf.br/index.php/res/v/'.$id);

        $tela .= $this -> load -> view('brapci/view/article', $dados, true);

        //$tela .= $this -> frbr_core -> view_data($id);
        return ($tela);
    }

    function show_subject($id) {
        $this -> load -> model("thesa_api");
        $tela = '';
        $data = $this -> frbr_core -> le_data($id);

        $tela .= $this -> frbr_core -> view_data($id);
        $tela .= $this -> thesa_api -> update_thesa($data);
        return ($tela);
    }

    function article_create($dt) {
        $name = $dt['li_identifier'] . '#' . strzero($dt['id_jnl'], 5);
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
        $idj = $this -> frbr_core -> find($jnl, 'hasIdRegister');
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
        echo '<pre>';
        print_r($dt);
        echo '</pre>';
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

    function issue_new($id) {
        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', '', '', false, true));
        array_push($cp, array('$S40', '', 'Volume', false, true));
        array_push($cp, array('$S40', '', 'Nr.', false, true));
        array_push($cp, array('$[1950-' . (date("Y") + 1) . ']', '', 'Ano', false, true));
        array_push($cp, array('$S100', '', 'Temática do fascículo', false, true));
        array_push($cp, array('$O 1:' . msg('yes'), '', 'Confirma', true, true));
        $sx = $form -> editar($cp, '');
        if (($form -> saved > 0) and (sonumero($id) > 0)) {
            $dt = $this -> sources -> le($id);
            $dt['issue']['issue_id'] = 'jnl:'.$id.':'.get("dd1").':'.get("dd2").':'.get("dd3");
            $dt['issue']['vol'] = get("dd1");
            $dt['issue']['nr'] = get("dd2");
            $dt['issue']['year'] = get("dd3");
            if (strlen(get("dd4")) > 0) {
                $dt['issue']['sourcer'] = get("dd4");
            }
            $id_issue = $this -> issue($dt);       
            
            $sx .= '<script>wclose();</script>';
        }
        return ($sx);
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

        $z = 0;

        /************** ISSUE id **********************************************/
        $name = 'ISSUE:' . UpperCaseSql($issue);
        $idf = $this -> frbr_core -> rdf_concept_create('Issue', $name, '');

        /* Associa com a revista ****/        
        $prop = 'hasIssue';
        if ($dt['jnl_frbr'] == 0)
            {
                echo msg('erro_801');
                echo '<hr><pre>';
                print_r($dt);
                echo '</pre>';
                exit;
            }
        

        /* Label */
        $name = ($nm);
        $nm = convert($nm);
        $prop = 'altLabel';
        $term = $this -> frbr_core -> frbr_name($name);
        $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);

        /******************************************************************************/
        $jnl = 'jnl:' . $dt['id_jnl'];
        $jnl = $this -> frbr_core -> find($jnl, 'hasIdRegister');
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

        $this -> export -> export_Issue_Single($idf);
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

                if (strlen($name) > 0) {
                    $idf = $this -> frbr_core -> rdf_concept_create('Person', $name, '');
                    if (strlen($email) > 0) {
                        $term = $this -> frbr_core -> frbr_name($email);
                        $this -> frbr_core -> set_propriety($idf, 'hasEmail', 0, $term);
                    }
                }

                if (strlen($aff) > 0) {
                    $id_aff = $this -> frbr_core -> rdf_concept_create('CorporateBody', $aff, '');
                    $this -> frbr_core -> set_propriety($idf, 'affiliatedWith', $id_aff, 0);
                }

            }
            return ($idf);
        }
    }
    
    function journal_update_id_frbr($id,$idf)
        {
            if ($idf > 0)
                {
                    $sql = "update source_source set jnl_frbr = $idf where id_jnl = $id";
                    $rlt = $this->db->query($sql);
                    return(1);
                }
            return(0);
        }

    function journal_manual($id, $idf) {
        $jnl = 'jnl:' . $id;
        $idj = $this -> frbr_core -> find($jnl, 'hasIdRegister');
        $sx = '';
        if ($idj == 0) {
            $sx = '<a href="' . base_url(PATH . 'jnl/' . $id . '?act=register') . '">' . msg('jnl_register_journal') . '</a>';
        } else {
            if ($idf == 0)
                {
                   $this->journal_update_id_frbr($id,$idj);
                }
        }
        if ((get("act")) and (perfil("#ADM"))) {
            $sx .= '<h3>' . msg('registred') . '</h3>';
            $dt = $this -> sources -> le($id);
            if (count($dt) > 0) {
                $name = trim($dt['jnl_name']);
            }
            $class = 'Journal';
            $id_jnl = $this -> frbr_core -> rdf_concept_create($class, $name, '');
            /* ID JNL */
            $jnl = 'jnl:' . $dt['id_jnl'];
            $prop = 'hasIdRegister';
            $term = $this -> frbr_core -> frbr_name($jnl);
            $this -> frbr_core -> set_propriety($id_jnl, $prop, 0, $term);

            if (strlen(trim($dt['jnl_url'])) > 0) {
                $prop = 'hasUrl';
                $term = $this -> frbr_core -> frbr_name(trim($dt['jnl_url']));
                $this -> frbr_core -> set_propriety($id_jnl, $prop, 0, $term);
            }
            $this->journal_update_id_frbr($id,$id_jnl);
            $newURL =base_url(PATH.'jnl/'.$id);
            header('Location: '.$newURL);
            exit;
        }
        return ($sx);
    }

}

//require ("Frbr_core.php");
?>
