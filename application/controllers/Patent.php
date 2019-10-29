<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('PATH', 'index.php/patent/');
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

    function v($id = 0) {
        $this -> load -> model('patents');
        $this -> cab();
        $sx = $this -> patents -> view($id);
        $data['content'] = $sx;
        $data['title'] = 'Patent';
        $this -> load -> view('show', $data);
        $this -> footer();
    }

    function vi($id = 0) {
        $this -> load -> model('patents');
        $this -> cab();
        $sx = $this -> patents -> instituicao($id);
        $sx .= $this -> patents -> instituicao_list($id);
        $data['content'] = $sx;
        $data['title'] = 'Patent';
        $this -> load -> view('show', $data);
        $this -> footer();
    }

    function summary() {
        $this -> load -> model('patents');
        $this -> cab();
        $sx = $this -> patents -> summary();
        $data['content'] = $sx;
        $data['title'] = 'Patent';
        $this -> load -> view('show', $data);
        $this -> footer();

    }

    /* LOGIN */
    function social($act = '') {
        if ($act == 'user_password_new') { $act = 'npass';
        }
        switch($act) {
            case 'perfil' :
                $this -> cab();
                break;
            case 'pwsend' :
                $this -> cab();
                $this -> socials -> resend();
                break;
            case 'signup' :
                $this -> cab();
                $this -> socials -> signup();
                break;
            case 'logoff' :
                $this -> socials -> logout();
                break;
            case 'forgot' :
                $this -> cab();
                $this -> socials -> forgot();
                break;
            case 'npass' :
                $this -> cab();
                $email = get("dd0");
                $chk = get("chk");
                $chk2 = checkpost_link($email . $email);
                $chk3 = checkpost_link($email . date("Ymd"));

                if ((($chk != $chk2) AND ($chk != $chk3)) AND (!isset($_POST['dd1']))) {
                    $data['content'] = 'Erro de Check';
                    $this -> load -> view('show', $data);
                } else {
                    $dt = $this -> socials -> le_email($email);
                    if (count($dt) > 0) {
                        $id = $dt['id_us'];
                        $data['title'] = '';
                        $tela = '<br><br><h1>' . msg('change_password') . '</h1>';
                        $new = 1;
                        // Novo registro
                        $data['content'] = $tela . $this -> socials -> change_password($id, $new);
                        $this -> load -> view('show', $data);
                        //redirect(base_url("index.php/thesa/social/login"));
                    } else {
                        $data['content'] = 'Email não existe!';
                        $this -> load -> view('error', $data);
                    }
                }

                $this -> footer();
                break;
            case 'login' :
                $this -> cab();
                $this -> socials -> login();
                break;
            case 'login_local' :
                $ok = $this -> socials -> login_local();
                if ($ok == 1) {
                    redirect(base_url(PATH));
                } else {
                    redirect(base_url(PATH . 'social/login/') . '?erro=ERRO_DE_LOGIN');
                }
                break;
            default :
                echo "Function not found";
                break;
        }
    }

}
