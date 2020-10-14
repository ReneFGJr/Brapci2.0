<?php
class Api_brapci extends CI_model
{
	function index($act,$token)
	{
		$q = get("q");
		$dt['error'] = 0;
		switch($act)
		{
			case 'dialogflow':
				$this->load->model('GoogleDialogFlow');
				$dt = $this->GoogleDialogFlow->run($token);
			break;
			
			case 'genere':
				$q = nbr_autor($q,7);
				$this->load->model("Genero");
				$dt['name'] = $q;
				$dt['response'] = $this->Genero->consulta($q);
			break;
			
			case 'catalog':
				$dt['services'] = 'genere';
			break;
			
			default:
			$dt = array();
			$dt['error'] = '404';
			$dt['description'] = 'Service not found';
		break;
	}
	echo json_encode($dt);
}
function affiliation_cron()
{
	$rdf = new rdf;
	$prop = $rdf->find_class('affiliatedWith');
	$sql = "select * from rdf_data 
	INNER JOIN rdf_concept ON id_cc = d_r2
	INNER JOIN rdf_name ON id_n = cc_pref_term
	where cc_origin = '' and d_p = $prop";
	$rlt = $this->db->query($sql);
	$rlt = $rlt->result_array();
	for ($r=0;$r < 1;$r++)
	{
		$line = $rlt[$r];
		$term = $line['n_name'];
		if (strpos($term,'(') > 0)
			{
				$term = substr($term,0,strpos($term,'('));
			}
		$orign = '#';
		echo 'Buscando '.$term.cr();
		$url = "https://www.ufrgs.br/tesauros/index.php/thesa/api/check?q=$term&th=8";
		$t = file_get_contents($url);
		$t = (array)json_decode($t);
		
		/****************** AUTORIZADO */					
		$auth = (array)$t['result'];
		$auth = (array)$auth['conecpt'];
		if (count($auth) > 0)
		{
			$auth = (array)$auth[0];
			$nname = $auth['autho'];
			$nlang = $rdf->language($auth['lang']);
			$concp = 'Thesa:c'.$auth['idc'];
			
			/****************** ID */
			$idn = $rdf->rdf_name($nname,$nlang);
			$class = 'CorporateBody';
			$idr = $rdf->rdf_concept($idn, $class, $concp);
			if ($idr > 0)
			{
				$orign = $concp;
			}
		} else {
			$idr = 0;
		}
		/***************** Atualiza Geral */
		$sql = "update rdf_concept set cc_origin = '$orign', cc_use = $idr where id_cc = ".$line['id_cc'];
		echo $sql.'<hr>'; 
		$rrr = $this->db->query($sql);
		echo '=IDR=>'.$idr;
	}
	exit;
}
function nlp($txt)
{
	$t = $txt;
	$tr = array();
	$ty = array();
	
	//$t = iconv('UTF-8','ISO-8859-1',$t);
	$t = ' '.LowerCaseSql($t).' ';
	$t = troca($t,' ',' ');
	$t = troca($t,'.',' ');
	$t = troca($t,',',' ');
	$t = troca($t,':',' ');
	$t2 = $t;
	
	//$t = trim(LowerCaseSQL($t));
	echo date("Y-m-d H:i:s").'<br>';
	/********************************************** STOP WORDS */
	$sw = file_get_contents("_blnp/Stopwords.txt","r");
	$sw = LowerCaseSql($sw);
	$sw = troca($sw,chr(13),';');
	$sw = troca($sw,chr(10),';');
	$sws = splitx(';',$sw);
	$sw = array();
	for ($r=1;$r < count($sws);$r++)
	{
		$tsw = ' '.$sws[$r].' ';
		$sw[$tsw] = 1;
	}	
	
	
	/* read line by line */
	$fn = fopen("_blnp/Subject.txt","r");
	$r = 0;
	while ($result = fgets($fn))
	{
		$r++;
		$tt = trim(substr($result,0,strpos($result,'{')));
			$te = trim(substr($result,strpos($result,'{'),strlen($result))).'';
				//echo $tt.'--'.$te.'<br>';
				if (strlen($tt) > 0)
				{
					if (strpos($t,' '.$tt.' '))
					{
						$tw = ' '.$tt.' ';
						if (!isset($sw[$tw]))
						{
							$t2 = troca($t2,$tw,' '.$te.' ');
							$tr[trim($tw)] = $te;
							$ty[trim($te)] = $tw;
						}
					}
				}
			}  		
			fclose($fn); 
			
			
			foreach ($ty as $key => $value) {
				$link = '<a href="#" style="color: bule;" title="'.$key.'">'.trim($value).'</a>';
				$t2 = troca($t2,$key,$link);
			}       
			
			echo '<tt>'.($t2).'</tt>';
		}
		
		function create_stopwords()
		{
			/* STOP WORDS */
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/69/txt';
			$txt = file_get_contents ($url);
			$txt = troca($txt,'=>[sw]','');
			$dir = '_blnp/';
			dircheck($dir);
			$file = $dir . 'Stopwords.txt';
			$hdl = fopen($file, 'w+');
			fwrite($hdl, $txt);
			fclose($hdl);
			return ('Exported');		
		}
		
		function create_index_list($class = 'Subject') {
			$this->load->model('frbr_core');
			$nouse = 0;
			$dir = '_blnp/';
			dircheck($dir);
			$sx = '';
			//for ($r=65;$r <= 90;$r++)
			$txt = $this -> frbr_core -> index_list_3('', 'Subject', 0);
			$file = $dir . ''.$class . '.txt';
			$hdl = fopen($file, 'w+');
			fwrite($hdl, $txt);
			fclose($hdl);
			return ('Exported');			
		}
		
		function genere($name='')
		{
			
		}
		
	}	
	?>