<?php
class searchs extends CI_Model {
    var $sz = 20;
    var $s = '';    
    function __construct()
        {
		global $MODO;
		if (isset($MODO))
			{
				return("modoROBOT");
			}
        if (!isset($_SESSION['s']))
            {
                if (!isset($_SESSION['s']))
                    {
                        $_SESSION['s'] = $_SESSION['__ci_last_regenerate'];
                    }
            }
           $this->s = $_SESSION['s'];
        }
        
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

    function historic($limit = 10)
        {
            $session = $this->s;
            $sql = "select * from _search 
                        where s_session = $session 
                        order by s_date desc, s_hour desc 
                        limit $limit ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '<h4>'.msg('historic_search').'</h4>';
            $sx .= '<table class="table" width="100%">';
            $sx .= '<tr>
                        <th width="15%">'.msg("s_date_hour").'</th>
                        <th width="70%">'.msg("s_query").'</th>
                        <th width="10%">'.msg("s_type").'</th>
                        <th width="5%">'.msg("s_result").'</th>
                    </tr>';
            for ($r=0;$r < count($rlt);$r++)
                {
                    $line = $rlt[$r];
                    $link = '<a href="'.base_url(PATH.'?q='.$line['s_query'].'&h=1&type='.$line['s_type']).'">';
                    $sx .= '<tr>';
                    $sx .= '<td align="center"><tt>'.$line['s_date'].' '.$line['s_hour'].'</tt></td>';
                    $sx .= '<td><tt>'.$link.$line['s_query'].'</a>'.'</tt></td>';
                    $sx .= '<td><tt>'.msg('search_'.$line['s_type']).'</tt></td>';
                    $sx .= '<td align="center"><tt>'.$line['s_total'].'</tt></td>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            if (count($rlt) == 0)
                {
                    $sx = '';
                }
            return($sx);
        }


    function save_history($data)
        {
            if (get("h") == '1')
                {
                    return("");
                }
                
            $user = 0;
            $date = date("Y-m-d");
            $hour = date("H:i:s");
            $session = $this->s;
            if (isset($_SESSION['id_us']))
                {
                    $user = $_SESSION['id_us'];
                }
            $q = UpperCase($data['q']);
            $t = round($data['type']);
            $total = round($data['total']);
            $page = round(GET("p"));
            if (strlen($q) > 0)
                {
                    $sql = "insert into _search (s_date, s_hour, s_query, s_type, s_user, s_total, s_session) ";
                    $sql .= " values ";
                    $sql .= "('$date', '$hour', '$q',$t,$user,$total,$session)";
                    $this->db->query($sql);                    
                }
            return('');
        }

    function s($n, $t = '') {
        
        $p = round(get("p"));
        if ($p == 0) { $p = 1;}
		    	
        $type = 'article';
        $q = $this -> elasticsearch -> query($type, $n, $t);
        //$q = $this->ElasticSearch->query_all($n);

        /*************** history ***************/
        $data['q'] = $n;
        $data['type'] = $t;
        
        if (!isset($q['hits'])) {
            $total = 0;
            $data['total'] = $total;
            $this->save_history($data);
            return ('Not found');
        } else {
            $total = $q['hits']['total'];
            
            /* History */
            $data['total'] = $total;
            $this->save_history($data);    
        }        
        
        
        
        
        $rst = $q['hits']['hits'];

        $st = '';        
        for ($r=0;$r < count($rst);$r++)
            {
                $line = $rst[$r];
                $st .= $line['_id'].';';
            }
                
        $sx = '<div class="container"><div class="row">';
        $sx .= $this->bs->script_all($st);        
        $sx .= '<div class="col-8">'.$this -> pages($n, $total).'</div>'.cr();
        $sx .= '<div class="col-4">Total ' . $total.'</div>'.cr();                
        $sx .= '</div></div>';


        /**************************************************** Busca Parte II *****************/
        $sx .= '<div class="row result">';        
        
        $sz = $this->sz;
        $ppi = (($p-1)*$sz);
        $ppf = $total;
        for ($r = 0; $r < count($rst); $r++) {
            $key = $rst[$r]['_source']['article_id'];
            $jnl = $rst[$r]['_source']['id_jnl'];
            $year = $rst[$r]['_source']['year'];
            $img = 'img/cover/cover_issue_' . $jnl . '.jpg';
            if (!is_file($img)) {
                //echo '==>' . $img . '<br>';
                $img = 'img/cover/cover_issue_0.jpg';
                //$sx .= '['.$jnl.']';
            }
            $sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . $img . '" class="img-fluid"></div>';
            $sx .= '<div class="col-10 " style="margin-bottom: 15px;">';
			$sx .= $this->bs->checkbox($key);
            $sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
            $sx .= $this -> frbr -> show_v($key);
            $sx .= '</a>';
            $sx .= ' <sup>' . number_format($rst[$r]['_score'], 4) . '</sup>';
            $sx .= '</div>';
            $sx .= '<div class="col-1 ">'.$year.'</div>';            
        }
        $sx .= '</div>';
        if ($total > 5)
            {
                $sx .= '<div class="container"><div class="row">';
                $sx .= '<div class="col-8">'.$this -> pages($n, $total).'</div>'.cr();
                $sx .= '<div class="col-4">Total ' . $total.'</div>'.cr();                
                $sx .= '</div></div>';
            } 
        if ($total == 0)
            {
                $sx = '<div class="container"><div class="row">';
                $sx .= '<div class="col-12">';
                $sx .= bs_alert("warning",msg("not_match_to") .' "<b>'.$n.'</b>"');
                $sx .= '</div>';
                $sx .= '</div></div>';
            }                   

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
