<?php
class frbr extends CI_model {
    function vv($id) {
        $this -> load -> model("frbr_core");
        return ($this -> frbr_core -> vv($id));
    }

    function article_create($dt) {
        $name = $dt['li_identifier'];
        $idf = $this -> frbr_core -> rdf_concept_create('Article', $name, '');
        /******************************************************************************/
        $prop = 'hasIssueOf';
        $this -> frbr_core -> set_propriety($dt['issue_uri'], $prop, $idf, 0);

        /***************************************************** SECTION ****************/
        if (isset($dt['issue']['section']))
            {
                $section = trim($dt['issue']['section']);
                $id_section = $this -> frbr_core -> rdf_concept_create('ArticleSection', $section, '');
                
                $section_id = $dt['li_setSpec'];
                $term = $this -> frbr_core -> frbr_name($section_id);
                $this -> frbr_core -> set_propriety($id_section, $prop, 0, $term);
                
                /* ASSOCIA ARTIGO A SESSÃO */
                $prop = 'hasSectionOf';
                $this -> frbr_core -> set_propriety($idf, $prop, $id_section, 0);
            }

        /***************************************************** JOURNAL ****************/
        $jnl = 'jnl:'.$dt['id_jnl'];
        $idj = $this->frbr_core->find($jnl);
        $prop = 'isPubishIn';
        $this -> frbr_core -> set_propriety($idf, $prop, $idj, 0);
        
                
        /******************************************************************************/
        $prop = 'hasSubject';
        $tt = $dt['subject'];

        for ($r = 0; $r < count($tt); $r++) {
            $name = $tt[$r];
            $lang = substr($name,strpos($name,'@')+1,strlen($name));
            $name = substr($name,0,strpos($name,'@'));
            /* TERMO */
            $idterm = $this -> frbr_core -> rdf_concept_create('Subject', $name, '', $lang);            
            $this -> frbr_core -> set_propriety($idf, $prop, $idterm, 0);
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

            $term = $this -> frbr_core -> frbr_name($title, $lang);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        } 
        /*********************************************************************** DATE */
        $prop = 'dateOfAvailability';
        if (isset($dt['date']))
            {
                for ($r=0;$r < count($dt['date']);$r++)
                    {                
                        $name = $dt['date'][$r];
                        $id_date = $this -> frbr_core -> rdf_concept_create('Date', $name,'');
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
            $term = $this -> frbr_core -> frbr_name($title,$lang);
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
        $name = 'Jnl: '.$dt['id_jnl'].' Issue:'.$dt['issue']['issue_id'];        
        $idf = $this -> frbr_core -> rdf_concept_create('Issue', $name, '');
        /* Label */
        $name = $nm;
        $prop = 'altLabel';
        $term = $this -> frbr_core -> frbr_name($name);
        $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);

        /******************************************************************************/
        $jnl = 'jnl:' . $dt['id_jnl'];
        $jnl = $this -> frbr_core->find($jnl);
        $prop = 'hasIssue';
        $this -> frbr_core -> set_propriety($idf, $prop, $jnl, 0);

        /*************** Year ************************/
        if (isset($iss['year'])) {
            $prop = 'dateOfPublication';
            $ano = $this -> frbr_core -> rdf_concept_create('Date', $iss['year'], '');
            $this -> frbr_core -> set_propriety($idf, $prop, $ano, 0);
        }
        if (isset($iss['vol']) and (strlen(trim($iss['vol'])) > 0)) {
            $prop = 'hasVolumeNumber';
            $term = $this -> frbr_core -> frbr_name('v. '.$iss['vol']);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }
        if (isset($iss['nr']) and (strlen(trim($iss['nr'])) > 0)) {
            $prop = 'hasVolumeNumber';
            $term = $this -> frbr_core -> frbr_name('n. '.$iss['nr']);
            $this -> frbr_core -> set_propriety($idf, $prop, 0, $term);
        }        
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
