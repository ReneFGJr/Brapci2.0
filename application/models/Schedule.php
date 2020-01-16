<?php
class schedule extends CI_model
{
	var $file = '/etc/crontab';
	var $roboti_path = '/usr/local/roboti/';
	var $roboti_name = 'Bob';
	VAR $bots = array();

	function __construct()
	{
		$this->bots();
	}

	function bots()
	{
		$config = $this->roboti_path.'config';
		check_dir($config);

		$directory = dir($config);
		$bots = array('roboti');

		while($file = $directory -> read()){
			if (strpos($file,'.roboti'))
			{
				$file = troca($file,'.roboti','') ;
				array_push($bots,$file);
			}			
		}
		$directory -> close();
		$this->bots = $bots;
	}		
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

	function cron_status($form='')
	{
		$json = '';
		$bots = $this->bots;
		$form = substr(strtolower(get("format").$form),0,4);
		$rsp = '';
		

		for ($r=0;$r < count($bots);$r++)
		{
			$bot =$bots[$r];
			$stlc = 'style="background-color: #ffd0d0; border: 1px solid #000000; margin: 5px;"';
			$botf = $this->roboti_path.'log/'.$bot.'.live';

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
				

				foreach ($dta as $key => $value) {
					if ($key=='robot')
					{
						$txt = '<h1>'.$value.'</h1>'.$txt;
					}
					if ($key=='data')
					{
						$txt .= '<h5>'.stodbr($value).' '.substr($value,11,10).'</h5>';
					}
					if ($key=='status')
					{
						$txt .= '<h3 style="color: green">'.$value.'</h3>';
						$stlc = 'style="background-color: #D0FFD0; border: 1px solid #000000; margin: 5px;"';
					}						
					if ($key=='task')
					{
						$txt .= '<h5 style="color: green">'.$value.'</h5>';
					}						
				}					
				/**************************************** Ajustes **********************/
				if ($form == 'html')
				{
					$txt = '<div class="col-md-2 text-center" '.$stlc.'>'.$txt.'</div>';
				}		
				$json .= $content;
			} else {
				{
					$txt = '<h1>'.$bot.'</h1>';
					$txt .= '<h5>File not found<br/>&nbsp;</h5>';
					$txt .= '<h3 style="color: red">dead</h3>';
					$txt = '<div class="col-md-2 text-center" '.$stlc.'>'.$txt.'</div>';
					$json .= '{"data":"0000-00-00 00:00:00","status":"dead","robot":"'.$bot.'"}';
				}				
				
			}
			$rsp .= $txt;			
		}
		if ($form == 'json') { $rsp = '['.$json.']'; }
		return($rsp);
	}

	function cron_execute()
	{
		$cmd = '';
		$bots = $this->bots;
		$wd = date("w");
		$task = array('cmd'=>'','bot'=>'','priority'=>999999);
		for ($r=1;$r < count($bots);$r++)
		{
			$filename = $this->roboti_path.'config/'.$bots[$r].'.roboti';
			echo '==>'.$filename.'<br>';
			$t = file_get_contents($filename);
			$t = troca($t,';','.,');
			$t = troca($t,chr(13),';');
			$t = troca($t,chr(10),';');
			$ln = splitx(';',$t);
			$cmd = '';
			$prio = 9999;
			$weekdays = '';
			$hours = '';

			for ($y=0;$y < count($ln);$y++)
			{
				$l = $ln[$y];
				if (strpos(' '.$l,'priority=') > 0) 
					{ $prio = substr($l,strpos($l,'=')+1,strlen($l)); }
				if (strpos(' '.$l,'cmd=') > 0) 
					{ $cmd = substr($l,strpos($l,'=')+1,strlen($l)); }
				if (strpos(' '.$l,'weekdays=') > 0) 
					{ $weekdays = substr($l,strpos($l,'=')+1,strlen($l)); }
				if (strpos(' '.$l,'hours=') > 0) 
					{ $hours = substr($l,strpos($l,'=')+1,strlen($l)); }
			}
			/* Regra 1 */
			if (($weekdays == '*') or ($weekdays == $wd))
			{
				/* Regra 2 */				
				if ($task['priority'] > $prio)
				{
					$task['priority'] = $prio;
					$task['bot'] = $bots[$r];
					$task['cmd'] = $cmd;
				}
			}

			if (strlen($task['cmd']) > 0)
			{
				$this->last_task(1,$task['bot']);
				$output = shell_exec($task['cmd']);
				echo $output;
				$this->save_log($task['bot'],$output);
			}
		}
	}

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

	function cron($path='')
	{
		switch($path)
		{
			case 'status':
			$dt = $this->cron_status('json');
			echo $dt;
			break;

			/****************** Default **************/
			default:
			$this->cron_execute();
			$this->last_task(1,'roboti');
			break;
		}
		
	}
	function no_service()
	{
		$data = array('service'=>'500','error'=>'not informed');
		header('Content-Type: application/json');
		echo json_encode($data);	
	}
	function status_json()
	{
		$data = array('status'=>'active');
		header('Content-Type: application/json');
		echo json_encode($data);
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
				$sx .= cr().'<tt>curl "http://www.brapci.inf.br/index.php/roboti/cron/" --max-time 60</tt>';
			}

		} else {
			$sx = 'File not found in '.$this->file;
		}
		return($sx);

	}
}
?>