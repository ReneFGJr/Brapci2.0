<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PATH','index.php/patent/');
class Patent extends CI_Controller {

    function __construct() {

        parent::__construct();
        $this -> lang -> load("app", "portuguese");
        $this -> load -> database();
        $this -> load -> helper('url');
        $this -> load -> library('session');

        //$this -> load -> library('tcpdf');

        $this -> load -> helper('form');
        $this -> load -> helper('form_sisdoc');
        $this -> load -> helper('xml');
        #$this -> load -> helper('xml_dom');
        $this -> load -> model("socials");
        $this -> load -> model('bs');
        //$this -> load -> helper('email');
        $this -> load -> helper('bootstrap');
        date_default_timezone_set('America/Sao_Paulo');
    }

    private function cab($data = array()) {
        $data['title'] = 'Brapci Patent';
        if (isset($data['meta'])) {
            for ($r = 0; $r < count($data['meta']); $r++) {
                $line = $data['meta'][$r];
                $class = trim($line['c_class']);
                if (trim($line['c_class']) == 'prefLabel') {
                    $data['title'] = trim($line['n_name']) . ' :: Brapci Patent';
                }
                if (trim($line['c_class']) == 'hasTitle') {
                    $data['title'] = trim($line['n_name']) . ' :: Brapci Patent';
                }
            }
        }
        $this -> load -> view('patent/header/header.php', $data);
        if (!isset($data['nocab'])) {
            $this -> load -> view('patent/header/menu_top.php', $data);
        }
        $this -> load -> model("socials");
    }

    private function footer($data = array()) {
        if ($data == 0) {
            $data = array('simple' => true);
        }
        $this -> load -> view('header/footer.php', $data);
    }

    function ea($pth = '', $q = '') {
        $this -> load -> model('elasticsearch');
        switch($pth) {
            case 'zera' :
                break;
            case 'status' :
                $this -> load -> model('elasticsearch_patent');
                print_r($this -> elasticsearch -> status());
                break;
            case 'journals' :
                $this -> load -> model('elasticsearch_patent');
                $this -> Elasticsearch_brapci20 -> journals_index();
                break;
            case 'query' :
                $this -> load -> model('elasticsearch_patent');
                $this -> Elasticsearch_brapci20 -> query($q);
                break;
            default :
                $this -> cab();

                break;
        }
        //$this->cab();

        //$result = $client->index($params);
    }

    public function index() {
        $this -> load -> model('elasticsearch');
        $this -> load -> model('libraries');
        $this -> load -> model('sources');
        $this -> load -> model('searchs');
        $this -> load -> model('patents');
        $this -> load -> model('events');
        $this -> load -> model('frbr');
        $this -> cab();

        $data['events'] = '';

        $this -> load -> view('patent/form', $data);

        if (strlen(get("q")) > 0) {
            /****************************************************************************/
            //$this -> ElasticSearch -> getStatus();
            $type = get("type");
            if ($type != 2) {
                $term = convert(get("q"));
            } else {
                $term = get("q");
            }
            $term = troca($term, '¢', '"');
            $data['content'] = '' . $this -> patents -> s($term, $type) . '';

            //$data['content'] .= $this->searchs->historic();
            $this -> load -> view('show', $data);

        } else {
            /****************************************************************************/
            $data['content'] = $this -> searchs -> historic();

            $this -> load -> view('show', $data);
        }

        $this -> footer();
    }

    function v($id=0) {
        $this->load->model('patents');
        $this->cab();
        $sx = $this->patents->view($id);
        $data['content'] = $sx;
        $data['title'] = 'Patent';
        $this->load->view('show',$data);
        $this->footer();
    }
    
    function vi($id=0) {
        $this->load->model('patents');
        $this->cab();
        $sx = $this->patents->instituicao_list($id);
        $data['content'] = $sx;
        $data['title'] = 'Patent';
        $this->load->view('show',$data);
        $this->footer();
    }    

}