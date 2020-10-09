<?php
// This file is part of the Brapci Software.
//
// Copyright 2015, UFPR. All rights reserved. You can redistribute it and/or modify
// Brapci under the terms of the Brapci License as published by UFPR, which
// restricts commercial use of the Software.
//
// Brapci is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details.
//
// You should have received a copy of the Brapci License along with the Brapci
// Software. If not, see
// https://github.com/ReneFGJ/Brapci/tree/master//LICENSE.txt
/* @author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
 * @date: 2015-12-01
 */
define("PATH",'index.php/article/');

class article extends CI_Controller {
	function __construct() {
		global $db_public;

		$db_public = 'brapc607_public.';
		parent::__construct();
		$this -> lang -> load("brapci", "pt_br");
		$this -> load -> library('form_validation');
		$this -> load -> database();
		$this -> load -> helper('form');
		$this -> load -> helper('form_sisdoc');
		$this -> load -> helper('email');
		$this -> load -> helper('url');
		$this -> load -> library('session');

        /* Language */
        $this -> load -> helper('language');
        $language = new language;
        $this -> lang -> load("brapci", $language->language());		

		date_default_timezone_set('America/Sao_Paulo');
	}

	function cab($data = array()) {
	    $this->load->model("bs");
		if (isset($data['title'])) {
			$data['title_page'] = $data['title'];
		}
		$this -> load -> view("header/header", $data);
		$data['title'] = '';
        if (!isset($data['nocab'])) {
            $this -> load -> view('header/menu_top.php', $data);
        }        
	}

	function index() {
		global $dd;
		$this -> load -> view("brapci/article");
	}
	
	function test()
		{
			$sql = "SELECT * FROM `mar_works` 
						left join bdoi_doi ON m_obra_bdoi = id_doi
						WHERE m_ref like '%BOURDIEU%' and m_obra_bdoi > 0";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			$ar = '';
			for ($r=0;$r < count($rlt);$r++)
				{
					$line = $rlt[$r];
					$arx = $line['m_work'];
					$ref = $line['doi_ref'];
					$ref = troca($ref,';','-');
					
					if ($ar != $arx)
						{
							$ar = $arx;
							echo cr();
							//echo $arx.';';
						}
					echo $ref.';';
				}
			
			return('');			
			$sql = "SELECT doi_ref, autor_nome_abrev 
					FROM `mar_works`
					INNER JOIN brapci_article_author on m_work = ae_article
					INNER JOIN brapci_autor ON ae_author = autor_codigo
					INNER JOIN bdoi_doi ON m_obra_bdoi = id_doi
					WHERE m_ref like '%BOURDIEU%' and m_obra_bdoi > 0";
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();
			for ($r=0;$r < count($rlt);$r++)
				{
					$line = $rlt[$r];
					echo '"'.trim($line['doi_ref']).'"';
					echo ';';
					echo '"'.trim($line['autor_nome_abrev']).'"';
					echo cr();
				}
		}

	function view($id, $check='', $status = '') {
		global $dd, $acao;

		/* Article */
		$this -> load -> model('articles');
		//$this -> load -> model('keywords');
		//$this -> load -> model('authors');
		//$this -> load -> model('archives');
		//$this -> load -> model('cites');
		//$this -> load -> model('tools/tools');
		//$this -> load -> model('metodologias');

		$data = $this -> articles -> le($id);

		/* Alterar Status */
		if (strlen($status) > 0) {
			$sql = "update brapci_article set ar_status = '$status' where id_ar = " . $id;
			$this -> db -> query($sql);
		}

		$data['title'] = $data['ar_titulo_1'];
		$data['title_page'] = 'ADMIN';
		$data['metadata'] = $this -> load -> view('old/article_metadata', $data, true);
		$this -> cab($data);

		$data['archives'] = $this -> articles -> show_files($id);
		$data['citeds'] = $this -> articles -> show_cited($id);

		/* Barra de status da indexação */
		$data['progress_bar'] = $this -> articles -> progress_bar($data['ar_status']);

		/* Botoes de acao */
		$data['botao_acoes'] = $this -> articles -> actions_buttons($data['ar_status'], $id);

		/* Links */
		$data['link_issue'] = '<A HREF="' . base_url('index.php/admin/issue_view/' . $data['id_ed']) . '/' . checkpost_link($data['id_ed']) . '" class="lt3">';

		$idioma_1 = trim($data['ar_idioma_1']);
		if (strlen($idioma_1) == 0) { $idioma_1 = 'pt_BR';
		}
		$idioma_2 = trim($data['ar_idioma_2']);
		if (strlen($idioma_2) == 0) { $idioma_2 = 'en';
		}

		$id_art = $data['ar_codigo'];
		$data['metodologias'] = '<div id="metodos" class="border1">' . '</div>';
		$data['metodologias'] .= '';

		/* article - parte I */
		$data['$tab_pdf'] = '';
		$data['tab_descript'] = $this -> load -> view('old/article_view_tt', $data, true);
		$links = $data['links'];

		$ll = '';
		for ($r = 0; $r < count($links); $r++) {
			$liz = $links[$r];
			$id = $liz['id_bs'];
			$tp = $liz['bs_type'];
			$lk = $liz['bs_adress'];
			switch ($tp) {
				case 'PDF' :
					if (substr($lk, 0, 4) == 'http') {
						$ll .= $this -> load -> view('old/pdf.php', $liz, true);
					}
					if (substr($lk, 0, 1) == '_') {
						if (file_exists($lk)) {
							$url = base_url('index.php/article/download_view/' . $id);
							$data['tab_pdf'] = '<iframe src="' . $url . '" class="iframe_pdf"></iframe>';

							$data['link_pdf'] = base_url('index.php/article/download/' . $id);
						} else {
							$data['tab_pdf'] = '<div class="alert alert-danger" role="alert">File not found!</div>';
						}
					}
					break;
				case '' :
					break;
			}
		}
		$data['linkss'] = $ll;

		$data['tab_marc21'] = $this -> load -> view('old/article_view_marc21', $data, true);
		$data['tab_rdf'] = $this -> load -> view('old/article_view_rdf', $data, true);
		$data['link_rdf'] = '';
		//$data['tab_marc21'] = '';
		$data['tab_editar'] = '';
		$data['tab_refer'] = $this -> load -> view('old/article_view_refer', $data, true);

		$this -> load -> view('old/article_view', $data);

		$data['content'] = '</table><br><br>';

		if (isset($_SESSION['nivel'])) {

			if (($_SESSION['nivel'] == 9) and (strlen($_SESSION['user']) > 0))
			{
				$data['title'] = '';
				$data['content'] = '<a href="' . base_url('index.php/article/view/' . $data['ar_codigo'] . '/' . checkpost_link($data['ar_codigo'])) . '" class="btn btn-default">editar metadados</a>';
				$this -> load -> view('content', $data);
			}
		}
		

		$this -> load -> view('header/footer', $data);

	}

	function download_view($id = '') {		
		$this -> load -> model('articles');
		$this -> articles -> view_pdf($id);
	}

	function download($id = '') {
		$this -> load -> model('articles');
		$this -> articles -> download_pdf($id);
	}

	function email($id) {
		$id = round($id);
		$this -> load -> model('articles');
		$data = $this -> articles -> le($id);
		$content = $this -> load -> view('article/article_email', $data, true);
		$content = utf8_decode($content);
		$subject = utf8_decode($data['ar_titulo_1']);
		$email = $_SESSION['email'];
		if (strlen($email) > 0) {
			try {
				//enviaremail('renefgj@gmail.com', '[Brapci] - ' . $subject, $content, 1);
				enviaremail($email, '[Brapci] - ' . $subject, $content, 1);
				$this -> load -> view('success', null);
			} catch (Exception $e) {
				echo 'Exceção capturada: ', $e -> getMessage(), "\n";
			}

		}
	}

}
function perfil()
    {
        return(false);
    }
?>
