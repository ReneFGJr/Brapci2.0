<?php
defined('BASEPATH') or exit('No direct script access allowed');
DEFINE("PATH", "index.php/books/");
DEFINE("LIBRARY_NAME", "Brapci Books 1.0");
DEFINE("LIBRARY", "9001");
define("SYSTEM_ID", 1);

class books extends CI_Controller
{

    function __construct()
    {

        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');

        //$this -> load -> library('tcpdf');

        $this->load->helper('form');
        $this->load->helper('form_sisdoc');
        $this->load->helper('xml');
        $this->load->helper('ai');
        #$this -> load -> helper('xml_dom');
        $this->load->helper("socials");
        $this->load->model('bs');
        $this->load->helper('rdf');
        //$this -> load -> helper('email');
        $this->load->helper('bootstrap');

        /* Language */
        $this->load->helper('language');
        $language = new language;
        $this->lang->load("brapci", $language->language());

        date_default_timezone_set('America/Sao_Paulo');
    }

    private function cab($data = array())
    {
        $data['title'] = '';
        $this->load->view('books/header/header.php', $data);
        if (!isset($data['nocab'])) {
            $this->load->view('books/header/menu_top.php', $data);
        }
    }

    private function footer($data = array())
    {
        $this->load->model('GoogleDialogFlow');
        if ($data == 0) {
            $data = array('simple' => true);
        }
        /* Google DialogFlow - IA - ChatBot*/
        $data['complement'] = $this->GoogleDialogFlow->bot();
        //$this->load->view('header/footer.php', $data);
    }


    public function book_register($act='', $id='')
    {
        $this->load->model('sources');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('export');
        $data['nocab'] = true;
        $this->cab();
    }

    public function index()
    {
        $this->load->model('elasticsearch');
        $this->load->model('libraries');
        $this->load->model('sources');
        $this->load->model('searchs');
        $this->load->model('events');
        $this->load->model('frbr');
        $this->cab();

        $this->footer();
    }

    public function about()
    {
        $this->cab();

        $data = array();
        $data['content'] = $this->load->view('brapci/about', null, true);
        $this->load->view('show', $data);
        $this->footer();
    } 

    public function v($id = '', $fmt = '')
    {
        if (round('0' . $id) == 0) {
            redirect(base_url(PATH));
        }
        $this->load->model('ias');
        $this->load->model('nets');
        $this->load->model('cited');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('genero');
        $this->load->model('frad');
        $this->load->model('handle');
        $this->load->model('altmets');
        $this->load->model('clicks');

        $this->clicks->click($id);

        $vv = $this->frbr_core->le_data($id);
        $data['meta'] = $vv;
        $data['id'] = $id;

        /***************************** formatos **/
        switch ($fmt) {
            case 'rdf':
                $rdf = new rdf;
                header("Content-Type: text/plain");
                header('Content-Disposition: attachment; filename="brapci_' . $id . '.rdf"');
                echo $rdf->export_rdf($id);
                return ('');
                exit;
            case 'json':
                $rdf = new rdf;
                //header("Content-Type: text/plain");
                //header('Content-Disposition: attachment; filename="brapci_'.$id.'.rdf"');
                echo $rdf->export_json($id);
                return ('');
                exit;
                break;
        }

        $this->cab($data);

        if (count($vv) == 0) {
            $this->load->view("error");
            return ("");
        }
        $tela = $this->frbr->vv($id);

        $data['content'] = $tela;
        $data['title'] = '';
        $this->load->view('show', $data);

        $this->footer();
    }

    function indice($type = '', $lt = '')
    {
        $this->load->model('frbr_core');
        $this->cab();
        switch ($type) {
            case 'article':
                if (perfil("#ADM")) {
                    $title = msg('index') . ': ' . msg('index_article');
                    $sx = $this->frbr_core->index_list($lt, 'Article');
                }
                break;
            case 'issue':
                if (perfil("#ADM")) {
                    $title = msg('index') . ': ' . msg('index_issue');
                    $sx = $this->frbr_core->index_list($lt, 'Issue');
                }
                break;
            case 'author':
                $title = msg('index') . ': ' . msg('index_authority');
                $sx = bs_pages(65, 90, PATH . 'indice/author');
                $sx .= $this->frbr_core->index_list_2($lt, 'Person', 1);
                break;
            case 'corporate':
                $title = msg('index') . ': ' . msg('index_serie');
                $sx = $this->frbr_core->index_list($lt, 'CorporateBody');

                break;
            case 'journal':
                $title = msg('index') . ': ' . msg('index_editor');
                $sx = $this->frbr_core->index_list($lt, 'Journal');
                break;
            case 'sections':
                $title = msg('index') . ': ' . msg('index_sections');
                $sx = $this->frbr_core->index_list($lt, 'ArticleSection');
                break;
            case 'subject':
                $title = msg('index') . ': ' . msg('index_sections');
                $sx = bs_pages(65, 90, PATH . 'indice/subject');
                $sx .= $this->frbr_core->index_list_2($lt, 'Subject', 1);
                break;

                //$title = msg('index') . ': ' . msg('index_sections');
                //$sx = $this -> frbr_core -> index_list($lt, 'Subject');
                //break;
            case 'words':
                $title = msg('index') . ': ' . msg('index_words');
                $sx = $this->frbr_core->index_list($lt, 'Word');
                break;
            case 'collection':
                $title = msg('Collection') . ': ' . msg('index_collection');
                $sx = $this->frbr_core->index_list($lt, 'Collection');
                break;
            default:
                $title = 'Índices';
                $sx = '<ul>';
                $sx .= '<h3>' . msg('Authorities') . '</h3>' . cr();
                $sx .= '<li><a href="' . base_url(PATH . 'indice/author') . '">' . msg('Authors') . '</a></li>';
                $sx .= '<li><a href="' . base_url(PATH . 'indice/subject') . '">' . msg('Subject') . '</a></li>';
                $sx .= '<li><a href="' . base_url(PATH . 'indice/corporate') . '">' . msg('CorporateBody') . '</a></li>';
                $sx .= '<br/>';
                $sx .= '<h3>' . msg('Journals') . '</h3>' . cr();
                $sx .= '<li><a href="' . base_url(PATH . 'indice/collection') . '">' . msg('Collection') . '</a></li>';
                $sx .= '<li><a href="' . base_url(PATH . 'indice/journal') . '">' . msg('Journal') . '</a></li>';
                $sx .= '<li><a href="' . base_url(PATH . 'indice/sections') . '">' . msg('Sections') . '</a></li>';
                $sx .= '<br/>';
                $sx .= '<h3>' . msg('Indiceadores') . '</h3>' . cr();
                //$sx .= '<li><a href="' . base_url(PATH . 'indice/words') . '">' . msg('Words') . '</a></li>';
                $sx .= '<li><a href="' . base_url(PATH . 'indicador/genere') . '">' . msg('Genere') . '</a></li>';

                $sx .= '</ul>';
        }
        $data['content'] = '<div class="row"><div class="col-md-12"><h1>' . $title . '</h1></div></div>' . $sx;
        $data['content'] = $sx;
        $this->load->view('show', $data);
        $this->footer();
    }

    function ajax($id = '', $id2 = '', $id3 = '', $id4 = '')
    {
        $this->load->model('searchs');
        $this->load->model('frbr_core');
        $q = get("q");
        switch ($id) {
            case 'rdf':
                $rdf = new rdf;
                $sx = $rdf->index($id, $id2, $id3, $id3);
                echo $sx;
                break;

            case 'inport':
                $cl = $this->frbr_core->le_class($id2);
                if (count($cl) == 0) {
                    echo "Erro de classe [$id2]";
                    exit;
                }
                $url = trim($cl['c_url']);
                $t = read_link($url);
                $this->frbr_core->inport_rdf($t, $id2);
                $sql = "update rdf_class set c_url_update = '" . date("Y-m-d") . "' where id_c = " . $cl['id_c'];
                $rlt = $this->db->query($sql);
                $sx = '';
                break;

            case 'exclude':
                $idc = $id2;
                $this->frbr_core->data_exclude($idc);
                $sx = '<div class="alert alert-success" role="alert">
                                                                                                                                                                        <strong>Sucesso!</strong> Item excluído da base.
                                                                                                                                                                        </div>';
                $sx .= '<meta http-equiv="refresh" content="1">';
                echo $sx;
                break;

            case 'ajax2':
                echo 'dd1=' . $id . '=dd2=' . $id2 . '=dd3=' . $id3 . '==' . $id4;
                echo $this->frbr_core->ajax2($id2, $id3, $id4);
                break;

            case 'ajax3':
                echo 'dd1=' . $id . '=dd2=' . $id2 . '=dd3=' . $id3 . '==' . $id4;
                $this->load->model('frbr_core');
                $val = get("q");
                $this->frbr_core->set_propriety($id3, $id2, $val, 0);
                echo '<meta http-equiv="refresh" content="0;">';
                break;

            case 'thesa':
                $this->load->model('thesa_api');
                $this->load->model('frbr_core');
                $this->thesa_api->ajax($id2);
                break;

            default:
                if (strlen($q) > 0) {
                    echo $this->searchs->ajax_q($q);
                } else {
                    //$type = $id2;
                    $this->load->model('frbr_core');
                    echo $this->frbr_core->model($id, $id2, '');
                }
        }
    }


}
