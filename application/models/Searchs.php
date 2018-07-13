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

	function ajax_q($q = '') {
		$limit = 15;
		$n = lowercasesql($q);
		$n = convert($n);
		$rs = '';
		/* recupera arquivo de index */
		$fl = 'c/search_subject.search';
		if (is_file($fl)) {
			$f = load_file_local($fl);
			$ln = splitx('¢', $f);
			for ($r = 0; $r < count($ln); $r++) {
				/* busca termo */
				if (strpos($ln[$r], $n)) {
					$limit--;
					$na = (string)$ln[$r];
					$na = substr($na, strpos($na, '[') + 1, strlen($na));
					$na = substr($na, 0, strpos($na, ']'));
					$rs .= ',"' . $na . '"';
					if ($limit <= 0) {
						$r = count($ln);
					}
				}
			}
		}
		echo '["' . $n . '*"' . $rs . ']';
	}

	function s($n, $t = '') {
		$type = 'article';
		$q = $this -> elasticsearch -> query($type, $n);
		//$q = $this->ElasticSearch->query_all($n);
		
		echo '<pre>xx';
		print_r($q['hits']['hits']);
		echo '</pre>';
		
		$sx = 'Total ' . $q['hits']['total'];

		/**************************************************** Busca Parte II *****************/
		$sx .= '<div class="row result">';
		$rst = $q['hits']['hits'];
		for($r=0;$r < count($rst);$r++) {
			$key = $rst[$r]['_source']['article_id'];
			$sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . 'img/cover/cover_issue_3477_pt_BR.jpg" class="img-fluid"></div>';
			$sx .= '<div class="col-11 " style="margin-bottom: 15px;">';
			$sx .= '<input type="checkbox"> ';
			$sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
			$sx .= $this -> frbr -> show_v($key);
			$sx .= '</a>';
			$sx .= ' <sup>'.number_format($rst[$r]['_score'],4).'</sup>';
			$sx .= '</div>';
		}
		$sx .= '</div>';

		return ($sx);
		/*********************************************************************************/
	}

	function func_and($g1, $g2) {
		$g = array();
		foreach ($g1 as $key => $value) {
			if (isset($g2[$key])) {
				$g[$key] = $value + $g2[$key];
			}
		}
		return ($g);
	}

}
?>
