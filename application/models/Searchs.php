<?php
class searchs extends CI_Model {
	function recover_reg($art, $t) {
		$t = substr($t, strpos($t, ']') + 1, strlen($t));
		$te = splitx(';', $t);
		for ($r = 0; $r < count($te); $r++) {
			if (isset($art[$te[$r]])) {
				$art[$te[$r]] = $art[$te[$r]] + 1;
			} else {
				$art[$te[$r]] = 1;
			}
		}
		return ($art);
	}

	function s($n, $t = '') {
		/* termo entre aspas */
		$i = 0;
		$s = '';
		$sp= ' ';
		for ($r = 0; $r < strlen($n); $r++) {
			$c = substr($n, $r, 1);
			if ($c == '"') {
				if ($i == 0) {
					$i = 1;
					$sp = '_';
					$c = '';
				} else {
					 $i = 0;
					 $sp = ' ';
					 $c = '';
				}
			}
			/****************** espaco ****/
			if ($c == ' ')
				{
					$c = $sp;
				}
			$s .= $c;
		}
		/* CONVERT ******************************************************/
		echo '<tt>'.$s.'</tt>';
		$n = troca($s, ' ', ';') . ';';
		$nn = splitx(';', $n);
		for ($t = 0; $t < count($nn); $t++) {
			$nn[$t] = troca($nn[$t],'_',' ');
			$nn[$t] = lowercasesql($nn[$t]);
		}
		$tot_term = count($nn);
		print_r($nn);
		$fl = 'c/search_subject.search';
		if (is_file($fl)) {
			$f = load_file_local($fl);
			$ln = splitx('¢', $f);

			$sx = '';
			/* method 1 */
			$art = array();
			$tot = 0;
			for ($r = 0; $r < count($ln); $r++) {
				$tot = 0;
				for ($t = 0; $t < $tot_term; $t++) {
					if (strpos($ln[$r], $nn[$t]) > 0) {
						//echo $ln[$r].'=='.$nn[$t].'<hr>';
						$tot++;
					}
				}
				if ($tot == $tot_term) {
					$art = $this -> recover_reg($art, $ln[$r]);
				}
			}
			$sx = '<div class="row result">';
			foreach ($art as $key => $value) {
				$sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . 'img/cover/cover_issue_3477_pt_BR.jpg" class="img-fluid"></div>';
				$sx .= '<div class="col-11 " style="margin-bottom: 15px;">';
				$sx .= '<input type="checkbox"> ';
				$sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
				$sx .= $this -> frbr -> show_v($key);
				$sx .= '</a>';
				$sx .= '</div>';
			}
			$sx .= '</div>';
		} else {
			$sx = '
                <div class="alert alert-warning" role="alert">
                  ERRO #1001! The Search index file not Found
                </div>';
		}
		return ($sx);
	}

	function convert($t) {
		$t .= ' ';
		/**** PT-BR ****/
		$a = array(' às ' => ' à ', ' ás ' => ' à ', ' as' => ' a', ' os' => ' o', ' Os' => ' o', ' As' => ' a', ' aos' => ' ao', 'aces' => 'ace', 'ais' => 'al', 'aos' => 'ao', 'afos' => 'afo', 'ãos' => 'ao', 'ães' => 'ão', 'ares' => 'ar', 'ubes' => 'ube', 'bês' => 'bê', 'bens' => 'bem', 'Bens' => 'Bem', 'Boas' => 'Boa', 'blemas' => 'blema', 'boas' => 'boa', 'cas' => 'ca', 'cias' => 'cia', 'cies' => 'cie', 'cios' => 'cio', 'chas' => 'cha', 'cas' => 'ca', 'cos' => 'co', 'ços' => 'ço', 'ças' => 'ça', 'çõs' => 'ção', 'ças' => 'ça', 'coes' => 'cao', 'ções' => 'ção', 'dados' => 'dado', 'des' => 'de', 'dios' => 'dio', 'deos' => 'deo', 'dos' => 'do', 'das' => 'da', 'dias' => 'dia', 'nios' => 'nio', 'dores' => 'dor', 'eias' => 'eia', 'eis' => 'el', 'eios' => 'eio', 'fas' => 'fa', 'fias' => 'fia', 'fías' => 'fia', 'fins' => 'fim', 'Fins' => 'Fim', 'fis' => 'fil', 'fios' => 'fio', 'gens' => 'gem', 'gias' => 'gia', 'gios' => 'gio', 'gos' => 'go', 'guas' => 'gua', 'gos' => 'go', 'iais' => 'ial', 'ioes' => 'iao', 'ões' => 'ão', 'ices' => 'ice', 'jos' => 'jo', 'jós' => 'jó', 'leis' => 'lei', 'lhos' => 'lho', 'lhas' => 'lha', 'las' => 'la', 'les' => 'le', 'leos' => 'leo', 'los' => 'lo', 'lores' => 'lor', 'mas' => 'ma', 'mes' => 'me', 'mias' => 'mia', 'mías' => 'mía', 'mos' => 'mo', 'mulas' => 'mula', 'nas' => 'na', 'nes' => 'ne', 'nos' => 'no', 'nhas' => 'nha', 'nhos' => 'nho', 'nais' => 'nal', 'nias' => 'ina', 'pias' => 'pia', 'pas' => 'pa', 'pes' => 'pe', 'pios' => 'pio', 'pos' => 'po', 'quais' => 'qual', 'ques' => 'que', 'ras' => 'ra', 'res' => 're', 'ros' => 'ro', 'rais' => 'ral', 'reas' => 'rea', 'rias' => 'ria', 'rías' => 'ria', 'rios' => 'rio', 'rois' => 'rol', 'ros' => 'ro', 'roes' => 'rao', 'soes' => 'sao', 'sas' => 'sa', 'ses' => 'se', 'temas' => 'tema', 'seus' => 'sel', 'sos' => 'so', 'soas' => 'soa', 'tas' => 'ta', 'tes' => 'te', 'tens' => 'tem', 'tins' => 'tim', 'tios' => 'tio', 'tras' => 'tra', 'toes' => 'tao', 'tos' => 'to', 'uais' => 'ual', 'uias' => 'uia', 'uns' => 'um', 'vas' => 'va', 'ves' => 've', 'veis' => 'vel', 'vis' => 'vil', 'vos' => 'vo', 'xos' => 'xo', 'xoes' => 'xao', 'zes' => 'z', );
		foreach ($a as $key => $value) {
			$t = troca($t, $key . ' ', $value . ' ');
		}

		while (strpos($t, '  ')) {
			$t = troca($t, '  ', ' ');
		}

		$t = trim($t);

		return ($t);
	}

	function ucwords($t) {
		$t = trim($t);
		$t = ucwords($t);
		$t = troca($t, ' A ', ' a ');
		$t = troca($t, ' Ao ', ' ao ');
		$t = troca($t, ' E ', ' e ');
		$t = troca($t, ' O ', ' o ');
		$t = troca($t, ' Com ', ' com ');
		$t = troca($t, ' Em ', ' em ');
		$t = troca($t, ' Na ', ' na ');
		$t = troca($t, ' No ', ' no ');
		$t = troca($t, ' Da ', ' da ');
		$t = troca($t, ' Das ', ' das ');
		$t = troca($t, ' De ', ' de ');
		$t = troca($t, ' Do ', ' do ');
		$t = troca($t, ' Nas ', ' no ');
		$t = troca($t, ' Nos ', ' no ');
		$t = troca($t, ' Para ', ' para ');
		$t = troca($t, ' Dos ', ' dos ');
		$t = troca($t, ' Of ', ' of ');
		$t = troca($t, ' Ou ', ' ou ');
		$t = troca($t, ' The ', ' the ');
		$t = troca($t, ' And ', ' and ');
		$t = troca($t, ' For ', ' for ');

		/************** Espanol ************/
		$t = troca($t, ' En ', ' en ');

		return ($t);
	}

}
?>
