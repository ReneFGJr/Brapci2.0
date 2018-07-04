<?php
class thesa_api extends CI_model {
	function ajax($id = '') {
		$name = trim(get("dd1"));
        $name = troca($name,'"','');
        $name = troca($name,"'",'');
        
		$id = trim(get("id"));

		echo msg('find') . ' <b>' . $name . '</b>';

		$data = array("term" => $name, );

		$curl = curl_init();
		$url = 'https://www.ufrgs.br/tesauros/index.php/thesa/api/64';
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($curl);
        
        $result = load_file_local($url.'?term='.$name);
		echo '<br>'.$url;

		if (strlen($result) == 0) {
			echo '<br><span style="color: red">Empty Result</span> =>' . count($result);
			return ("");
		}
		$xml = simplexml_load_string($result);
		$erro = (string)$xml -> error;

		if (strlen($erro) == 0) {
			$pref = (string)$xml -> prefLabel -> term;
			$pref_lang = $this -> frbr_core -> language((string)$xml -> prefLabel -> lang);
			echo '<hr>' . $pref . ' <sup>(' . $pref_lang . ')</sup>';
			if (strlen($pref) > 0) {
			    /* Checa se já não existe Termo Preferencial */
			    $idpref = $this->frbr_core->find($pref);
                
                if ($idpref != $id)
                    {
                        echo '<h2>'.$idpref.'=='.$id.'</h2>';
                        $prop = 'equivalentClass';
                        $this -> frbr_core -> equivalentClass($id, $idpref);
                    } else {
                        
                    }
                
				$idt = $this -> frbr_core -> frbr_name($pref, $pref_lang);
				//$rsp = $this -> frbr_core -> prefTerm_chage($id, $idt);
				$rsp = 0;
				if ($rsp == 1) {
					echo " - Atualizado (prefTerm)";
				} else {
					echo " - Não atualizado (prefTerm)";
				}
				/******** Remissivas *******/

				$hide = $xml -> hiddenLabel;
				$hide_lang = (string)$xml -> prefLabel -> lang;
				//print_r($hide);
				for ($r = 0; $r < count($xml -> hiddenLabel); $r++) {
					$term = (string)$hide[$r] -> term;
					$lang = $this -> frbr_core -> language((string)$hide[$r] -> lang);
					echo '==>' . $term . '--' . $lang . '<br>';
					$lit = $this -> frbr_core -> frbr_name($term, $lang);
					$prop = 'altLabel';
					$this -> frbr_core -> set_propriety($id, $prop, 0, $lit);
				}

				echo '<h2>' . $idt . '-' . $id . '</h2>';
				echo '<pre>';
				print_r($xml);
				echo '</pre>';
			}

		} else {
			Echo "<br>ERRO: " . $erro;
		}

	}

	function update_thesa($d) {
		$term = $this -> frbr_core -> prefTerm($d);
		$id = $d[0]['d_r1'];
		$sx = '';
		$sx .= '<div class="col-md-12">';
		$sx .= '<input type="button" class="btn btn-scondary" id="thesa"';
		$sx .= ' value="' . msg('check Thesa') . '">';
		$sx .= '<hr>';
		$sx .= '<div style="width: 400px; height; 100px; display: none;" id="thesa_query">';
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