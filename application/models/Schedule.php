<?php
class schedule extends CI_model
{
	var $file = '/etc/crontab';
	var $roboti_path = 'script/roboti/';
	var $roboti_name = 'Bob';
	VAR $bots = array();
	
	function __construct()
	{
		$this->bots();
	}
	
	/******************************
	* CRON
	*/	
	function cron($path='',$bot='')
	{
		switch($path)
		{
			case 'status':
				$dt = $this->cron_status('json');
				echo $dt;
			break;
			
			case 'exec':
				$dt = $this->bot_execute($bot);
				echo $dt;
			break;			
			
			/****************** Default **************/
			default:
			$this->cron_execute();
			$this->last_task(1,'roboti');
		}		
	}	
	
	/*************************************************
	* MENU DO ADMINISTRADOR
	*/	
	function admin($act='',$d1='',$d2='',$d3='')
	{
		$serv = array();
		array_push($serv,'Robots');
		$sx = '<h1>'.msg('Admin').'</h1>';
		
		$sx .= '<div class="row">';
		for ($r=0;$r < count($serv);$r++)
		{			
			$sx .= '<div class="col-md-3" style="paddin: 5px 5px; border: 1px solid #000000; border-radius: 10px; height: 120px;">';
			$sx .= '<a href="'.base_url(PATH.'admin/'.strtolower($serv[$r])).'">';
			$sx .= '<h2>'.$serv[$r].'</h2>';
			$sx .= '</a>';
			$sx .= '<i>'.msg($serv[$r].'_info').'</i>';
			$sx .= '</div>';			
		}
		$sx .= '</div>';
		
		switch($act)
		{
			case 'robots':
				$sx = '<h1>'.msg($act).'</h1>';
				$sx .= '<a href="'.base_url(PATH.'admin/robots/edit/0').'" class="btn btn-primary">';
				$sx .= msg('roboti_create_bot');
				$sx .= '</a>';
				
				switch($d1)
				{
					case 'edit':						
						$sx .= $this->bot_edit();
					break;
				}
			break;
		}
		return($sx);
	}
	
	/*********************************************
	* BOT EDIT & CREATE
	*/
	function bot_edit()
	{
		$cp = array();
		array_push($cp,array('$H8','','',false,false));
		array_push($cp,array('$S50','',msg('bot_name'),true,true));
		array_push($cp,array('$T80:5','',msg('bot_cmd'),true,true));
		
		array_push($cp,array('$A','',msg('week_day'),false,true));		
		array_push($cp,array('$C','',msg('monday'),false,true));
		array_push($cp,array('$C','',msg('tuesday'),false,true));
		array_push($cp,array('$C','',msg('wedesney'),false,true));
		array_push($cp,array('$C','',msg('thursday'),false,true));
		array_push($cp,array('$C','',msg('friday'),false,true));
		array_push($cp,array('$C','',msg('saturday'),false,true));
		array_push($cp,array('$C','',msg('sunday'),false,true));
		array_push($cp,array('$[0-23]','',msg('bot_hour'),false,true));

		array_push($cp,array('$[0-99]','',msg('bot_priority'),false,true));
		$form = new form;
		$form->id = 0;
		$sx = $form->editar($cp,'');
		
		if ($form->saved > 0)
		{
			$bot = array();
			$bot['bot'] = lowercasesql(get("dd1"));
			$bot['created'] = date("Y-m-d");
			$bot['lastupdate'] = date("Y-m-d");
			$bot['cmd'] = get("dd2");
			$bot['day'] = '0000000';
			$bot['hour'] = get("dd11");
			$bot['priority'] = get("dd12");
			for ($r=4;$r <= 7;$r++)
			{
				$n = 'dd'.$r;
				if (get($n) == 1)
				{
					$bot['day'][$r-4] = '1';
				}
			}
			$this->bot_save($bot);
			redirect(base_url(PATH.'admin/robots'));
		}
		return($sx);
	}
	
	/***********************
	* SAVE BOT
	*/
	
	function bot_save($bot)
	{	
		$file = $this->roboti_path.'config/'.$bot['bot'].'.roboti';
		echo $file;
		$txt = json_encode($bot);
		file_put_contents($file,$txt);
		return(1);
	}
	
	/***************************
	* BOTS
	*/
	
	function bots()
	{

	}
	
	/****************************************
	* HELP */		
	function help()
	{
		$sx = '<div class="container"><div class="row"><div class="col-md-12">';
		$sx .= '<img src="'.base_url('img/logo/roboti.png').'">';
		$sx .= '</div>';
		$sx .= '<div class="col-md-12">';
		$sx .= '<h2>Schedule</h2>';
		$sx .= '<pre>';
		$sx .= '<a href="'.base_url(PATH.'service/schedule/status').'">:status</a> - situacao do serviço</a>'.cr();
		$sx .= '<a href="'.base_url(PATH.'service/schedule/config').'">:config</a> - lista tarefas agendadas</a>'.cr();		
		$sx .= '</pre>';
		
		$sx .= '<h2>Cron</h2>';
		$sx .= '<pre>';
		$sx .= '<a href="'.base_url(PATH.'cron/status').'">:status</a> - status dos Robots</a>'.cr();
		$sx .= '</pre>';
		
		$sx .= '</div>';
		$sx .= '</div>';
		$sx .= '</div>';
		return($sx);			
	}
	
	/****************************************
	* CRON STATUS
	*/
	
	function cron_status($form='')
	{
		$json = '';
		$bots = $this->bots;
		$form = substr(strtolower(get("format").$form),0,4);
		$rsp = '';
		
		
		for ($r=0;$r < count($bots);$r++)
		{
			$bot =$bots[$r];
			$stlc = 'style="background-color: #ffd0d0; border: 1px solid #000000; border-radius: 10px; box-shadow: 5px 5px 8px #888; margin: 5px;"';
			$botf = $this->roboti_path.'log/'.$bot.'.live';
			
			$link = base_url(PATH.'bots/'.$bot);
			$link = '<a href="'.$link.'" class="col-md-4" style="color: black; text-decoration: none;">';
			$linka = '</a>';
			
			if (strlen($json) > 0)
			{
				$json .= ',';
			}			
			
			if (file_exists($botf))
			{
				$content = file_get_contents($botf);
				/*************************************************** HTML *************************/
				$dta = json_decode($content);
				$txt = '';
				$dias = 0;
				
				foreach ($dta as $key => $value) {
					if ($key=='robot')
					{
						$txt = '<h1>'.$value.'</h1>'.$txt;
					}
					if ($key=='data')
					{
						$data = strtotime($value);
						$database = date_create($value);
						$datadehoje = date_create();
						$resultado = date_diff($database, $datadehoje);
						$dias = date_interval_format($resultado, '%a');
						$txt .= '<h5>'.stodbr($value).' '.substr($value,11,10).'</h5>';
					}
					if ($key=='status')
					{
						$color = '#D0FFD0';
						if ($dias > 7)
						{
							$color = '#FFFFD0';
							if ($dias > 14) { $color = '#FF8080'; }
						}
						$txt .= '<br/>';
						$txt .= '<h3 style="color: green">'.$value.'</h3>';
						$stlc = 'style="background-color: '.$color.'; border: 1px solid #000000; border-radius: 10px; box-shadow: 5px 5px 8px #888; margin: 5px;"';
					}						
					if ($key=='task')
					{
						$txt .= '<h5 style="color: green">'.$value.'</h5>';
					}						
				}					
				/**************************************** Ajustes **********************/
				if ($form == 'html')
				{
					$link = base_url(PATH.'bots/'.$bot);
					$link = '<a href="'.$link.'" class="col-md-4" style="color: black; text-decoration: none;">';
					$linka = '</a>';

					$txt = $link.'<div class="text-center" '.$stlc.'>'.$txt.'</div>'.$linka;
				}		
				$json .= $content;
			} else {
				{
					$txt = '<h1>'.$bot.'</h1>';
					$txt .= '<h5>File not found<br/>&nbsp;</h5>';
					$txt .= '<h3 style="color: red">dead</h3>';
					$txt = $link.'<div class="text-center" '.$stlc.'>'.$txt.'</div>'.$linka;
					$json .= '{"data":"0000-00-00 00:00:00","status":"dead","robot":"'.$bot.'"}';
				}				
				
			}
			$rsp .= $txt;			
		}
		if ($form == 'json') { $rsp = '['.$json.']'; }
		return($rsp);
	}
	
	/*********************
	* CROM EXECUTE
	*/
	
	function cron_execute()
	{
		$cmd = '';
		$bots = $this->bots;
		$sx = 'Roboti 2.1'.cr();
		$wd = date("w");
		$hr = date("H");
		$task = array('cmd'=>'','bot'=>'','priority'=>999999);
		for ($r=1;$r < count($bots);$r++)
		{
			$filename = $this->roboti_path.'config/'.$bots[$r].'.roboti';
			//echo '==>'.$filename.'<br>';
			$t = file_get_contents($filename);
			$dt = json_decode($t);
			$dt = (array)$dt;

			$cmd = $dt['cmd'];
			$day = $dt['day'];
			$hour = $dt['hour'];
			$ok = 1;
			if ($hour > 0)
				{
					if ($hour != round(date("h"))) { $ok = 0; }
				}

			$dtx = round(date("w"));
			if (substr($day,$dtx,1) != '1')
				{
					$ok = 0;
				}
			$task['priority'] = $dt['priority'];
			$task['bot'] = $dt['bot'];
			$task['cmd'] = $cmd;

			if ($ok == 1)
			{
				$sx = $this->bot_execute($task['bot']);
			}
		}
		return($sx);
	}
	
	/***************************************************************
	* Execute Command Bot
	*/
	function bot_execute($bot)
	{
		$task = $this->bot_read($bot);
		if (strlen($task['cmd']) > 0)
		{
			echo $task['cmd'].cr();
			$this->last_task(1,$task['bot']);
			$output = shell_exec($task['cmd']);
			echo $output;
			$this->save_log($task['bot'],$output);
		}
	}
	/********************************
	* BOT SAVE LOG
	*/
	
	
	function save_log($bot,$txt2)
	{
		$file = $this->roboti_path.'log/'.$bot;
		check_dir($file);
		$file .= '/'.date("Y-m").'-'.$bot.'.log';
		if (file_exists($file))
		{
			$txt = file_get_contents($file);
		} else {
			$txt = '';
		}
		
		$sp = '------------------------------------------------------------------------------------'.cr();
		$txt = date("Y-m-d H:i:s").' - '.$bot.cr().$txt2.cr().$sp.$txt;
		file_put_contents($file, $txt);
	}
	
	


	
	function last_task($new=0,$bot='roboti')
	{
		$file = $this->roboti_path.'/log/';
		check_dir($file);
		
		if ($new == 1)
		{
			$rst['data'] = date("Y-m-d H:i:s");
			$rst['status'] = 'live';
			$rst['robot'] = substr(strtoupper($bot),0,1).substr($bot,1,strlen($bot));
			$txt = json_encode($rst);
			file_put_contents($file.$bot.'.live',$txt);
		}
	}
	function read_crontab()
	{
		$sx = '';
		if (file_exists($this->file))
		{
			$t = file_get_contents($this->file);
			$sx .= '<h3>'.$this->file.'</h3>';
			$sx .= '<pre>'.$t.'</pre>';
			
			if (!strpos($t,'roboti'))
			{
				$sx .= cr().'<b>Robot<i>i</i></b> not found, append the this line in crontab';
				$sx .= cr().'<span style="color: red"><b>30 * * * * root '.$this->roboti_path.'</b></span>';
			} else {
				$sx .= cr().'<h3><b>Robot<i>i</i></b> <span class="color:#008000">service <b>live</b></span></h3>';
			}
			
			$file = '/usr/local/roboti';
			
			if (!file_exists($file))
			{
				$sx .= cr().'<hr>';
				$sx .= '<span style="color: red">File not found</span><br/>';
				$sx .= cr().'Criar o arqui '.$this->roboti_path.' com o conteúdo de:<br>';
				$sx .= cr().'<tt>curl "http://brapci.inf.br/index.php/roboti/cron/" --max-time 60</tt>';
			}
			
		} else {
			$sx = 'File not found in '.$this->file;
		}
		return($sx);
	}
	
	function bot_read($act)
	{
		$file = $this->roboti_path.'config/'.$act.'.roboti';
		if (file_exists($file))
		{
			$cnt = file_get_contents($file);
			$data = json_decode($cnt);
			return((array)$data);
		} else {
			return(array());
		}
	}
	function bot_read_logs($act)
	{
		$file = $this->roboti_path.'log/'.$act.'.live';
		if (file_exists($file))
		{
			$cnt = file_get_contents($file);
			$data = json_decode($cnt);
			return((array)$data);
		} else {
			return(array());
		}
	}		
	
	function bot_check($act)
	{
		$sx = '';
		
		/* Bot name */
		$sx .= '<div class="row"><div class="col-md-12">';
		$sx .= 'Bot name:<br/><h2>'.$act.'</h2>';
		$sx .= '</div></div>';
		
		//$sx .= '<style> div { border: 1px solid #000000; } </style>';
		
		/* Dados do Arquivo */
		$sx .= '<div class="row"><div class="col-md-8">';
		$file = $this->schedule->roboti_path.'config/'.$act.'.roboti';
		$sx .= '<h5>'.$file.'</h5>';
		$sta = '';
		$stl = '';
		$ok = ' - <span style="color: #006000;"><b>OK</b></span>';
		$erro = ' - <span style="color: #ff0000;"><b>ERROR</b></span>';
		
		$sx .= '<a href="'.base_url(PATH.'cron/exec/'.$act).'" class="btn btn-outline-primary" target="roboti_iframe">'.msg('execute_now').'</a>';
		$sx .= '<iframe name="roboti_iframe" style="width: 100%; height: 400px;"></iframe>';
		
		/* Checa arquivo */
		if (file_exists($file))
		{
			$sta .= '<li>'.msg('file').$ok.'</li>';
		} else {
			$sta .= '<li>'.msg('file').$erro.'</li>';
		}
		
		$bots = $this->bot_read($act);
		$logs = $this->bot_read_logs($act);
		if (count($logs) == 0)
		{
			$stl .= '<li>Status: '.$erro.'</li>';
		} else {
			$stl .= '<li>Executed in: '.stodbr($logs['data']).'</li>';
		}
		
		$sx .= '<hr>';
		$sx .= 'bot name: '.$bots['bot'];
		$sx .= '<br>cmd: <tt>'.$bots['cmd'].'</tt>';
		$sta .= '<li>'.msg('created').': '.stodbr($bots['created']).'</li>';
		$sta .= '<li>'.msg('last_update').': '.stodbr($bots['lastupdate']).'</li>';
		$sx .= '</div>';
		
		/***************************************** CHECKIT */
		$sx .= '<div class="col-md-4">';
		$sx .= 'Check-It';
		$sx .= '<ul>'.$sta.'</ul>';
		
		$sx .= 'Live';
		$sx .= '<ul>'.$stl.'</ul>';
		$sx .= '</div>';
		$sx .= '</div>';
		$sx .= '</div>';
		return($sx);			
	}
}
?>