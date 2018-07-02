<?php
class searchs extends CI_Model {
    function recover_reg($art, $t) {
        $t = substr($t, strpos($t, ']') + 1, strlen($t));
        $te = splitx(';', $t);
        for ($r = 0; $r < count($te); $r++) {
            if (isset($art[$te[$r]])) {
                $art[$te[$r]] = $art[$te[$r]] + 1;
            } else {
                $art[$te[$r]] = 1;
            }
        }
        return ($art);
    }

    function ajax_q($q = '') {
        $limit = 15;
        $n = lowercasesql($q);
        $n = convert($n);
        $rs = '';
        /* recupera arquivo de index */
        $fl = 'c/search_subject.search';
        if (is_file($fl)) {
            $f = load_file_local($fl);
            $ln = splitx('¢', $f);
            for ($r = 0; $r < count($ln); $r++) {
                /* busca termo */
                if (strpos($ln[$r], $n)) {
                    $limit--;
                    $na = (string)$ln[$r];
                    $na = substr($na,strpos($na,'[')+1,strlen($na));
                    $na = substr($na,0, strpos($na,']'));
                    $rs .= ',"' . $na . '"';
                    if ($limit <= 0) {
                        $r = count($ln);
                    }
                }
            }
        }
        echo '["' . $n . '*"' . $rs . ']';
    }

    function s($n, $t = '') {
        /* termo entre aspas */
        $i = 0;
        $s = '';
        $sp = ' ';
        for ($r = 0; $r < strlen($n); $r++) {
            $c = substr($n, $r, 1);
            if ($c == '"') {
                if ($i == 0) {
                    $i = 1;
                    $sp = '_';
                    $c = '';
                } else {
                    $i = 0;
                    $sp = ' ';
                    $c = '';
                }
            }
            /****************** espaco ****/
            if ($c == ' ') {
                $c = $sp;
            }
            $s .= $c;
        }
        $nc = '['.trim((string)$s).']';
        /* CONVERT ******************************************************/
        $recover = array();
        $key = array();
        $n = troca($s, ' ', ';') . ';';
        $nn = splitx(';', $n);
        for ($t = 0; $t < count($nn); $t++) {
            $recover[$t] = array();
            $nn[$t] = troca($nn[$t], '_', ' ');

            $nn[$t] = lowercasesql($nn[$t]);
            $nn[$t] = convert($nn[$t]);

            $na = '[' . trim($nn[$t]) . ']';
            /* METHOD 1 */
            if (strpos(' ' . $na, '[*') > 0) {
                $na = troca($na, '[*', '');
            }
            /* METHOD 2 */
            if (strpos($na, '*]')) {
                $na = troca($na, '*]', '');
            }

            $nn[$t] = $na;
            $key[$t] = array();
        }
        $tot_term = count($nn);
        /* recupera arquivo de index */
        $fl = 'c/search_subject.search';

        if (is_file($fl)) {
            $f = load_file_local($fl);
            $ln = splitx('¢', $f);

            $sx = '';
            $art = array();
            $tot = 0;
            for ($r = 0; $r < count($ln); $r++) {
                $tot = 0;
                /*************** busca os termos na linha */
                for ($t = 0; $t < count($nn); $t++) {
                    $term = $nn[$t];
                    if ((strpos(' ' . $ln[$r], $term) > 0) or (strpos(' '.$ln[$r],$nc))) {
                        //echo $ln[$r].'=>'.$term.'<br>';
                        $key[$t] = $this -> recover_reg($key[$t], $ln[$r]);
                    }
                }
            }

            /**************************************************** Busca Parte II ****************/
            for ($t = 0; $t < count($nn); $t++) {
                $art = array();
                foreach ($key[$t] as $nkey => $value) {
                    $filename = 'c/' . $nkey . '/works.nm';
                    if (is_file($filename)) {
                        $txt = load_file_local($filename);
                        $ln = splitx(';', $txt);
                        for ($r = 0; $r < count($ln); $r++) {
                            if (!isset($art[$ln[$r]])) {
                                $art[$ln[$r]] = 1;
                            } else {
                                $art[$ln[$r]] = $art[$ln[$r]] + 1;
                            }
                        }

                    }
                }
                $recover[$t] = $art;
            }
            /**************************************************** Busca Parte III *****************/
            $art = $recover[0];
            
            for ($t = 0; $t < (count($nn) - 1); $t++) {
                $art = $this -> func_and($art, $recover[$t]);
            }
            
            $sx = '';
            $sx .= '<div class="row">';
            $sx .= '<div class="col-md-12">';
            $sx .= 'Total de ' . count($art) . ' registros recuperados';
            $sx .= '</div></div>';

            $sx .= '<div class="row result">';
            foreach ($art as $key => $value) {
                $sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . 'img/cover/cover_issue_3477_pt_BR.jpg" class="img-fluid"></div>';
                $sx .= '<div class="col-11 " style="margin-bottom: 15px;">';
                $sx .= '<input type="checkbox"> ';
                $sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
                $sx .= $this -> frbr -> show_v($key);
                $sx .= '</a>';
                $sx .= '</div>';
            }
            $sx .= '</div>';
        } else {
            $sx = '
                <div class="alert alert-warning" role="alert">
                  ERRO #1001! The Search index file not Found
                </div>';
        }
        return ($sx);
    }

    function func_and($g1, $g2) {
        $g = array();
        foreach ($g1 as $key => $value) {
            if (isset($g2[$key])) {
                $g[$key] = $value + $g2[$key];
            }
        }
        return ($g);
    }
}
?>
