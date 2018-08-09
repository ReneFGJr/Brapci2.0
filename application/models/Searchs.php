<?php
class searchs extends CI_Model {
    var $sz = 20;
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
                    $na = substr($na, strpos($na, '[') + 1, strlen($na));
                    $na = substr($na, 0, strpos($na, ']'));
                    $rs .= ',"' . $na . '"';
                    if ($limit <= 0) {
                        $r = count($ln);
                    }
                }
            }
        }
        echo '["' . $n . '*"' . $rs . ']';
    }

    function pages($n = 0, $t = 0) {
        $sz = $this->sz;
        $pgs = ((int)($t / $sz)+1);
        $link = base_url('index.php/res/?q='.get("q").'&type='.get("type"));
        
        $p = round(get("p"));
        if ($p == 0) { $p = 1; }
        /********* PAGINA INICIAL ******************/
        $pgi = $p - 5;
        if ($pgi < 1) { $pgi = 1; }
                
        $pgf = ($pgi+9);
        $pgm = ((int)($t/$sz)+1);
        if ($pgf > $pgm) { $pgf = $pgm; }
        
        $sx = '<nav aria-label="Page navigation example">
                    <ul class="pagination">';
        if ($pgi > 1)
            {
                $sx .= '    <li class="page-item"><a class="page-link" href="'.$link.'&p='.($pgi-1).'">&laquo;</a></li>'.cr();
            }

        
        for ($r = $pgi; $r <= $pgf; $r++) {
            $class = "";
            if ($r == $p)
                {
                    $class = ' active ';
                }
            $sx .= '<li class="page-item '.$class.'"><a class="page-link" href="'.$link.'&p='.$r.'">' . $r . '</a></li>'.cr();
        }
        
        if ($pgf < $pgm)
            {
                $sx .= '<li class="page-item"><a class="page-link" href="'.$link.'&p='.($pgf+1).'">&raquo;</a></li>'.cr();        
            }
        $sx .= '</ul></nav>'.cr();
        return ($sx);
    }

    function s($n, $t = '') {
        $type = 'article';
        $q = $this -> elasticsearch -> query($type, $n);
        //$q = $this->ElasticSearch->query_all($n);

        if (!isset($q['hits'])) {
            return ('Not found');
        }

        $total = $q['hits']['total'];
        
        $sx = '<div class="container"><div class="row">';
        $sx .= '<div class="col-8">'.$this -> pages($n, $total).'</div>'.cr();
        $sx .= '<div class="col-4">Total ' . $total.'</div>'.cr();                
        $sx .= '</div></div>';
        
        $p = round(get("p"));
        if ($p == 0) { $p = 1;}

        /**************************************************** Busca Parte II *****************/
        $sx .= '<div class="row result">';
        $rst = $q['hits']['hits'];
        
        $sz = $this->sz;
        $ppi = (($p-1)*$sz);
        $ppf = $total;
        for ($r = 0; $r < count($rst); $r++) {
            $key = $rst[$r]['_source']['article_id'];
            $jnl = $rst[$r]['_source']['id_jnl'];
            //$img = 'img/cover/cover_issue_3477_pt_BR.jpg';
            $img = 'img/cover/cover_issue_' . $jnl . '.jpg';
            if (!is_file($img)) {
                //echo '==>' . $img . '<br>';
                $img = 'img/cover/cover_issue_0.jpg';
                //$sx .= '['.$jnl.']';
            }
            $sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . $img . '" class="img-fluid"></div>';
            $sx .= '<div class="col-11 " style="margin-bottom: 15px;">';
            $sx .= '<input type="checkbox"> ';
            $sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
            $sx .= $this -> frbr -> show_v($key);
            $sx .= '</a>';
            $sx .= ' <sup>' . number_format($rst[$r]['_score'], 4) . '</sup>';
            $sx .= '</div>';
        }
        $sx .= '</div>';

        return ($sx);
        /*********************************************************************************/
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
