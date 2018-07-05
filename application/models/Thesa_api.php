<?php
class thesa_api extends CI_model {
	function ajax($id = '') {
		$name = trim(get("dd1"));
		$name = troca($name, '"', '');
		$name = troca($name, "'", '');
		$name = ucase($name);
		$name = convert($name);

		$id = trim(get("id"));

		echo msg('find') . ' <b>' . $name . '</b>';

		$data = array("term" => $name, );

		$curl = curl_init();
		$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/api/64';
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		//$result = curl_exec($curl);

		$result = load_file_local($url . '?term=' . $name);
		echo '<br>' . $url;

		if (strlen($result) == 0) {
			echo '<br><span style="color: red">Empty Result</span> =>' . count($result);
			return ("");
		}
		$xml = simplexml_load_string($result);
		$erro = (string)$xml -> error;
		$orign = (string)$xml -> concept;
		$sx = '<hr>';

		if (strlen($erro) == 0) {
			/******************** RECUPERA TERMO PREFERENCIAL DO XML *************/
			$pref = trim((string)$xml -> prefLabel -> term);
			$pref_lang = $this -> frbr_core -> language((string)$xml -> prefLabel -> lang);

			echo '<hr>' . $pref . ' <sup>(' . $pref_lang . ')</sup>';
			/* primaryTopic */

			/* verifica se existe term preferencial ******************************/
			if (strlen($pref) > 0) {
				/* Checa se já não existe Termo Preferencial */
				$idpref = $this -> frbr_core -> find($pref, 'prefLabel', True);
				$sx .= date("Y-m-d H:i:s") . ' <font color="blue">Find idpref for <b>' . $pref . '</b> (' . $idpref . ')</font><br>';
				if ($idpref == 0) {
					$sx .= date("Y-m-d H:i:s") . ' <font color="red">Prefterm <b>' . $pref . '<b> not found</font><br>';
					$class = 'Subject';
					$idpref = $this -> frbr_core -> rdf_concept_create($class, $pref, $orign, $pref_lang);
					$sx .= date("Y-m-d H:i:s") . ' <font color="green">Make Concept "';
					$sx .= $pref . '" <sup>' . $pref_lang . '</sup><br>';
					$sx .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$sx .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					$sx .= 'Concept ' . $idpref . ' (' . $orign . ')</font><br>';
				} else {

				}

				/* ASSOCIA ORIGEN */
				if (strlen($orign) > 0) {
					$prop = 'primaryTopic';
					$lit = $this -> frbr_core -> frbr_name($orign, 'en');
					$this -> frbr_core -> set_propriety($idpref, $prop, 0, $lit);
					$sx .= date("Y-m-d H:i:s") . ' <font color="green">Orign <b>' . $orign . '<b> (' . $idpref . ')</font><br>';
				}

				/* CRIA AS EQUIVALENCIAS */
				if ($idpref != $id) {
					echo '<h2>' . $idpref . '==' . $id . '</h2>';
					$prop = 'equivalentClass';
					$sx .= date("Y-m-d H:i:s") . ' <font color="green">Create ' . msg($prop) . ' <b>' . $idpref . '<=>' . $id . '</font><br>';
					$this -> frbr_core -> equivalentClass($id, $idpref);
					
					/* Transfere remissivas para o termo equivalente */
					$this -> frbr_core -> transfRemissive($id, $idpref);
					
					
				} else {

				}

				/******** Remissivas *******/
				if ($idpref == $id) {
					$hide = $xml -> hiddenLabel;
					$hide_lang = (string)$xml -> prefLabel -> lang;
					$hd = $xml -> hiddenLabel;
					for ($r = 0; $r < count($hd); $r++) {
						$term = (string)$hd[$r] -> term;
						$lang = $this -> frbr_core -> language((string)$hd[$r] -> lang);
						$sx .= date("Y-m-d H:i:s") . ' <font color="green">Make Hidden Term "' . $term . '" <sup>' . $lang . '</sup></font><br>';
						$lit = $this -> frbr_core -> frbr_name($term, $lang);
						$prop = 'altLabel';
						$this -> frbr_core -> set_propriety($id, $prop, 0, $lit);
					}
					$hd = $xml -> isSingular;
					for ($r = 0; $r < count($hd); $r++) {
						$term = (string)$hd[$r] -> term;
						$lang = $this -> frbr_core -> language((string)$hd[$r] -> lang);
						$sx .= date("Y-m-d H:i:s") . ' <font color="green">Make Singular Term "' . $term . '" <sup>' . $lang . '</sup></font><br>';
						$lit = $this -> frbr_core -> frbr_name($term, $lang);
						$prop = 'altLabel';
						$this -> frbr_core -> set_propriety($id, $prop, 0, $lit);
					}
					$hd = $xml -> is_synonymous;
					for ($r = 0; $r < count($hd); $r++) {
						$term = (string)$hd[$r] -> term;
						$lang = $this -> frbr_core -> language((string)$hd[$r] -> lang);
						$sx .= date("Y-m-d H:i:s") . ' <font color="green">Make Singular Term "' . $term . '" <sup>' . $lang . '</sup></font><br>';
						$lit = $this -> frbr_core -> frbr_name($term, $lang);
						$prop = 'altLabel';
						$this -> frbr_core -> set_propriety($id, $prop, 0, $lit);
					}
				}

				echo '<tt>' . $sx . '</tt>';
				echo '<pre>';
				print_r($xml);
				echo '</pre>';
			}

		} else {
			Echo "<br>ERRO: " . $erro;
		}

	}

	function update_thesa($d) {
		if (!perfil("#ADM")) {
			return ("");
		}
		$term = $this -> frbr_core -> prefTerm($d);
		$id = $d[0]['d_r1'];
		$sx = '';
		$sx .= '<div class="col-md-12">';
		$sx .= '<input type="button" class="btn btn-scondary" id="thesa"';
		$sx .= ' value="' . msg('check Thesa') . '">';
		$sx .= '<hr>';
		$sx .= '<div style="width: 100%; height; 100px; display: none;" id="thesa_query">';
		$sx .= 'Consultando...';
		$sx .= '</div>';
		$sx .= '</div>';

		$sx .= '<script>' . cr();
		$sx .= ' $("#thesa").click(function() {' . cr();
		$sx .= '   $("#thesa_query").show(1000);' . cr();
		$sx .= '   $term = "' . $term . '";
                    $id = "' . $id . '";
                   $.ajax({
                      url: "' . base_url(PATH . 'ajax/thesa/') . '",
                      data: { "id": $id, "dd1": $term },
                      context: document.body
                    }).done(function(html) {
                      $("#thesa_query").html(html);
                    });';
		$sx .= ' }); ' . cr();
		$sx .= '</script>' . cr();

		return ($sx);
	}

}
