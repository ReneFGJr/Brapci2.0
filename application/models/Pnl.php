<?php
class pnl extends CI_model
{
	function process()
	{
		$file = '/var/www/html/xxx/aa.txt';

		if (file_exists($file))
		{
			echo 'Processing '.$file;
			$txt = file_get_contents($file);
			$lns = $this->lns($txt);
		} else {
			echo '<br/>File not found '.$file;
		}
	}

	function remove_lines($ln,$txt,$s='REMOVED')
	{
		$lns = array();
		for ($r=0;$r < count($ln);$r++)
		{
			$ok = 1;
			/* sem texto */
			$sn = strlen(trim($ln[$r]));
			if ($sn == 0)
			{
				$ok = 0;
			}


			/* texto de paginas */
			$sn = sonumero(trim($ln[$r]));
			if ($sn == trim($ln[$r]))
			{
				$ok = 0;
			}

			/* texto de legendas */
			if (($ln[$r] == $txt))
			{
				$ok = 0;
			}
			if ($ok==1)
			{
				array_push($lns,$ln[$r]);
			}
		}
		return($lns);
	}

	function lns($txt)
	{
		$txt = troca($txt,';','.,');
		$txt = troca($txt,chr(13),';');
		$txt = troca($txt,chr(10),';');
		$ln = splitx(';',$txt);

		$lns = $ln;
		sort($ln);

		/***************** PROCESSAR LEGENDAS E LINHAS NULAS *****/
		$xl = '';
		foreach ($ln as $key => $value) {
			$l = $value;
			if ($l == $xl)
			{
				$lns = $this->remove_lines($lns,$l);
			}
			$xl = $l;
		}

		/***************** UNIR LINHAS *****/


		/***************** REFERÊNCIAS *****/
		$ref = 0;
		$text = '';
		$aref = array();
		for ($r=0;$r < count($lns);$r++)
		{
			if ($lns[$r] == 'REFERÊNCIAS')
			{
				for ($y=$r+1;$y < count($lns);$y++)		
				{
					$l = trim($lns[$y]);
					$tp = $this->word_type($l);	
					$lc = $this->last_type($lns[$y-1]);

					switch ($tp)
					{
						/************* REDE NEURAL ************/

						case 'UPPER':
						if (($lc == 'PT') or ($lc == 'NC') or ($lc == 'NUMBER'))
						{
							$text .= '['.$lc.']'.cr();	
						}
						break;
					}
					$text .= $l.' ';						
				}
				$r = count($lns);
			}

		}

		/* Preparação */
		$text = troca($text,'[','');
		$text = troca($text,']','');

		echo '<pre>';
		print_r($text);
	}

	function has_date($t)
	{
		for ($r=1900;$r <= (date("Y")+1);$r++)
		{
			if (strpos($t,(string)($r)) > 0) { return(1); }
		}
		return(0);
	}

	function last_type($l)
	{
		$tp = 'NC';
		$l = trim($l);
		while (strpos($l,' ') > 0)
		{
			$l = trim(substr($l,strpos($l,' ')+1,strlen($l)));
		}
		$lc = substr($l,strlen($l)-1,1);

		if ($lc == '.') { return('PT'); }
		if ($lc == ':') { return('DP'); }
		if ($lc == ',') { return('VG'); }
		if ($lc == ')') { return('APAR'); }
		if ($lc == '(') { return('FPAR'); }
		if (sonumero($lc) == $lc) { return('NUMBER'); }
		if ((UpperCaseSql($lc) == $lc) and ($lc >= 'A') and ($lc <= 'Z')) { return('UPPER'); }		
		return($tp);
	}

	function word_type($t)
	{
		$tp = 'NC';
		$t = troca($t,',',' ');
		$t = troca($t,'.',' ');
		$t = troca($t,';',' ');
		$t = troca($t,'(',' ');
		$t = troca($t,'-',' ');
		$t = ascii($t);
		$s = UpperCaseSql($t);

		$s = substr($s,0,strpos($s,' '));
		$t = substr($t,0,strpos($t,' '));
		/* RULE 1 - All UpperCase */

		$is_number = (sonumero($s)==$s);

		if (($s == $t) and (strlen($t) > 0) and ($is_number != 1))
		{
			$tp = 'UPPER';
		} else {
			if ($is_number)
			{
				$tp = 'NUMBER';
			} else {

			}
		}
		return($tp);
	}


}
?>