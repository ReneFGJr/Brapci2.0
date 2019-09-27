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


	function cmd($tkn='',$cmd='',$a2='',$a3='',$a4='',$a5='',$a6='')
	{
		$this->load->model("API_Brapci");
		$rsp = array();
		if ($tkn == '') 
		{
			$cmd = 'tkn';

		}
		$rsp = array(date("Y-m-d H:i:s")=>'data',$cmd=>'command');
		switch($cmd)
		{
			case 'nlp':
				$txt = get("dd1");
				$txt = (string)$txt;
				$this->API_Brapci->nlp($txt);
				print_r($rsp);
				exit;
			break;
			case 'null':
			break;	
			case 'export':
				$this->API_Brapci->create_index_list();
				$this->API_Brapci->create_stopwords();
				
			case 'tkn':
			
			break;
			default:
			$rsp['000'] = 'status';
			break;
		}
		header('Content-Type: application/xml');

		$xml = new SimpleXMLElement('<api/>');
		array_walk_recursive($rsp, array ($xml, 'addChild'));		
		print $xml->asXML();
	}
}
?>