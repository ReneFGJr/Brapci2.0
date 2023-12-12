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
class articles extends CI_model {

	var $table = 'brapc607_base.brapci_article';
	var $saved = 0;

	function excluir_suportes()
		{
			$sql = "delete from brapc607_base.brapci_article_suporte where bs_status = 'Z' ";
			$rlt = $this->db->query($sql);
		}

	function mudar_status($ar, $st) {
		$this -> inserir_historico($ar, 'alt_status_' . $st);
		$sql = "update " . $this -> table . " set ar_status = '$st' where ar_codigo = '$ar' ";
		$this -> db -> query($sql);
		return (1);
	}

	function inserir_historico($ar, $acao) {
		$user = $_SESSION['id'];
		$sql = "insert into brapc607_base.brapci_historico
					( h_artigo, h_user, h_acao)
					values
					('$ar','$user','$acao')	";
		$this -> db -> query($sql);
		return (1);
	}

	function inserir_keywords($id) {
		$data = $this -> le($id);
		$k1 = $data['ar_keyw_1'];
		$k2 = $data['ar_keyw_2'];
		$k3 = $data['ar_keyw_3'];
		$sql = "update " . $this -> table . " set ar_key1='$k1', ar_key2='$k2', ar_key3='$k3' where id_ar = $id";
		$this -> db -> query($sql);
	}

	function acao($id, $ar) {
		$sx = '<br><br>ações para o registro:<br>';
		switch($ar) {
			case 'A' :
				$sx .= '<a href="' . base_url('index.php/admin/article_alterar_status/' . $id . '/B') . '" class="btn btn-primary">Enviar para Revisão(I)</a>';
				$sx .= ' | ';
				$sx .= '<a href="' . base_url('index.php/admin/article_alterar_status/' . $id . '/X') . '" class="btn btn-danger">Cancelar trabalho</a>';
				break;
			case 'B' :
				$sx .= '<a href="' . base_url('index.php/admin/article_alterar_status/' . $id . '/C') . '" class="btn btn-primary">Enviar para Revisão(II)</a>';
				$sx .= ' | ';
				$sx .= '<a href="' . base_url('index.php/admin/article_alterar_status/' . $id . '/A') . '" class="btn btn-warning">Reenviar para Edição</a>';
				break;
			case 'X' :
				$sx .= '<a href="' . base_url('index.php/admin/article_alterar_status/' . $id . '/A') . '" class="btn btn-warning">Reenviar para Edição</a>';
		}
		return ($sx);
	}

	function le_support($id) {
		$id_art = strzero($id, 10);
		$sql = "select * from brapc607_base.brapci_article_suporte where bs_article = '$id_art'";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			return ($rlt[0]);
		} else {
			return ( array());
		}
	}

	function support_alterar_status($id, $status) {
		$id_art = strzero($id, 10);
		$sql = "update brapc607_base.brapci_article_suporte set bs_status = '$status' where bs_article = '$id_art'";
		$rlt = $this -> db -> query($sql);
		return (1);
	}

	function supports_novo($id) {
		$id = strzero($id, 10);
		$link = ' onclick="newxy(\'' . base_url('index.php/admin/support_editar/0/' . $id) . '\',600,300);" ';
		$link2 = ' onclick="newxy(\'' . base_url('index.php/admin/support_upload/' . $id) . '\',600,300);" ';
		$link3 = ' onclick="newxy(\'' . base_url('index.php/admin/cover_upload/' . $id) . '\',600,300);" ';
		$sx = '<br><br><span class="btn btn-primary" ' . $link . '>Inserir Suporte</span>';
		$sx .= '&nbsp;';
		$sx .= '<span class="btn btn-primary" ' . $link2 . '>Upload de PDF</span>';
		$sx .= '&nbsp;';
		$sx .= '<span class="btn btn-primary" ' . $link3 . '>Upload de Capa</span>';
		return ($sx);
	}

	function supports($id) {
		$id_art = strzero($id, 10);
		$sql = "select * from brapc607_base.brapci_article_suporte where bs_article = '$id_art'";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();

		$sx = '<table class="table" border=5>';
		$sx .= '<tr>
						<th>tipo</th>
						<th>endereço</th>
						<th>sit.</th>
						<th width="3%">#</th>
						<th width="20%">status</th>
						<th>atual.</th>
						<th width="4%">ação</th>
					</tr>' . cr();
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$id = $line['id_bs'];

			$sx .= '<tr bgcolor="#f0f0f0">';

			$sx .= '<td>';
			$sx .= $line['bs_type'];
			$sx .= '</td>';

			$sx .= '<td>';
			$sx .= $line['bs_adress'];
			$sx .= '</td>';

			$sx .= '<td>';
			$sx .= $line['bs_status'];
			$sx .= '</td>';

			$divm = 'id' . $r;

			$sx .= '<td align="right">';
			$link = '<a href="#">';
			$sx .= $link.'<span id="' . $divm . 'a"  class="glyphicon glyphicon-refresh" aria-hidden="true"></span>'.'</a>';

			$pag= '1';
			$sx .= '</td>';

			$sx .= '<td>';
			$sx .= '<div id="' . $divm . '" style="width:150px; height: 30px; border:1px solid #333;" class="lt1"></div>';
			$sx .= '</td>' . cr();

			$sx .= '<script>' . cr();
			$sx .= '
				$("#' . $divm . 'a").click(function() {
						$("#' . $divm . '").html("Coletando...");
						$.ajax({
		  					method: "POST",
		  					url: "' . base_url('index.php/oai/coletar_pdf/' . $id . '/' . ($pag + 1)) . '",
		  					data: { name: "OAI", location: "PDF" }
							})
		  					.done(function( data ) {
		    						$("#' . $divm . '").html(data);
		  					});
					});
			' . cr();
			$sx .= '</script>' . cr();

			$sx .= '</td>';

			$sx .= '<td>';
			$sx .= stodbr($line['bs_update']);
			$sx .= '</td>';

			$sx .= '<td align="right">';
			$link = 'onclick="newxy(\'' . base_url('index.php/admin/support_editar/' . $line['id_bs'] . '/' . $line['bs_article']) . '\',600,400);"';
			$sx .= '<span ' . $link . ' class="btn btn-primary">editar</span>';

			$sx .= '</td>';

			$sx .= '</tr>' . cr();
		}
		$sx .= '</table>';
		return ($sx);
	}

	function task_next($id) {
		$sql = "select * from brapc607_base.brapci_article
						where ar_status = '$id'
						order by ar_section, id_ar desc
						limit 50
					";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '<table class="table">';
		$sx .= '<tr>
						<th>título</th>
						<th>tipo</th>
						<th>atualização</th>
					</tr>';
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$link = '<a href="' . base_url('index.php/admin/article_view/' . $line['id_ar'] . '/' . checkpost_link($line['id_ar'])) . '">';
			$sx .= '<tr>';
			$sx .= '<td>' . $link . $line['ar_titulo_1'] . '</a></td>';
			$sx .= '<td>' . $link . $line['ar_section'] . '</a></td>';
			$sx .= '<td>' . $link . stodbr($line['ar_data_envio']) . '</a></td>';
			$sx .= '</tr>';
		}
		$sx .= '</table>';
		return ($sx);
	}

	function resumo() {
		$sql = "SELECT count(*) as total, ar_status FROM brapc607_base.brapci_article group by ar_status";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '';
		$rs = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$tp = $line['ar_status'];
			$total = $line['total'];
			switch ($tp) {
				case 'A' :
					$rs[0] = $rs[0] + $total;
					break;
				case 'B' :
					$rs[1] = $rs[1] + $total;
					break;
				case 'C' :
					$rs[2] = $rs[2] + $total;
					break;
				case 'D' :
					$rs[3] = $rs[3] + $total;
					break;
				case 'F' :
					$rs[3] = $rs[3] + $total;
					break;
				case 'X' :
					$rs[5] = $rs[5] + $total;
					break;
				default :
					$rs[0] = $rs[0] + $total;
					break;
			}
		}
		$link1 = '<a href="' . base_url('index.php/admin/resumo_status/0/') . '">';
		$sx .= '<div class="row">';
		$sx .= '<div class="col-md-6">';
		$sx .= '<table width="400">
					<tr><th>situação</th><th>quant.</th></tr>
					<tr><td>' . $link1 . 'Em indexação</a></td><td align="right">' . number_format($rs[0], 0, ',', '.') . '</td></tr>
					<tr><td>Em 1º Revisão</td><td align="right">' . number_format($rs[1], 0, ',', '.') . '</td></tr>
					<tr><td>Em 2º Revisão</td><td align="right">' . number_format($rs[2], 0, ',', '.') . '</td></tr>
					<tr><td>Indexados</td><td align="right">' . number_format($rs[3], 0, ',', '.') . '</td></tr>
					<tr><th>Total</th><td align="right"><b>' . number_format(($rs[0] + $rs[1] + $rs[2] + $rs[3] + $rs[4]), 0, ',', '.') . '</b></td></tr>
					</table>';
		$sx .= '</div>';
		$sx .= '<div class="col-md-6">';
		if (perfil('#ADM'))
			{
				$sx .= $this -> oai_pmh -> oai_resumo();
			}
		$sx .= '</div>';
		$sx .= '</div>';
		return ($sx);
	}

	function double_articles() {
		$sql = "select * from ( SELECT ar_oai_id, count(*) as total, max(id_ar) as idmax
									FROM brapc607_base.brapci_article
								WHERE ar_oai_id <> '' and ar_status <> 'X'
								GROUP by ar_oai_id
								) as tabela where total > 1
								ORDER BY total desc";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx = '<h1>Verificando duplicados</h2>';
		$sx .= '<h2>Método I - ID OAI Duplicados</h2>';
		$sx .= '<ul>';
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$sx .= '<li>';
			$sx .= '<a href="'.base_url('index.php').'">';
			$sx .= $line['ar_oai_id'];
			$sx .= ' ('.$line['total'].')';
			$sx .= ' - '.$line['idmax'];
			$sx .= '</li>';

			$oaiid = $line['ar_oai_id'];
			$max = $line['idmax'];
			$sql = "update brapc607_base.brapci_article set ar_status = 'X'
						where ar_oai_id = '$oaiid' and ar_oai_id <> '' and id_ar < $max ";
			$rrr = $this->db->query($sql);
		}
		$sx .= '<ul>';
		if (count($rlt) == 0)
			{
				$sx .= '<span color="green">Nenhum OAI ID duplicado</span>';
			}


		/*** METODO II **/
		$sx .= '<h2>Método II - Título, fascículo duplicados</h2>';
		$sql = "select * from (
				SELECT count(*) as total, ar_titulo_1, ar_ano, ar_journal_id, ar_edition, max(id_ar) as idmax FROM brapc607_base.brapci_article
				where ar_status <> 'X' and NOT (ar_titulo_1 like '%editorial%')
				group by substr(ar_titulo_1,1,50), ar_ano, ar_journal_id, ar_edition
			) as tabela
			where total > 1
			order by total desc
			limit 100";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx .= '<ul>';
		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$sx .= '<li>';
			$sx .= '<a href="'.base_url('index.php').'">';
			$sx .= $line['ar_titulo_1'];
			$sx .= ' ('.$line['total'].')';
			$sx .= ' - '.$line['idmax'];
			$sx .= '</li>';

			$max = $line['idmax'];
			$journal = $line['ar_journal_id'];
			$ed = $line['ar_edition'];
			$title = substr($line['ar_titulo_1'],0,100);
			$i = strlen($title)-1;
			while (substr($title,$i,1) != ' ')
				{
					if (strlen($title) < 20) { $title = substr($line['ar_titulo_1'],0,100).'  ';  }
					$title = substr($title,0,strlen($title)-1);
					$i = strlen($title)-1;
				}
			$title = trim($title);
			$sql = "update brapc607_base.brapci_article set ar_status = 'X'
							where ar_journal_id = '$journal'
						    AND ar_edition = '$ed'
						    AND ar_titulo_1 LIKE '$title%'
						    AND id_ar < $max
						 ";
			$rrr = $this->db->query($sql);
//			$sx .= '<br>'.$sql;
		}
		$sx .= '<ul>';
		return ($sx);
	}

	function insert_suporte($codigo, $link, $jid) {
		$sql = "select * from brapc607_base.brapci_article_suporte where bs_adress = '$link' ";
		$rlt = db_query($sql);
		if ($line = db_read($rlt)) {
			return (0);
		} else {
			$data = date("Ymd");

			$type = 'URL';
			if (substr($link, 0, 3) == '10.') { $type = 'DOI';
			}

			$sql = "insert into brapc607_base.brapci_article_suporte
							(
							bs_article, bs_type, bs_adress,
							bs_status, bs_journal_id, bs_update
							) values (
							'$codigo','$type','$link',
							'A','$jid',$data
							)
					";
			$this -> db -> query($sql);
			return (1);
		}
	}

	function insert_new_article($article) {
		$id = $article['idf'];
		$xsql = "select * from brapc607_base.brapci_article where ar_oai_id = '$id' ";
		$rlt = db_query($xsql);

		if ($line = db_read($rlt)) {
			$cod = $line['ar_codigo'];
			$section = $article['section'];
			$edition = $article['issue_id'];
			$sql = "update brapc607_base.brapci_article set
					ar_edition= '$edition',
					ar_section = '$section'
					where id_ar = " . $line['id_ar'];
			$rlt = $this -> db -> query($sql);
			return ($cod);
		} else {
			$section = $article['section'];
			$edition = $article['issue_id'];

			$journal_id = $article['journal_id'];
			$idioma = $article['titles'][0]['idioma'];
			$titulo = troca($article['titles'][0]['title'], "'", '´');
			if (isset($article['abstract'][0]['content'])) {
				$ar_resumo_1 = $article['abstract'][0]['content'];
				$ar_resumo_1 = troca($ar_resumo_1, "'", '´');
			} else {
				$ar_resumo_1 = '';
			}

			if (isset($article['titles'][1]['idioma'])) {
				$idioma2 = $article['titles'][1]['idioma'];
				$titulo2 = troca($article['titles'][1]['title'], "'", '´');
				$ar_resumo_2 = troca($article['abstract'][1]['content'], "'", '´');
			} else {
				$idioma2 = '';
				$titulo2 = '';
				$ar_resumo_2 = '';
			}

			if ($idioma2 == 'pt-BR') {
				$idioma3 = $idioma;
				$titulo3 = $titulo;
				$ar_resumo_3 = $ar_resumo_1;

				$idioma = $idioma2;
				$titulo = $titulo2;
				$ar_resumo_1 = $ar_resumo_2;

				$idioma2 = $idioma3;
				$titulo2 = $titulo3;
				$ar_resumo_2 = $ar_resumo_3;
			}

			$ano = $article['ano'];

			$titulo_asc = UpperCaseSql($titulo);

			$sql = "insert into brapc607_base.brapci_article

			(ar_journal_id, ar_codigo, ar_titulo_1,
			ar_titulo_1_asc, ar_titulo_2,
			ar_idioma_1, ar_idioma_2,
			ar_status, ar_resumo_1, ar_resumo_2,

			ar_edition, ar_section,

			ar_doi, ar_oai_id, ar_brapci_id,
			ar_ano, ar_bdoi, at_citacoes
			) values (
			'$journal_id','','$titulo',
			'$titulo_asc','$titulo2',
			'$idioma','$idioma2',
			'A', '$ar_resumo_1','$ar_resumo_2',

			'$edition','$section',

			'','$id','',
			'$ano','',0)
			";
			$this -> db -> query($sql);
			$this -> updatex();

			$rlt = db_query($xsql);
			$line = db_read($rlt);
			$cod = $line['ar_codigo'];
			return ($cod);
		}
	}

	function save_ISSUE($id, $p1, $p2, $issue, $doi, $sec) {
		/* title 1 */
		$p1 = trim($p1);
		$p2 = trim($p2);

		/* Regras */
		if ((strpos($p1, '-') > 0) and (strlen($p2) == 0)) {
			$p2 = trim(substr($p1, strpos($p1, '-') + 1, 10));
			$p1 = trim(substr($p1, 0, strpos($p1, '-')));
		}

		$sql = "update brapc607_base.brapci_article set
					ar_pg_inicial = '$p1',
					ar_pg_final = '$p2',
					ar_edition = '$issue',
					ar_section = '$sec',
					ar_doi = '$doi'
					where id_ar = " . $id;
		$this -> db -> query($sql);
	}

	function save_TITLE($id, $title_1, $title_2, $idioma_1, $idioma_2) {
		/* title 1 */
		$title_1 = trim($title_1);
		$title_1 = troca($title_1, chr(13), ' ');
		$title_1 = troca($title_1, chr(10), '');
		$title_1 = troca($title_1, '  ', ' ');
		/* title 2 */
		$title_2 = trim($title_2);
		$title_2 = troca($title_2, chr(13), ' ');
		$title_2 = troca($title_2, chr(10), '');
		$title_2 = troca($title_2, '  ', ' ');

		$sql = "update brapc607_base.brapci_article set
					ar_titulo_1 = '$title_1',
					ar_titulo_2 = '$title_2',
					ar_idioma_1 = '$idioma_1',
					ar_idioma_2 = '$idioma_2'
					where id_ar = " . $id;
		$this -> db -> query($sql);
	}

	function save_ABSTRACT($id, $abstract, $lc = 1) {
		if ($lc == 2) { $fld = 'ar_resumo_2';
		} else { $fld = 'ar_resumo_1';
		}
		/* Abstract */
		$abstract = trim($abstract);
		$abstract = troca($abstract, chr(13), ' ');
		$abstract = troca($abstract, chr(10), '');
		$abstract = troca($abstract, '  ', ' ');
		$sql = "update brapc607_base.brapci_article set
					$fld = '$abstract'
					where id_ar = " . $id;
		$this -> db -> query($sql);
		return (1);
	}

	function updatex() {
		$c = 'ar';
		$c1 = 'id_' . $c;
		$c2 = $c . '_codigo';
		$c3 = 10;
		$sql = "update brapc607_base.brapci_article set $c2 = lpad($c1,$c3,0) where $c2='' ";
		$rlt = $this -> db -> query($sql);
	}

	function le($id) {
		$id = strzero($id, 10);
		$sql = "select * from brapc607_base.brapci_article
						left join brapc607_base.brapci_journal on ar_journal_id = jnl_codigo
						left join brapc607_base.brapci_edition on ar_edition = ed_codigo
						left join brapc607_base.brapci_section on ar_section = se_codigo
						left join brapc607_base.ajax_cidade on cidade_codigo = jnl_cidade
						where ar_codigo = '$id' ";
		$query = $this -> db -> query($sql);
		$query = $query -> result_array();
		$line = $query[0];

		/* Title */
		$line['ar_titulo_1'] = troca($line['ar_titulo_1'], chr(13), '');
		$line['ar_titulo_1'] = troca($line['ar_titulo_1'], chr(10), '');
		$line['ar_titulo_2'] = troca($line['ar_titulo_2'], chr(13), '');
		$line['ar_titulo_2'] = troca($line['ar_titulo_2'], chr(10), '');

		/* RESUMO */
		$line['ar_resumo_1'] = troca($line['ar_resumo_1'], chr(13), '');
		$line['ar_resumo_1'] = troca($line['ar_resumo_1'], chr(10), '');
		$line['ar_resumo_2'] = troca($line['ar_resumo_2'], chr(13), '');
		$line['ar_resumo_2'] = troca($line['ar_resumo_2'], chr(10), '');

		/* Kwywords */
		$line['ar_keyw_1'] = $this -> retrieve_keywords($id, $line['ar_idioma_1']);
		$line['ar_keyw_2'] = $this -> retrieve_keywords($id, $line['ar_idioma_2']);
		$line['ar_keyw_3'] = $this -> retrieve_keywords($id, $line['ar_idioma_3']);
		$line['keywords'] = $this -> retrieve_keywords_all($id);
		$line = $this -> authors($id, $line);

		$line['cited'] = $this -> cited($id);
		$line['link_pdf'] = $this -> arquivos_id($id);
		$line['links'] = $this -> arquivos_files($id);

		/* reference */
		$line['reference'] = $this -> cited2($line);

		/* Pages */
		$p1 = $line['ar_pg_inicial'];
		$p2 = $line['ar_pg_final'];
		$line['pages'] = '';
		if ($p1 > 0) {
			$line['pages'] = ', p.' . $p1;
			if (strlen($p2) > 0) {
				$line['pages'] .= '-' . $p2;
			}
		}

		if (strlen(trim($line['ar_doi'])) == 0) { $line['ar_doi'] = '<font color="red">empty</font>';
		}
		return ($line);
	}

	function arquivos_id($id) {
		$sql = "select * from brapc607_base.brapci_article_suporte where bs_article = '$id' and bs_adress like '_repo%' order by bs_type ";
		$rlt = db_query($sql);
		$line = db_read($rlt);
		$link = sround($line['id_bs']);
		if ($link > 0) { $fl = base_url('index.php/article/download/' . $link);
		} else {$fl = '';
		}
		return ($fl);
	}

	function arquivos($id) {
		$sql = "select * from brapc607_base.brapci_article_suporte where bs_article = '$id' and bs_adress like '_repo%' order by bs_type ";
		$rlt = db_query($sql);
		$line = db_read($rlt);
		$link = trim($line['bs_adress']);
		if (strlen($link) > 0) { $fl = base_url($link);
		} else {$fl = '';
		}
		return ($fl);
	}

	function arquivos_files($id) {
		$sql = "select * from brapc607_base.brapci_article_suporte where bs_article = '$id' order by bs_type ";
		$rlt = $this -> db -> query($sql);
		$line = $rlt -> result_array();
		return ($line);
	}

	function cp($id = '',$jid='') {
		$cv = array("dd2", "dd4", "dd5", "dd7", "dd9", "dd10", "dd12", "dd14", "dd15");
		for ($r = 0; $r < count($cv); $r++) {
			$dd = $cv[$r];
			$_POST[$dd] = troca(get($dd), chr(13), ' ');
			$_POST[$dd] = troca($_POST[$dd], chr(10), '');
		}
		/* paginacao */
		$pg = get("dd17");
		if (strpos($pg, '-')) {
			$_POST['dd18'] = substr($pg, strpos($pg, '-') + 1, strlen($pg));
			$_POST['dd17'] = substr($pg, 0, strpos($pg, '-'));
		}
		/* idioma */
		$sqlido = "ido_codigo:ido_descricao:select * from brapc607_base.ajax_idioma order by ido_ordem";

		$cp = array();
		array_push($cp, array('$H8', 'id_ar', '', false, true));

		array_push($cp, array('$A', '', msg('idioma_01'), false, true));
		array_push($cp, array('$T80:4', 'ar_titulo_1', msg('ar_titulo_1'), true, true));
		array_push($cp, array('$Q ' . $sqlido, 'ar_idioma_1', msg('ar_idioma_1'), true, true));
		array_push($cp, array('$T80:7', 'ar_resumo_1', msg('ar_resumo_1'), false, true));
		array_push($cp, array('$T80:2', 'ar_key1', msg('ar_key1'), false, true));

		array_push($cp, array('$A', '', msg('idioma_02'), false, true));
		array_push($cp, array('$T80:4', 'ar_titulo_2', msg('ar_titulo_2'), false, true));
		array_push($cp, array('$Q ' . $sqlido, 'ar_idioma_2', msg('ar_idioma_2'), false, true));
		array_push($cp, array('$T80:7', 'ar_resumo_2', msg('ar_resumo_2'), false, true));
		array_push($cp, array('$T80:2', 'ar_key2', msg('ar_key2'), false, true));

		array_push($cp, array('$A', '', msg('idioma_03'), false, true));
		array_push($cp, array('$T80:4', 'ar_titulo_3', msg('ar_titulo_3'), false, true));
		array_push($cp, array('$Q ' . $sqlido, 'ar_idioma_3', msg('ar_idioma_3'), false, true));
		array_push($cp, array('$T80:7', 'ar_resumo_3', msg('ar_resumo_3'), false, true));
		array_push($cp, array('$T80:2', 'ar_key3', msg('ar_key3'), false, true));

		array_push($cp, array('$A', '', msg('other_info'), false, true));
		array_push($cp, array('$S10', 'ar_pg_inicial', msg('ar_pg_inicial'), false, true));
		array_push($cp, array('$S10', 'ar_pg_final', msg('ar_pg_final'), false, true));
		array_push($cp, array('$S80', 'ar_doi', msg('ar_doi'), false, true));

		$sql = "select * from brapc607_base.brapci_section where se_ativo = 1 order by se_descricao";
		array_push($cp, array('$Q se_codigo:se_descricao:'.$sql, 'ar_section', msg('ar_section'), true, true));

		$sql = "select ed_codigo, concat(ed_ano,', v.',ed_vol,', ',ed_nr) as description from brapc607_base.brapci_edition where ed_journal_id = '".$jid."' order by description desc";
		array_push($cp, array('$Q ed_codigo:description:'.$sql, 'ar_edition', msg('ar_edition'), true, true));

		return ($cp);
	}

	function change_language($id) {
		$id = strzero($id, 10);
		$data = $this -> le($id);
		$id = $data['id_ar'];

		$t1 = $data['ar_titulo_1'];
		$t2 = $data['ar_titulo_2'];

		$l1 = $data['ar_idioma_1'];
		$l2 = $data['ar_idioma_2'];

		$r1 = $data['ar_resumo_1'];
		$r2 = $data['ar_resumo_2'];

		$k1 = $data['ar_key1'];
		$k2 = $data['ar_key2'];

		$sql = "update " . $this -> table . " set
						ar_titulo_1 = '$t2',
						ar_titulo_2 = '$t1',
						ar_idioma_1 = '$l2',
						ar_idioma_2 = '$l1',
						ar_resumo_1 = '$r2',
						ar_resumo_2 = '$r1',
						ar_key1 	= '$k2',
						ar_key2 	= '$k1'
					where id_ar = " . $id;

		$this -> db -> query($sql);
		return ('');
	}

	function cited($id) {

		$sql = "select * from brapc607_base.mar_works
						where m_work = '$id'
					order by m_ref";
		$query = $this -> db -> query($sql);
		$query = $query -> result();
		$sx = '<ul id="refs">';
		while ($line = db_read($query)) {
			$bdoi = trim($line['m_bdoi']);
			if (strlen($bdoi) > 0) {
				$bdoi = ' <font color="blue">(' . $bdoi . ')</font>';
			}
			$sx .= '<li>' . htmlspecialchars($line['m_ref']) . $bdoi . '</li>';
		}
		$sx .= '</ul>';
		return ($sx);
	}

	function authors($id, $line) {
		$sql = "
				SELECT * FROM brapc607_base.brapci_article_author
				inner join brapc607_base.brapci_autor on autor_codigo = ae_author
				where ae_article = '$id'
				order by ae_pos	, id_ae
				 ";
		$rlt2 = $this -> db -> query($sql);
		$rlt2 = $rlt2 -> result_array();
		$sxa = '';
		$sxb = '';
		$sxc = '';
		$id = 0;
		for ($r = 0; $r < count($rlt2); $r++) {
			$line1 = $rlt2[$r];
			$id++;
			if (strlen($sxa) > 0) { $sxa .= '; ';
			}
			$sxa .= htmlspecialchars($line1['autor_nome']);
			$info = trim($line1['ae_bio']);
			if (strlen($info) > 0) {
				$sxb .= ' <sup><a href="#" title="' . $info . '">' . $id . '</a></sup>';
				$sxc .= htmlspecialchars($line1['autor_nome']) . chr(13) . chr(10);
			}
			$sxa .= '.';
		}

		$line['authors'] = $rlt2;
		$line['author'] = $sxb;
		$line['authores_row'] = $sxa;
		return ($line);

	}

	function author_article($id) {
		$sql = "
				SELECT * FROM brapc607_base.brapci_article_author
				inner join brapc607_base.brapci_autor on autor_codigo = ae_author
				where ae_article = '$id'
				order by ae_pos
				 ";
		$query = $this -> db -> query($sql);
		$query = $query -> result();
		$sx = '';
		$id = 0;
		while ($line = db_read($query)) {
			$id++;
			if (strlen($sx) > 0) { $sx .= '; ';
			}
			$sx .= htmlspecialchars($line['autor_nome']);
			$info = trim($line['ae_bio']);
			if (strlen($info) > 0) {
				$sx .= ' <sup><a href="#" title="' . $info . '">' . $id . '</a></sup>';
			}
		}
		$sx .= '.';
		return ($sx);

	}

	function author_article_row($id) {
		$sql = "
				SELECT * FROM brapc607_base.brapci_article_author
				inner join brapc607_base.brapci_autor on autor_codigo = ae_author
				where ae_article = '$id'
				order by ae_pos
				 ";
		$query = $this -> db -> query($sql);
		$query = $query -> result_array();
		$sx = '';
		$id = 0;
		for ($r = 0; $r < count($query); $r++) {
			$sx .= htmlspecialchars($query[$r]['autor_nome']) . chr(13) . chr(10);
		}
		return ($sx);

	}

	function articles_author($codigo) {
		$sql = "
				SELECT * FROM brapc607_base.brapci_article_author
				inner join brapc607_base.brapci_article on ae_article = ar_codigo
				inner join brapc607_base.brapci_edition on ar_edition = ed_codigo
				inner join brapc607_base.brapci_journal on jnl_codigo = ar_journal_id
				where ae_author = '$codigo' and ar_status <> 'X'
				order by ae_pos, ed_ano desc ";

		$query = $this -> db -> query($sql);
		$query = $query -> result();
		$sx = '<table width="100%" class="tabela00">';
		$r = 0;
		foreach ($query as $row) {
			$r++;
			$link = '<A HREF="' . base_url('index.php/res/v/a/' . $row -> id_ar . '/' . checkpost_link($row -> id_ar)) . '" target="_new' . $row -> id_ar . '">';
			//$title = trim($row['ar_titulo_1']);
			$title = $row -> ar_titulo_1;
			$journal = $row -> jnl_nome;
			$ano = $row -> ed_ano;
			$vol = $row -> ed_vol;
			$nr = $row -> ed_nr;
			$tipo = $row -> ar_tipo;

			$sx .= '<tr valign="top">';
			$sx .= '<td width="30" align="right">' . $r . '.&nbsp;</td>';
			$sx .= '<td>';
			$sx .= $link;
			$sx .= trim($title) . '. ';
			$sx .= trim($journal);
			if (strlen($vol)) { $sx .= ', v. ' . $vol;
			}
			if (strlen($nr)) { $sx .= ', n. ' . $nr;
			}
			$sx .= ', ' . $ano;
			/* tipo */
			if (strlen($tipo)) { $sx .= ' (' . $tipo . ')';
			}
			$sx .= '</A>';
			$sx .= '</td>';
			$sx .= '</tr>';
		}
		$sx .= '</table>';
		return ($sx);
	}

	function autoindex_change_linguage() {
		$sx = '';
		$sql = "select count(*) as total from " . $this -> table . " where ar_idioma_2 = 'pt_BR' and ar_idioma_1 <> 'pt_BR' limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx .= '<h4>Total ' . $rlt[0]['total'] . '</h4>';

		$sql = "select * from " . $this -> table . " where ar_idioma_2 = 'pt_BR' and ar_idioma_1 <> 'pt_BR' limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();

		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$id = $line['ar_codigo'];
			$sx .= '' . $line['ar_titulo_1'] . ' - ' . $line['ar_idioma_1'] . ' <b>&lt;==&gt;</b> ' . $line['ar_idioma_2'];
			$sx .= '<hr>';
			$this -> change_language($id);
			$sx .= cr() . '<meta http-equiv="refresh" content="1">';
		}
		return ($sx);
	}
	function autoindex_linguage() {
				$sql = "update " . $this -> table . " set
						ar_idioma_1 = 'pt_BR',
						ar_idioma_2 = 'en'
					where (ar_idioma_1 = '0' or ar_idioma_1 = '' or ar_idioma_1 is null)";
		$rlt = $this -> db -> query($sql);

	}

	function autoindex_linguage_second() {
		$sx = '';
		$sql = "select count(*) as total from " . $this -> table . " where (ar_idioma_2 = '' or ar_idioma_2 is null) and (ar_idioma_1 <> 'en') limit 1";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		$sx .= '<h4>Total ' . $rlt[0]['total'] . '</h4>';

		$sql = "update " . $this -> table . " set ar_idioma_2 = 'en' where (ar_idioma_2 = '' or ar_idioma_2 is null) and (ar_idioma_1 <> 'en') ";
		$rlt = $this -> db -> query($sql);

		$sql = "update " . $this -> table . " set ar_idioma_3 = 'en' where ar_idioma_3 = 'us' ";
		$rlt = $this -> db -> query($sql);
		$sql = "update " . $this -> table . " set ar_idioma_2 = 'en' where ar_idioma_2 = 'us' ";
		$rlt = $this -> db -> query($sql);
		$sql = "update " . $this -> table . " set ar_idioma_1 = 'en' where ar_idioma_1 = 'us' ";
		$rlt = $this -> db -> query($sql);

		return ($sx);
	}

	function view_pdf($id) {
		$sql = "select * from brapc607_base.brapci_article_suporte where id_bs = " . sround($id);
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$line = $rlt[0];
			$file = trim($line['bs_adress']);
			if (file_exists($file)) {
				$this -> pdf_register($id, 1);
				header('Content-type: application/pdf');
				readfile($file);
			}
		}
	}

	function download_pdf($id) {
		$sql = "select * from brapc607_base.brapci_article_suporte where id_bs = " . sround($id);
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();
		if (count($rlt) > 0) {
			$line = $rlt[0];
			$file = trim($line['bs_adress']);
			echo $file;
			if (file_exists($file)) {
				header('Content-type: application/pdf');
				readfile($file);
			} else {
				echo 'File not found in this server';
			}
		}
	}

	function pdf_register($id, $type) {
		if (isset($_SESSION['bp_session']))
			{
				$session = $_SESSION['bp_session'];
			} else {
				$session = date("YmdHis");
				$_SESSION['bp_session'] = $session;
			}

		$ip = ip();
		$sql = "insert into pdf_download
						(
						pdf_ip, pdf_id, pdf_session, pdf_session_type
						) values (
						'$ip','$id','$session','$type')	";
		//$this -> db -> query($sql);
	}
    function retrieve_keywords($id, $idioma = 'pt_BR') {
        $sql = "select * from brapc607_base.brapci_article_keyword
                    inner join brapc607_base.brapci_keyword on kw_keyword = kw_codigo
                    where kw_article = '$id' and kw_idioma = '$idioma'
                    order by kw_ord
            ";

        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $keys = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            if (strlen($keys) > 0) { $keys .= '; ';
            }
            $keys .= trim($line['kw_word']);
        }
        return ($keys);
    }

    function retrieve_keywords_all($id) {
        $sql = "select * from brapc607_base.brapci_article_keyword
                    inner join brapc607_base.brapci_keyword on kw_keyword = kw_codigo
                    where kw_article = '$id'
                    order by kw_idioma, kw_ord
            ";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function trata_keywords($keys) {
        $d = $keys;
        if (mb_detect_encoding($d) == 'UFT-8') {
            $d = utf8_decode($d);
        }
        /* Limpa */
        $s = $d;
        $d = '';
        for ($r = 0; $r < strlen($s); $r++) {
            $as = ord(substr($s, $r, 1));
            if ($as < 128) {
                $d .= chr($as);
            }
        }
        $keys = $d;

        /* Keywords */
        $keys = troca($keys, chr(13), ' ');
        $keys = troca($keys, chr(10), '');
        $keys = troca($keys, '.', '; ');
        $keys = troca($keys, ',', '; ');
        $keys = troca($keys, '‟', '´');
        $keys = troca($keys, "'", '´');
        $keys = troca($keys, '  ', '');
        $keys = troca($keys, '  ', '');
        $keys = troca($keys, '  ', '');
        $keys = troca($keys, 'ś', 's');
        $keys = troca($keys, '/ ', '; ');
        $keys .= ';';
        $keys = splitx(';', $keys);
        for ($r = 0; $r < count($keys); $r++) {
            $keys[$r] = substr($keys[$r], 0, 50);
        }
        return ($keys);
    }
    function cited2($ln, $fnt = 'ABNT') {
        $sx = '';
        switch ($fnt) {
            case 'ABNT' :
                $sx .= $this -> abnt($ln);
                break;
            default :
                $sx .= $this -> abnt($ln);
                break;
        }
        return ($sx);
    }
    function abnt($ln) {
        $title = LowerCase($ln['ar_titulo_1']);
        $title = Uppercase(substr($title, 0, 1)) . substr($title, 1, strlen($title));

        $journal = $ln['jnl_nome_abrev'];
        $journal = $ln['jnl_nome'];

        $autor = troca($ln['authores_row'], chr(13), ';') . ';';
        $au = splitx(';', $autor);
        $autor = '';

        $vol = trim($ln['ed_vol']);
        $num = trim($ln['ed_nr']);
        $ano = trim($ln['ed_ano']);
        $vol = trim($ln['ed_vol']);
        $pgi = trim($ln['ar_pg_inicial']);
        $pgf = trim($ln['ar_pg_final']);
        $doi = trim($ln['ar_doi']);
        $tipo = trim($ln['jnl_tipo']);
        $link = ' Disponível em: http://hdl.handle.net/20.500.11959/brapci/'.$ln['id_ar'] . '.';
        $acesso = ' Acesso em: ' . date("d") . ' ' . msg('mes_' . date("m") . 'a') . ' ' . date("Y") . '.';
        $pag = '';

        /* Total de Autores */
        if (count($au) <= 3) {
            for ($r = 0; $r < count($au); $r++) {
                $aut = trim($au[$r]);
                if (strlen($aut) > 0) {
                    if (strlen($autor) > 0) {
                        $autor .= '; ';
                    }
                    $autor .= nbr_autor($aut, 5);
                }
            }
        } else {
                    $autor .= nbr_autor($au[0], 5);
                    $autor .= ' et al.';
        }

        /* Volume */
        if (strlen($vol) > 0) { $vol = ', v. ' . $vol;
        }
        /* Number */
        if (strlen($num) > 0) { $num = ', n. ' . $num;
        }
        /* Pagina */
        if (strlen($pgi) > 0) { $pag = ', p. ' . $pgi;
            if (strlen($pgf) > 0) { $pag .= '-' . $pgf;
            }
        }
        /* Ano */
        if (strlen($ano) > 0) { $ano = ', ' . $ano;
        }
        /* DOI */
        if (strlen($doi) > 0) {
            $doi .= '. DOI:<a href="#">' . $doi . '</a>';
            $link = '';
        } else {

        }

        switch ($tipo) {
            case 'J' :
                $sx = $autor . '. ' . trim($title) . '. <b>' . $journal.'</b>' . $vol . $num . $pag . $ano . $doi . '. '.$link.$acesso;
                $sx .= $link;
                $sx .= $acesso;
                break;
            default :
                $sx = $autor . '. ' . trim($title) . '. <b>' . $journal.'</b>' . $vol . $num . $pag . $ano . $doi . '.';
                break;
        }

        $sx = troca($sx, '.. ', '. ');

        return ($sx);
        exit ;
    }
    function show_files($id) {
        $id = strzero($id, 10);
        $sql = "select * from brapc607_base.brapci_article_suporte where bs_article = '$id' order by bs_type";
        $rlt = db_query($sql);
        $pdf = 0;
        $sx = '<table width="100%" class="tabela00">';
        $sx .= '<tr><th>archive</th></tr>';
        $idbs = 0;
        while ($line = db_read($rlt)) {
            $xlink = trim($line['bs_adress']);
            $tipo = trim($line['bs_type']);
            $link = '';
            $linkf = '';
            $ajax = '';
            if (substr($xlink, 0, 5) == '_repo') {
                $link = '<a href="' . base_url(trim($line['bs_adress'])) . '" target="_new">';
                $linkf = '</a>';
                $idbs = -1;
            }
            if (substr($xlink, 0, 4) == 'http') {
                $link = '<a href="' . trim($line['bs_adress']) . '" target="_new">';
                $linkf = '</a>';
                if ((trim($line['bs_status']) == '@') or (trim($line['bs_status']) == 'A') or (trim($line['bs_status']) == 'D')) {
                    $onclick = ' onclick="coletar('.$line['id_bs'].',\'coletar'.$line['id_bs'].'\');" ';
                    $ajax = '<div id="coletar'.$line['id_bs'].'" style="color: blue; cursor: pointer; width: 100px; border: 1px #A0A0A0 solid; text-align: center;" '.$onclick.'>coletar</div>';
                    $idbs = $line['id_bs'];
                }
                if ((trim($line['bs_status']) == 'T')) {
                    $onclick = ' onclick="converter('.$line['id_bs'].',\'converter'.$line['id_bs'].'\');" ';
                    $ajax = '<div id="converter'.$line['id_bs'].'" style="color: blue; cursor: pointer; width: 100px; border: 1px #A0A0A0 solid; text-align: center;" '.$onclick.'>converter</div>';
                    $idbs = $line['id_bs'];
                }
            }

            $sx .= '<tr>';
            $sx .= '<td>';
            $sx .= $link . trim($line['bs_adress']) . $linkf;
            $sx .= '</td>';
            $sx .= '<td>';
            $sx .= trim($line['id_bs']);
            $sx .= '</td>';
            $sx .= '<td>';
            $sx .= trim($line['bs_status']);
            $sx .= '</td>';
            $sx .= '<td>' . $ajax . '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';

        if ($idbs > 0) {
            $sx .= '
            <script>
            function coletar($id,$div) {
                $("#"+$div).html("coletando...");
                $.ajax({
                    method: "POST",
                    url: "' . base_url('index.php/oai/coletar_pdf'). '/" + $id,
                    data: { name: "OAI", location: "PDF" }
                    })
                    .done(function( data ) {
                            $("#"+$div).html(data);
                    });
            };
            function converter($id,$div) {
                $("#"+$div).html("converter...");
                $.ajax({
                    method: "POST",
                    url: "' . base_url('index.php/oai/converter'). '/" + $id,
                    data: { name: "OAI", location: "PDF" }
                    })
                    .done(function( data ) {
                            $("#"+$div).html(data);
                    });
            };
            </script>
            ';
        }
        return ($sx);
    }
    function show_cited($id = '') {
        $id = strzero($id, 10);
        $sql = "select * from brapc607_base.mar_works where m_work = '$id' order by id_m";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '<ul>' . cr();
        $admin = 0;
        if (isset($_SESSION['nivel']))
            {
                if ($_SESSION['nivel'] > 1)
                    {
                        $admin = 1;
                    }
            }

        $link = '';
        $link_a = '';

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];

            if ($admin == 1)
                {
                    $link = '<a href="#" onclick="newwin(\''.base_url('index.php/cited/ref_edit/'.$line['id_m'].'/'.checkpost_link($line['id_m'])).'\',600,800);">';
                    $link_a = '</a>';
                }
            $sx .= '<li>' . $link. $line['m_ref'] . $link_a. '</li>' . cr();
        }
        $sx .= '</ul>' . cr();
        return ($sx);
    }
    function progress_bar($status) {
        $pb = array();
        $pb[0] = array('', 'Indexação', 'Indexação do trabalho');
        $pb[1] = array('', '1º Revisão', '1º Revisão');
        $pb[2] = array('', '2º Revisão', '2º Revisão');
        $pb[3] = array('', 'Concluído', 'Concluído');

        switch ($status) {
            case 'A' :
                $pb[0][0] = '_active';
                break;
            case 'B' :
                $pb[0][0] = '_executed';
                $pb[1][0] = '_active';
                break;
            case 'C' :
                $pb[0][0] = '_executed';
                $pb[1][0] = '_executed';
                $pb[2][0] = '_active';
                break;
            case 'D' :
                $pb[0][0] = '_executed';
                $pb[1][0] = '_executed';
                $pb[2][0] = '_executed';
                $pb[3][0] = '_active';
                break;
        }

        $pb[4] = array('pbar_nr', 'Cancelado', 'Indexação do trabalho');
        $sx = '<div id="progress">';
        for ($r = 0; $r < count($pb); $r++) {
            $sx .= '
                            <div class="pbar pbar' . $pb[$r][0] . '">
                                <div class="pbar_nr' . $pb[$r][0] . '"></div>
                                <B>' . $pb[$r][1] . '</B>
                                <br><span class="lt0">' . $pb[$r][2] . '</span>
                            </div>

                        ';
        }
        $sx .= '</div>';
        return ($sx);
    }
    function actions_buttons($status,$id_ar) {
        $btns = array();
        $btns[0] = '<td><input id="bt_B" type="button" value="PARA 1º REVISÃO" class="botao3d back_green back_green_shadown" onclick="alterar_status(\'B\',\'Primeira Revisão\');"></td>';
        $btns[1] = '<td><input id="bt_C" type="button" value="PARA 2º REVISÃO" class="botao3d back_green back_green_shadown" onclick="alterar_status(\'C\',\'Segunda Revisão\');"></td>';
        $btns[2] = '<td><input id="bt_D" type="button" value="FINALIZAR" class="botao3d back_green back_green_shadown" onclick="alterar_status(\'D\',\'Finalizar Revisão\');"></td>';
        $btns[3] = '<td><input id="bt_X" type="button" value="CANCELAR TRABALHO" class="botao3d back_red back_red_shadown" onclick="alterar_status(\'X\',\'Cancelado\');"></td>';
        $btns[4] = '<td><input id="bt_A" type="button" value="REENVIAR PARA INDEXAÇÃO" class="botao3d back_blue back_blue_shadown" onclick="alterar_status(\'A\',\'Revisão da Reindexação\');"></td>';

        switch ($status) {
            case 'A' :
                $btns[1] = '';
                $btns[4] = '';
                break;
            case 'B' :
                $btns[0] = '';
                $btns[2] = '';
                break;
            case 'C' :
                $btns[0] = '';
                $btns[1] = '';
                break;
            case 'X' :
                $btns[0] = '';
                $btns[1] = '';
                break;
            case 'D' :
                $btns[0] = '';
                $btns[1] = '';
                $btns[2] = '';
                $btns[3] = '';
                break;
        }

        $sx = '
                <table cellspacing=10 class="border01">
                    <tr>
                    <td colspan=3>Enviar para:
                    <tr>' . $btns[0] . $btns[1] . $btns[2] . $btns[3] . $btns[4] . '</tr>
                </table>';

        $sx .= '
                <script>
                    function alterar_status(sta,stb)
                        {
                            rsp = confirm(\'Confirmar alteração do estatus para \' + stb);
                            if (rsp)
                                {
                                    window.location.assign("' . base_url('index.php/admin/article_view/' . $id_ar . '/' . checkpost_link($id_ar)) . '/"+sta);
                                }
                        }
                    </script>
                    ';
        return ($sx);
    }
}
?>
