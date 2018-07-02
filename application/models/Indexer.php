<?php
class indexer extends CI_model {

    function indexing_word($n='', $lang='', $article_id=0) {
        $s = '';
        $n = lowercasesql($n);
        for ($r = 0; $r < strlen($n); $r++) {
            $c = substr($n, $r, 1);
            $co = ord($c);
            switch($c) {
                case '-' :
                    $c = ' ';
                    break;
                case '?' :
                    $c = ' ';
                    break;
                case '!' :
                    $c = ' ';
                    break;
                case '@' :
                    $c = ' ';
                    break;
                case '#' :
                    $c = ' ';
                    break;
                case ':' :
                    $c = ' ';
                    break;
                case '.' :
                    $c = ' ';
                    break;
                case ';' :
                    $c = ' ';
                    break;
                case ':' :
                    $c = ' ';
                    break;
                case ',' :
                    $c = ' ';
                    break;
                case '/' :
                    $c = ' ';
                    break;
                case '(' :
                    $c = ' ';
                    break;
                case ')' :
                    $c = ' ';
                    break;
                case '"' :
                    $c = ' ';
                    break;
            }
            $s .= $c;
        }

        /* WORD */
        $s = troca($s, ' ', ';') . ';';
        $wds = splitx(';', $s);
        for ($r = 0; $r < count($wds); $r++) {
            $name2 = $wds[$r];
            $name = $wds[$r];
            $name = $this -> searchs -> ucwords($name);
            $name = convert($name);

            $prop = 'hasContent';
            $idf = $this -> frbr_core -> rdf_concept_create('Word', $name, '', $lang);
            if ($name2 != $name) {
                $idw = $this -> frbr_core -> frbr_name($name2, $lang);
                $this -> frbr_core -> set_propriety($idf, $prop, 0, $idw);
            }
            $this -> frbr_core -> set_propriety($article_id, $prop, $idf, 0);
        }
        return(1);
    }

    function indexing($dt) {

        $s = '';
        $article_id = $dt['article_id'];
        /****************************** TITULO ***************************************/
        $tit = $dt['title'];
        for ($t = 0; $t < count($tit); $t++) {
            $n = (string)$tit[$t]['title'];
            $n = $this -> frbr_core -> utf8_detect($n);
            $lang = (string)$tit[$t]['lang'];
            $this -> indexing_word($n, $lang,$article_id);
        }
       
        /****************************** ASSUNTO *************************************/
        $tit = $dt['subject'];
        for ($t = 0; $t < count($tit); $t++) {
            $n = $tit[$t];
            $n1 = substr($n,0,strpos($n,'@'));
            $n2 = substr($n,strpos($n,'@')+1,strlen($n));
            $n = $this -> frbr_core -> utf8_detect($n1);
            $lang = $n2;
            $this -> indexing_word($n, $lang, $article_id);
        }
        
        /****************************** TITULO ***************************************/
        $tit = $dt['abstract'];
        for ($t = 0; $t < count($tit); $t++) {
            $n = (string)$tit[$t]['descript'];
            $n = $this -> frbr_core -> utf8_detect($n);
            $lang = (string)$tit[$t]['lang'];
            $this -> indexing_word($n, $lang,$article_id);
        }        
    }

}
?>
