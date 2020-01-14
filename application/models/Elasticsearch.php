<?php
/**
 * Elasticsearch Library
 *
 * @package OpenLibs
 *
 */
class elasticsearch extends CI_model {
    var $index = 'brp';
    var $server = '';

    /**
     * constructor setting the config variables for server ip and index.
     */

    /* http://127.0.0.1:9200/names/family/_mapping?pretty=true
     *
     *
     *
     * */

    public function __construct() {
        $ci = &get_instance();
        //$ci -> config -> load("elasticsearch");
        $this -> server = ELASTIC;
        $this -> index = ELASTIC_PREFIX;
    }

    /**
     * Handling the call for every function with curl
     *
     * @param type $path
     * @param type $method
     * @param type $data
     *
     * @return type
     * @throws Exception
     */

    private function call($path, $method = 'GET', $data = null) {
        if (strlen($this -> index) == 0) {
            echo('index needs a value');
            return ( array());
        }

        $url = $this -> server . '/' . $this -> index . '/' . $path;
        
        if (perfil("#ADM"))
            {
            //echo '<pre style="color: blue">';
            //echo '<tt>'.$url.'</tt>';
            //echo '</pre>';
            }

        $headers = array('Accept: application/json', 'Content-Type: application/json', );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        switch($method) {
            case 'GET' :
                break;
            case 'POST' :
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //exit;

        return json_decode($response, true);
    }

    /**
     * create a index with mapping or not
     *
     * @param json $map
     */

    public function create($map = false) {
        if (!$map) {
            $this -> call(null, 'PUT');
        } else {
            $this -> call(null, 'PUT', $map);
        }
    }

    /**
     * get status
     *
     * @return array
     */

    public function status() {
        return $this -> call('_status');
    }

    /**
     * count how many indexes it exists
     *
     * @param string $type
     *
     * @return array
     */

    public function count($type) {
        return $this -> call($type . '/_count?' . http_build_query(array(null => '{matchAll:{}}')));
    }

    /**
     * set the mapping for the index
     *
     * @param string $type
     * @param json   $data
     *
     * @return array
     */

    public function map($type, $data) {
        return $this -> call($type . '/_mapping', 'PUT', $data);
    }

    public function fulltext($type='fulltext',$id,$dt)
        {
            $this -> index = 'full';
            $rst = $this -> call($type . '/' . $id, 'PUT', $dt);        
        }
    

    /**
     * set the mapping for the index
     *
     * @param type $type
     * @param type $id
     * @param type $data
     *
     * @return type
     */

    public function update($id) {
        $file_full = 'c/' . $id . 'full.txt';
        $auth = '';
        $title = '';
        $abstract = '';
        $subject = '';
        $journal = '';
        $year = '';
        $id_jnl = 0;
        $source = '';
        $type = 'article';
        $status = 'A';

        $dt = array();
        /****************************************************************************/
        $data = $this -> frbr_core -> le_data($id);

        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            $prop = trim($line['c_class']);
            switch($prop) {
                case 'hasSectionOf' :
                    $sec = $line['d_r2'];
                    $sc = $this -> frbr_core -> le_data($sec, 'hasSectionIndex');
                    if (count($sc) > 0) {
                        for ($n = 0; $n < count($sc); $n++) {
                            $sr = $sc[$n];
                            if (isset($sr['n_name'])) {
                                if (substr($sr['n_name'], 0, 1) == 'N') {
                                    $status = 'N';
                                }
                            }
                        }
                    }
                    break;
                case 'hasSource' :
                    //$source .= LowerCaseSql($line['n_name']).'; ';
                    break;
                case 'hasIssueOf' :
                    $id_jnl = $line['n_name'];
                    $id_jnl = round(sonumero(substr($id_jnl, 0, strpos($id_jnl, '-'))));
                    $id_jnl = (string)$id_jnl;
                    $source .= LowerCaseSql($line['n_name']) . '; ';
                    break;
                case 'hasTitle' :
                    $title .= LowerCaseSql($line['n_name']) . ' (' . $line['n_lang'] . '); ';
                    break;
                case 'hasAuthor' :
                    $auth .= LowerCaseSql($line['n_name']) . '; ';
                    break;
                case 'hasAbstract' :
                    $abstract .= LowerCaseSql($line['n_name']) . '; ';
                    break;
                case 'hasSubject' :
                    $subject .= LowerCaseSql($line['n_name']) . '; ';
                    break;
                case 'isPubishIn' :
                    $journal = LowerCaseSql($line['n_name']);
                    break;
                case 'dateOfAvailability' :
                    $year = substr(LowerCaseSql($line['n_name']), 0, 4);
                    break;
            }
        }

        $dt['article_id'] = $id;
        $dt['authors'] = $auth;
        $dt['title'] = $title;
        $dt['abstract'] = $abstract;
        $dt['subject'] = $subject;
        $dt['journal'] = $journal;
        $dt['id_jnl'] = $id_jnl;
        $dt['year'] = $year;
        $dt['issue'] = $source;
        $dt['all'] = $title . ' ' . $abstract . ' ' . $subject;
        $dt['full'] = '::';

        if (file_exists($file_full))
            {
                $dt['full'] = file_get_contents($file_full);
            }        
       
        if ($status == 'N') {
            $rst = $this -> delete($type, $id);
            $rst = "<font color=red><b>Deleted " . $id . " " . $type . '</b></font>';
        } else {
            $rst = $this -> call($type . '/' . $id, 'PUT', $dt);
            $rst = "<font color=green><b>Update " . $id . " " . $type . '</b></font>';
        }
        return ($rst);

    }

    public function add($type, $id, $data) {
        $dt = array();
        /****************************************************************************/
        $auth = '';
        if (isset($data['authors'])) {
            for ($r = 0; $r < count($data['authors']); $r++) {
                if ($data['authors'][$r]['type'] == 'author') {
                    $auth .= $data['authors'][$r]['name'] . '; ';
                }
            }
        }
        /****************************************************************************/
        $title = '';
        if (isset($data['title'])) {
            for ($r = 0; $r < count($data['title']); $r++) {
                $title .= $data['title'][$r]['title'] . ' (' . $data['title'][$r]['lang'] . '); ';
            }
        }
        /****************************************************************************/
        $abstract = '';
        if (isset($data['abstract'])) {
            for ($r = 0; $r < count($data['abstract']); $r++) {
                $abstract .= $data['abstract'][$r]['descript'] . ' (' . $data['abstract'][$r]['lang'] . '); ';
            }
        }
        /****************************************************************************/
        $subject = '';
        if (isset($data['subject'])) {
            for ($r = 0; $r < count($data['subject']); $r++) {
                $term = substr($data['subject'][$r], 0, strpos($data['subject'][$r], '@'));
                $subject .= $term . '; ';
            }
        }
        $dt['article_id'] = $id;
        $dt['authors'] = $auth;
        $dt['title'] = $title;
        $dt['abstract'] = $abstract;
        $dt['subject'] = $subject;
        $dt['journal'] = $data['jnl_name'];
        $dt['id_jnl'] = $data['id_jnl'];
        $dt['year'] = round($data['issue']['year']);
        $dt['issue'] = $data['issue']['issue_id'];
        return $this -> call($type . '/' . $id, 'PUT', $dt);
    }

    /**
     * delete a index
     *
     * @param type $type
     * @param type $id
     *
     * @return type
     */

    public function delete($type, $id) {
        return $this -> call($type . '/' . $id, 'DELETE');
    }

    public function delete_all($type) {
        return $this -> call($type . '/', 'DELETE');
    }

    /**
     * make a simple search query
     *
     * @param type $type
     * @param type $q
     *
     * @return type
     */

    public function query($type, $q, $t, $ord = 0,$full=0) {
        $OR = 0;
        if (strpos($q, ' OR ')) { $OR = 1;
            $q = troca($q, ' OR ', ' ');
        }
        $q = troca($q, ' AND ', ' ');
        $q = lowercasesql($q);
        $qr = $q;
        // https://www.youtube.com/watch?v=Q0oy9-lXJ18
        // https://www.youtube.com/watch?v=5lO4cAQlaEw&t=26s
        // https://www.youtube.com/watch?v=MXFp4OPdV4I
        /******************* PAGINACAO *******/
        if ($full == 0)
        {
            $sz = $this -> searchs -> sz;    
        } else {
            $sz = 10000;
        }
        
        $p = round(get("p"));
        $fr = ($p - 1);
        if ($fr < 0) { $fr = 0;
        }
        $fr = $fr * $sz;
        $DATA = '';
        $method = 'POST';
        $qs = '';

        /*********************************/
        $order = trim(get("order"));
        if (strlen($order) == 0) {
            $order = 0;
        }
        $_SESSION['order'] = $order;
        switch($order) {
            case '1' :
                $ord = '"sort": [ { "year" : "desc" } ] ,';
                $ord = '"sort": [ { "year": { "order": "asc" } } ] , ';
                $ord = '';
                //$ord = '"sort": ["_score"] ,';
                //$ord = ', "sort": ["year"]';
                break;
            default :
                $ord = '';
                break;
        }

        switch($t) {
            case '2' :
                $fld = 'authors';
                break;
            case '3' :
                $fld = 'title';
                break;
            case '4' :
                $fld = 'subject';
                break;
            case '5' :
                $fld = 'abstract';
                break;
            case '6' :
                $fld = 'full';
                break;                
            default :
                $fld = 'all';
                break;
        }
        /************** NOVO **************************/

        /********* MIN SCORE ************************************************/
        $min_score = '"min_score": 0.5,' . cr();
        $min_score = '';

        /********* QUERY ****************************************************/
        $tq = $this -> searchs -> terms($q);
        $ttt = '';
        for ($r = 0; $r < count($tq); $r++) {
            $t = $tq[$r];
            $tp = 'match';
            if (strpos(' ' . $t, '_') > 0) {
                $t = troca($t, '_', ' ');
                $tp = 'match_phrase';
            }
            if (strpos(' ' . $t, '*') > 0) {
                $tp = 'wildcard';
            }
            if (strlen($ttt) > 0) {
                $ttt .= ', ' . cr();
            }
            $ttt .= '{"' . $tp . '" : {"' . $fld . '": "' . $t . '" } }';
        }

        /********* RANGE ****************************************************/
        if (!isset($_SESSION['year_s'])) {
            $_SESSION['year_s'] = 0;
            $_SESSION['year_e'] = date("Y");
        }

        $year1 = $_SESSION['year_s'];
        $year2 = $_SESSION['year_e'];
        $range = '"range" : { "year" : { "gte": "' . $year1 . '", "lte": "' . $year2 . '" } }' . cr();
        $ttt .= ', { ' . $range . ' }';
        
        /********* OPERADOR BOOLEANO **************/
        if ($OR == 0) {
            $ooo = ' "must" : [ ' . $ttt . ' ] ';
        } else {
            $ooo = ' "should" : [ ' . $ttt . ' ] ';
        }

        $ttt = ' "bool": { ' . $ooo . ' } ';
        $qqq = ' "query": { ' . $ttt . ' } ';
        $data = '
                        {
                          ' . $min_score . '
                          "from": "' . $fr . '",
                          "size": "' . $sz . '",
                          ' . $ord . '
                          ' . $qqq . '                          
                        }              
                ';

        $rq = $this -> call($type . '/_search?' . $qs, $method, $data);
        return $rq;
    }

    /**
     * make a advanced search query with json data to send
     *
     * @param type $type
     * @param type $query
     *
     * @return type
     */

    public function advancedquery($type, $query) {
        return $this -> call($type . '/_search', 'POST', $query);
    }

    /**
     * make a search query with result sized set
     *
     * @param string  $type  what kind of type of index you want to search
     * @param string  $query the query as a string
     * @param integer $size  The size of the results
     *
     * @return array
     */

    public function query_wresultSize($type, $query, $size = 999) {
        return $this -> call($type . '/_search?' . http_build_query(array('q' => $q, 'size' => $size)));
    }

    /**
     * get one index via the id
     *
     * @param string  $type The index type
     * @param integer $id   the indentifier for a index
     *
     * @return type
     */

    public function get($type, $id) {
        return $this -> call($type . '/' . $id, 'GET');
    }

    /**
     * Query the whole server
     *
     * @param type $query
     *
     * @return type
     */

    public function query_all($query) {
        return $this -> call('_search?' . http_build_query(array('q' => $query)));
    }

    /**
     * get similar indexes for one index specified by id - send data to add filters or more
     *
     * @param string  $type
     * @param integer $id
     * @param string  $fields
     * @param string  $data
     *
     * @return array
     */

    public function morelikethis($type, $id, $fields = false, $data = false) {
        if ($data != false && !$fields) {
            return $this -> call($type . '/' . $id . '/_mlt', 'GET', $data);
        } else if ($data != false && $fields != false) {
            return $this -> call($type . '/' . $id . '/_mlt?' . $fields, 'POST', $data);
        } else if (!$fields) {
            return $this -> call($type . '/' . $id . '/_mlt');
        } else {
            return $this -> call($type . '/' . $id . '/_mlt?' . $fields);
        }
    }

    /**
     * make a search query with result sized set
     *
     * @param type $query
     * @param type $size
     *
     * @return type
     */
    public function query_all_wresultSize($query, $size = 999) {
        return $this -> call('_search?' . http_build_query(array('q' => $query, 'size' => $size)));
    }

    /**
     * make a suggest query based on similar looking terms
     *
     * @param type $query
     *
     * @return array
     */
    public function suggest($query) {
        return $this -> call('_suggest', 'POST', $query);
    }

}
