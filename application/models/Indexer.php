<?php
class indexer extends CI_model {

	function indexing($dt) {
		$s = '';
		$tit = count($dt['title']);
		for ($t = 0; $t < $tit; $t++) {
			$n = $this -> frbr_core -> utf8_detect((string)$dt['title'][$t]['title']);
			$n = lowercasesql($n);
			for ($r = 0; $r < strlen($n); $r++) {
				$c = substr($n, $r, 1);
				$co = ord($c);
				switch($c) {
					case '-' :
						$c = ' ';
						break;
					case '?' :
						$c = ' ';
						break;
					case '!' :
						$c = ' ';
						break;
					case '@' :
						$c = ' ';
						break;
					case '#' :
						$c = ' ';
						break;
					case ':' :
						$c = ' ';
						break;
					case '.' :
						$c = ' ';
						break;
					case ';' :
						$c = ' ';
						break;
					case ':' :
						$c = ' ';
						break;						
					case ',' :
						$c = ' ';
						break;
					case '/' :
						$c = ' ';
						break;
					case '(' :
						$c = ' ';
						break;
					case ')' :
						$c = ' ';
						break;
						
				}
				$s .= $c;
			}
			echo '<h4>' . $s . '</h4>';
			
			/* WORD */
			$s = troca($s,' ',';').';';
			$wds = splitx(';',$s);
			$article_id = $dt['article_id'];
			for ($r=0;$r < count($wds);$r++)
				{
					$name = $wds[$r];
					$prop = 'prefLabel';
					//$idn = $this->frbr_core->frbr_name($name);
					$idf = $this -> frbr_core -> rdf_concept_create('Word', $name, '');
					$this -> frbr_core -> set_propriety($article_id, $prop, $idf, 0);
					echo '<br>=>'.$idf.' = '.$name;
				}
			
			print_r($dt);
			exit;
		}
	}

}
?>
