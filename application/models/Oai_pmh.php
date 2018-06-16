<?php
// This file is part of the Brapci Software.
//
// Copyright 2015, UFPR. All rights reserved. You can redistribute it and/or modify
// Brapci under the terms of the Brapci License as published by UFPR, which
// restricts commercial use of the Software.
//
// Brapci is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details.
//
// You should have received a copy of the Brapci License along with the Brapci
// Software. If not, see
// https://github.com/ReneFGJ/Brapci/tree/master//LICENSE.txt
/* @author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
 * @date: 2015-12-01
 */

class oai_pmh extends CI_model {
	var $issue;
	var $token = '';

	var $erro = 0;
	var $erro_msg = '';

	function menu($id_jnl = 0) {
		$sx = '';
		$sx .= '<a href="' . base_url(PATH . 'oai/info/' . $id_jnl) . '">OAI-PMH</a>';
		$sx .= ' | ';
		$sx .= '<a href="' . base_url(PATH . 'oai/Identify/' . $id_jnl) . '">Identify</a>';
		$sx .= ' | ';
		$sx .= '<a href="' . base_url(PATH . 'oai/ListIdentifiers/' . $id_jnl) . '">ListIdentifiers</a>';
		$sx .= ' | ';
		$sx .= '<a href="' . base_url(PATH . 'oai/GetRecord/' . $id_jnl) . '">GetRecord</a>';

		return ($sx);
	}

	function le_cache($id) {
		$sql = "select * from source_listidentifier
                            WHERE id_li = $id";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$line = $rlt[0];
		} else {
			$line = array();
		}
		return ($line);
	}

	function process($dt) {
		/*********************************** PROCESS **************************************/
		$dt2 = $this -> le_cache($dt['idc']);
		$dt3 = $this -> sources -> le($dt2['li_jnl']);
		$dt = array_merge($dt, $dt2, $dt3);

		/*********************************** ISSUE ****************************************/
		if (isset($dt['issue'])) {
			$ida = $this -> frbr -> issue($dt, 'Issue');
		} else {
			echo "ERRO DE ISSUE";
			exit ;
		}
		$dt['issue_uri'] = $ida;
		/*********************************** ARTICLE **************************************/
		$article_id = $this -> frbr -> article_create($dt);

		/*********************************** AUTHORS **************************************/
		if (isset($dt['authors'])) {
			$d = $dt['authors'];
			for ($r = 0; $r < count($d); $r++) {
				$type = $d[$r]['type'];
				if ($type == 'author') {
					$author = $this -> frbr -> frad($d[$r], 'Person');
					$this -> frbr_core -> set_propriety($article_id, 'hasAuthor', $author, 0);
				}
			}
		}
		$link = '<a href="' . base_url(PATH . 'v/' . $article_id) . '" target="_new' . $article_id . '">';
		$this -> cache_alter_status($dt['idc'], 3);
		return ("<h1>Index Article: " . $link . $article_id . '</a></h1>');
	}

	function author($dt) {
		$id = $this -> frbr -> frad($dt);
	}

	function cache_alter_status($id_jnl, $status) {
		$sql = "update source_listidentifier
                        set li_s = $status
                        WHERE id_li = $id_jnl ";
		$rlt = $this -> db -> query($sql);
		return (1);
	}

	function getRecord($id_jnl = 0) {
		$sql = "select * from source_listidentifier 
                    where li_jnl = '$id_jnl' and li_status = 'active' and li_s = 1
                    order by li_s, li_u, id_li
                    limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();

		if (count($rlt) == 0) {
			return (0);
		}
		$line = $rlt[0];

		$id = $line['id_li'];
		$this -> cache_alter_status($id, 2);
		return ($id);
	}

	function getRecordNlM($id = 0, $dt) {
		$this -> load -> model("sources");

		$sql = "select * from source_listidentifier where id_li = $id";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$line = $rlt[0];
			$jnl = $line['li_jnl'];

			$data = $this -> sources -> le($jnl);
			$url = $this -> oai_url($data, 'GetRecordNlm') . $line['li_identifier'];
			$cnt = $this -> readfile($url);
			$cnt = troca($cnt, 'abstract-', 'abstract_');
			$cnt = troca($cnt, 'article-', 'article_');
			$cnt = troca($cnt, 'contrib-', 'contrib_');
			$cnt = troca($cnt, 'given-', 'given_');
			$cnt = troca($cnt, 'title-', 'title_');
			$cnt = troca($cnt, 'trans-', 'trans_');
			$cnt = troca($cnt, 'issue-', 'issue_');
			$cnt = troca($cnt, 'self-', 'self_');
			$cnt = troca($cnt, 'subj-', 'subj_');
			$cnt = troca($cnt, 'pub-', 'pub_');
			$cnt = troca($cnt, 'xlink:', '');
			$cnt = troca($cnt, 'xml:', '');

			$xml = simplexml_load_string($cnt);
			/******************************************************* AUTHOR ********************/
			$is = $xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> contrib_group -> contrib;
			$authors = array();
			for ($r = 0; $r < count($is); $r++) {
				$at = $is[$r] -> attributes();
				$n1 = UpperCase((string)$is[$r] -> name -> surname);
				$n2 = (string)$is[$r] -> name -> given_names;
				$aff = (string)$is[$r] -> aff;
				$email = (string)$is[$r] -> email;
				$func = (string)$at['contrib_type'];
				$nm = trim($n1) . ', ' . trim($n2);
				$tit = $nm;
				$autho = array('name' => $tit, 'email' => $email, 'aff' => $aff, 'type' => $func);
				array_push($authors, $autho);
			}
			$dt['authors'] = $authors;
			/******************************************************* TITLE **********************/
			$is = $xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> title_group -> trans_title;
			$title = array();
			for ($r = 0; $r < count($is); $r++) {
				$at = $is[$r] -> attributes();
				$title = (string)$is[$r];
				$lang = $at['lang'];
				//array_push($title, $tit);
			}
			/******************************************************* ABSTRACT *******************/
			$is = $xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> abstract_trans;
			$title = array();
			for ($r = 0; $r < count($is); $r++) {
				$at = $is[$r] -> attributes();
				$title = (string)$is[$r] -> p;
				$lang = $at['lang'];
				//array_push($title, $tit);
			}
			/******************************************************* DATA ***********************/
			$is = $xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> pub_date;
			$day = (string)$is -> day;
			$month = (string)$is -> month;
			$year = (string)$is -> year;

			$vol = (string)$xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> volume;
			$issue = (string)$xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> issue;

			$issue_id = (string)$xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> issue_id;
			$section = (string)$xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> article_categories -> subj_group -> subject;
			$iss = array();
			$iss['year'] = $year;
			$iss['month'] = $month;
			$iss['day'] = $day;
			$iss['section'] = $section;
			$iss['issue_id'] = $issue_id;
			$iss['vol'] = $vol;
			$iss['nr'] = $issue;
			$dt['issue'] = $iss;
			/******************************************************* URI ************************/
			$is = $xml -> GetRecord -> record -> metadata -> article -> front -> article_meta -> self_uri;
			$uri = array();
			for ($r = 0; $r < count($is); $r++) {
				$at = $is[$r] -> attributes();
				$title = (string)$at['href'];
				$lang = (string)$at['content-type'];
				array_push($uri, array('href' => $title, 'type' => $lang));
			}
			$dt['uri'] = $uri;
		}
		return ($dt);
	}

	function getRecord_oai_dc($id = 0, $dt) {
		$this -> load -> model("sources");
		$sql = "select * from source_listidentifier where id_li = $id";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$line = $rlt[0];
			$jnl = $line['li_jnl'];

			$data = $this -> sources -> le($jnl);
			$url = $this -> oai_url($data, 'GetRecord') . $line['li_identifier'];
			$cnt = $this -> readfile($url);
			$cnt = troca($cnt, 'oai_dc:', 'oai_');
			$cnt = troca($cnt, 'dc:', '');
			$cnt = troca($cnt, 'xml:', '');
			$xml = simplexml_load_string($cnt);

			$rcn = $xml -> GetRecord -> record -> metadata -> oai_dc;

			/******************************************************* TITLE **********************/
			$is = $this -> xml_values($rcn -> title);
			$title = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> title[$r];
				$lang = '';
				foreach ($rcn -> title[$r] -> attributes() as $atrib => $value) {
					if ($atrib == 'lang') { $lang = (string)$value;
					}
				}
				array_push($title, array('title' => $tit, 'lang' => $lang));
			}
			$dt['title'] = $title;
			/******************************************************* CREATOR *******************/
			$is = $this -> xml_values($rcn -> creator);
			$author = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> creator[$r];
				array_push($author, $tit);
			}

			/******************************************************* SUBJECT *******************/
			$is = $this -> xml_values($rcn -> subject);
			$key = '';
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> subject[$r];
				$tit = troca($tit, '.', ';');
				$tit = troca($tit, ',', ';');
				$tit = splitx(';', $tit);
				$lang = '';
				foreach ($rcn -> subject[$r] -> attributes() as $atrib => $value) {
					if ($atrib == 'lang') { $lang = $value;
					}
				}
				for ($z = 0; $z < count($tit); $z++) {
					$key .= $tit[$z] . '@' . $lang . ';';
				}
			}
			$subject = splitx(';', $key);
			/*************************************************** description ********************/
			$is = $this -> xml_values($rcn -> description);
			$abstract = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> description[$r];
				$lang = '';
				foreach ($rcn -> description[$r] -> attributes() as $atrib => $value) {
					if ($atrib == 'lang') { $lang = (string)$value;
					}
				}
				array_push($abstract, array('descript' => $tit, 'lang' => $lang));
			}
			$dt['abstract'] = $abstract;
			/*************************************************** source ************************/
			$is = $this -> xml_values($rcn -> source);
			$source = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> source[$r];
				$lang = '';
				foreach ($rcn -> source[$r] -> attributes() as $atrib => $value) {
					if ($atrib == 'lang') { $lang = '@' . $value;
					}
				}
				array_push($source, array('name' => $tit, 'lang' => $lang));
			}
			/*************************************************** relation **********************/
			$is = $this -> xml_values($rcn -> relation);
			$relation = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> relation[$r];
				array_push($relation, $tit);
			}
			/*************************************************** relation **********************/
			$is = $this -> xml_values($rcn -> date);
			$date = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> date[$r];
				array_push($date, $tit);
			}
			/*************************************************** relation **********************/
			$is = $this -> xml_values($rcn -> identifier);
			$identifier = array();
			for ($r = 0; $r < count($is); $r++) {
				$tit = (string)$rcn -> identifier[$r];
				array_push($identifier, $tit);
			}
			$dt['subject'] = $subject;
			$dt['identifier'] = $identifier;
			$dt['source'] = $source;
			$dt['relation'] = $relation;
			$dt['date'] = $date;
		}
		return ($dt);
	}

	public function identify($id) {
		$data = $this -> sources -> le($id);
		$url = $this -> oai_url($data, 'identify');
		$cnt = $this -> readfile($url);
		$xml = simplexml_load_string($cnt);

		$dt = array();
		$dt['id'] = $id;
		$dt['repositoryName'] = $this -> xml_value($xml -> Identify -> repositoryName);
		$dt['protocolVersion'] = $this -> xml_value($xml -> Identify -> protocolVersion);
		$dt['adminEmail'] = $this -> xml_value($xml -> Identify -> adminEmail);
		$dt['deletedRecord'] = $this -> xml_value($xml -> Identify -> deletedRecord);
		$dt['granularity'] = $this -> xml_value($xml -> Identify -> granularity);
		$dt['baseURL'] = $this -> xml_value($xml -> Identify -> baseURL);
		$dt['responseDate'] = $this -> xml_value($xml -> responseDate);
		$dt = array_merge($data, $dt);
		$this -> frbr -> journal($dt);
	}

	public function cache_link($line = array()) {
		$sx = '';
		$link = '<a href="' . base_url(PATH . 'oai/harvesting/' . $line['li_s']) . '">';
		$sx .= $link . msg('cache_status_' . $line['li_s']) . '</a>';
		return ($sx);
	}

	public function cache_resume($id) {
		/* Alter status - Deleted registers */
		$sql = "update source_listidentifier set li_s = 9 where li_status = 'deleted' and li_s <> 9";
		$rlt = $this -> db -> query($sql);

		/* Counter Registers */
		$sql = "select count(*) as total, li_s 
                        FROM source_listidentifier
                        WHERE li_jnl = $id 
                        GROUP BY li_s
                        ORDER BY li_s ";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '<h5>' . msg("cache_status") . '</h5>';
		$sx .= '<ul>';
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$sx .= '<li>' . $this -> cache_link($line) . ': <span>' . $line['total'] . '</span>' . '</li>' . CR;
		}
		$sx .= '</ul>';
		return ($sx);
	}

	public function ListIdentifiers($id) {
		$data = $this -> sources -> le($id);
		$url = $this -> oai_url($data, 'ListIdentifiers');

		$cnt = $this -> readfile($url);
		$xml = simplexml_load_string($cnt);

		$LI = $this -> xml_values_array($xml -> ListIdentifiers -> header);
		$token = $this -> xml_value($xml -> ListIdentifiers -> resumptionToken);
		$response = $this -> xml_value($xml -> responseDate);

		$this -> update_token($id, $token);
		$sx = '<br><b>Token: ' . $token . '</b>';
		$sx .= '<br><b>Response: ' . $response . '</b>';
		$sx .= '<ul>';
		for ($r = 0; $r < count($LI); $r++) {
			$line = $LI[$r];
			$sx .= '<li>' . $this -> cache($data['id_jnl'], $line) . '</li>';
		}
		$sx .= '</ul>';
		if (strlen($token) > 0) {
			$sx .= $this -> ListIdentifiers($id);
		}
		return ($sx);

	}

	private function update_token($id_jnl, $token) {
		$sql = "update source_source set jnl_oai_token = '$token' where id_jnl = $id_jnl";
		$rlt = $this -> db -> query($sql);
	}

	private function cache($id_jnl, $data) {
		$identifier = $data['identifier'];
		$sql = "select * from source_listidentifier 
                            where li_identifier = '$identifier'
                                AND li_jnl = $id_jnl ";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '';
		if (count($rlt) > 0) {
			$line = $rlt[0];
			$sqlu = "li_status = '" . $data['status'] . "'";
			$sqlu .= ", li_datestamp = '" . $data['datestamp'] . "'";
			$sqlu .= ", li_setSpec = '" . $data['setSpec'] . "'";
			$up = 0;
			if (($data['status'] != $line['li_status']) or ($data['setSpec'] != $line['li_setSpec'])) {
				$sqlu .= ", li_update = 1";
				$up = 1;
			}
			$sql = "update source_listidentifier set " . $sqlu . " where id_li = " . $line['id_li'];
			if ($up == 1) { $this -> db -> query($sql);
			}
			$sx .= $identifier . ' ' . '<span class="alert-warning">harvested</span>';
		} else {
			$fld1 = 'li_jnl';
			$fld2 = $id_jnl;
			foreach ($data as $key => $value) {
				$fld1 .= ', li_' . $key;
				if ($key == 'datestamp') {
					$value = troca($value, 'Z', '');
					$value = troca($value, 'T', ' ');
				}
				$fld2 .= ", '" . $value . "'";
			}
			$sql = "insert into source_listidentifier
                                ($fld1)
                                values
                                ($fld2)";
			$this -> db -> query($sql);
			$sx .= $identifier . ' ' . '<span class="alert-success">inserted</span>';
		}
		return ($sx);
	}

	public function xml_values_array($x) {
		$v = array();
		for ($r = 0; $r < count($x); $r++) {
			$xx = $x[$r];
			$rg = array();
			$rg['status'] = 'active';
			/******************* atributes *************/
			foreach ($xx->attributes() as $a => $b) {
				$rg[$a] = (string)$b;
			}

			/******************* values ****************/
			foreach ($xx as $key => $value) {
				$rg[$key] = (string)$value;
			}
			if (count($rg) > 0) {
				array_push($v, $rg);
			}
		}
		return ($v);
	}

	public function xml_values($x) {
		$v = array();
		foreach ($x as $key => $value) {
			array_push($v, (string)$value);
		}
		return ($v);
	}

	public function xml_value($x) {
		foreach ($x as $key => $value) {
			return ((string)$value);
		}
	}

	public function readfile($url) {
		if (substr($url, 0, 5) == 'https') {
			$data = load_page($url);
			$data = $data['content'];
			return($data);
		}
		try {
			$cnt = file_get_contents($url);
		} catch(Exception $e) {
			$this -> erro = -1;
			$this -> erro_msg = $e -> getMessage();
			$cnt = '';
		}

		return ($cnt);
	}

	public function oai_url($data, $verb) {
		$url = trim($data['jnl_url_oai']);
		switch($verb) {
			case 'GetRecord' :
				$url .= '?verb=GetRecord&metadataPrefix=oai_dc&identifier=';
				break;
			case 'GetRecordNlm' :
				$url .= '?verb=GetRecord&metadataPrefix=nlm&identifier=';
				break;
			case 'ListIdentifiers' :
				if (strlen($data['jnl_oai_token']) > 5) {
					$url .= '?verb=ListIdentifiers&resumptionToken=' . trim($data['jnl_oai_token']);
				} else {
					$url .= '?verb=ListIdentifiers&metadataPrefix=oai_dc';
				}

				break;
			case 'identify' :
				$url .= '?verb=Identify';
				break;
		}
		return ($url);
	}

	function rescan_xml($id, $art) {
		$art = strzero($art, 10);
		$idx = strzero($id, 7);
		$file = 'ma/oai/' . $idx . '.xml';
		$txt = load_file_local($file);
		$sx = '<dc:identifier>';
		$txt = substr($txt, strpos($txt, $sx) + strlen($sx), strlen($txt));
		$txt = trim(substr($txt, 0, strpos($txt, '<')));

		if (substr($txt, 0, 4) == 'http') {
			$data['content'] = '==>' . $txt . '<==';
			$sql = "select * from brapci_article_suporte where bs_adress = '$txt' and bs_article = '$idx' ";
			$rlt = $this -> db -> query($sql);
			$rlt = $rlt -> result_array();
			if (count($rlt) == 0) {
				$this -> load -> view('content', $data);
				$sql = "insert brapci_article_suporte 
						(
						bs_status, bs_article, bs_type, 
						bs_adress, bs_journal_id, bs_update
						) values (
						'@','$art','URL',
						'$txt',''," . date("Ymd") . ')';
				$this -> db -> query($sql);
			} else {
			}
		} else {
		}
		redirect(base_url('index.php/article/view/' . $art));
	}

	public function repository_list() {
		$sql = "select * from source_source 
						where jnl_active <> 'X'
						AND jnl_url_oai <> ''	
						order by jnl_name
						";
		$rlt = db_query($sql);
		$sx = '';
		while ($line = db_read($rlt)) {
			$last = $line['update_at'];
			$url = $line['jnl_url'];
			$link = '<A HREF="' . trim($line['jnl_url']) . '" target="_new">';
			$link_oai = base_url(PATH . 'oai/Identify/' . $line['id_jnl']);

			$sx .= '<div class="col-md-6">' . CR;
			$sx .= '<a href="' . $link_oai . '" class="link">';
			$sx .= $line['jnl_name'];
			$sx .= '</a>';
			$sx .= '</div>';

			$sx .= '<div class="col-md-4">' . CR;
			$sx .= $line['jnl_oai_token'];
			$sx .= '</div>' . CR;

			$sx .= '<div class="col-md-2">' . CR;
			$sx .= stodbr($line['update_at']);
			$sx .= '</div>' . CR;
		}
		return ($sx);
	}

	function oai_listset($ida, $setSepc, $date) {
		$ida = trim($ida);
		$jid = $this -> jid;
		$njid = strzero($jid, 7);

		$sql = "select * from oai_cache where cache_oai_id = '$ida' ";
		$rlt = $this -> db -> query($sql);
		$line = $rlt -> result_array();

		if (count($line) > 0) {
			return ('<span class="label label-warning">Already!</span>');
			/* já existe */
		} else {
			$data = date("Ymd");
			$sql = "update brapci_journal set jnl_last_harvesting = '$data', jnl_update = '$data' where id_jnl = $jid ";
			$rlt = $this -> db -> query($sql);

			/* Insere na agenda */
			$sql = "insert into oai_cache (
					cache_oai_id, cache_status, cache_journal, 
					cache_prioridade, cache_datastamp, cache_setSpec, 
					cache_tentativas
					) values (
					'$ida','@','$njid',
					'1','$date','$setSepc',
					0
					)";
			$this -> db -> query($sql);
			return ('<span class="label label-success">Insert!</span>');
		}
	}

	function ListIdentifiers_Method_1($url) {
		$rs = load_page($url);

		$xml_rs = $rs['content'];
		$xml = simplexml_load_string($xml_rs);

		$token = $xml -> ListIdentifiers -> resumptionToken;
		$this -> token = $token;

		$xml = $xml -> ListIdentifiers -> header;
		$sx = '<ul>';
		$status = 'ok';
		for ($r = 0; $r < count($xml); $r++) {
			foreach ($xml[$r]->attributes() as $a => $b) {
				if ($a == 'status') {
					//$status = $b;
				}
			}
			$ida = $xml[$r] -> identifier;
			$date = $xml[$r] -> datestamp;
			$setSpec = $xml[$r] -> setSpec;

			if ($status == 'deleted') {
				$rt = '<span class="label label-important">deleted</span>';
				$sx .= '<li>' . $ida . ' - ' . $status . '</li>';

			} else {
				$rt = $this -> oai_listset($ida, $setSpec, $date);
				$sx .= '<li>' . $ida . ' - ' . $rt . '</li>';
			}
		}
		$sx .= '</ul>';

		return ($sx);

	}

	/** Altera Status **/
	function altera_status_chache($id, $sta) {
		$sql = "update oai_cache set cache_status = '$sta' where id_cache = $id ";
		$this -> db -> query($sql);
		return (1);
	}

	/* SetSepc */
	function save_setspec($set, $tema, $jid) {
		$jid = strzero($jid, 7);
		$sql = "select * from oai_listsets where ls_setspec = '$set' and ls_journal = '$jid' ";
		$rlt = db_query($sql);
		if ($line = db_read($rlt)) {
			$sql = "update oai_listsets set ls_equal = '$tema' where id_ls = " . round($line['id_ls']);
			$this -> db -> query($sql);
			return ('');
		} else {
			$data = date("Ymd");
			$sql = "insert into oai_listsets (
							ls_setspec, ls_setname, ls_setdescription,
							ls_journal, ls_status, ls_data,
							ls_equal, ls_tipo, ls_equal_ed
							) values (
							'$set','$set','',
							'$jid','A','$data',
							'$tema','S','')";
			$rlt = $this -> db -> query($sql);
		}
		return ('');
	}

	/** PROCESS */
	function process_oai($jid = 0) {
		$wh = ' 1 = 1 ';
		if ($jid > 0) { $wh = " cache_journal = '" . strzero($jid, 7) . "' ";
		}
		$sql = "select * from oai_cache 
						where cache_status = 'A'
						and $wh
						order by cache_tentativas
						limit 1
					";
		$rlt = db_query($sql);

		if ($line = db_read($rlt)) {
			$idc = $line['id_cache'];
			$file_id = strzero($line['id_cache'], 7);
			$file_id = 'ma/oai/' . $file_id . '.xml';
			if (file_exists($file_id)) {
				$xml = load_file_local($file_id);
				/* Le XML */
				$article = $this -> process_le_xml($xml, $file_id);

				/*********************** registro deleted *******************/
				if ($article['status'] == 'deleted') {
					$this -> altera_status_chache($idc, 'X');
					echo '<meta http-equiv="refresh" content="1">';
					return ('');
				}
				/*********************** registro deleted *******************/
				if ($article['status'] == 'reload') {
					$this -> altera_status_chache($idc, '@');
					echo '<meta http-equiv="refresh" content="1">';
					return ('');
				}

				$article['file'] = $file_id;
				/* Processa dados */

				/* Recupera Issue */
				$article['issue_id'] = strzero($this -> recupera_issue($article, $jid), 7);
				$article['issue_ver'] = $this -> issue;

				/* Recupera ano */
				$source = $article['sources'][0]['source'];
				$article['ano'] = $this -> recupera_ano($source);

				/* Recupera Journals ID */
				$article['journal_id'] = strzero($jid, 7);

				/* Titulo principal */
				$titulo = utf8_decode($article['titles'][0]['title']);
				$titulo = utf8_decode(substr($titulo, 0, 44));
				$titulo = UpperCaseSql($titulo);

				/* Valida se existe article cadastrado */
				$sql = "select * from brapci_article where ar_edition = '" . $article['issue_id'] . "' 
						and 
						(ar_titulo_1 like '$titulo%' or ar_titulo_2 like '$titulo%')
						and 
						ar_journal_id = '" . strzero($jid, 7) . "'
				";
				$article['section'] = '';
				$rlt = db_query($sql);
				if ($line = db_read($rlt)) {
					/* Existe */

					$this -> altera_status_chache($idc, 'C');
					$this -> load -> view("oai/oai_process", $article);
				} else {
					if ($article['issue_id'] != '0000000') {
						$article['setSpec'] = troca($article['setSpec'], '+', '_');
						$article['setSpec'] = troca($article['setSpec'], ':', '_');

						/* Bloqueado */
						if ($article['issue_id'] == '9999999') {
							$this -> altera_status_chache($idc, 'F');
							echo '<meta http-equiv="refresh" content="1">';
							return ('');
						} else {
							/* processa e grava dados */
							$ids = $this -> recupera_section($article['setSpec'], $article['journal_id']);
							$article['section'] = $ids;

							if (strlen($ids) == 0) {
								$data = array();
								$data['setspec'] = $article['setSpec'];

								$data['links'] = $article['links'];

								$sql = "select * from brapci_section order by se_descricao ";
								$rlt = $this -> db -> query($sql);
								$rlt = $rlt -> result_array();

								$sx = '<table width="100%" class="tabela01"><tr valign="top"><td>';
								$id = 0;
								$div = round(count($rlt) / 4) + 1;
								for ($r = 0; $r < count($rlt); $r++) {
									$line = $rlt[$r];
									if ($id > $div) { $sx .= '</td><td width="25%">';
										$id = 0;
									}
									$sx .= '<a href="' . base_url('index.php/oai/setspec/' . $jid . '/' . $line['se_codigo'] . '/' . $article['setSpec']) . '">' . $line['se_descricao'] . '</a><br>';
									$id++;
								}
								$sx .= '</table>';
								$data['opcoes'] = $sx;
								$this -> load -> view('oai/oai_setname', $data);
								return (0);
							}

							$this -> load -> model('articles');
							$article['codigo'] = $this -> articles -> insert_new_article($article);

							/* Arquivos */
							for ($r = 0; $r < count($article['links']); $r++) {
								$link = $article['links'][$r]['link'];
								$this -> articles -> insert_suporte($article['codigo'], $link, $article['journal_id']);
							}

							/* Autores */
							$this -> load -> model('authors');
							$authors = '';
							for ($r = 0; $r < count($article['authors']); $r++) {
								$au = $article['authors'][$r]['name'];
								if (strpos($au, ';') > 0) { $au = substr($au, 0, strpos($au, ';'));
								}
								$authors .= trim($au) . chr(13) . chr(10);
							}
							$this -> authors -> save_AUTHORS($article['codigo'], $authors);

							/* Salva Keywords */
							$this -> load -> model('keywords');
							$authors = '';
							$keys = array();
							if (isset($article['keywords'])) {
								for ($r = 0; $r < count($article['keywords']); $r++) {
									$ido = $article['keywords'][$r]['idioma'];
									if ($ido == 'pt_BR') { $ido = 'pt-BR';
									}
									if ($ido == 'en-US') { $ido = 'en';
									}
									$au = $article['keywords'][$r]['term'];
									if (isset($keys[$ido])) {
										$keys[$ido] .= $au . ';';
									} else {
										$keys[$ido] = $au . ';';
									}
								}
							}
							foreach ($keys as $key => $value) {
								$this -> keywords -> save_KEYWORDS($article['codigo'], $value, $key);
							}
							$this -> altera_status_chache($idc, 'B');
							/**************** FIM DO PROCESSAMENTO ***************************************/
						}
					} else {
						$jid = $article['journal_id'];
					}
					//exit;
				}

				$this -> load -> view("oai/oai_process", $article);

			} else {
				$this -> altera_status_chache($idc, '@');
				echo '<meta http-equiv="refresh" content="1">';
				return ('ERROR');
			}
		}
	}

	function recupera_ano($s) {
		//$s = trim(sonumero($s));
		$ano = '';
		for ($r = (date("Y") + 1); $r > 1940; $r--) {
			if (strpos($s, trim($r)) > 0) {
				if (strlen($ano) == 0) {
					return ($r);
				}
			}
		}
		return ($ano);
	}

	/******************************************************************************
	 * RECUPERA NUMERO ************************************************************
	 ******************************************************************************/
	function recupera_nr($s) {
		$nr = '';
		$s = troca($s, 'esp.', '');
		$s = troca($s, 'Esp.', '');
		$s = troca($s, 'esp', '');
		if (strpos($s, 'n.')) { $nr = substr($s, strpos($s, 'n.'), strlen($s));
		}
		if (strpos($s, 'No ')) { $nr = substr($s, strpos($s, 'No ') + 3, strlen($s));
		}
		if (strpos($s, 'No. ')) { $nr = substr($s, strpos($s, 'No. ') + 4, strlen($s));
		}
		if (strlen($nr) > 0) {
			if (strpos($nr, ',') > 0) { $nr = substr($nr, 0, strpos($nr, ','));
			}
			if (strpos($nr, '-') > 0) { $nr = substr($nr, 0, strpos($nr, '-'));
			}
			if (strpos($nr, '(') > 0) { $nr = substr($nr, 0, strpos($nr, '('));
			}
			$nr = troca($nr, 'n. ', '');
			$nr = troca($nr, ' ', 'x');
			if (strpos($nr, 'x') > 0) { $nr = substr($nr, 0, strpos($nr, 'x'));
			}
			$nr = troca($nr, 'x', '');
			$nr = troca($nr, 'n.', '');
			$nr = trim($nr);
		}
		return ($nr);
	}

	/******************************************************************************
	 * RECUPERA VOLUME ************************************************************
	 ******************************************************************************/
	function recupera_vol($s) {
		$vl = '';
		$s = troca($s, 'V.', 'v.');
		if (strpos($s, 'v.')) { $vl = substr($s, strpos($s, 'v.'), strlen($s));
		}
		if (strpos($s, 'Vol ')) { $vl = substr($s, strpos($s, 'Vol ') + 4, strlen($s));
		}
		if (strpos($s, 'Vol. ')) { $vl = substr($s, strpos($s, 'Vol. ') + 5, strlen($s));
		}

		if (strlen($vl) > 0) {
			if (strpos($vl, ',') > 0) { $vl = substr($vl, 0, strpos($vl, ','));
			}
			$vl = troca($vl, 'v. ', '');
			if (strpos($vl, ' ') > 0) { $vl = substr($vl, 0, strpos($vl, ' '));
			}
			$vl = troca($vl, 'v.', '');
			$vl = trim($vl);
		}
		return ($vl);
	}

	function recupera_section($sec, $jid) {
		$sql = "select * from oai_listsets where ls_setspec = '$sec' and ls_journal = '$jid'";
		$rlt = db_query($sql);
		if ($line = db_read($rlt)) {
			$rsec = trim($line['ls_equal']);
		} else {
			$data = array();
			return ('');
			$rsec = '';
		}
		return ($rsec);
	}

	function recupera_issue($rcn, $jid) {
		$issue = $rcn['sources'];
		for ($r = 0; $r < count($issue); $r++) {
			$si = $issue[$r]['source'];
			$ano = $this -> recupera_ano($si);
			$nr = $this -> recupera_nr($si);
			$vol = $this -> recupera_vol($si);
			/* Trata issue */
			$jid = strzero($jid, 7);

			$sql = "select * from brapci_edition where 
									ed_vol = '$vol'
									and ed_nr = '$nr'
									and ed_ano = '$ano' 
									and ed_journal_id = '$jid' ";
			$rlt = db_query($sql);
			$sx = "v. $vol, n. $nr, $ano";
			$this -> issue = $sx;

			if ($line = db_read($rlt)) {
				$eds = $line['ed_status'];
				if ($eds == 'A') {
					return ($line['id_ed']);
				} else {
					return ('9999999');
				}
			} else {
				return (0);
			}
		}

	}

	function process_le_xml($xml_rs, $file) {
		$dom = new DOMDocument;
		$dom = new DOMDocument;

		/* Arquivo vazio */
		$fr = fopen($file, 'r');
		$st = fread($fr, 512);
		fclose($fr);

		if (strlen($st) == 0) {
			$doc['status'] = 'reload';
			echo '<meta http-equiv="refresh" content="1">';
			return ($doc);
		}
		$dom -> load($file);

		/* Array */
		$doc = array();

		/* Header */
		$headers = $dom -> getElementsByTagName('header');
		$status = '';
		foreach ($headers as $header) {
			//$setSpec = $header -> nodeValue;
			if (isset($header -> attributes -> getNamedItem('status') -> value)) {
				$status = $header -> attributes -> getNamedItem('status') -> value;
			}
		}

		/* Registro deletado, nao processar */
		if ($status == 'deleted') {
			//echo '<br>'.$status;
			$doc['status'] = 'deleted';
			return ($doc);
		} else {
			$doc['status'] = 'active';
		}

		/* setSpec */
		$headers = $dom -> getElementsByTagName('setSpec');
		$size = ($headers -> length);
		/* Header inválido */
		if ($size < 1) {
			$doc['status'] = 'deleted';
			return ($doc);
			exit ;
		}

		foreach ($headers as $header) {
			$setSpec = $header -> nodeValue;
		}
		$setSpec = troca($setSpec, ':', '_');
		$setSpec = troca($setSpec, ' ', '_');
		$setSpec = troca($setSpec, '+', '_');
		$doc['setSpec'] = $setSpec;

		/* setSpec */
		$idf = '';
		$headers = $dom -> getElementsByTagName('identifier');
		foreach ($headers as $header) {
			if (strlen($idf) == 0) {
				$idf = $header -> nodeValue;
			}
		}
		$doc['idf'] = $idf;

		$nodes = $dom -> getElementsByTagName('metadata');

		/* Recupeda dados */
		foreach ($nodes as $node) {

			/* Recupera titulos */
			$titles = $node -> getElementsByTagName("title");
			$id = 0;
			foreach ($titles as $title) {
				$value = $title -> nodeValue;
				$value = troca($value, "'", "´");
				$lang = $title -> attributes -> getNamedItem('lang') -> value;
				if ($lang == 'pt-BR') { $lang = 'pt-BR';
				}
				if ($lang == 'en-US') { $lang = 'en';
				}

				$dt = array();
				$dt['title'] = $value;
				$dt['idioma'] = $lang;
				$doc['titles'][$id] = $dt;
				$id++;
			}
			/* Recupera autores */
			$titles = $node -> getElementsByTagName("creator");
			$id = 0;
			foreach ($titles as $title) {
				$value = troca($title -> nodeValue, "'", '´');
				$dt = array();
				$dt['name'] = $value;
				$doc['authors'][$id] = $dt;
				$id++;
			}
			/* Recupera KeyWorkds */
			$titles = $node -> getElementsByTagName("subject");
			$id = 0;
			foreach ($titles as $title) {
				$value = $title -> nodeValue;
				$lang = $title -> attributes -> getNamedItem('lang') -> value;
				if ($lang == 'pt-BR') { $lang = 'pt-BR';
				}
				if ($lang == 'en-US') { $lang = 'en';
				}
				$dt = array();
				$dt['term'] = $value;
				$dt['idioma'] = $lang;
				$doc['keywords'][$id] = $dt;
				$id++;
			}
			/* Recupera Resumos */
			$titles = $node -> getElementsByTagName("description");
			$id = 0;
			foreach ($titles as $title) {
				$value = $title -> nodeValue;
				$lang = $title -> attributes -> getNamedItem('lang') -> value;
				if ($lang == 'pt_BR') { $lang = 'pt-BR';
				}
				if ($lang == 'en-US') { $lang = 'en';
				}
				$dt = array();

				$value = troca($value, '  ', ' ');
				$dt['content'] = $value;
				$dt['idioma'] = $lang;
				$doc['abstract'][$id] = $dt;
				$id++;
			}

			/* link */

			$titles = $node -> getElementsByTagName("identifier");
			$id = 0;
			foreach ($titles as $title) {
				$value = $title -> nodeValue;
				$dt = array();
				$dt['link'] = $value;
				$doc['links'][$id] = $dt;
				$id++;
			}

			/* Source */
			$titles = $node -> getElementsByTagName("source");
			$id = 0;
			foreach ($titles as $title) {
				$value = $title -> nodeValue;
				$dt = array();
				$dt['source'] = $value;
				$doc['sources'][$id] = $dt;
				$id++;
			}
			return ($doc);
		}
		return ( array());
	}

	function coleta_oai_cache_next($id) {
		$jid = strzero($id, 7);
		$sql = "select * from oai_cache
					inner join brapci_journal on jnl_codigo = cache_journal
					where cache_journal = '$jid'
					and cache_status = '@'
			";
		$rlt = db_query($sql);

		$sr = 'nothing to harvesting';

		if ($line = db_read($rlt)) {
			$url = trim($line['jnl_url_oai']);
			$ido = trim($line['cache_oai_id']);
			$idr = $line['id_cache'];

			/* Atualiza registro de coleta */
			$sql = "update oai_cache set cache_tentativas = cache_tentativas + 1 where id_cache = " . $id;
			$this -> db -> query($sql);

			/* Method 1 */
			$link = $url . '?verb=GetRecord';
			$link .= '&metadataPrefix=oai_dc';
			$link .= '&identifier=' . $ido;
			$xml_rt = load_page($link);
			$xml = $xml_rt['content'];

			$sr = '<BR><font color="grey">Cache:</font> ' . $ido . ' <font color="green">coletado</font>';

			$file = 'ma/oai/' . strzero($idr, 7) . '.xml';
			$f = fopen($file, 'w+');
			fwrite($f, $xml);
			fclose($f);

			$sql = "update oai_cache set cache_status='A' where id_cache = " . $idr;
			$this -> db -> query($sql);

			/* Meta refresh */
			$sr .= '<meta http-equiv="refresh" content="3">';
		}
		return ($sr);

	}

	function oai_resumo_to_harvesing() {
		$sql = "select count(*) as total, cache_journal, jnl_nome from oai_cache 
					inner join brapci_journal on jnl_codigo = cache_journal
						where cache_status = '@'
						group by cache_journal, jnl_nome
						order by jnl_nome ";
		$rlt = db_query($sql);
		$t = array(0, 0, 0, 0);
		$sx = '<h1>Record to harvesting</h1>';
		while ($line = db_read($rlt)) {
			$link = '<A HREF="' . base_url('index.php/oai/Harvesting/' . $line['cache_journal']) . '">';
			$sx .= '' . $link . $line['jnl_nome'] . '</A>';
			$sx .= ' (' . $line['total'] . ')<BR>';
		}
		return ($sx);
	}

	function oai_resumo_to_progress() {
		$sql = "select count(*) as total, cache_journal, jnl_nome from oai_cache 
					inner join brapci_journal on jnl_codigo = cache_journal
						where cache_status = 'A'
						group by cache_journal, jnl_nome
						order by jnl_nome ";
		$rlt = db_query($sql);
		$t = array(0, 0, 0, 0);
		$sx = '<br><br><h1>Record to process</h1>';
		while ($line = db_read($rlt)) {
			$link = '<A HREF="' . base_url('index.php/oai/Harvesting/' . $line['cache_journal']) . '">';
			$sx .= '' . $link . $line['jnl_nome'] . '</A>';
			$sx .= ' (' . $line['total'] . ')<BR>';
		}
		return ($sx);
	}

	function oai_resset_cache($id) {
		$sql = "update oai_cache set cache_status = '@' where cache_journal = '" . strzero($id, 7) . "'";
		$rlt = $this -> db -> query($sql);
		return (1);
	}

	function oai_resumo($jid = 0) {
		$wh = ' 1 = 1 ';
		if ($jid > 0) { $wh = " cache_journal = '" . strzero($jid, 7) . "' ";
		}

		$sql = "select count(*) as total, cache_status from oai_cache 
						where $wh 
						group by cache_status ";
		$rlt = db_query($sql);
		$t = array(0, 0, 0, 0);
		while ($line = db_read($rlt)) {
			$sta = $line['cache_status'];
			$tot = $line['total'];
			switch($sta) {
				case '@' :
					$t[0] = $t[0] + $line['total'];
					break;
				case 'B' :
					$t[1] = $t[1] + $line['total'];
					break;
				case 'A' :
					$t[2] = $t[2] + $line['total'];
					break;
				default :
					$t[3] = $t[3] + $line['total'];
					break;
			}
		}

		$sx = '';
		$sx .= 'OAI-PMH Status';
		$sx .= '<ul class="nav nav-tabs nav-justified">';
		$sx .= '<li><a href="#">para coletar <span class="badge">' . number_format($t[0], 0, ',', '.') . '</span></a></li>';
		$sx .= '<li><a href="#">coletado <span class="badge">' . number_format($t[2], 0, ',', '.') . '</span></a></li>';
		$sx .= '<li><a href="#">processado <span class="badge">' . number_format(($t[1] + $t[3]), 0, ',', '.') . '</span></a></li>';
		$sx .= '<li><a href="#">total <span class="badge">' . number_format(($t[0] + $t[1] + $t[2] + $t[3]), 0, ',', '.') . '</span></a></li>';
		$sx .= '</ul>';
		return ($sx);
	}

	function doublePDFlink() {
		$sql = "select * from (
						SELECT bs_adress, count(*) as total, max(id_bs) as id 
							FROM `brapci_article_suporte` 
						WHERE bs_type = 'URL' 
						 	and bs_adress like 'http%'
						 	and (bs_status ='A' or bs_status = '@')
						 	and bs_adress <> ''
						 group by bs_adress
					) as tabela
				where total > 1
				";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			for ($r = 0; $r < count($rlt); $r++) {
				$line = $rlt[$r];
				$adress = $line['bs_adress'];
				$id = $line['id'];
				$sql = "update brapci_article_suporte 
						set bs_status = 'D' 
					WHERE bs_adress = '$adress' 
							and id_bs <> $id ";
				$xrlt = $this -> db -> query($sql);
			}
		} else {
			return (0);
		}
	}

	function artcle_wifout_file($pag = 0) {
		$off = $pag * 350;
		$sql = "select count(*) as total from brapci_article
					LEFT JOIN (
						select count(*) as total, bs_article from brapci_article_suporte 
								where bs_status <> 'X' and bs_type = 'PDF' 
								group by bs_article
						) as tabela ON bs_article = ar_codigo
					WHERE TOTAL is null AND ar_status <> 'X' 
					limit 50 offset $off";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '<h4>' . $rlt[0]['total'] . '</h4>';

		$sql = "select ar_codigo, ar_titulo_1, jnl_nome from brapci_article
					LEFT JOIN (
						select count(*) as total, bs_article from brapci_article_suporte 
								where bs_status <> 'X' and bs_type = 'PDF' 
								group by bs_article
						) as tabela
					ON bs_article = ar_codigo
					INNER JOIN brapci_journal ON jnl_codigo = ar_journal_id
					
					WHERE TOTAL is null AND ar_status <> 'X'					
					ORDER BY jnl_nome, ar_codigo";
		/* removido em 27/07/2017 - limit 350 offset $off"; */

		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();

		$sx .= '<ul>';
		$jnl = '';
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$xjnl = $line['jnl_nome'];
			if ($jnl != $xjnl) {
				$sx .= '<h4>' . $xjnl . '</h4>';
				$jnl = $xjnl;
			}
			$link = '<a href="' . base_url('index.php/admin/article_view/' . $line['ar_codigo'] . '/' . checkpost_link($line['ar_codigo'])) . '">';
			$sx .= '<li>' . $link . $line['ar_titulo_1'] . '</a></li>';
		}
		$sx .= '</ul>';
		return ($sx);
	}

	function fileExistPDFlink($pag = 0) {
		$sz = 30;
		$OFFSET = ($pag * 100);
		$data = date("Ymd");
		$sql = "select * from brapci_article_suporte 
					WHERE bs_update <> '$data' 
						and bs_status <> 'X'
						and bs_type = 'PDF'
					order by id_bs 
					LIMIT 100 OFFSET $OFFSET
					
					";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '';
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$sx .= '<br>';
			$sx .= ($r + $pag * 100) . '. ';
			$file = $line['bs_adress'];
			$sx .= $file;
			if (file_exists($file)) {
				$sx .= ' <b><font color="green">OK</font></b>' . cr();
			} else {
				$sx .= ' <b><font color="red">file not found</font></b>' . cr();
				$sql = "update brapci_article_suporte set bs_status = 'X', bs_update = '" . date("Ymd") . "' where id_bs = " . $line['id_bs'];
				$rla = $this -> db -> query($sql);
			}
		}
		if (count($rlt) > 0) {
			$sx .= '<META http-equiv="refresh" content="5;URL=' . base_url('index.php/admin/fileexist_pdf/' . ($pag + 1)) . '">';
		}
		return ($sx);
	}

	function totalPDFharvesting() {
		$sql = "select count(*) as total from (
						SELECT `bs_article` as art, count(*) as total FROM `brapci_article_suporte` WHERE bs_type = 'URL' group by bs_article
						   )
						   as tebela
						 inner join brapci_article_suporte on art = bs_article
						 where total = 1 and bs_adress like 'http%'
						 and bs_status ='A' or bs_status = '@'
						 and art <> '' 
					limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			return ($rlt[0]['total']);
		} else {
			return (0);
		}

	}

	function nextPDFharvesting() {
		$sql = "select * from (
							SELECT `bs_article` as art, count(*) as total 
							FROM `brapci_article_suporte` 
							WHERE bs_type = 'URL' group by bs_article
						   )
						   as tebela
						 inner join brapci_article_suporte on art = bs_article
						 where total = 1 and bs_adress like 'http%'
						 and bs_status ='A' or bs_status = '@'
						 and art <> '' 
					order by art desc
					limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$id = $rlt[0]['id_bs'];
			$sql = "update brapci_article_suporte set bs_status = 'T' where id_bs = " . $id;
			$this -> db -> query($sql);
			return ($rlt[0]);
		} else {
			return (0);
		}

	}

	function nextPDFconvert() {
		$data = date("Ymd");
		$sql = "select * from brapci_article_suporte where bs_status = 'T'
					and bs_update <> $data
					limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$id = $rlt[0]['id_bs'];
			$sql = "update brapci_article_suporte set bs_status = 'U', bs_update = $data 
						where id_bs = " . $id;
			$this -> db -> query($sql);
			return ($rlt[0]);
		} else {
			return (0);
		}

	}

}
?>
