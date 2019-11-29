<?php
defined('BASEPATH') OR exit('No direct script access allowed');
DEFINE("PATH", "index.php/res/");
class api extends CI_Controller {

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
	function index()
	{
		$data['token'] = md5('api');
		$this->load->view('api/ide',$data);
	}


	function cmd($cmd='',$a2='',$a3='',$a4='',$a5='',$a6='')
	{
		$this->load->model("api_brapci");
		$tkn = get("token");
		$q = get("q");

		$rsp = array();
		$rsp = array(date("Y-m-d H:i:s")=>'data',$cmd=>'command');
		switch($cmd)
		{
			case 'nlp':
			$txt = get("dd1");
			$txt = (string)$txt;
			$this->api_brapci->nlp($txt);
			print_r($rsp);
			exit;
			break;

			/************************************ GENERO ***************************/
			case 'genere':
			$this->load->model("genero");
			$rsp[$q] = 'name';	
			$rsp[$this->genero->api($q)] = 'genre';
			$rsp[strtoupper(substr($this->genero->api($q),0,1))] = 'genre_abrev';
			$type = get("type");
			switch ($type)
			{
				case 'abrev':
				foreach ($rsp as $key => $value) {
					# code...
					if ($value == 'genre_abrev') { echo $key; exit; }
				}
				break;
			
				case 'txt':
				foreach ($rsp as $key => $value) {
					# code...
					if ($value == 'genre') { echo $key; exit; }
				}
				break;				
			}
			break;

			case 'null':
			break;	
			case 'export':
			$this->api_brapci->create_index_list();
			$this->api_brapci->create_stopwords();

			case 'tkn':
			
			break;
			default:
			$rsp['999'] = 'status';
			$rsp['API "'.(string)$cmd.'" not found'] = 'message';
			$rsp['Looking http://www.brapci.inf.br/api'] = 'url';
			break;
		}
		header('Content-Type: application/xml');

		$xml = new SimpleXMLElement('<api/>');
		array_walk_recursive($rsp, array ($xml, 'addChild'));		
		print $xml->asXML();
	}
}
?>