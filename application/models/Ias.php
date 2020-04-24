<?php
class Ias extends CI_model
{
	var $version_nlp = '0.1';

	function check($f=0)
	{
		dircheck('_ia');
		if ($f==1)
		{
			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/69/txt';
			$file = '_ia/stopword.txt';
			$this->import_thesa($url,$file);

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/64/txt';
			$file = '_ia/domain_ci.txt';
			$this->import_thesa($url,$file);

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/243/txt';
			$file = '_ia/domain_methodology.txt';
			$this->import_thesa($url,$file);	

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/232/txt';
			$file = '_ia/domain_isko.txt';
			$this->import_thesa($url,$file);

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/262/txt';
			$file = '_ia/domain_instituicoes.txt';
			$this->import_thesa($url,$file);	

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/8/txt';
			$file = '_ia/domain_universidades.txt';
			$this->import_thesa($url,$file);

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/9/txt';
			$file = '_ia/domain_areas_do_conhecimento.txt';
			$this->import_thesa($url,$file);	

			$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/10/txt';
			$file = '_ia/domain_datas.txt';
			$this->import_thesa($url,$file);														
		}
	}	

	function index($act='',$d1='',$d2='',$d3='')
	{
		$sx = '';
		switch($act)
		{
			case 'nlp':
			$this->check(1);
			$sx = '<h1>'.msg("NLP").' <sup>v.'.$this->version_nlp.'</sup></h1>';
			$sx .= $this->nlp($d1,$d2,$d3);
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
			$sx .= '<li>'.'<a href="'.base_url(PATH.'ai/nlp/run').'">'.msg("Processing Teste").'</a>';
			$sx .= '</ul>';
		}
		return($sx);
	}

	function nlp_process($t)
	{
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

	function services()
	{
		$sx = '<div class="row">';
		$sx .= '<a href="'.base_url(PATH.'ai/nlp').'">';
		$sx .= '<div class="col-md-2" class="btn-primary btn">';			
		$sx .= 'NLP '.$this->version_nlp;
		$sx .= '</div>';
		$sx .= '</a>';
		$sx .= '</div>';
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
				$t = LowerCaseSql($ln[0]);
				$te = troca($ln[1],' ','_');				
				$w = strzero(strlen($t),5).'$sw[\''.$t.'\'] = ';
				$w .= "'".$te."';";
				array_push($s,$w);
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
		foreach ($l as $ln => $f) {
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

	function v($d)
	{
		$this->check();
		$rdf = new rdf;
		$vv = $rdf->extract_content($d,'hasFileStorage');
		$file = troca($vv[0],'.pdf','.txt');
		if (file_exists($file))
		{
			$txt = $this->file_get($file);
			$txt = '<tt>'.$txt.'</tt>';

			$txt = $this->nlp_words($txt).cr().$txt;
		} else {
			$txt = message(1,msg('File not found'));
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
			$link = '<a href="'.base_url(PATH.'ia/'.$id).'">';
			$linka = '</a>';
		}

		$div = '<div class="infobox" style="width: 100px;">';
		$div .= '<div class="infobox_name" style="background-color: #e0e0ff; float: left; width: 70%; padding: 0px 5px;">'.$t.'</div>';
		$div .= '<div class="infobox_version" style="float: left; background-color: #e0ffe0; width: 30%; padding: 0px 2px; text-align: right;">'.$link.$ver.$linka.'</div>';
		$div .= '</div>';
		return($div);
	}

	function limpa_text($t)
	{
		$ts = array('...','..','. ',' .',';.','“','”',',',':','>','<','?','"','/','\\','!','|','[',']','–','(',')');
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
		$ts = '';
		$ts .= file_get_contents('_ia/domain_universidades.txt');
		$ts .= file_get_contents('_ia/domain_instituicoes.txt');
		$ts .= file_get_contents('_ia/domain_areas_do_conhecimento.txt');
		$ts .= file_get_contents('_ia/domain_ci.txt');
		$ts .= file_get_contents('_ia/domain_methodology.txt');
		$ts .= file_get_contents('_ia/domain_isko.txt');
		$ts .= file_get_contents('_ia/domain_datas.txt');
		$ts = splitx(';',$ts);
		sort($ts);

		$domains = '';
		for($r=(count($ts)-1);$r >= 0;$r--)
		{
			$key = $ts[$r];
			$domains .= substr($key,5,strlen($key)).';'.cr();
			//$w .= $key.';'.cr();
		}
		file_put_contents('_ia/domain.txt', $domains);
		$sw = array();
		eval($domains);

		/* Realiza trocas nos domínios */
		$t = $this->change_array($t,$sw);

		/************************** Substitui no texto ***********/
		$t1 = LowerCaseSql($txt);
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
		//echo '<tt style="color: green;">'.$t1.'</tt>';

		foreach ($sw as $key => $value) {
			$t1 = troca($t1,' '.$key.' ',' ['.$value.'] ');
			/*
			while (strpos($t1,$key.' ') > 0)
			{
				$pos = strpos($t1,$key.' ');
				$t1 = substr($t1,0,$pos).'['.$value.']'.substr($t1,$pos+strlen($key),strlen($t1));
			}
			*/
		}
		$t1 = troca($t1,' . ','.');

		//echo '<tt style="color: red;">'.$txt.'</tt>';

		/* Separa as palavras */
		$t = $this->limpa_text($t1);
		$t = troca($t,' ',';');
		$w = splitx(';',$t);

		/******************************************* STOP WORDS */

		/* recupera stop words */
		$ic_stopword = file_get_contents('_ia/stopword.txt');
		for ($r=0;$r <=9;$r++)
		{
			$ic_stopword = troca($ic_stopword,$r,'');
		}
		eval($ic_stopword);

		/* Gera lista de palavras */
		$wd = array();
		for ($r=0;$r < count($w);$r++)
		{
			$key = $w[$r];			
			if (!isset($sw[$key]) and (strlen($key) > 1))
			{
				if (isset($wd[$key]))
				{
					$wd[$key] = 1 + $wd[$key];
				} else {
					$wd[$key] = 1;
				}
			}
		}


		echo '<tt style="color: green;">'.$t1.'</tt>';


		/************************************************/
		$qt = array();
		foreach ($wd as $key => $value) {
			array_push($qt,strzero($value,5).$key);
		}
		sort($qt);
		$min = 2;

		$sx = '';
		for ($r=(count($qt)-1);$r >= 0;$r--)
		{
			$value = round(substr($qt[$r],0,5));
			$key = substr($qt[$r],5,strlen($qt[$r]));

			if ($value > $min)
			{
				$sx .= '<br>'.$key.' - '.$value;
			}
		}
		//$qt = $this->quartil($qt,2,1,2);
		return($sx);
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
?>