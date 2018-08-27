<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class res extends CI_Controller {

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
		$this -> load -> helper('email');
		$this -> load -> helper('bootstrap');
		date_default_timezone_set('America/Sao_Paulo');
	}

	private function cab($data = array()) {
		$data['title'] = 'Brapci';
		$this -> load -> view('header/header.php', $data);
		if (!isset($data['nocab'])) {
			$this -> load -> view('header/menu_top.php', $data);
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
				$this -> load -> model('elasticsearch_brapci20');
				print_r($this -> elasticsearch -> status());
				break;
			case 'journals' :
				$this -> load -> model('elasticsearch_brapci20');
				$this -> Elasticsearch_brapci20 -> journals_index();
				break;
			case 'query' :
				$this -> load -> model('Elasticsearch_brapci20');
				$this -> Elasticsearch_brapci20 -> query($q);
				break;
			default :
				$this -> cab();

				break;
		}
		//$this->cab();

		//$result = $client->index($params);
	}

	public function zera($pg = '') {
		$this -> cab();
		$this -> load -> model('elasticsearch');
		if ($pg == '') {
			$sql = "TRUNCATE source_listidentifier;";
			$this -> db -> query($sql);
			$sql = "TRUNCATE rdf_concept;";
			$this -> db -> query($sql);
			$sql = "TRUNCATE rdf_data;";
			$this -> db -> query($sql);
			$sql = "TRUNCATE rdf_name;";
			$this -> db -> query($sql);
			echo '<meta http-equiv="refresh" content="1;' . base_url('index.php/res/zera/0') . '">';
		} else {
			$sz = 250;
			$pg = round($pg);
			$ok = 0;
			for ($r = (($pg * $sz) + 1); $r <= ($pg * $sz + $sz); $r++) {
				$path = ELASTIC_PREFIX;
				$rst = $this -> elasticsearch -> delete('article', $r);
				echo ' - ' . $rst['result'] . '<br>';
				if ($rst['result'] == 'deleted') { $ok = 1;
				}
			}
			$pg = $pg + 1;
			if ($ok == 1) {
				echo '<meta http-equiv="refresh" content="1;' . base_url('index.php/res/zera/' . ($pg)) . '">';
			} else {
				echo 'FIM';
			}

		}
	}

	public function index() {
		$this -> load -> model('elasticsearch');
		$this -> load -> model('libraries');
		$this -> load -> model('sources');
		$this -> load -> model('frbr');
		$this -> cab();

		$this -> load -> view('brapci/form');

		if (strlen(get("q")) > 0) {
			//$this -> ElasticSearch -> getStatus();
			$this -> load -> model('searchs');
			$term = convert(get("q"));
			$type = get("type");
			$data['content'] = '' . $this -> searchs -> s($term, $type) . '';
			$this -> load -> view('show', $data);
		} else {
			//$tela = $this -> libraries -> show();
			//$data = array();
			//$data['content'] = $tela;
			//$this -> load -> view('show', $data);
		}

		$this -> footer();
	}

	public function about() {
		$this -> cab();

		$data = array();
		$data['content'] = $this -> load -> view('brapci/about', null, true);
		$this -> load -> view('show', $data);
		$this -> footer();
	}

	public function collections() {
		$this -> load -> model('sources');
		$this -> cab();
		$tela = $this -> sources -> list_sources();
		$tela .= $this -> sources -> timelines(1972);

		$data = array();
		$data['content'] = '<h1>' . msg('our_colletions') . '</h1>' . $tela;
		$this -> load -> view('show', $data);
		$this -> footer();
	}

	public function v($id) {
		$this -> load -> model('nets');
		$this -> load -> model('frbr');
		$this -> load -> model('genero');
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

		if (perfil("#ADM")) {
			$html .= $this -> sources -> button_new_sources($id);
		}

		$data['content'] = $html;
		$this -> load -> view('show', $data);
		$this -> footer();
	}

	public function jnl_edit($id = '', $chk = '') {
		if (perfil("#ADM")) {
			$this -> load -> model('oai_pmh');
			$this -> load -> model('sources');
			$this -> cab();

			$cp = $this -> sources -> cp($id);
			$form = new form;
			$form -> id = $id;
			$html = $form -> editar($cp, $this -> sources -> table);
			if ($form -> saved > 0) {
				redirect(base_url(PATH . 'journals'));
			}
			$data['content'] = $html;
			$this -> load -> view('show', $data);
			$this -> footer();
		} else {
			redirect(base_url(PATH));
		}
	}

	public function journals($act = '', $p = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');
		$this -> load -> model('oai_pmh');
		$this -> load -> model('sources');
		$this -> cab();
		$html = '';
		$data = array();
		$data['content'] = '';

		if (strlen($act) == 0) {

			$data['content'] = $this -> sources -> list_sources();
			if (perfil("#ADM")) {
				$data['content'] .= $this -> sources -> button_new_sources();
				$data['content'] .= $this -> sources -> button_harvesting_all();
			}
			$data['title'] = msg('journals');
		} else {
			$id = $this -> sources -> next_harvesting($p);
			if ($id > 0) {
				$this -> oai_pmh -> ListIdentifiers($id);
				$html = $this -> sources -> info($id);
				$html .= '<meta http-equiv="Refresh" content="5;' . base_url(PATH . 'journals/harvesting/' . ($id)) . '">';
			} else {
				$html .= '<div class="col-md-12">' . bs_alert('success', msg('harvesting_finished')) . '</div>';
				$html .= '<div class="col-md-12">' . $this -> oai_pmh -> cache_resume() . '</div>';
			}
		}
		$data['content'] .= $html;
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
				$sx = $this -> frbr_core -> index_list($lt, 'Person');

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
			case 'words' :
				$title = msg('index') . ': ' . msg('index_words');
				$sx = $this -> frbr_core -> index_list($lt, 'Word');
				break;
			default :
				$title = 'Índices';
				$sx = '<ul>';
				$sx .= '<li><a href="' . base_url(PATH . 'indice/author') . '">' . msg('Authors') . '</a></li>';
				$sx .= '<li><a href="' . base_url(PATH . 'indice/corporate') . '">' . msg('Corporate Body') . '</a></li>';
				$sx .= '<li><a href="' . base_url(PATH . 'indice/sections') . '">' . msg('Sections') . '</a></li>';
				$sx .= '<li><a href="' . base_url(PATH . 'indice/subject') . '">' . msg('Subject') . '</a></li>';
				$sx .= '<li><a href="' . base_url(PATH . 'indice/journal') . '">' . msg('Journal') . '</a></li>';
				$sx .= '<li><a href="' . base_url(PATH . 'indice/words') . '">' . msg('Words') . '</a></li>';
				$sx .= '</ul>';
		}
		$data['content'] = '<h1>' . $title . '</h1>' . $sx;
		$this -> load -> view('show', $data);

		$this -> footer();
	}

	public function oai($verb = '', $id = 0, $id2 = '', $id3 = '') {
		$this -> load -> model('sources');
		$this -> load -> model('searchs');
		$this -> load -> model('oai_pmh');
		$this -> load -> model('export');
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');
		$this -> load -> model('Elasticsearch');
		$this -> load -> model('Elasticsearch_brapci20');
		$this -> cab();
		$data['title'] = 'OAI';
		switch($verb) {
			case 'GetRecord' :
				$dt = array();
				$idc = $this -> oai_pmh -> getRecord($id);
				if ($idc > 0) {
					$dt = $this -> oai_pmh -> getRecordNlM($idc, $dt);
					$dt = $this -> oai_pmh -> getRecord_oai_dc($idc, $dt);
					$dt['idc'] = $idc;
					$html = $this -> sources -> info($id);
					$html .= $this -> oai_pmh -> process($dt);
					$html .= '<meta http-equiv="Refresh" content="5">';
				} else {
					$html = $this -> sources -> info($id);
					$html .= '<h3>Fim da coleta</h3>';
					$html .= '<br>' . date("d/m/Y H:i:s");
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
			case 'cache' :
				$html = $this -> sources -> info($id);
				$html .= '<div class="col-2">';
				$html .= '<h1>CACHE</h1>';
				$html .= $this -> oai_pmh -> cache_change_to($id, $id2, $id3) . '</div>';
				$html .= '<div class="col-4">' . $this -> oai_pmh -> list_cache($id, $id2) . '</div>';
				break;
			case 'cache_status_to' :
				$html = $this -> sources -> info($id);
				$html .= '<div class="col-2">';
				$html .= '<h1>CACHE ID</h1>';
				$this -> oai_pmh -> cache_reprocess($id3);
				$html .= $this -> oai_pmh -> list_cache($id, $id2, $id3);
				$html .= '</div>';
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

	function help() {
		$this -> cab();
		$this -> load -> view('brapci/help');
		$this -> footer();
	}

	function mark($key = '', $vlr = '') {
		$this -> bs -> ajax_mark($key, $vlr);
	}

	function basket($fcn = '', $arg = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');

		switch($fcn) {
			case 'export' :
				switch($arg) {
					case 'csv' :
						$this -> bs -> mark_export_csv();
						break;
					default :
						redirect(base_url(PATH . 'basket'));
						break;
				}
				break;
			case 'clean' :
				$this -> bs -> mark_clear();
				redirect(base_url(PATH . 'basket'));
			default :
				$this -> cab();
				$data['content'] = $this -> bs -> tools();
				$data['content'] .= $this -> bs -> basket();
				$this -> load -> view('show', $data);
				$this -> footer();
				break;
		}
	}

	function ajax($id = '', $id2 = '', $id3 = '', $id4 = '') {
		$this -> load -> model('searchs');
		$this -> load -> model('frbr_core');
		$q = get("q");
		switch($id) {
			case 'inport' :
				$cl = $this -> frbr_core -> le_class($id2);
				if (count($cl) == 0) {
					echo "Erro de classe [$id2]";
					exit ;
				}
				$url = trim($cl['c_url']);
				$t = read_link($url);
				$this -> frbr_core -> inport_rdf($t, $id2);
				$sql = "update rdf_class set c_url_update = '" . date("Y-m-d") . "' where id_c = " . $cl['id_c'];
				$rlt = $this -> db -> query($sql);
				$sx = '';
				break;
				break;
			case 'exclude' :
				$idc = $id2;
				$this -> frbr_core -> data_exclude($idc);
				$sx = '<div class="alert alert-success" role="alert">
                                  <strong>Sucesso!</strong> Item excluído da base.
                                </div>';
				$sx .= '<meta http-equiv="refresh" content="1">';
				echo $sx;
				break;
			case 'ajax2' :
				echo 'dd1=' . $id . '=dd2=' . $id2 . '=dd3=' . $id3 . '==' . $id4;
				echo $this -> frbr_core -> ajax2($id2, $id3, $id4);
				break;
			case 'ajax3' :
				echo 'dd1=' . $id . '=dd2=' . $id2 . '=dd3=' . $id3 . '==' . $id4;
		        $this -> load -> model('frbr_core');
		        $val = get("q");
		        $this -> frbr_core -> set_propriety($id3, $id2, $val, 0);
		        echo '<meta http-equiv="refresh" content="0;">';	
				break;
			case 'thesa' :
				$this -> load -> model('thesa_api');
				$this -> load -> model('frbr_core');
				$this -> thesa_api -> ajax($id2);
				break;
			default :
				if (strlen($q) > 0) {
					echo $this -> searchs -> ajax_q($q);
				} else {
					//$type = $id2;
					$this -> load -> model('frbr_core');
					echo $this -> frbr_core -> model($id, $id2, '');
				}
				break;
		}
	}

	function export($tp = '', $pg = 0) {
		$this -> load -> model('export');
		$this -> load -> model('frbr_core');
		$this -> cab();

		switch($tp) {
			case 'article' :
				$tela = $this -> export -> export_Article($pg);
				break;
			case 'subject' :
				$tela = $this -> export -> export_subject($pg);
				break;
			case 'subject_reverse' :
				$tela = $this -> export -> export_subject_reverse($pg);
				break;
			default :
				$tela = '<h1>' . msg('export') . '</h1>';
				$tela .= '<ul>' . cr();
				$tela .= '<li><a href="' . base_url(PATH . 'export/article') . '">' . msg('export_article') . '</a></li>' . cr();
				$tela .= '<li><a href="' . base_url(PATH . 'export/subject') . '">' . msg('export_subject') . '</a></li>' . cr();
				$tela .= '<li><a href="' . base_url(PATH . 'export/subject_reverse') . '">' . msg('export_subject_reverse') . '</a></li>' . cr();
				$tela .= '</ul>' . cr();
				break;
		}

		$data['content'] = $tela;
		$data['title'] = '';
		$this -> load -> view('show', $data);

		$this -> footer();
	}

	function download($d1 = '') {
		$d1 = round($d1);
		$this -> load -> model('pdfs');
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');
		$this -> pdfs -> download($d1);
	}

	function pdf_download($d1 = '', $d2 = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');
		$this -> load -> model('pdfs');
		$this -> pdfs -> harvesting_pdf($d1);
		exit ;
		$this -> load -> library('Pdfmerger');
		$pdf = new PDFMerger;

		$pdf -> addPDF('d:/lixo/pdf/one.pdf', '1, 3, 4') -> addPDF('d:/lixo/pdf/two.pdf', '1-2') -> addPDF('d:/lixo/pdf/three.pdf', 'all');
		$pdf -> merge('file', 'd:/lixo/pdf/TEST3.pdf');
		echo "FIM";
	}

	function pdf_upload($d1 = '', $d2 = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');
		$this -> load -> model('pdfs');
		$data['nocab'] = true;
		$this -> cab($data);
		$data['content'] = $this -> pdfs -> upload($d1);
		$this -> load -> view('show', $data);
	}

	/* LOGIN */
	function social($act = '') {
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
			case 'npass' :
				$this -> cab();
				$email = get("dd0");
				$chk = get("chk");
				$chk2 = checkpost_link($email . $email);

				if (($chk != $chk2) AND (!isset($_POST['dd1']))) {
					$data['content'] = 'Erro de Check';
					$this -> load -> view('content', $data);
				} else {
					$dt = $this -> socials -> le_email($email);
					if (count($dt) > 0) {
						$id = $dt['id_us'];
						$data['title'] = '';
						$tela = '<br><br><h1>' . msg('change_password') . '</h1>';
						$new = 1;
						// Novo registro
						$data['content'] = $tela . $this -> socials -> change_password($id, $new);
						$this -> load -> view('content', $data);
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

	function tools($p = '', $id = '0', $id2 = '') {
		$this -> cab();
		switch($p) {
			case '' :
				$data['title'] = msg('Tools');
				$txt = '<div class="col-md-12">';
				$txt .= '<h1>' . msg('tools_title') . '</h1>';
				$txt .= '</div>';

				$txt .= '<div class="col-md-12">';
				$txt .= '<ul>';
				$txt .= '<li>' . '<a href="' . base_url(PATH . 'tools/pdf_import') . '">' . msg('tools_pdf_import') . '</a>';
				$txt .= '</ul>';
				$txt .= '</div>';
				$data['content'] = $txt;
				$this -> load -> view('show', $data);
				break;
			case 'pdf_import' :
				$this -> load -> model("pdfs");
				$this -> load -> model("frbr");
				$this -> load -> model("frbr_core");

				$data['title'] = msg('Tools');
				$txt = '<div class="col-md-12">';
				$txt .= '<h1>' . msg('tools_harvesting') . '</h1>';
				$txt .= '</div>';
				$txt .= $this -> pdfs -> harvestinf_next($id);
				$data['content'] = $txt;
				$this -> load -> view('show', $data);
				break;
		}
		$this -> footer(0);
	}

	function a($id = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');

		$data = $this -> frbr_core -> le($id);

		$this -> cab();

		//$tela = $this -> frbr -> show($id);
		$tela = '';
		$linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="middle">';
		$linkca = '</a>';

		if (strlen($data['n_name']) > 0) {
			$tela .= '<h2>' . $linkc . $data['n_name'] . $linkca . '</h2>';
		}
		$linkc = '<a href="' . base_url(PATH . 'v/' . $id) . '" class="btn btn-secondary">';
		$linkca = '</a>';

		$tela .= '
                    <div class="row">
                    <div class="col-md-11">
                    <h5>' . msg('class') . ': ' . $data['c_class'] . '</h5>
                    </div>
                    <div class="col-md-1 text-right">
                    ' . $linkc . msg('return') . $linkca . '
                    </div>
                    </div>';

		//$tela .= $this -> frbr -> form($id, $data);
		$tela .= $this -> frbr_core -> form($id, $data);

		switch($data['c_class']) {
			case 'Person' :
				$tela .= $this -> frbr_core -> show($id);
				break;
			case 'Family' :
				$tela .= $this -> frfrbr_corebr -> show($id);
				break;
			case 'Corporate Body' :
				$tela .= $this -> frbr_core -> show($id);
				break;
			default :
				break;
		}

		$data['title'] = '';
		$data['content'] = $tela;

		$this -> load -> view('show', $data);
		$this -> footer();
	}

	function vocabulary_ed($id = '') {
		$this -> cab();
		$cp = array();
		array_push($cp, array('$H8', 'id_c', '', false, true));
		array_push($cp, array('$S100', 'c_class', 'Classe', true, true));
		array_push($cp, array('$O : &C:Classe&P:Propriety', 'c_type', 'Tipo', true, true));
		array_push($cp, array('$O 1:SIM&0:NÃO', 'c_find', 'Busca', true, true));
		array_push($cp, array('$O 1:SIM&0:NÃO', 'c_vc', 'Vocabulário Controlado', true, true));
		array_push($cp, array('$S100', 'c_url', 'URL', false, true));
		array_push($cp, array('$B8', '', 'Gravar', false, true));
		$form = new form;
		$form -> id = $id;
		$tela = $form -> editar($cp, 'rdf_class');
		if ($form -> saved > 0) {
			redirect(base_url(PATH . 'vocabulary'));
		}

		$data['content'] = '<h1>Classes e Propriedades</h1>' . $tela;
		$this -> load -> view('show', $data);
		$this -> footer();
	}

	public function vocabulary($id = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('vocabularies');
		$this -> cab();

		$dd1 = get("dd1");
		if (strlen($dd1) > 0) {
			$class = $id;
			$id_s = $this -> frbr -> frbr_name($dd1);
			$p_id = $this -> frbr -> rdf_concept($id_s, $class);
			$this -> frbr -> set_propriety($p_id, 'prefLabel', 0, $id_s);
		}

		$t1 = $this -> vocabularies -> list_vc($id);
		$t1 .= $this -> vocabularies -> modal_vc($id);
		$t1 = '<h3>Classe: ' . msg($id) . '</h3>' . $t1;

		$t2 = $this -> vocabularies -> list_thesa($id);
		$t2 = '<h3>Classe Thesa: ' . msg($id) . '</h3>' . $t2;

		$tela = '
                <div class="row">
                    <div class="col-md-6">' . $t1 . '</div>
                    <div class="col-md-6">' . $t2 . '</div>
                </div>';

		$data['content'] = $tela;
		$this -> load -> view('show', $data);

	}

	public function thesa($id = '') {
		$this -> load -> model('frbr');
		$this -> load -> model('frbr_core');
		$this -> load -> model('vocabularies');
		$this -> cab();

		$datac = $this -> frbr_core -> le_class($id);

		$tela = $this -> load -> view('find/view/class', $datac, true);
		$tela .= '<div class="container">
					<div class="row"><div class="col-md-4" >'.$this -> vocabularies -> modal_th($id).'</div>';
		$tela .= '<div class="col-md-8">'.$this -> vocabularies -> list_vc($id).'</div></div>
					</div>';

		$data['content'] = $tela;
		$this -> load -> view('show', $data);

	}
    public function config($tools = '', $ac = '') {
        $this -> load -> model("frbr");

        /********************* EXPORTS ************************/
        switch($tools) {
            case 'class_export' :
                /* acao */
                $this -> load -> model("frbr_clients");
                $this -> frbr_clients -> export_class($ac);
                return ('');
                exit ;
        }

        $cab = 1;
        $this -> cab();

        if (!perfil("#ADM")) {
            redirect(base_url('index.php/main'));
        }

        $this -> load -> view('welcome');
        $tela = '';

        switch($tools) {
            case 'class_export' :
                /* acao */
                $this -> load -> model("frbr_clients");
                $tela .= $this -> frbr_clients -> export_class();
                break;
            case 'class' :
                /* acao */
                $this -> load -> model("frbr_core");
                if (strlen($ac) > 0) {
                    //$tela .= msg('MAKE_MESSAGES');
                } else {
                    $tela .= $this -> frbr_core -> classes_lista();
                }
                break;
            case 'msg' :
                /* acao */
                if (strlen($ac) > 0) {
                    $tela .= msg('MAKE_MESSAGES');
                } else {
                    $tela .= msg_lista();
                }

                break;
            case 'forms' :
                $tela .= '<h1>'.msg('FORMS').'</h1>';
				$tela .= '<hr>';
				$this -> load -> model("frbr_core");
                $tela .= $this -> frbr_core -> form_class();
                break;
            case 'authority' :
                if (perfil("#ADM") == 1) {
                    if ($ac == 'update') {
                        $tela .= $this -> frbr -> viaf_update();
                    } else {
                        $tela .= '<br><a href="' . base_url(PATH.'config/authority/update') . '" class="btn btn-secondary">' . msg('authority_update') . '</a>';
                    }
                    $tela .= '<br><br><h3>' . msg('Authority') . ' ' . msg('viaf') . '</h3>';
                    $tela .= $this -> frbr -> authority_class();
                }
                break;
			default:
				$tela = '<h1>'.msg('config').'</h1><ul>';
				$tela .= '<li>'.'<a href="'.base_url(PATH.'config/forms').'">'.msg('config_forms').'</a></li>';
				$tela .= '<li>'.'<a href="'.base_url(PATH.'config/class').'">'.msg('config_class').'</a></li>';
				
				$tela .= '</ul>';
				break;
        }
        $data['content'] = $tela;
        $this -> load -> view('show', $data);
        $this -> footer();
    }

    public function pop_config($tools = '', $id = '') {
        $this -> load -> model("frbr_core");
		$data['nocab'] = true;
        $this -> cab($data);
        $tela = '';
        $tela .= $tools;
        switch($tools) {
            case 'msg' :
                $tela .= $this -> frbr_core -> form_msg_ed($id);
                break;
            case 'forms' :
                $tela .= msg('FORMS');
                $tela .= $this -> frbr_core -> form_class_ed($id);
                break;
        }
        $data['content'] = $tela;
        $this -> load -> view('show', $data);
    }
}
