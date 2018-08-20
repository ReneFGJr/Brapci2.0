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
        $headers = array('Accept: application/json', 'Content-Type: application/json', );
        //echo '<tt>['.$url.']</tt>';
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

    /**
     * set the mapping for the index
     *
     * @param type $type
     * @param type $id
     * @param type $data
     *
     * @return type
     */

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
        $dt['year'] = $data['issue']['year'];
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

    /**
     * make a simple search query
     *
     * @param type $type
     * @param type $q
     *
     * @return type
     */

    public function query($type, $q, $t) {
        // https://www.youtube.com/watch?v=Q0oy9-lXJ18
        // https://www.youtube.com/watch?v=5lO4cAQlaEw&t=26s
        // https://www.youtube.com/watch?v=MXFp4OPdV4I
        /******************* PAGINACAO *******/
        $sz = $this -> searchs -> sz;
        $p = round(get("p"));
        $fr = ($p - 1);
        if ($fr < 0) { $fr = 0;
        }
		$fr = $fr * $sz;
        $DATA = '';
        $method = 'POST';
        $qs = '';
        switch($t) {
            case '2':
                $fld = 'authors';
                $data = '
                        {
                          "from": "'.$fr.'",
                          "size": "'.$sz.'",
                          "query": {
                            "query_string": {
                              "fields": [
                                "'.$fld.'"
                              ],                                
                              "query": "'.$q.'"
                            }
                          }
                        }                
                ';               
                break;
            case '3':
                $fld = 'title';
                $data = '
                        {
                          "from": "'.$fr.'",
                          "size": "'.$sz.'",
                          "query": {
                            "query_string": {
                              "fields": [
                                "'.$fld.'"
                              ],                                
                              "query": "'.$q.'"
                            }
                          }
                        }                
                ';               
                break;
            case '4':
                $fld = 'subject';
                $data = '
                        {
                          "from": "'.$fr.'",
                          "size": "'.$sz.'",
                          "query": {
                            "query_string": {
                              "fields": [
                                "'.$fld.'"
                              ],                                
                              "query": "'.$q.'"
                            }
                          }
                        }                
                ';               
                break;
            case '5':
            $fld = 'abstract';
                $data = '
                        {
                          "from": "'.$fr.'",
                          "size": "'.$sz.'",
                          "query": {
                            "query_string": {
                              "fields": [
                                "'.$fld.'"
                              ],                                
                              "query": "'.$q.'"
                            }
                          }
                        }                
                ';                              

                break;
            default :
                /**** NOVO ***/
                $data = '
                        {
                          "from": "'.$fr.'",
                          "size": "'.$sz.'",
                          "query": {
                            "query_string": {
                              "fields": [
                                "authors",
                                "title^10",
                                "subject^5",
                                "abstract",
                                "journal",
                                "year"
                              ],                                 
                              "query": "'.$q.'"
                            }
                          }
                        }                
                ';                            
                break;
        }
        
        return $this -> call($type . '/_search?'.$qs,$method,$data);
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
