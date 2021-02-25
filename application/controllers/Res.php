<?php
$ip = $_SERVER['REMOTE_ADDR'];
if ((file_exists('maintenance')) and (substr($ip,0,11) != '143.54.144.'))
    {
        require("application/views/maintenance.php");
        exit;
    }
defined('BASEPATH') or exit('No direct script access allowed');
DEFINE("PATH", "index.php/res/");
DEFINE("LIBRARY_NAME", "Brapci 2.1");
DEFINE("LIBRARY", "9001");
define("SYSTEM_ID", 1);

class res extends CI_Controller
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
        $this->load->view('header/header.php', $data);
        if ((!isset($data['nocab'])) and (strlen(get("nocab")) == 0)) {
            $this->load->view('header/menu_top.php', $data);
        }
    }

    private function footer($data = array())
    {
        if ((!isset($data['nocab'])) and (strlen(get("nocab")) == 0)) {
            $this->load->model('GoogleDialogFlow');
            if ($data == 0) {
                $data = array('simple' => true);
            }
            /* Google DialogFlow - IA - ChatBot*/
            
            $data['complement'] = $this->GoogleDialogFlow->bot();
            $this->load->view('header/footer.php', $data);
        }
    }

    function evaluation($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $this->load->model('journal_evaluations');
        $this->cab();
        $data['content'] = $this->journal_evaluations->index($d1, $d2, $d3, $d4);
        $this->load->view('content', $data);
        $this->footer();
    }

    function ea($pth = '', $q = '')
    {
        $this->load->model('elasticsearch');
        switch ($pth) {
            case 'zera':
                break;
            case 'status':
                $this->load->model('elasticsearch_brapci20');
                print_r($this->elasticsearch->status());
                break;
            case 'journals':
                $this->load->model('elasticsearch_brapci20');
                $this->Elasticsearch_brapci20->journals_index();
                break;
            case 'query':
                $this->load->model('Elasticsearch_brapci20');
                $this->Elasticsearch_brapci20->query($q);
                break;
            default:
                $this->cab();

                break;
        }
        //$this->cab();

        //$result = $client->index($params);
    }

    public function issue($act, $id)
    {
        $this->load->model('sources');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('export');
        $data['nocab'] = true;
        $this->cab($data);
        $data['content'] = $this->frbr->issue_new($id);
        $this->load->view('show', $data);
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

        if (strlen(get("q")) == 0) {
            $data['events'] = $this->events->events_actives();
        } else {
            $data['events'] = '';
        }
        $this->load->view('brapci/form', $data);
        //$this -> load -> view('brapci/manutention', $data);

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
            $data['content'] = '' . $this->searchs->s($term, $type) . '';

            $data['content'] .= $this->searchs->historic();
            $this->load->view('show', $data);
        } else {
            /****************************************************************************/
            $data['content'] = $this->searchs->historic();
            //$data['content'] .= '<div class="col-md-2">xxxxxxxxxx</div>';

            $this->load->view('show', $data);
        }

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

    public function collections()
    {
        $this->load->model('sources');
        $this->cab();
        $tela = '<div class="row">';
        $tela .= '<div class="col-md-6">';
        $tela .= '<h1>' . msg('our_colletions') . '</h1>';
        $tela .= $this->sources->list_sources();
        $tela .= '</div>';
        $tela .= '<div class="col-md-6">';
        $tela .= $this->sources->timelines(1960);
        $tela .= '</div>';
        $tela .= '</div>';

        $data = array();
        $data['content'] = $tela;
        $this->load->view('show', $data);
        $this->footer();
    }

    public function article_new($id)
    {
        $this->load->model('nets');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('genero');
        $this->load->model('frad');

        $vv = $this->frbr_core->le_data($id);
        $data['meta'] = $vv;
        $data['id'] = $id;
        $this->cab($data);

        $dt['content'] = $this->frbr->form_article($id);
        $this->load->view('show', $dt);
        $this->footer();
    }

    public function ia($act = '', $id1 = '', $id2 = '', $id3 = '')
    {
        $this->load->model("ias");
        $this->cab();

        $data['content'] = $this->ias->index($act, $id1, $id2, $id3);
        $data['title'] = '';
        $this->load->view('content', $data);

        $this->footer();

        /*    
                        $this -> load -> model('ias');
                        $this -> load -> model('frbr');
                        $this -> load -> model('frbr_core');  
                        
                        $this->cab();
                        
                        if ($id == 0)
                        {
                            
                        } else {
                            $vv = $this -> frbr_core -> le_data($id);
                            echo '<pre>';
                            print_r($vv);
                            exit;    
                            $data['content'] = $this->ias->v($vv);
                            $data['title'] = '';
                            
                            $this -> load -> view('show', $data);
                        }
                        */
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

    public function jnl($id = '')
    {
        $this->load->model('frbr_core');
        $this->load->model('oai_pmh');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('sources');
        $this->cab();

        $data = $this->sources->le($id);
        $html = $this->sources->info($data);

        if (perfil("#ADM")) {
            $html .= $this->sources->button_new_sources($id);
            $html .= '&nbsp;';
            $html .= $this->sources->button_new_issue($id);
        }

        $html .= '<br><br>';
        $html .= $this->sources->show_issues($id);

        $data['content'] = $html;
        $this->load->view('show', $data);
        $this->footer();
    }

    public function jnl_edit($id = '', $chk = '')
    {
        if (perfil("#ADM")) {
            $this->load->model('oai_pmh');
            $this->load->model('sources');
            $this->cab();

            $cp = $this->sources->cp($id);
            $form = new form;
            $form->id = $id;
            $html = $form->editar($cp, $this->sources->table);
            if ($form->saved > 0) {
                redirect(base_url(PATH . 'journals'));
            }
            $data['content'] = $html;
            $this->load->view('show', $data);
            $this->footer();
        } else {
            redirect(base_url(PATH));
        }
    }

    /**********************************************
     * Funções de coleta das dados - Harvesting
     */
    public function cron($act = '', $token = '', $id = '')
    {
        $sx = cr();
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('oai_pmh');
        $this->load->model('sources');
        $socials = new socials;
        $usr = $socials->token($token);
        if (count($usr) == 0) {
            if ($token == 'free') {
            } else {
                $sx .= msg('Token not informed') . cr();
                $id = '';
            }
        }

        if (strlen($id) == 0) {
            $id = 'journal';
        }
        $sx .= '================================================' . cr();
        $sx .= date("Y-m-d H:i:s") . ' ACT:' . $id . cr();
        echo $sx;
        switch ($id) {
            case 'affiliation':
                $sx .= 'CROM Affiliation' . cr();
                $this->load->model('api_brapci');
                $sx .= $this->api_brapci->affiliation_cron();
                break;

            case 'journal':
                $data = $this->oai_pmh->NextHarvesting();
                if (count($data) > 0) {
                    $sx .= 'Harvesting OAI-PMH Journal' . cr();
                    $sx .= 'Journal: ' . $data['jnl_name'] . cr();
                    $idx = $data['id_jnl'];
                    $sr = $this->oai_pmh->ListIdentifiers($idx);
                    $sr = troca($sr, '</li>', '</li>' . cr());
                    $sr = strip_tags($sr);
                    $sx .= 'Status: ' . $this->oai_pmh->erro . cr();
                    $sx .= 'New registers: ' . $this->oai_pmh->new . cr();
                    $sx .= 'URL :' . $this->oai_pmh->oai_url($data, 'ListIdentifiers');
                    //$sx .= $sr;
                } else {
                    $sx .= msg('not_journal_to_harvesting') . cr();
                }
                break;
            default:
                $sx .= msg('not_action') . ' ' . $id . cr();
                echo "ACT:$act<br>Token:$token<br>id = $id";
        }

        /* save log */
        $filename = '/var/www/html/Brapci2.0/script/cron.oai.html';
        $t = date('Y-m-d H:i:s') . $sx . cr();
        $fld = fopen($filename, 'w+');
        fwrite($fld, $sx);
        fclose($fld);
        $sx .= cr() . '</pre>';
        echo $sx;
    }

    public function journals($act = '', $p = '')
    {
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('oai_pmh');
        $this->load->model('sources');
        $this->cab();
        $html = '';
        $data = array();
        $data['content'] = '';

        if (strlen($act) == 0) {

            //$data['content'] = '';
            if (perfil("#ADM")) {
                $data['content'] .= $this->sources->button_new_sources();
                $data['content'] .= $this->sources->button_harvesting_all();
                $data['content'] .= $this->sources->button_harvesting_status();
            }
            $data['content'] .= $this->sources->list_sources();
            $data['title'] = msg('journals');
        } else {
            $id = $this->sources->next_harvesting($p);
            if ($id > 0) {
                $this->oai_pmh->ListIdentifiers($id);
                $html = $this->sources->info($id);
                $html .= '<meta http-equiv="Refresh" content="5;' . base_url(PATH . 'journals/harvesting/' . ($id)) . '">';
            } else {
                $html .= '<div class="col-md-12">' . bs_alert('success', msg('harvesting_finished')) . '</div>';
                $html .= '<div class="col-md-12">' . $this->oai_pmh->cache_resume() . '</div>';
            }
        }
        $data['content'] .= $html;
        $this->load->view('show', $data);
        $this->footer();
    }

    function timeline($year)
    {
        $this->load->model('sources');
        $this->cab();
        $html = $this->sources->timelines($year);
        $data['content'] = $html;
        $this->load->view('show', $data);
        $this->footer();
    }

    function agent($nm = '')
    {
        $this->load->model('frbr');
        $this->load->model('sources');
        $this->cab();
        $html = $this->sources->agents_list();
        $data['content'] = $html;
        $this->load->view('show', $data);
        $this->footer();
    }

    public function norma()
    {
        $this->load->model('apa');
        $this->cab();

        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$T80:10', '', 'Referências ABNT', true, true));
        $form = new form;
        $tela = $form->editar($cp, '');
        if ($form->saved > 0) {
            $l = get("dd1");
            $l = troca($l, ';', '¢');
            $l = troca($l, chr(13), ';');
            $l = troca($l, chr(10), '');
            $ln = splitx(';', $l);
            for ($r = 0; $r < count($ln); $r++) {
                $ll = $ln[$r];
                $ll = troca($ll, '¢', ';');
                if (strlen($ll) > 0) {
                    $tela .= '<br>' . $this->apa->ABNTtoAPA($ll);
                }
            }
        }
        $data['content'] = $tela;
        $data['title'] = 'Result';
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

    function indicador($act = '', $id = '')
    {
        $this->cab();
        $this->load->model('bi');
        $data['content'] = $this->bi->action($act, $id);
        $this->load->view('show', $data);
        $this->footer();
    }

    public function oai($verb = '', $id = 0, $id2 = '', $id3 = '')
    {
        if (!perfil("#ADM")) {
            redirect(base_url(PATH));
        }

        $this->load->model('sources');
        $this->load->model('searchs');
        $this->load->model('oai_pmh');
        $this->load->model('export');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('Elasticsearch');
        $this->load->model('Elasticsearch_brapci20');
        $this->cab();
        $data['title'] = 'OAI';
        $url = '';

        switch ($verb) {
            case 'GetRecordScielo':
                $this->load->model('oai_pmh_scielo');

                $dt = array();
                $idc = $this->oai_pmh->getRecord($id);
                if ($idc > 0) {
                    $dt = $this->oai_pmh_scielo->getRecordScielo_oai_dc($idc, $dt);
                    $dt['idc'] = $idc;
                    $html = $this->sources->info($id);
                    $html .= $this->oai_pmh->process($dt);
                    $html .= '<meta http-equiv="Refresh" content="5">';
                } else {
                    $html = $this->sources->info($id);
                    $html .= '<h3>Fim da coleta</h3>';
                    $html .= '<br>' . date("d/m/Y H:i:s");
                }
                /***************************************************/

                //$html = '';
                //http://www.viaf.org/viaf/AutoSuggest?query=Zen, Ana Maria
                //http://www.viaf.org/processed/search/processed?query=local.personalName+all+"ZEN, Ana Maria Dalla"
                break;

            case 'GetRecord':
                $dt = array();
                $idc = $this->oai_pmh->getRecord($id);
                if ($idc > 0) {
                    $dt = $this->oai_pmh->getRecord_oai_dc($idc, $dt);
                    $dt['idc'] = $idc;
                    $html = $this->sources->info($id);
                    $html .= $this->oai_pmh->process($dt);
                    $html .= '<meta http-equiv="Refresh" content="5">';
                } else {
                    $html = $this->sources->info($id);
                    $html .= '<h3>Fim da coleta</h3>';
                    $html .= '<br>' . date("d/m/Y H:i:s");
                }
                /***************************************************/

                //$html = '';
                //http://www.viaf.org/viaf/AutoSuggest?query=Zen, Ana Maria
                //http://www.viaf.org/processed/search/processed?query=local.personalName+all+"ZEN, Ana Maria Dalla"
                break;

            case 'ListIdentifiers':
                $html = $this->sources->info($id);
                $html .= '<div class="row"><div class="col-12">';
                $data = $this->oai_pmh->ListIdentifiers($id);
                if (isset($data['Status'])) {
                    $html .= 'Status: ' . $data['Status'];
                    if ($data['Token'] != '') {
                        $html .= '<br>Token: <span style="color:red">' . $data['Token'] . '</span>';
                    }
                }
                $html .= '</div></div>';
                $url = $this->oai_pmh->url;
                break;

            case 'info':
                $html = $this->sources->info($id);
                break;

            case 'cache':
                $html = $this->sources->info($id);
                $html .= '<div class="col-2">';
                $html .= '<h1>CACHE</h1>';
                $html .= $this->oai_pmh->cache_change_to($id, $id2, $id3) . '</div>';
                $html .= '<div class="col-4">' . $this->oai_pmh->list_cache($id, $id2) . '</div>';
                break;
            case 'cache_status_to':
                $html = $this->sources->info($id);
                $html .= '<div class="col-2">';
                $html .= '<h1>CACHE ID</h1>';
                $this->oai_pmh->cache_reprocess($id3);
                $html .= $this->oai_pmh->list_cache($id, $id2, $id3);
                $html .= '</div>';
                break;
            case 'Identify':
                $html = $this->sources->info($id);
                $html .= $this->oai_pmh->Identify($id);
                redirect(base_url(PATH . 'oai/info/' . $id));
                break;
            default:
                $html = $this->oai_pmh->repository_list($id);
        }

        /* Status */
        $html .= '<div class="col-md-12">';
        $html .= '<pre>';
        $html .= 'Status: ' . $this->oai_pmh->erro . cr();
        $html .= 'Verb: ' . $verb . cr();
        $html .= 'URL: ' . $url . cr();
        $html .= '</pre>';
        $html .= '</div>';

        $data['content'] = $html;
        $this->load->view("show", $data);
        $this->footer();
    }

    public function authority()
    {
        $this->load->model('frbr');

        $this->cab();

        /***************/
        $tela = '<br>';
        $tela .= '<div class="row">' . CR;
        /***************/
        $tela .= '  <div class="col-md-12">';
        msg('find_viaf') . '</div>' . CR;
        $tela .= '      <a href="' . base_url(PATH . 'authority_create') . '" class="btn btn-secondary">' . CR;
        $tela .= '      Criar autoridade' . CR;
        $tela .= '      </a> ' . CR;
        $tela .= '  </div>' . CR;
        $tela .= '</div>' . CR;

        /***************/
        $tela .= '<div class="row" style="margin-top: 30px;">' . CR;
        $tela .= '      <div class="col-md-12">';
        $tela .= '          <a href="https://viaf.org/" target="_new_viaf_' . date("dhs") . '" class="btn btn-secondary">
                                                                                                        <img src="' . base_url('img/logo/logo_viaf.jpg') . '" class="img-fluid"></a>' . CR;
        $tela .= '      </div>' . CR;
        $tela .= '      <div class="col-md-12">' . CR;
        $tela .= msg('find_viaf');
        $tela .= '          <form method="post" action="' . base_url(PATH . "authority/") . '">' . CR;
        $tela .= '          ' . CR;
        $tela .= '          <div class="input-group">
                                                                                                        <input type="text" name="ulr_viaf" value="" class="form-control">
                                                                                                        <input type="hidden" name="action" value="viaf_inport">
                                                                                                        <span class="input-group-btn">
                                                                                                        <input type="submit" name="acao"  class="btn btn-danger" value="' . msg('inport') . '">
                                                                                                        </span>
                                                                                                        
                                                                                                        </div>';
        $tela .= '          </form>' . CR;
        $tela .= '          <span class="small">Ex: https://viaf.org/viaf/122976/#Souza,_Herbert_de</span>';
        $tela .= '      </div>' . CR;
        $tela .= '  </div>' . CR;

        /***********************************/
        $tela .= '</div>' . CR;
        $data['content'] = $tela;
        $data['title'] = '';
        $this->load->view('show', $data);

        /***************** inport VIAF ***********/
        $acao = get("action");
        switch ($acao) {
                /***************** inport VIAF ***********/
            case 'viaf_inport':
                $url = get("ulr_viaf");
                $data['content'] = $this->frbr->viaf_inport($url);
                $this->load->view('show', $data);
                break;
                /***************** inport GEONames ***********/
            case 'geonames_inport':
                $url = get("ulr_geonames");
                $data['content'] = $this->frbr->geonames_inport($url);
                $this->load->view('show', $data);
                break;
            default:
                echo $acao;
        }

        $this->footer();
    }

    function help()
    {
        $this->cab();
        $this->load->view('brapci/help');
        $this->footer();
    }

    function mark($key = '', $vlr = '')
    {
        $this->bs->ajax_mark($key, $vlr);
    }

    function mark_all($key = '', $vlr = '')
    {
        $this->bs->ajax_mark_all($key, $vlr);
    }

    function basket($fcn = '', $arg = '')
    {
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        switch ($fcn) {
            case 'metrics':
                $this->load->model("bibliometrics");
                $this->bibliometrics->metrics_basket();
                break;

                /* Export */
            case 'export':
                switch ($arg) {
                    case 'xls':
                        $this->bs->mark_export_xls();
                        break;
                    case 'csv':
                        $this->bs->mark_export_csv();
                        break;
                    case 'doc':
                        $this->bs->mark_export_doc();
                        break;
                    case 'ris':
                        $this->bs->mark_export_ris();
                        break;
                    default:
                        redirect(base_url(PATH . 'basket'));
                        break;
                }
                break;
            case 'clean':
                $this->bs->mark_clear();
                redirect(base_url(PATH . 'basket'));
            case 'inport':
                $this->cab();
                $data = array();
                $data['content'] = '';
                $data['content'] .= '<h1>' . msg('References') . '</h1>' . cr();
                $data['content'] .= $this->bs->mark_form_inport();
                $this->load->view('show', $data);
                $this->footer();

            case 'save':
                $this->cab();
                $data = array();
                $data['content'] = '';
                $data['content'] .= '<h1>' . msg('References') . '</h1>' . cr();
                $data['content'] .= $this->bs->mark_save();
                $this->load->view('show', $data);
                $this->footer();
                break;
            case 'saved':
                $this->cab();
                $data = array();
                $data['content'] = '';
                $data['content'] .= '<h1>' . msg('References') . '</h1>' . cr();
                $data['content'] .= $this->bs->mark_saved();
                $this->load->view('show', $data);
                $this->footer();
                break;

            default:
                $this->cab();
                $data['content'] = $this->bs->tools();
                $data['content'] .= '<h1>' . msg('References') . '</h1>' . cr();
                $data['content'] .= $this->bs->basket();
                $this->load->view('show', $data);
                $this->footer();
                break;
        }
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

    function event($act = '', $id = '')
    {
        $this->load->model('events');
        if ($id != '') {
            $this->events->click($id);
            exit;
        }
        $this->cab();
        $data['content'] = '<div class="row">';
        $data['content'] .= '<div class="col-md-12">';
        $data['content'] .= '<h1>' . msg('event') . '</h1>' . '<p>Para registrar um evento, envie um e-mail para brapcici@gmail.com com o assunto [Evento]<p>';
        $data['content'] .= $this->events->events_actives(1);
        $data['content'] .= '</div>';
        $data['content'] .= '</div>';
        $this->load->view('show', $data);

        $this->footer();
    }

    function export($tp = '', $pg = 0)
    {
        $this->load->model('export');
        $this->load->model('genero');
        $this->load->model('frbr_core');
        $this->load->model('elasticsearch');

        $this->cab();

        switch ($tp) {
            case 'genere':
                $tela = $this->genero->export();
                break;
            case 'all_xls':
                $this->export->all_xls();
                break;
            case 'issue':
                $tela = $this->export->export_Issue($pg);
                if (strlen($tela) <= 25) {
                    redirect(base_url(PATH . '/export'));
                }
                break;
            case 'article':
                $tela = $this->export->export_Article($pg);
                break;
            case 'subject':
                if ($pg == 0) {
                    $pg = 65;
                }
                $tela = $this->export->export_subject_index_list($pg);
                break;
            case 'subject_reverse':
                $tela = $this->export->export_subject_reverse($pg);
                break;
            case 'index_authors':
                if ($pg == 0) {
                    $pg = 65;
                }
                $tela = $this->export->export_author_index_list($pg);
                break;
            case 'collections_form':
                $tela = $this->export->collections_form();
                break;
            default:
                $tela = '<h1>' . msg('export') . '</h1>';
                $tela .= '<ul>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/issue') . '">' . msg('export_issue') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/article') . '">' . msg('export_article') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/subject') . '">' . msg('export_subject') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/subject_reverse') . '">' . msg('export_subject_reverse') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/index_authors') . '">' . msg('export_index_authors') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/collections_form') . '">' . msg('export_collections_form') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/all_xls') . '">' . msg('export_all_xls') . '</a></li>' . cr();
                $tela .= '<li><a href="' . base_url(PATH . 'export/genere') . '">' . msg('export_genere') . '</a></li>' . cr();
                $tela .= '</ul>' . cr();
        }

        $data['content'] = $tela;
        $data['title'] = '';
        $this->load->view('show', $data);

        $this->footer();
    }

    function concept_del($id = '', $chk = '')
    {
        $this->load->model('frbr');
        $chk = checkpost_link($id . 'Concept');
        if (checkpost_link($id . 'Concept') == $chk) {
            $this->frbr->remove_concept($id);
        } else {
            echo "Erro de Post";
        }
    }

    function download($d1 = '')
    {
        $d1 = round($d1);
        $this->load->model('pdfs');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->pdfs->download($d1);
    }

    function txt($d1 = '')
    {
        $d1 = round($d1);
        $this->load->model('pdfs');
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->pdfs->txt($d1);
    }

    function pdf_download($d1 = '', $d2 = '')
    {
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('pdfs');
        $this->pdfs->harvesting_pdf($d1);
        echo '===>' . $d1 . '-' . $d2;
        exit;
        $this->load->library('Pdfmerger');
        $pdf = new PDFMerger;

        $pdf->addPDF('d:/lixo/pdf/one.pdf', '1, 3, 4')->addPDF('d:/lixo/pdf/two.pdf', '1-2')->addPDF('d:/lixo/pdf/three.pdf', 'all');
        $pdf->merge('file', 'd:/lixo/pdf/TEST3.pdf');
        echo "FIM";
    }

    function pdf_upload($d1 = '', $d2 = '')
    {
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('pdfs');
        $data['nocab'] = true;
        $this->cab($data);
        $data['content'] = $this->pdfs->upload($d1);
        $this->load->view('show', $data);
    }

    /* LOGIN */
    function social($act = '', $id = '', $chk = '')
    {
        $this->cab();
        $socials = new socials;
        $data['content'] = $socials->social($act, $id, $chk);
        if (strlen($data['content']) > 0) {
            $this->load->view('content', $data);
        }
        return ('');
    }


    function tools($p = '', $id = '0', $id2 = '')
    {
        $this->cab();

        switch ($p) {
            case '':
                $data['title'] = msg('Tools');
                $txt = '<div class="col-md-12">';
                $txt .= '<h1>' . msg('tools_title') . '</h1>';
                $txt .= '</div>';

                $txt .= '<div class="col-md-12">';
                $txt .= '<ul>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/pdf_import') . '">' . msg('tools_pdf_import') . '</a>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/oai_import') . '">' . msg('tools_oai_import') . '</a>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'journals/harvesting') . '">' . msg('harvesting_all') . '</a>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/dates') . '">' . msg('harvesting_all_dates') . '</a>';
                $txt .= '</ul>';

                $txt .= '<h4>' . msg('tools_title_check') . '</h4>';
                $txt .= '<ul>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/pdf_check') . '">' . msg('tools_pdf_check') . '</a>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/pdf_check_article') . '">' . msg('tools_pdf_check_article') . '</a>';
                $txt .= '</ul>';

                $txt .= '<h4>' . msg('tools_remissive') . '</h4>';
                $txt .= '<ul>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/remissive') . '">' . msg('tools_remissive_check') . '</a>';
                $txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/genere') . '">' . msg('tools_genre') . '</a>';
                $txt .= '</ul>';

                $txt .= '</div>';
                $data['content'] = $txt;
                $this->load->view('show', $data);
                break;
            case 'import_issue':
                $this->load->model('oai_pmh');
                $this->load->model('frbr');
                $this->load->model('export');
                $this->load->model('frbr_core');
                $this->load->model('indexer');
                $this->load->model('genero');
                $this->load->model('frad');

                $txt = '<div class="row"><div class="col-12">';
                $txt .= '<h1>Import DC Articles</h1>';
                $form = new form;
                $cp = array();
                array_push($cp, array('$FILE', '', 'Arquivo CSV', true, true));
                $txt .= $form->editar($cp, '');
                $txt .= '</div></div>';
                $txt .= '<ol>';
                if (isset($_FILES['fileToUpload'])) {
                    $flv = $_FILES['fileToUpload'];
                    $flv = $flv['tmp_name'];
                    $csv = read_csv($flv);
                    $rdf = new rdf;
                    for ($r = 2; $r < count($csv); $r++) {
                        $ln = $csv[$r];
                        /* Title */
                        $_POST['dd5'] = $ln[0];
                        $_POST['dd10'] = '';
                        $_POST['dd2'] = $ln[6] . chr(13) . $ln[7] . chr(13) . $ln[8] . chr(13) . $ln[9] . chr(13) . $ln[10] . chr(13) . $ln[11] . chr(13) . $ln[12] . chr(13) . $ln[13] . chr(13) . $ln[14];
                        $_POST['dd8'] = 'pt-BR';
                        $_POST['dd15'] = $ln[2];
                        $_POST['dd16'] = $ln[3];
                        $_POST['dd17'] = $ln[5];
                        if (strlen(trim($ln[0])) == 0) {
                            $r = 99999999;
                        } else {
                            $txt .= '<li>' . $ln['0'] . ' [' . $this->frbr->form_article_save($id) . ']</>';
                        }
                    }
                }
                $txt .= '</ol>';
                $data['content'] = $txt;
                $this->load->view('show', $data);
                break;
                break;
            case 'pdf_check_article':
                $this->load->model("frbr");
                $this->load->model("frbr_core");
                $this->load->model("pdfs");
                $sx = '<h1>PDF Check</h1>';
                $sx .= '<br><p>Localizar artigos sem PDF</p>';
                $data['content'] = $sx . $this->pdfs->journals_files();
                $this->load->view('show', $data);
                break;
            case 'genere':
                $this->load->model("frbr");
                $this->load->model("frbr_core");
                $this->load->model("Genero");
                $sx = $this->Genero->author_check($p, $id);
                $data['content'] = $sx;
                $this->load->view('show', $data);
                break;
            case 'oai_import':
                $data['title'] = msg('Tools');
                $txt = '<div class="col-md-12">';
                $txt .= '<h1>' . msg('tools_oai_harvesting') . '</h1>';
                $txt .= '</div>';
                //$txt .= $this -> pdfs -> harvestinf_next($id);

                /* Coleta */
                $this->load->model('sources');
                $this->load->model('searchs');
                $this->load->model('oai_pmh');
                $this->load->model('export');
                $this->load->model('frbr');
                $this->load->model('frbr_core');
                $this->load->model('Elasticsearch');
                $this->load->model('Elasticsearch_brapci20');
                $dt = array();
                $idc = $this->oai_pmh->getRecord(0);
                if ($idc > 0) {
                    //$dt = $this -> oai_pmh -> getRecordNlM($idc, $dt);
                    $dt = $this->oai_pmh->getRecord_oai_dc($idc, $dt);
                    $dt['idc'] = $idc;
                    //$txt = $this -> sources -> info($id);
                    $txt .= $this->oai_pmh->process($dt);
                    $txt .= '<meta http-equiv="Refresh" content="5">';
                } else {
                    $txt = $this->sources->info($id);
                    $txt .= '<h3>Fim da coleta</h3>';
                    $txt .= '<br>' . date("d/m/Y H:i:s");
                }
                /***************************************************/
                $data['content'] = $txt;
                $this->load->view('show', $data);

                //$html = '';
                //http://www.viaf.org/viaf/AutoSuggest?query=Zen, Ana Maria
                //http://www.viaf.org/processed/search/processed?query=local.personalName+all+"ZEN, Ana Maria Dalla"

                break;
            case 'remissive':
                $this->load->model("frbr");
                $this->load->model("frbr_core");
                $sx = $this->frbr->author_check_remissive($p, $id);
                $data['content'] = $sx;
                $this->load->view('show', $data);
                break;
            case 'pdf_check':
                $this->load->model("pdfs");
                $this->load->model("frbr");
                $this->load->model("frbr_core");

                $this->cab();
                $data['content'] = $this->pdfs->check_pdf();
                $this->load->view('show', $data);
                break;
            case 'pdf_import':
                $this->load->model("pdfs");
                $this->load->model("frbr");
                $this->load->model("frbr_core");

                $data['title'] = msg('Tools');
                $txt = '<div class="col-md-12">';
                $txt .= '<h1>' . msg('tools_harvesting') . '</h1>';
                $txt .= '</div>';
                $txt .= $this->pdfs->harvesting_next($id);
                $data['content'] = $txt;
                $this->load->view('show', $data);
                break;
            case 'dates':
                $this->load->model("pdfs");
                $this->load->model("frbr");
                $this->load->model("frbr_core");

                $data['title'] = msg('Tools');
                $txt = '<div class="col-md-12">';
                $txt .= '<h1>' . msg('harvesting_all_dates') . '</h1>';
                $txt .= '</div>';
                $txt .= $this->pdfs->harvesting_dates($id);
                $data['content'] = $txt;
                $this->load->view('show', $data);
                break;
        }
        $this->footer(0);
    }

    function a($id = '')
    {
        if (!perfil("#ADM"))
            {
                redirect(base_url(PATH.'v/'.$id));
            }

        $rdf = new rdf;
        $data = $rdf->le($id);

        $this->cab();
        $this->load->view('welcome');

        $tela = '';
        $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="middle">';
        $linkca = '</a>';

        if (strlen($data['n_name']) > 0) {
            $tela .= '<h2>' . $linkc . $data['n_name'] . $linkca . '</h2>';
        }
        $linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="btn btn-secondary">';
        $linkca = '</a>';

        $linkd = '<a href="' . base_url(PATH . 'd/' . $id) . '" class="btn btn-danger">';
        $linkda = '</a>';

        $tela .= '
            <div class="container">
            <div class="row">
            <div class="col-md-8">
            <h5>' . msg('class') . ': ' . $data['c_class'] . '</h5>
            </div>
            
            <div class="col-md-4 text-right">';
        if ((perfil("#ADM") > 0)) {
            $tela .= $linkd . msg('delete') . $linkda . ' ';
        }
        $tela .= $linkc . msg('return') . $linkca;

        $tela .= '</div></div>';
        $tela .= $rdf->form($id, $data);

        switch ($data['c_class'].'X') {
            case 'Person':
                $tela .= $this->frbr->show($id);
                break;
            case 'Family':
                $tela .= $this->frbr->show($id);
                break;
            case 'Corporate Body':
                $tela .= $this->frbr->show($id);
                break;
            default:
                break;
        }
        $tela .= '</div>';
        $data['title'] = '';
        $data['content'] = $tela;

        $this->load->view('content', $data);
        $this->footer();
    }

    function rdf($path = '', $id = '', $form = '', $idx = 0, $idy = '')
    {
        $link = 'x';
        $rdf = new rdf;
        $sx = $rdf->index($link, $path, $id, $form, $idx, $idy);

        if (strlen($sx) > 0) {
            $data['nocab'] = true;
            $this->cab($data);

            $data['content'] = $sx;
            $this->load->view("content", $data);
        }
    }

    function vocabulary_ed($id = '')
    {
        $this->cab();
        $cp = array();
        array_push($cp, array('$H8', 'id_c', '', false, true));
        array_push($cp, array('$S100', 'c_class', 'Classe', true, true));
        array_push($cp, array('$O : &C:Classe&P:Propriety', 'c_type', 'Tipo', true, true));
        array_push($cp, array('$O 1:SIM&0:NÃO', 'c_find', 'Busca', true, true));
        array_push($cp, array('$O 1:SIM&0:NÃO', 'c_vc', 'Vocabulário Controlado', true, true));
        array_push($cp, array('$S100', 'c_url', 'URL', false, true));
        array_push($cp, array('$B8', '', 'Gravar', false, true));
        $form = new form;
        $form->id = $id;
        $tela = $form->editar($cp, 'rdf_class');
        if ($form->saved > 0) {
            redirect(base_url(PATH . 'vocabulary'));
        }

        $data['content'] = '<h1>Classes e Propriedades</h1>' . $tela;
        $this->load->view('show', $data);
        $this->footer();
    }

    public function vocabulary($id = '')
    {
        $this->load->model('frbr');
        $this->load->model('vocabularies');
        $this->cab();

        $dd1 = get("dd1");
        if (strlen($dd1) > 0) {
            $class = $id;
            $id_s = $this->frbr->frbr_name($dd1);
            $p_id = $this->frbr->rdf_concept($id_s, $class);
            $this->frbr->set_propriety($p_id, 'prefLabel', 0, $id_s);
        }

        $t1 = $this->vocabularies->list_vc($id);
        $t1 .= $this->vocabularies->modal_vc($id);
        $t1 = '<h3>Classe: ' . msg($id) . '</h3>' . $t1;

        $t2 = $this->vocabularies->list_thesa($id);
        $t2 = '<h3>Classe Thesa: ' . msg($id) . '</h3>' . $t2;

        $tela = '
                                                                                                                                                                                                                                                                <div class="container">
                                                                                                                                                                                                                                                                <div class="row">
                                                                                                                                                                                                                                                                <div class="col-md-6">' . $t1 . '</div>
                                                                                                                                                                                                                                                                <div class="col-md-6">' . $t2 . '</div>
                                                                                                                                                                                                                                                                </div>';
        if (perfil("#ADM")) {
            $tela .= '<div class="row">';
            $tela .= '<div class="col-md-12">';
            $tela .= '<a href="' . base_url(PATH . 'vocabulary_ed/0') . '" class="btn btn-secondary">' . msg('new') . '</a>';
            $tela .= '</div>';
            $tela .= '</div>';
        }
        $tela .= '</div>';
        $data['content'] = $tela;
        $this->load->view('show', $data);
    }

    public function thesa($id = '')
    {
        $this->load->model('frbr');
        $this->load->model('frbr_core');
        $this->load->model('vocabularies');
        $this->cab();

        $datac = $this->frbr_core->le_class($id);

        $tela = $this->load->view('find/view/class', $datac, true);
        $tela .= '<div class="container">
                                                                                                                                                                                                                                                                <div class="row"><div class="col-md-4" >' . $this->vocabularies->modal_th($id) . '</div>';
        $tela .= '<div class="col-md-8">' . $this->vocabularies->list_vc($id) . '</div></div>
                                                                                                                                                                                                                                                                </div>';

        $data['content'] = $tela;
        $this->load->view('show', $data);
    }

    public function config($tools = '', $ac = '', $i1 = '', $i2 = '', $i3 = '')
    {
        $this->load->model("frbr");

        /********************* EXPORTS ************************/
        switch ($tools) {
            case 'class_export':
                /* acao */
                $this->load->model("frbr_clients");
                $this->frbr_clients->export_class($ac);
                return ('');
                exit;
        }

        $cab = 1;
        $this->cab();

        if (!perfil("#ADM")) {
            redirect(base_url('index.php/main'));
        }

        $this->load->view('welcome');
        $tela = '';

        switch ($tools) {
            case 'email':
                $tela .= '<h1>e-mail</h1>';
                $this->load->helper('email');
                $email = 'renefgj@gmail.com';
                echo 'Enviando para ' . $email;
                enviaremail($email, 'teste', 'teste');
                echo '===>' . $this->email->send();
                break;
            case 'class_export':
                /* acao */
                $this->load->model("frbr_clients");
                $tela .= $this->frbr_clients->export_class();
                break;
            case 'event':
                /* acao */
                $this->load->model("events");
                if (strlen($ac) > 0) {
                    $tela .= $this->events->$ac($i1, $i2, $i3);
                } else {
                    $tela .= $this->events->events_lista();
                }
                break;
            case 'class':
                /* Classes */
                $rdf = new rdf;
                $tela .= $rdf->index($tools,$ac, $i1, $i2, $i3);
                break;
            case 'msg':
                /* acao */
                if (strlen($ac) > 0) {
                    $tela .= msg('MAKE_MESSAGES');
                } else {
                    $tela .= msg_lista();
                }

                break;
            case 'forms':
                $tela .= '<h1>' . msg('FORMS') . '</h1>';
                $tela .= '<hr>';
                $this->load->model("frbr_core");
                $tela .= $this->frbr_core->form_class();
                break;
            case 'authority':
                if (perfil("#ADM") == 1) {
                    if ($ac == 'update') {
                        $tela .= $this->frbr->viaf_update();
                    } else {
                        $tela .= '<br><a href="' . base_url(PATH . 'config/authority/update') . '" class="btn btn-secondary">' . msg('authority_update') . '</a>';
                    }
                    $tela .= '<br><br><h3>' . msg('Authority') . ' ' . msg('viaf') . '</h3>';
                    $tela .= $this->frbr->authority_class();
                }
                break;
            default:
                $tela = '<div class="col-md-12">' . cr();
                $tela .= '<h1>' . msg('config') . '</h1>' . cr();
                $tela .= '<ul>' . cr();
                $tela .= '<li>' . '<a href="' . base_url(PATH . 'config/forms') . '">' . msg('config_forms') . '</a></li>' . cr();
                $tela .= '<li>' . '<a href="' . base_url(PATH . 'config/class') . '">' . msg('config_class') . '</a></li>' . cr();
                $tela .= '<li>' . '<a href="' . base_url(PATH . 'config/email') . '">' . msg('config_email') . '</a></li>' . cr();
                $tela .= '<li>' . '<a href="' . base_url(PATH . 'config/event') . '">' . msg('config_event') . '</a></li>' . cr();
                $tela .= '</ul>' . cr();
                $tela .= '</div>' . cr();
                break;
        }
        $data['content'] = $tela;
        $this->load->view('show', $data);
        $this->footer();
    }

    public function pop_config($tools = '', $id = '')
    {
        $this->load->model("frbr_core");
        $data['nocab'] = true;
        $this->cab($data);
        $tela = '';
        $tela .= $tools;
        switch ($tools) {
            case 'msg':
                $tela .= $this->frbr_core->form_msg_ed($id);
                break;
            case 'forms':
                $tela .= msg('FORMS');
                $tela .= $this->frbr_core->form_class_ed($id);
                break;
        }
        $data['content'] = $tela;
        $this->load->view('show', $data);
    }

    function frad($id = '')
    {
        $this->load->model("frad");
        $this->load->model("frbr_core");
        $data['nocab'] = true;

        $this->cab($data);
        $data = $this->frbr_core->le($id);
        $id = $data['id_cc'];
        $nm = $data['n_name'];
        $tela = '<div class="container">' . cr();
        $tela .= '<div class="row"><div class="col-md-12">';
        $tela .= '<h1>' . $data['n_name'] . ' (' . $id . ')' . '</h1>';
        $tela .= '</div></div></div>' . cr();

        $tela .= $this->frad->find_remissiva_form($id, $nm);

        $data['content'] = $tela;
        $this->load->view('show', $data);
    }

    function frad_corporate($id = '')
    {
        $this->load->model("frad");
        $this->load->model("frbr_core");
        $data['nocab'] = true;

        $this->cab($data);
        $data = $this->frbr_core->le($id);
        $id = $data['id_cc'];
        $nm = $data['n_name'];
        $tela = '<div class="container">' . cr();
        $tela .= '<div class="row"><div class="col-md-12">';
        $tela .= '<h1>' . $data['n_name'] . ' (' . $id . ')' . '</h1>';
        $tela .= '</div></div></div>' . cr();

        $tela .= $this->frad->find_remissiva_form($id, $nm, 'CorporateBody');

        $data['content'] = $tela;
        $this->load->view('show', $data);
    }

    function summary($cmd = '')
    {
        $this->load->model("frad");
        $this->load->model("frbr_core");
        $this->load->model("sources");

        $this->cab();
        $tela = $this->sources->summary($cmd);
        $data['content'] = $tela;

        $this->load->view('show', $data);
        $this->footer();
    }

    function collection()
    {
        $this->cab();
        $tela = '<div class="row">' . cr();
        $tela .= '<div class="col-md12">' . cr();
        $tela .= '<h1>' . msg("Collection") . '</h1>';

        $tela .= '</div>' . cr();
        $tela .= '</div>' . cr();

        $data['content'] = $tela;
        $this->load->view('show', $data);

        $this->footer();
    }

    function metadata($id = 0)
    {
        $this->load->model("sources");
        $this->load->model("oai_pmh");
        $this->cab();
        $tela = '<div class="row"><div class="col-md-12">';
        $tela .= '<h1>' . msg('metadata') . '</h1>';
        $tela .= $this->oai_pmh->valida_metadata($id);
        $tela .= '</div></div>';
        $data['content'] = $tela;

        $this->load->view('show', $data);
        $this->footer();
    }

    function patent($ac = '', $pg = 0)
    {
        $this->load->model("frad");
        $this->load->model("frbr_core");
        $this->load->model('patents');
        $this->cab();
        $tela = $this->patents->import();

        $data['content'] = $tela;
        $this->load->view('show', $data);
        $this->footer();
    }

    function bibliometric($ac = '')
    {
        $this->load->model("bibliometrics");
        $tela = '';
        $tela .= '<div class="row">';
        $tela .= '<div class="col-md-12">';
        $tela .= $this->bibliometrics->index($ac);
        $this->cab();
        $tela .= '</div>';
        $tela .= '</div>';
        $data['content'] = $tela;
        $this->load->view('show', $data);
        $this->footer();
    }

    function handle($act = '', $token = '')
    {
        if ((perfil("#ADM") > 0) or ($token == '0mhHuERfFBpuwULJSZXGNJc5agPVZZHe')) {
            switch ($act) {
                case 'register':
                    $this->cab();
                    $this->load->model("handle");
                    echo '<pre>' . $this->handle->handle_register() . '</pre>';
                    break;
                case 'form':
                    $this->cab();
                    $this->load->model("handle");
                    echo '<pre>' . $this->handle->form() . '</pre>';
                    break;
                default:
                    echo 'use: register';
                    break;
            }
        } else {
            echo "OPS - Não está logado";
        }
    }

    function labels($pg = '')
    {
        if (perfil("#ADM") > 0) {
            $this->load->model('frbr');
            $this->cab();
            $this->frbr->labels($pg);
            $this->footer();
        } else {
            redirect(base_url(PATH));
        }
    }

    function labels_ed($id = '', $chk = '', $close = 0)
    {
        if (perfil("#ADM") > 0) {

            $this->load->model('frbr');
            $this->cab();
            $this->frbr->labels_ed($id, $chk, $close);
            $this->footer();
        } else {
            redirect(base_url(PATH));
        }
    }

    function qualis($id = '', $act = '')
    {
        $this->load->model("qualis");
        $this->cab();
        switch ($act) {
            case 'row':
                $data = $this->qualis->journal_row();
                break;
            case 'journal':
                $data = $this->qualis->journal_show($id);
                break;
            case 'inport':
                $data['title'] = 'Qualis Inport';
                $data['content'] = $this->qualis->inport();
                break;
            default:
                $data['title'] = 'Qualis';
                $data['content'] = $this->qualis->resume();
                break;
        }
        $this->load->view('show', $data);
        $this->footer();
    }

    function api($act = '', $token = '')
    {
        $this->load->model('api_brapci');
        $this->api_brapci->index($act, $token);
    }

    function bot()
    {
        $this->cab();


        $this->load->model('GoogleDialogFlow');

        /* Google DialogFlow - IA - ChatBot*/
        $data['content'] = '<h1>DialogFlow Brapci Bot</h1>';
        $data['content'] .= $this->GoogleDialogFlow->bot();
        $data['content'] .= '<iframe
                            allow="microphone;"
                            width="350"
                            height="550"
                            src="https://console.dialogflow.com/api-client/demo/embedded/31e54c28-4130-4c99-9712-d3b330327b0a">
                            </iframe>';
        $data['title'] = '';
        $this->load->view('content', $data);

        /* Footer */
        $this->footer();
    }
    function pq($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $this->cab();
        $this->load->model("pqs");

        $data['title'] = '';
        $data['content'] = $this->pqs->index($d1, $d2, $d3, $d4);
        $this->load->view('content', $data);

        /* Footer */
        $this->footer();
    }
}
