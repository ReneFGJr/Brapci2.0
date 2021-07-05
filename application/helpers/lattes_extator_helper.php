<?php
/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	WebService
 * @category	Campus Solution
 * @author		Rene F. Gabriel Junior <renefgj@gmail.com>
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ws_cnpq extends CI_model {
	var $producao = 'http://servicosweb.cnpq.br/srvcurriculo/WSCurriculo?wsdl';
	var $homologacao = 'http://servicosweb.cnpq.br/srvcurriculo/WSCurriculo?wsdl';
	var $desenvolvimento = 'http://servicosweb.cnpq.br/srvcurriculo/WSCurriculo?wsdl';

	function getDataAtualizacaoCV($lattes) {
		/* create the client for my rpc/encoded web service */
		require ("_server_type.php");
		switch ($server_type) {
			case '1' :
				$wsdl = $this -> producao;
				break;
			case '2' :
				$wsdl = $this -> homologacao;
				break;
			case '3' :
				$wsdl = $this -> desenvolvimento;
				break;
		}
		$endpoint = "http://servicosweb.cnpq.br/srvcurriculo/WSCurriculo";
		$client = new soapclient($wsdl, true);
		$client -> setEndpoint($endpoint);

		$param = array('id' => $lattes);
		//print_r($client);
		$response = $client -> call('getDataAtualizacaoCV', $param);

		if (strlen($response) > 0) {
			$data = $response;
			$data = sonumero($data);
			$data = substr($data, 4, 4) . '-' . substr($data, 2, 2) . '-' . substr($data, 0, 2);
			return ($data);
		}
		return ('0000-00-00');
	}

	function getCurriculoCompactado($id = '',$debug=0) {
		$debug = 0;
		if (strlen($id) > 0) {

			/* create the client for my rpc/encoded web service */
			require ("_server_type.php");
			switch ($server_type) {
				case '1' :
					$wsdl = $this -> producao;
					break;
				case '2' :
					$wsdl = $this -> homologacao;
					break;
				case '3' :
					$wsdl = $this -> desenvolvimento;
					break;
			}
			$endpoint = "http://servicosweb.cnpq.br/srvcurriculo/WSCurriculo";
			$client = new soapclient($wsdl, true);
			$client -> setEndpoint($endpoint);
			if ($debug==1) { echo date("d/m/Y H:i:s").' - Call WebService<br>'; }
			$param = array('id' => $id);
			//print_r($client);
			if ($debug==1) { echo date("d/m/Y H:i:s").' - Get Service<br>'; }
			$response = $client -> call('getCurriculoCompactado', $param);

			//if ($debug==1) { echo date("d/m/Y H:i:s"); print_r($response); }		
			if (strlen($response) > 0) {
				$response = base64_decode($response);
				$filename = '_document/lattes/xml-lattes-' . $id . '.zip';
				$file = fopen($filename, 'w+');
				fwrite($file, $response);
				fclose($file);

				$zip = new ZipArchive;
				if ($zip -> open($filename) === TRUE) {
					$zip -> extractTo('_document/lattes/');
					$zip -> close();
					$sx = '1';
					unlink($filename);
				} else {
					$sx = '0';
				}

				$sx .= '<a href="' . base_url($filename) . '">download</a>';
				return ($sx);
			}
			return ('0');

		}

	}

	function xml_artigos($filename, $cpf, $nome) {
		$params = array('path' => $filename);
		$this -> load -> library('ci_easyxml', $params);

		$nome = UpperCase($nome);

		if (strlen($cpf) == 0) {
			return ('');
		}
		$sql = "delete from cnpq_lattes_artigos_publicados where a_cpf = '$cpf' ";
		//$rlt = $this -> db -> query($sql);

		$sql = "delete from cnpq_acpp where acpp_autor = '$nome' ";
		$rlt = $this -> db -> query($sql);

		$rs = $this -> ci_easyxml;

		if ($childs = $this -> ci_easyxml -> child_exists('/CURRICULO-VITAE/PRODUCAO-BIBLIOGRAFICA/ARTIGOS-PUBLICADOS/ARTIGO-PUBLICADO')) {
			/* recupera os registros */
			foreach ($childs as $key => $artigos) {
				$dd = array();
				$autores = array();
				$keyword = array();
				$autor = 0;
				$idA = 0;

				/********************************************************** SEQUENCIA **/
				$atributos = $artigos -> attributes();
				foreach ($atributos as $k => $v) {
					$v = (array)$v;
					if ($k == 'SEQUENCIA-PRODUCAO') { $idA = $v[0];
						$dd['id'] = $v[0];
					}
				}
				//echo '<h1>' . $idA . '</h1>';

				/********************************************************** DADOS BÁSICO DOS ARTIGO **/

				foreach ($artigos as $k => $v) {
					//echo '<br>' . $k . '=[]=' . $v . '';
					/************************************************ dados do artigo ******************/
					if ($k == 'DADOS-BASICOS-DO-ARTIGO') {
						$at = $v -> attributes();
						$av = (array)$at;

						foreach ($at as $key => $value) {
							//echo $key.'--<b>'.$value[0].'</b><br><hr>';
							$value = (array)$value;
							switch ($key) {
								case 'NATUREZA' :
									$dd['natureza'] = $value[0];
									break;
								case 'FLAG-RELEVANCIA' :
									$dd['importancia'] = $value[0];
									break;
								case 'TITULO-DO-ARTIGO' :
									$dd['title'] = $value[0];
									break;
								case 'TITULO-DO-ARTIGO-INGLES' :
									$dd['title_alt'] = $value[0];
									break;
								case 'PAIS-DE-PUBLICACAO' :
									$dd['pais'] = $value[0];
									break;
								case 'IDIOMA' :
									$dd['idioma'] = $value[0];
									break;
								case 'DOI' :
									$dd['doi'] = $value[0];
									break;
								case 'ANO-DO-ARTIGO' :
									$dd['ano'] = $value[0];
									break;
								case 'MEIO-DE-DIVULGACAO' :
									$dd['suporte'] = substr($value[0], 0, 1);
							}
						}
					}

					/**************/
					if ($k == 'DETALHAMENTO-DO-ARTIGO') {
						$at = $v -> attributes();
						$av = (array)$at;
						foreach ($at as $key => $value) {
							$value = (array)$value;
							//echo $key . '--<b>' . $value[0] . '</b><br><hr>';
							switch ($key) {
								case 'TITULO-DO-PERIODICO-OU-REVISTA' :
									$dd['journal'] = $value[0];
									break;
								case 'ISSN' :
									$dd['issn'] = $value[0];
									break;
								case 'VOLUME' :
									$dd['vol'] = $value[0];
									break;
								case 'FASCICULO' :
									$dd['nr'] = $value[0];
									break;
								case 'SERIE' :
									$dd['serie'] = $value[0];
									break;
								case 'PAGINA-INICIAL' :
									$dd['pg_ini'] = $value[0];
									break;
								case 'PAGINA-FINAL' :
									$dd['pg_fim'] = $value[0];
									break;
								case 'LOCAL-DE-PUBLICACAO' :
									$dd['local'] = $value[0];
									break;
							}
						}
					}

					/********************************************************************** DADOS */
					if ($k == 'PALAVRAS-CHAVE') {
						$at = $v -> attributes();
						$av = (array)$at;
						foreach ($at as $key => $value) {
							$value = (array)$value;
							//echo ''.$key . '-y-<b>' . $value[0] . '</b><hr>';
							switch ($key) {
								case 'PALAVRA-CHAVE-1' :
									array_push($keyword, $value[0]);
									break;
								case 'PALAVRA-CHAVE-2' :
									array_push($keyword, $value[0]);
									break;
								case 'PALAVRA-CHAVE-3' :
									array_push($keyword, $value[0]);
									break;
								case 'PALAVRA-CHAVE-4' :
									array_push($keyword, $value[0]);
									break;
								case 'PALAVRA-CHAVE-5' :
									array_push($keyword, $value[0]);
									break;
								case 'PALAVRA-CHAVE-6' :
									array_push($keyword, $value[0]);
									break;
							}
						}
					}
					/********************************************************************** AUTORES */
					if ($k == 'AUTORES') {
						$at = $v -> attributes();
						$av = (array)$at;

						foreach ($at as $key => $value) {
							$value = (array)$value;
							//echo $key . '-y-<b>' . $value[0] . '</b><br><hr>';
							switch ($key) {
								case 'NOME-COMPLETO-DO-AUTOR' :
									$autores[$autor]['autor'] = $value[0];
									break;
								case 'NOME-PARA-CITACAO' :
									$autores[$autor]['autor_abrev'] = $value[0];
									break;
								case 'NRO-ID-CNPQ' :
									$autores[$autor]['autor_nri'] = $value[0];
									break;
							}
						}
						$autor++;
					}
					/********************************************************************** AREA DO CONHECIMENTO */
					if ($k == 'AREAS-DO-CONHECIMENTO') {
						$at = $v -> attributes();
						$av = (array)$at;

						foreach ($at as $key => $value) {
							$value = (array)$value;
							echo $key . '-y-<b>' . $value[0] . '</b><br><hr>';
							switch ($key) {
								case 'xxx' :
									array_push($keyword, $value[0]);
									break;
							}
						}
					}
				}
				/* ISSNL */
				$dd['issnl'] = $dd['issn'];
				if (strlen($dd['issn']) > 0) {
					$sql = "select * from issn_l where il_issn2 = '" . $dd['issn'] . "'";
					$rlt = $this -> db -> query($sql);
					$rlt = $rlt -> result_array();
					if (count($rlt) > 0) {
						$lis = $rlt[0];
						$dd['issnl'] = $lis['il_issn_l2'];
					}
				}

				/* AREAS */
				$dd['area1'] = '';
				$dd['area2'] = '';
				$dd['area3'] = '';

				/* AUTORES */
				$au = '';
				for ($ra = 0; $ra < count($autores); $ra++) {
					if (strlen($au) > 0) { $au .= '; ';
					}
					$au .= troca($autores[$ra]['autor_abrev'], "'", "´");
				}
				if (strlen($au) > 0) { $au .= '. ';
				}
				$dd['autores'] = $au;

				/* */
				foreach ($dd as $key => $value) {
					$dd[$key] = troca($value, "'", "´");
				}
				/* INSERE NA BASE */
				$sql = "insert into cnpq_lattes_artigos_publicados 
							(
								a_seq, a_cpf, a_importancia,
								a_natureza, a_titulo, a_titulo_ingles,
								a_doi, a_pais, a_idioma,
								
								a_suporte, a_ano, a_journal, 
								a_issn, a_issn_l, a_vol,
								a_fasciculo, a_serie, 	a_pagina_inicial,
								
								a_pagina_final, a_local, a_autores,
								a_area_1, a_area_2, a_area_3
							)
							values
							(
								'" . $dd['id'] . "','" . $cpf . "','" . $dd['importancia'] . "',
								'" . $dd['natureza'] . "','" . $dd['title'] . "','" . $dd['title_alt'] . "',
								'" . $dd['doi'] . "','" . $dd['pais'] . "','" . $dd['idioma'] . "',
								
								'" . $dd['suporte'] . "','" . $dd['ano'] . "','" . $dd['journal'] . "',
								'" . $dd['issn'] . "','" . $dd['issnl'] . "','" . $dd['vol'] . "',
								'" . $dd['nr'] . "','" . $dd['serie'] . "','" . $dd['pg_ini'] . "',
								
								'" . $dd['pg_fim'] . "','" . $dd['local'] . "','" . $dd['autores'] . "',
								'" . $dd['area1'] . "','" . $dd['area2'] . "','" . $dd['area3'] . "'	
							)";
				$sql = utf8_decode($sql);
				//$rrx = $this -> db -> query($sql);

				/* base 2 */
				$sql = "insert into cnpq_acpp 
								(
								acpp_issn_link, acpp_autor, acpp_tipo,
								acpp_idioma, acpp_ano, acpp_titulo,
								acpp_ordem, acpp_relevante, acpp_periodico,
								
								acpp_issn, acpp_volume, acpp_fasciculo,
								acpp_pg_ini, acpp_pg_fim, acpp_editora,
								acpp_doi, acpp_jcr, acpp_qualis, 
								
								acpp_circulacao, acpp_qt_autores, acpp_autores
								)
								values
								(
								'" . $dd['issnl'] . "','" . $nome . "','" . $dd['natureza'] . "',
								'" . $dd['idioma'] . "','" . $dd['ano'] . "','" . $dd['title'] . "',
								'" . $dd['id'] . "','" . $dd['importancia'] . "','" . $dd['journal'] . "',
								
								'" . $dd['issn'] . "','" . $dd['vol'] . "','" . $dd['nr'] . "',
								'" . $dd['pg_ini'] . "','" . $dd['pg_fim'] . "','" . $dd['local'] . "',
								'" . $dd['doi'] . "','','',
								
								'','" . count($autores) . "','" . $dd['autores'] . "'
								)";
								//echo $sql.'<br>';
				$sql = utf8_decode($sql);
				$rrx = $this -> db -> query($sql);
			}

		} else {
			echo "<br>The child does not exists.";
		}
	}

}