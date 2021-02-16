<?php
defined("BASEPATH") OR exit("No direct script access allowed");

/**
* CodeIgniter Form Helpers
*
* @package     CodeIgniter
* @subpackage  OAI
* @category    OAI-PHM-SCIELO
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/CodIgniter
* @version     v0.21.02.16
*/

class oai_pmh_scielo extends CI_model {
    
    function getRecordScielo_oai_dc($id = 0, $dt) {
        $sql = "select * from source_listidentifier where id_li = $id";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $jnl = $line['li_jnl'];

            $data = $this -> sources -> le($jnl);
            //$url = $this -> oai_pmh -> oai_url($data, 'GetRecordScielo') . troca($line['li_identifier'], 'oai:scielo:', '');
            $url = 'http://www.scielo.br/scieloOrg/php/articleXML.php?pid='.troca($line['li_identifier'], 'oai:scielo:', '').'&lang=en';
            //$url = 'http://www.scielo.br/scieloOrg/php/articleXML.php?pid=S0103-37862005000200006&lang=en';
            $cnt = $this -> oai_pmh -> readfile($url);
            $cnt2 = $cnt;
            
            //$cnt = troca($cnt,'<![CDATA[ ','');
            //$cnt = troca($cnt,'<![CDATA[','');
            //$cnt = troca($cnt,' ]]>','');
            //$cnt = troca($cnt,']]>','');
            //$cnt = html_entity_decode($cnt);
            //$cnt = utf8_encode($cnt);
            //echo '<pre>'.$cnt.'</pre>';

            $cnt = troca($cnt, 'oai_dc:', '');
            $cnt = troca($cnt, 'xml:', '');
            $cnt = troca($cnt, 'xlink:', '');

            $cnt = troca($cnt, 'article-meta', 'article_meta');
            $cnt = troca($cnt, 'journal-meta', 'journal_meta');
            $cnt = troca($cnt, 'journal-title', 'journal_title');
            $cnt = troca($cnt, 'article-id', 'article_id');
            $cnt = troca($cnt, 'title-group', 'title_group');
            $cnt = troca($cnt, 'article-title', 'article_title');
            $cnt = troca($cnt, 'contrib-group', 'contrib_group');
            $cnt = troca($cnt, 'given-names', 'given_names');
            $cnt = troca($cnt, 'kwd-group', 'kwd_group');
            $cnt = troca($cnt, 'pub-date', 'pub_date');
            $cnt = troca($cnt, 'self-uri', 'self_uri');

            $xml = simplexml_load_string($cnt);
            
            //echo '<pre>';
            //print_r($xml);
            //echo '</pre>';

            /*************************************************** relation **********************/
            $rcn = $xml -> front -> article_meta;
            $identifier = $this -> oai_pmh -> xml_values($rcn -> article_id);
            $dt['identifier'] = $identifier;

            /******************************************************* TITLE **********************/
            $is = $xml -> front -> article_meta -> title_group -> article_title;
            $title = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$is[$r];
                $lang = '';
                foreach ($is[$r] -> attributes() as $atrib => $value) {
                    if ($atrib == 'lang') { $lang = (string)$value;
                    }
                }
                array_push($title, array('title' => $tit, 'lang' => $lang));
            }
            $dt['title'] = $title;

            /******************************************************* AFILIATION *******************/
            $aff = array();
            $is = $xml -> front -> article_meta -> aff;

            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$is[$r] -> institution;
                if (substr($tit, 0, 1) == ',') { $tit = trim(substr($tit, 1, strlen($tit)));
                }
                $tit = NBR_autor($tit, 3);
                foreach ($is[$r] -> attributes() as $atrib => $value) {
                    //echo '<br>'.$atrib.'=='.$value;
                    if ($atrib == 'id') { $aff[(string)$value] = $tit;
                    }
                }
            }
            /******************************************************* CREATOR *******************/
            $is = $xml -> front -> article_meta -> contrib_group;
            $author = array();

            for ($r = 0; $r < count($is -> contrib); $r++) {
                $tit = (string)$is -> contrib[$r] -> name -> given_names;
                $tit = trim(trim($tit) . ' ' . (string)$is -> contrib[$r] -> name -> surname);
                $af = '';
                foreach ($is -> contrib[$r]->xref -> attributes() as $atrib => $value) {
                    if ($atrib == 'rid') { $af = trim((string)$value);
                    }
                }
                $tit = NBR_autor($tit, 1);
                array_push($author, array('name' => $tit, 'type' => 'author', 'aff' => '', 'email' => ''));
            }
            $dt['authors'] = $author;

            /******************************************************* SUBJECT *******************/
            $is = $xml -> front -> article_meta -> kwd_group;
            $key = '';
            for ($r = 0; $r < count($is -> kwd); $r++) {
                $tit = (string)$is -> kwd[$r];
                $tit = lowercase($tit);
                $tit = UpperCase(substr($tit, 0, 1)) . substr($tit, 1, strlen($tit));
                $lang = 'pt';

                foreach ($is -> kwd[$r]-> attributes() as $atrib => $value) {
                    if ($atrib == 'lng') { $lang = trim((string)$value);
                    }
                }
                $key .= trim($tit) . '@' . $lang . '; ';
            }
            $subject = splitx(';', $key);
            $dt['subject'] = $subject;

            /*************************************************** description ********************/
            $is = $xml -> front -> article_meta -> abstract;
            $abstract = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = trim((string)$is[$r] -> p);
                $lang = '';
                foreach ($is[$r] -> attributes() as $atrib => $value) {
                    if ($atrib == 'lang') { $lang = (string)$value;
                    }
                }
                array_push($abstract, array('descript' => $tit, 'lang' => $lang));
            }
            $dt['abstract'] = $abstract;

            /*************************************************** issue ********************/
            $is = $xml -> front -> article_meta -> pub_date;

            $year = (string)$is -> year;
            $month = (string)$is -> month;
            $day = (string)$is -> day;

            $date = strzero($year, 4) . '-' . strzero($month, 2) . '-' . strzero($day, 2);

            /******************************************************* Source *******************/
            $is = $xml -> front -> journal_meta;
            $source = (string)$is -> journal_title;

            /******************************************************* setSpec *******************/
            $is = $xml -> front -> article_meta;
            $vol = (string)$is -> volume;
            $nr = (string)$is -> numero;

            if (strlen($vol) > 0) {
                $source .= ', v. ' . $vol;
            }
            if (strlen($nr) > 0) {
                $source .= ', n. ' . $nr;
            }
            if (strlen($year) > 0) {
                $source .= ', ' . $year . '.';
            }

            /******************************************************* setSpec *******************/
            $section = "Artigo";

            $issue_id = 'jnl:' . strzero($line['li_jnl'], 5) . '-' . $year . '-' . $vol . '-' . $nr;

            $iss['year'] = $year;
            $iss['month'] = $month;
            $iss['day'] = $day;

            $iss['start_page'] = (string)$is -> fpage;
            $iss['end_page'] = (string)$is -> lpage;
            $iss['section'] = $section;
            $iss['issue_id'] = $issue_id;
            $iss['vol'] = $vol;
            $iss['nr'] = $nr;
            $src = array();
            $src[0]['name'] = $source;
            $src[0]['lang'] = 'pt';
            
            $iss['sourcer'] = array($src); 
            $dt['issue'] = $iss;
            $dt['source'] = $src; 

            /*************************************************** source ************************/
            $relation = array();
            $is = $xml -> front -> article_meta -> self_uri;
            
            for ($r = 0; $r < count($is); $r++) {
                $iss = $is[$r];
                foreach ($iss -> attributes() as $atrib => $value) {
                    $vlr = (string)$value;                    
                    if ($atrib == 'href') { array_push($relation,(string)$value);
                    }
                }
            }
            
            //http://www.scielo.br/scieloOrg/php/articleXML.php?pid=S0103-37862005000200006&lang=en
            $dt['subject'] = $subject;
            $dt['identifier'] = $identifier;
            $dt['relation'] = $relation;
            $dt['date'] = $date;
        }
        return ($dt);
    }
}
