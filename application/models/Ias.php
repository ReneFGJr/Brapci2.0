<?php
/*
* Inteligencê Artificial
*
*/
class Ias extends CI_model
{
	var $version_nlp = '0.21';
	
	function check($f=0)
	{
		if ((file_exists('_ia/stopword.txt')) and ($f==0))
		{
			$f = 0;
		}
		$sx = '<ul>';
		dircheck('_ia');
		if ($f==1)
		{
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/69/txtb';
			$file = '_ia/domain_stopword.txt';
			$this->import_thesa($url,$file);
			$sx .= '<li>Stopwords  <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/64/txtb';
			$file = '_ia/domain_ci.txt';
			$this->import_thesa($url,$file);
			$sx .= '<li>Informacition Science Domain  <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/243/txtb';
			$file = '_ia/domain_methodology.txt';
			$this->import_thesa($url,$file);	
			$sx .= '<li>Informacition Science Methodology Domain  <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/232/txtb';
			$file = '_ia/domain_isko.txt';
			$this->import_thesa($url,$file);
			$sx .= '<li>Knowledge Organization Domain <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/106/txtb';
			$file = '_ia/domain_paises.txt';
			$this->import_thesa($url,$file);
			$sx .= '<li>Knowledge Organization Domain <span style="color: green"><b>Update</b></span></li>';			
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/262/txtb';
			$file = '_ia/domain_instituicoes.txt';
			$this->import_thesa($url,$file);	
			$sx .= '<li>Corporate Board Authority <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/8/txtb';
			$file = '_ia/domain_universidades.txt';
			$this->import_thesa($url,$file);
			$sx .= '<li>Universities Authority <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/9/txtb';
			$file = '_ia/domain_areas_do_conhecimento.txt';
			$this->import_thesa($url,$file);	
			$sx .= '<li>Knowledge Domain <span style="color: green"><b>Update</b></span></li>';
			
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/10/txtb';
			$file = '_ia/domain_datas.txt';
			$this->import_thesa($url,$file);
			$sx .= '<li>Dates <span style="color: green"><b>Update</b></span></li>';			
			
			$sx .= '<li>Dates Gerate <span style="color: green"><b>Update</b></span></li>';			
			$file = '_ia/domain_datas_g.txt';
			$this->gerate_dates($file);
		}
		$this->create_domain();
		return($sx);
	}
	
	function create_domain()
	{
		$fl = fls('_ia');
		$term = array();
		
		for ($r=0;$r < count($fl);$r++)
		{
			$ft = $fl[$r][2];
			$ext = substr($ft,strlen($ft)-4,4);
			if ($ext == '.txt')
			{
				$file = $fl[$r][2];
				$handle = fopen($file, "r");
				if ($handle) {
					while (($line = fgets($handle)) !== false) {
						array_push($term,$line);
					}					
					fclose($handle);
				}				
			}
		}
		sort($term);
		$txt = '';
		for($r=(count($term)-1);$r >= 0;$r--)
		{
			$t = $term[$r];
			$t = substr($t,5,strlen($t));
			$txt .= $t.chr(10);
		}
		file_put_contents('_ia/domain.sw',$txt);
	}
	
	function gerate_dates($file='')
	{
		$sx = '';
		$i = mktime(0,0,0,0,1,0);
		$d = mktime(0,0,0,1,1,1950);
		//00007$sw['anos 80'] = 'Década_1980';
		for($r=0;$r <= 365;$r++)
		{
			$sa = date("d/m/Y",$d);
			$sx = date("Ymd",$d);
			$sx .= strzero(strlen($sx),5).'$sx[\''.$sa.'\'] = \'[D'.$sx.']\';'.cr();
			$d = $d + 60*60*24;
		}
	}
	
	function services()
	{
		$sx = '<div class="row">';
		$sx .= '<div class="col-md-12">';
		$sx .= '<h1>Services</h1>';
		$sx .= '</div>';
		
		$sx .= '<div class="col-md-2 btn btn-outline-primary">';
		$sx .= '<a href="'.base_url(PATH.'ia/nlp').'">';	
		$sx .= 'NLP '.$this->version_nlp;
		$sx .= '</a>';
		$sx .= '</div>';
		
		$sx .= '<div class="col-md-2 btn btn-outline-secondary">';
		$sx .= '<a href="'.base_url(PATH.'ia/thesa').'">';	
		$sx .= 'Atualiza vocabulários';
		$sx .= '</a>';
		$sx .= '</div>';		
		
		$sx .= '</div>';
		return($sx);
	}		
	
	function index($act='',$d1='',$d2='',$d3='')
	{
		$sx = '';
		$this->load->model('frbr_core');
		switch($act)
		{
			case 'thesa':
				$sx = $this->check(1);
				$sx .= '<a href="'.base_url(PATH.'ia').'" class="btn btn-outline-primary">'.msg('return').'</a>';
			break;
			
			case 'nlp':
				switch($d1)
				{
					case 'analyse':
						$vv = $this -> frbr_core -> le_data($d2);
						$sx = $this->ias->nlp_v($vv);					
					break;
					
					default:
					$sx = '<h1>'.msg("NLP").' <sup>v.'.$this->version_nlp.'</sup></h1>';
					$sx .= $this->nlp($d1,$d2,$d3);
					
				}
			break;
			
			default:
			$sx = $this->services();
		break;
	}
	return($sx);
}

function nlp($d1='',$d2='',$d3='')
{
	$sx = '';
	switch($d1)
	{
		case 'run':
			$sx .= $this->nlp_form();
			$txt = $this->nlp_process(get("dd1"));
			$sx .= $this->nlp_words($txt);
		break;
		
		default:
		$sx .= '<ul>';
		$sx .= '<li>'.'<a href="'.base_url(PATH.'ia/nlp/run').'">'.msg("Processing Teste").'</a>';
		$sx .= '</ul>';
	}
	return($sx);
}

function submit_accept($txt)
{
	$this->gerate_dates();
	
	/* IA POR REGRAS */
	$w = array('Aceito:');
	$r = 0;
	while (strpos($txt,$w[$r]) > 0)
	{
		$i = strpos($txt,$w[$r]);
		$f = trim(substr($txt,$i,30));
		$f = substr($f,0,strpos($f,' '));
		echo '=>'.$i.'-'.$f;
		$txt = '';
	}
}

function nlp_process($t)
{
	/************/
	//???
	$sx = $this->submit_accept($t);
	$sx = '';
	if (strlen($t) == 0)
	{
		return("");
	}
	$t = $this->text_get($t);
	return($t);
}

function nlp_form()
{
	$form = new form;
	$cp = array();
	array_push($cp,array('$H8','','',false,false));
	array_push($cp,array('$T80:15','',msg('text'),True,True));
	array_push($cp,array('$B8','',msg('run'),false,false));
	
	$sx = $form->editar($cp,'');
	return($sx);
}



function import_thesa($url,$file)
{
	$rsp = file($url);
	$s = array();
	foreach ($rsp as $line_num => $line) {
		//echo "Linha #<b>{$line_num}</b> : " .$line . "<br>\n";
		$line = troca($line,'>','');
		$ln = splitx('=',$line);
		if (count($ln) == 2)
		{
			$t = trim(LowerCaseSql($ln[0]));
			if (strlen($t) > 0)
			{
				$te = troca($ln[1],' ','_');				
				$w = strzero(strlen($t),5).'$sw[\''.$t.'\'] = ';
				$w .= "'".$te."';";
				array_push($s,$w);
			}
		}
	}
	$w = '';
	/* Salvando arquivo invertido */
	for($r=(count($s)-1);$r >= 0;$r--)
	{
		$key = $s[$r];
		//$w .= substr($key,5,strlen($key)).cr();
		$w .= $key.cr();
	}
	file_put_contents($file, $w);
	return(1);	
}

function file_get($file)
{
	$l = array();
	$rsp = file($file);
	$w = '';
	foreach ($rsp as $line_num => $line) {
		//echo "Linha #<b>{$line_num}</b> : " .$line . "<br>\n";
		$line = trim($line);
		if (sonumero($line) == $line)
		{
			$line = '';
		}
		
		if (isset($l[$line]))
		{
			$l[$line] = $l[$line]+1;
		} else {
			$l[$line] = 1;
		}
	}
	$txt = $this->file_get_prepare($l);
	return($txt);		
}

function text_get($t)
{
	$l = array();
	$t = troca($t,chr(13),';');
	$ln = splitx(';',$t);
	$w = '';
	for ($r=0;$r < count($ln);$r++)
	{
		$line = $ln[$r];
		$line = trim($line);
		if (sonumero($line) == $line)
		{
			$line = '';
		}
		
		if (isset($l[$line]))
		{
			$l[$line] = $l[$line]+1;
		} else {
			$l[$line] = 1;
		}
	}
	$txt = $this->file_get_prepare($l);
	return($txt);		
}

function file_get_prepare($l)
{
	$txt = '';
	foreach ($l as $ln => $f) 
	{
		if ($f <= 1)
		{
			$ln = '> '.trim($ln);
			if (strlen($ln) > 3) 
			{
				for ($a = 64; $a <= 90;$a++)
				{
					$ln = troca($ln,'. '.chr($a),'.> '.chr($a));
				}
				
				$ln = troca($ln,'“','');
				$ln = troca($ln,'”','');
				$ln = troca($ln,'"','');
				$ln = trim($ln);
				while (strpos($ln,'  ')>0)
				{
					$ln = ' '.	trim(troca($ln,'  ',' '));
				}
				
				$final = substr($ln,strlen($ln)-1,1);
				$txt .= $ln;
				switch($final)
				{
					case '.':
						$txt .= '>'.cr();
					break;
					
					default:
					$txt .= ' ';
				break;
			}
		}
	}	
}	
$txt = troca($txt,' > ',' ');
return($txt);
}

function nlp_v($d)
{
	$this->check();
	$rdf = new rdf;
	$vv = $rdf->extract_content($d,'hasFileStorage');
	$file = troca($vv[0],'.pdf','.txt');
	if (file_exists($file))
	{
		$txtf = $this->file_get($file);
		//$txt = '<tt>'.$txtf.'</tt>';		
		$txt = $this->nlp_words($txtf).cr();
	} else {
		$txt = message(msg('File not found').' '.$file,3);
	}
	return($txt);
}

function status($id)
{
	$sx = $this->icone('PNL',1,$id);
	return($sx);
}

function icone($i,$v=0,$id)	
{
	switch($i)
	{
		case 'PNL':
			$t = 'NLP';
			$ver = $this->version_nlp;
			$link = '<a href="'.base_url(PATH.'ia/nlp/analyse/'.$id).'">';
			$linka = '</a>';
		}
		
		$div = '<div class="infobox" style="width: 100px;">';
		$div .= '<div class="infobox_name" style="background-color: #e0e0ff; float: left; width: 70%; padding: 0px 5px;">'.$t.'</div>';
		$div .= '<div class="infobox_version" style="float: left; background-color: #e0ffe0; width: 30%; padding: 0px 2px; text-align: right;">'.$link.$ver.$linka.'</div>';
		$div .= '</div>';
		return($div);
	}
	
	function limpa_text($t,$all=1)
	{
		$ts = array('...','..','. ',' .',';.','“','”',',',':','>','<','?','"','/','\\','!','|','–','(',')');
		if ($all == 1)
		{
			array_push($ts,'[');
			array_push($ts,']');
		}
		for ($r=0;$r < count($ts);$r++)
		{
			$t = troca($t,$ts[$r],' ');
		}
		$t = LowerCaseSql($t);
		for ($r=0;$r < 31;$r++)
		{
			$t = troca($t,chr($r),' ');
		}
		while (strpos($t,'  ') > 0)
		{
			$t = trim(troca($t,'  ',' '));
		}
		return(' '.$t.' ');	
	}
	
	function nlp_words($txt)
	{
		$t = $txt;
		
		/************************************ Busca termos **************/		
		/* recupera Domínio da CI */
		$domains = file_get_contents('_ia/domain.sw');
		$sw = array();
		eval($domains);
		
		/* REDE DE NEURONIO */
		$t1 = $t;
		$tx = neuro_referencias($t1);
		$refs = $tx[1];
		$t1 = $tx[0];
		
		$t1 = neuro_link($t1);
		
		/************************** Substitui no texto ***********/
		$t1 = LowerCaseSql($t1);
		
		
		$t1 = troca($t1,'','');
		$t1 = troca($t1,'.',' [.] ');
		$t1 = troca($t1,';',' ; ');
		$t1 = troca($t1,',',' , ');
		$t1 = troca($t1,'?',' ? ');
		$t1 = troca($t1,'!',' ! ');
		$t1 = troca($t1,':',' : ');
		$t1 = troca($t1,'/',' / ');
		$t1 = troca($t1,'(',' ( ');
		$t1 = troca($t1,')',' ) ');
		$t1 = troca($t1,'*',' * ');
		$t1 = troca($t1,'‘'," ' ");
		$t1 = troca($t1,'’'," ' ");
		$t1 = ascii($t1);
		$t1 = strtolower($t1);
		//echo '<tt style="color: green;">'.$t1.'</tt>';
		
		foreach ($sw as $key => $value) {
			
			$key = LowerCaseSQL($key);
			//echo '<br>==><tt>'.$key.'=='.$value.'</tt>';
			$t1 = str_replace(array(' '.$key.' '),array(' ['.$value.'] '),$t1);
		}
		$t1 = troca($t1,' . ','.');
		
		//echo '<tt style="color: red;">'.$t1.'</tt>';
		
		/* Separa as palavras */
		$t = $this->limpa_text($t1,0);
		
		$t = troca($t,' ',';');
		$wd = splitx(';',$t);
		
		/******************************************* Quantifica frequencia dos termos */
		$tm = array();
		for ($r=0;$r < count($wd);$r++)
		{
			$term = $wd[$r];
			if (isset($tm[$term]))
			{
				$tm[$term] = $tm[$term] + 1;
			} else {
				$tm[$term] = 1;
			}
		}
		
		/******************************************* STOP WORDS */
		$qt = array();
		foreach ($tm as $key => $value) {
			array_push($qt,strzero($value,5).$key);
		}
		sort($qt);
		$min = 2;
		
		$sx = '';
		$col1 = '';
		$col2 = '';
		for ($r=(count($qt)-1);$r >= 0;$r--)
		{
			$value = round(substr($qt[$r],0,5));
			$key = substr($qt[$r],5,strlen($qt[$r]));
			
			if ($value > $min)
			{
				if (substr($key,0,1) == '[')
				{
					$tz = $this->show_conecpt($key);
					$t0 = $tz[0];
					$t1 = $tz[1];
					if (($t0 != '[[sw]]') and ($t0 != '[.]'))
					{
						$col1 .= '<br>'.$t1.' - '.$value;
					}
				} else {
					$col2 .= '<br>'.$key.' - '.$value;
				}				
			}
		}
		$sx = '<table width="100%"><tr valign="top"><td width="50%">'.$col1.'</td><td>'.$col2.'</td></tr></table>';
		$tq = troca($t,';',' ');
		$tq = troca($tq,'[[sw]]',' ');
		$tq = troca($tq,' [.]','.<br>');
		$sx .= '<hr>texto<hr><div class="text-justify">'.$tq.'</div>';
		$sx .= '<hr>Referencias<hr>'.$refs;
		return($sx);
	}
	
	function show_conecpt($key='')
	{
		$domain = array('#thesa');
		$link = '';
		for ($r=0;$r < count($domain);$r++)
		{
			if (strpos($key,$domain[$r]) > 0)
			{
				$link = substr($key,strpos($key,$domain[$r]),strlen($key));
				$key = trim(substr($key,0,strpos($key,$domain[$r]))).']';
				$link = '<a href="https://www.ufrgs.br/tesauros/index.php/thesa/c/'.sonumero($link).'" target="_new">'.$key.'</a>';
			}
		}
		$key = trim($key);
		return(array($key,$link));
	}
	
	function change_array($t,$w)
	{
		foreach ($w as $key => $v) {
			$t = troca($t,$key.' ',$v.' ');
		}
		return($t);
	}
	
	function nlp_phases($txt)
	{
		
	}
}

/************************************************** NEURONIOS */
function neuro_link($t)
{
	$loop = 0;
	$t .= ' ';
	while ((($pos = strpos($t,'https:')) or ($pos = strpos($t,'http:'))) and ($loop < 500))
	{
		$s = substr($t,$pos,strlen($t));
		//echo substr($s,0,100).'<br>';
		$lk = substr($s,0,strpos($s,' '));
		/**************************************************************/
		$c = '>';
		if (strpos($lk,$c)) { $lk = substr($lk,0,strpos($lk,$c)); }
		if (substr($lk,strlen($lk)-1,1) == '.') { $lk = substr($lk,0,strlen($lk)-1); }
		
		$lku = troca($lk,'https','URLs');
		$lku = troca($lk,'http','URL');
		$t = troca($t,$lk,'[{'.$lku.'}]');
		$loop++;
	}
	return($t);
}

function neuro_referencias($t)
{
	$a = array('Referências','REFERÊNCIAS','referencias');
	for ($r=0;$r < count($a);$r++)
	{
		$pos = strpos($t,'> '.$a[$r]);
		if ($pos > 0)
		{
			$ref = trim(substr($t,$pos+strlen($a[$r])+2,strlen($t)));
			$ref = troca($ref,'>',(chr(13)));
			$ref = troca($ref,'','');
			
			$ref = troca($ref,chr(13),chr(10));
			$ln = explode(chr(10),$ref);

			$lst = '';
			echo '<tt>';
			for ($r=0;$r < count($ln);$r++)
				{
					$n2 = caixa_alta($ln[$r]);
					$rf = trim($lst.' '.trim($ln[$r]));	
									
					$n1 = tem_ano($rf);
					if ($n1 == 0)
						{ 
							$lst = $rf;
						} else {
							$lst = '';
							echo '<hr><b>'.$rf.'</b>';
							echo 'Neuronio #1:'.$n1.'<br>';
						}
				}
			$text = substr($t,0,$pos);
			return(array($text,$ref));
		}
	}
	return(array($t,''));
}

#validador 1
function tem_ano($lz)
	{
		for ($r=(date("Y")+2);$r > 1900;$r--)
			{
				$ano = (string)$r;
				$pos = strpos($lz,$ano);
				if ($pos > 0) 
					{ 						
						return(1); 
					}
			}
		return(0);

	}
#validador 2
function caixa_alta($lz)
	{
		$lzr = str_replace(array(',',';','.','!','?'),array(' '),$lz);
		$lzr = trim(substr($lzr,0,strpos($lzr,' ')));
		$lzru = UpperCaseSQL($lzr);

		if ($lzru == $lzr)
			{
				return(1);
			}
		echo $lz.'<br>'.$lzr;
		exit;
	}

function neuro_email($t)
{
	
}
?>
