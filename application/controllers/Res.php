<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class res extends CI_Controller {

    function __construct() {

        parent::__construct();
        $this -> lang -> load("app", "portuguese");
        $this -> load -> database();
        $this -> load -> helper('url');
        $this -> load -> library('session');

        $this -> load -> helper('form');
        $this -> load -> helper('form_sisdoc');
        $this -> load -> helper('xml');
        #$this -> load -> helper('xml_dom');

        date_default_timezone_set('America/Sao_Paulo');
    }

    private function cab($data = array()) {
        $data['title'] = 'Brapci';
        $this -> load -> view('header/header.php', $data);
        $this -> load -> view('header/menu_top.php', $data);
        $this -> load -> model("socials");
    }

    private function footer($data = array()) {
        $this -> load -> view('header/footer.php');
    }

    public function index() {
        $this -> load -> model('sources');
        $this -> cab();
        
        $this->load->view('brapci/resume.php');

        $data = array();
        $data['content'] = $this -> sources -> list_sources();
        $this -> load -> view('show', $data);
        $this -> footer();
    }
    
    public function about() {
        $this -> cab();

        $data = array();
        $data['content'] = $this -> load->view('brapci/about',null,true);
        $this -> load -> view('show', $data);
        $this -> footer();
    }    

    public function v($id) {
        $this -> load -> model('frbr');
        $this -> cab();

        $tela = $this -> frbr -> vv($id);

        $data['content'] = $tela;
        $this -> load -> view('show', $data);

        $this -> footer();
    }

    public function jnl($id = '') {
        $this -> load -> model('oai_pmh');
        $this -> load -> model('sources');
        $this -> cab();

        $data = $this -> sources -> le($id);
        $html = $this -> sources -> info($data);
        $data['content'] = $html;
        $this -> load -> view('show', $data);
        $this -> footer();
    }

    function timeline($year) {
        $this -> load -> model('sources');
        $this -> cab();
        $html = $this -> sources -> timelines($year);
        $data['content'] = $html;
        $this -> load -> view('show', $data);
        $this -> footer();
    }

    function agent($nm = '') {
        $this -> load -> model('frbr');
        $this -> load -> model('sources');
        $this -> cab();
        $html = $this -> sources -> agents_list();
        $data['content'] = $html;
        $this -> load -> view('show', $data);
        $this -> footer();
    }

    public function norma() {
        $this -> load -> model('apa');
        $this -> cab();

        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$T80:10', '', 'Referências ABNT', true, true));
        $form = new form;
        $tela = $form -> editar($cp, '');
        if ($form -> saved > 0) {
            $l = get("dd1");
            $l = troca($l, ';', '¢');
            $l = troca($l, chr(13), ';');
            $l = troca($l, chr(10), '');
            $ln = splitx(';', $l);
            for ($r = 0; $r < count($ln); $r++) {
                $ll = $ln[$r];
                $ll = troca($ll, '¢', ';');
                if (strlen($ll) > 0) {
                    $tela .= '<br>' . $this -> apa -> ABNTtoAPA($ll);
                }

            }

        }
        $data['content'] = $tela;
        $data['title'] = 'Result';
        $this -> load -> view('show', $data);
        $this -> footer();
    }
    
    function indice($type = '', $lt = '') {
        $this -> load -> model('frbr_core');
        $this -> cab();
        switch ($type) {
            case 'author' :
                $title = msg('index') . ': ' . msg('index_authority');
                $sx = $this -> frbr_core -> index_list($lt);

                break;
            case 'corporate' :
                $title = msg('index') . ': ' . msg('index_serie');
                $sx = $this -> frbr_core -> index_list($lt, 'Corporate Body');

                break;
            case 'journal' :
                $title = msg('index') . ': ' . msg('index_editor');
                $sx = $this -> frbr_core -> index_list($lt, 'Journal');
                break;
            case 'sections' :
                $title = msg('index') . ': ' . msg('index_sections');
                $sx = $this -> frbr_core -> index_list($lt, 'ArticleSection');
                break;     
            case 'subject' :
                $title = msg('index') . ': ' . msg('index_sections');
                $sx = $this -> frbr_core -> index_list($lt, 'Subject');
                break;                 
                           
            default:
                $title = 'Índices';
                $sx = '<ul>';
                $sx .= '<li><a href="'.base_url(PATH.'indice/author').'">'.msg('Authors').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'indice/corporate').'">'.msg('Corporate Body').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'indice/sections').'">'.msg('Sections').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'indice/subject').'">'.msg('Subject').'</a></li>';
                $sx .= '<li><a href="'.base_url(PATH.'indice/journal').'">'.msg('Journal').'</a></li>';
                $sx .= '</ul>';
        }
        $data['content'] = '<h1>' . $title . '</h1>' . $sx;
        $this -> load -> view('show', $data);

        $this -> footer();
    }    

    public function oai($verb = '', $id = 0) {
        $this -> load -> model('sources');
        $this -> load -> model('oai_pmh');
        $this -> load -> model('frbr');
        $this -> load -> model('frbr_core');
        $this -> cab();
        $data['title'] = 'OAI';
        switch($verb) {
            case 'GetRecord' :
                $dt = array();
                $idc = $this -> oai_pmh -> getRecord($id);
                if ($idc > 0)
                    {
                        $dt = $this -> oai_pmh -> getRecordNlM($idc, $dt);
                        $dt = $this -> oai_pmh -> getRecord_oai_dc($idc, $dt);
                        $dt['idc'] = $idc;
                        $html = $this -> sources -> info($id);
                        $html .= $this -> oai_pmh -> process($dt);
                        $html .= '<meta http-equiv="Refresh" content="5">';
                    } else {
                        $html = $this -> sources -> info($id);
                        $html .= '<h3>Fim da coleta</h3>';
                        $html .= '<br>'.date("d/m/Y H:i:s");
                    }    
                /***************************************************/
                
                //$html = '';
                //http://www.viaf.org/viaf/AutoSuggest?query=Zen, Ana Maria
                //http://www.viaf.org/processed/search/processed?query=local.personalName+all+"ZEN, Ana Maria Dalla"
                break;
            case 'ListIdentifiers' :
                $html = $this -> sources -> info($id);
                $html .= '<div class="row"><div class="col-12">' . $this -> oai_pmh -> ListIdentifiers($id) . '</div></div>';
                break;
            case 'info' :
                $html = $this -> sources -> info($id);
                break;
            case 'Identify' :
                $html = $this -> sources -> info($id);
                $html .= $this -> oai_pmh -> Identify($id);
                break;
            default :
                $html = $this -> oai_pmh -> repository_list($id);
                break;
        }

        $data['content'] = $html;
        $this -> load -> view("show", $data);
        $this -> footer();
    }

    public function authority() {
        $this -> load -> model('frbr');

        $this -> cab();

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
        $this -> load -> view('show', $data);

        /***************** inport VIAF ***********/
        $acao = get("action");
        switch ($acao) {
            /***************** inport VIAF ***********/
            case 'viaf_inport' :
                $url = get("ulr_viaf");
                $data['content'] = $this -> frbr -> viaf_inport($url);
                $this -> load -> view('show', $data);
                break;
            /***************** inport GEONames ***********/
            case 'geonames_inport' :
                $url = get("ulr_geonames");
                $data['content'] = $this -> frbr -> geonames_inport($url);
                $this -> load -> view('show', $data);
                break;
            default :
                echo $acao;
        }

        $this -> footer();
    }

}
