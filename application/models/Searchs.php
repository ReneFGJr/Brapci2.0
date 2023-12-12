<?php
class searchs extends CI_Model {
    var $sz = 20;
    var $s = '';
    var $base = 'brapci_search.';
    function __construct() {
        global $MODO;
        if (isset($MODO)) {
            return ("modoROBOT");
        }
        if (!isset($_SESSION['s'])) {
            if (!isset($_SESSION['s'])) {
                if (isset($_SESSION['__ci_last_regenerate']))
                {
                    $_SESSION['s'] = $_SESSION['__ci_last_regenerate'];
                } else {
                    $_SESSION['s'] = date("Ymdihs");
                }

            }
        }
        $this -> s = $_SESSION['s'];
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
        $sz = $this -> sz;
        $pgs = ((int)($t / $sz) + 1);
        $q = get("q");
        $q = troca($q, '"', '¢');
        $link = base_url('index.php/res/?q=' . $q . '&type=' . get("type"));
        if (strlen(get("year_s")) > 0) { $link .= '&year_s='.sround(get("year_s")); }
        if (strlen(get("year_e")) > 0) { $link .= '&year_e='.sround(get("year_e")); }
        $p = sround(get("p"));
        if ($p == 0) { $p = 1;
        }
        /********* PAGINA INICIAL ******************/
        $pgi = $p - 5;
        if ($pgi < 1) { $pgi = 1;
        }

        $pgf = ($pgi + 9);
        $pgm = ((int)($t / $sz) + 1);
        if ($pgf > $pgm) { $pgf = $pgm;
        }

        $sx = '<nav aria-label="Page navigation example">
                    <ul class="pagination">';
        if ($pgi > 1) {
            $sx .= '    <li class="page-item"><a class="page-link" href="' . $link . '&p=' . ($pgi - 1) . '">&laquo;</a></li>' . cr();
        }

        for ($r = $pgi; $r <= $pgf; $r++) {
            $class = "";
            if ($r == $p) {
                $class = ' active ';
            }
            $sx .= '<li class="page-item ' . $class . '"><a class="page-link" href="' . $link . '&p=' . $r . '">' . $r . '</a></li>' . cr();
        }

        if ($pgf < $pgm) {
            $sx .= '<li class="page-item"><a class="page-link" href="' . $link . '&p=' . ($pgf + 1) . '">&raquo;</a></li>' . cr();
        }
        $sx .= '</ul></nav>' . cr();
        return ($sx);
    }

    function historic($limit = 10) {
        $session = $this -> s;
        $sql = "select * from ".$this->base."_search
                        where s_session = $session
                        order by s_date desc, s_hour desc
                        limit $limit ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<h4>' . msg('historic_search') . '</h4>';
        $sx .= '<table class="table" width="100%">';
        $sx .= '<tr>
                        <th width="15%">' . msg("s_date_hour") . '</th>
                        <th width="50%">' . msg("s_query") . '</th>
                        <th width="15%">' . msg("s_type") . '</th>
                        <th width="15%">' . msg("s_order") . '</th>
                        <th width="5%">' . msg("s_result") . '</th>
                    </tr>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $q = $line['s_query'];
            $q = troca($q, '"', '¢');
            $link = '<a href="' . base_url(PATH . '?q=' . $q . '&h=1&type=' . $line['s_type']) . '&order='.$line['s_order'].'">';
            $sx .= '<tr>';
            $sx .= '<td align="center"><tt>' . $line['s_date'] . ' ' . $line['s_hour'] . '</tt></td>';
            $sx .= '<td><tt>' . $link . $line['s_query'] . '</a>' . '</tt></td>';
            $sx .= '<td><tt>' . msg('search_' . $line['s_type']) . '</tt></td>';
            $sx .= '<td><tt>' . msg('order_' . $line['s_order']) . '</tt></td>';
            $sx .= '<td align="center"><tt>' . $line['s_total'] . '</tt></td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        if (count($rlt) == 0) {
            $sx = '';
        }
        return ($sx);
    }

    function save_history($data) {
        if (get("h") == '1') {
            return ("");
        }

        $user = 0;
        $date = date("Y-m-d");
        $hour = date("H:i:s");
        $session = $this -> s;
        $ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SESSION['id_us'])) {
            $user = $_SESSION['id_us'];
        }
        $q = UpperCase($data['q']);
        $t = sround($data['type']);
        if (!isset($data['total'])) { $data['total'] = 0; }
        $total = $data['total'];
        $page = sround(GET("p"));
        $order = sround('0'.$data['order']);

        $sql = "select * from ".$this->base."_search
					where s_date = '$date'
						and s_hour = '$hour'
						and s_query = '$q'
						and s_type = $t
						and s_user = $user
                        and s_total = $total
						and s_session = s_session";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            if (strlen($q) > 0) {
                $sql = "insert into ".$this->base."_search (s_date, s_hour, s_query, s_type, s_user, s_total, s_session, s_ip, s_order) ";
                $sql .= " values ";
                $sql .= "('$date', '$hour', '$q',$t,$user,$total,$session,'$ip','$order')";
                $this -> db -> query($sql);

                /************* Elastic Search */
                $d = [];
                $d['query'] = $q;
                $d['ip'] = $ip;
                $d['ano'] = date("Y");
                $d['mes'] = date("m");
                $d['dia'] = date("d");
                $d['hora'] = date("H");
                $d['minuto'] = date("i");

                /*
                $this->load->model('elasticsearch');
                $this->elasticsearch->index = 'consultas';
                $this->elasticsearch->server = 'http://143.54.112.91:9200';
                print_r($d);
                $id = 1;
                print_r($this->elasticsearch->call('_doc/'.$id,'POST',$d));
                */
            }
        }
        return ('');
    }

    function rage($var = 'year_end', $i = '2019', $f = '1972') {
        $year2 = '';
        $y2 = get($var);
        if (strlen($y2) == 0) {
            if (isset($_SESSION[$var])) {
                $y2 = $_SESSION[$var];
            } else {
                if ($i > $f) {
                    $y2 = date("Y");
                } else {
                    $y2 = $i;
                }
            }
        }
        $_SESSION[$var] = $y2;
        $r = $i;
        $loop = 1;
        $sx = '';
        $sx .= '<select name="' . $var . '" size=1 style="width: 100px;">' . cr();
        while ($loop == 1) {
            $sel = '';
            if ($r == $y2) { $sel = "selected";
            }
            $sx .= '<option value="' . $r . '" ' . $sel . '>' . $r . '</option>' . cr();

            if ($i > $f) {
                $r--;
                if ($r < $f) { $loop = 0;
                }
            } else {
                $r++;
                if ($r > $f) { $loop = 0;
                }
            }
        }
        $sx .= '</select>' . cr();
        return ($sx);
    }

    function terms($q) {
        $terms = array();
        $s = '';
        $set = 0;
        $q .= ' ';
        $q = troca($q, '(', '"');
        $q = troca($q, ')', '"');
        for ($r = 0; $r < strlen($q); $r++) {
            $c = substr($q, $r, 1);
            if ($c == '"') {
                if ($set == 0) {
                    $set = 1;
                } else {
                    $set = 0;
                }
            } else {
                if ($c == ' ') {
                    if ($set == 1) {
                        $s .= '_';
                    } else {
                        $s = trim($s);
                        if (strlen($s) > 0) {
                            array_push($terms, $s);
                        }
                        $s = '';
                    }
                } else {
                    $s .= $c;
                }
            }
        }
        return ($terms);
    }

    function s($n, $t = '') {
        $p = sround(get("p"));
        if ($p == 0) { $p = 1;
        }

        $type = 'article';
        $q = $this -> elasticsearch -> query($type, $n, $t);

        /*************** history ***************/
        $data['q'] = $n;
        $data['type'] = $t;

        if (strlen(get("order")) > 0)
        {
            $data['order'] = get("order");
        } else {
            if (isset($_SESSION['order']))
                {
                    $data['order'] = get("order");
                } else {
                    $data['order'] = '0';
                }
        }

        if (!isset($q['hits'])) {
            $total = 0;
            $data['total'] = $total;

            $this -> save_history($data);
            return ('Not found');
        } else {
            $total = $q['hits']['total']['value'];
            /* History */
            $data['total'] = $total;
            $this -> save_history($data);
        }

        $rst = $q['hits']['hits'];

        $st = '';
        for ($r = 0; $r < count($rst); $r++) {
            $line = $rst[$r];
            $st .= $line['_id'] . ';';
        }

        $sx = '<div class="container"><div class="row">';
        $sx .= $this -> bs -> script_all($st,$n,$t);
        $sx .= '<div class="col-8">' . $this -> pages($n, $total) . '</div>' . cr();
        $sx .= '<div class="col-4">Total ' . $total . '</div>' . cr();
        $sx .= '</div></div>';
        /**************************************************** Busca Parte II *****************/
        $sx .= '<div class="row result">';

        $sz = $this -> sz;
        $ppi = (($p - 1) * $sz);
        $ppf = $total;
        for ($r = 0; $r < count($rst); $r++) {
            $key = $rst[$r]['_source']['article_id'];
            $jnl = $rst[$r]['_source']['id_jnl'];
            $year = $rst[$r]['_source']['year'];
            $img = 'img/cover/cover_issue_' . $jnl . '.jpg';
            if (!is_file($img)) {
                $img = 'img/cover/cover_issue_0.jpg';
            }
            $sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . $img . '" class="img-fluid"></div>';
            $sx .= '<div class="col-10 " style="margin-bottom: 15px;">';
            $sx .= $this -> bs -> checkbox($key);
            $sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
            $sx .= $this -> frbr -> show_v($key);
            $sx .= '</a>';
            $sx .= ' <sup>' . number_format($rst[$r]['_score'], 4) . '</sup>';
            $sx .= '</div>';
            $sx .= '<div class="col-1 ">' . $year . '</div>';
        }
        $sx .= '</div>';
        if ($total > 5) {
            $sx .= '<div class="container"><div class="row">';
            $sx .= '<div class="col-8">' . $this -> pages($n, $total) . '</div>' . cr();
            $sx .= '<div class="col-4">Total ' . $total . '</div>' . cr();
            $sx .= '</div></div>';
        }
        if ($total == 0) {
            $sx = '<div class="container"><div class="row">';
            $sx .= '<div class="col-12">';
            $sx .= bs_alert("warning", msg("not_match_to") . ' "<b>' . $n . '</b>"');
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
