
<div class="row">
	<div class="col-md-12 text-center">
		<h2><?php echo $ar_titulo_1; ?></h2>
	</div>
	<div class="col-md-12 text-center">
		<h4><i><?php echo $ar_titulo_2; ?></i></h4>
	</div>
	<div class="col-md-12 text-center">
		<h4><i><?php echo $ar_titulo_3; ?></i></h4>
	</div>	
</div>

<div class="row">
	<div class="col-md-12 text-right big">
		<b><?php echo mst($authores_row); ?></b>
	</div>
</div>
<hr>
<?php
echo '<div class="row"><div class="col-md-10 col-xs-10 text-justify">';
if (strlen($ar_resumo_1) > 0) {
	$title_abstract_1 = show_abastract_name($ar_idioma_1);
	$title_key_1 = show_key_name($ar_idioma_1);
	echo $title_abstract_1 . $ar_resumo_1 . cr();
	echo '<br>' . $title_key_1 . $ar_keyw_1 . cr();
	echo '<br>';
}

if (strlen($ar_resumo_2) > 0) {
	$title_abstract_2 = show_abastract_name($ar_idioma_2);
	$title_key_2 = show_key_name($ar_idioma_2);
	echo '<br>';
	echo $title_abstract_2 . $ar_resumo_2 . cr();
	echo '<br>' . $title_key_2 . $ar_keyw_2 . cr();
	echo '<br>';
}

if (strlen($ar_resumo_3) > 0) {
	$title_abstract_3 = show_abastract_name($ar_idioma_3);
	$title_key_3 = show_key_name($ar_idioma_3);
	echo '<br>';
	echo $title_abstract_3 . $ar_resumo_3 . cr();
	echo '<br>' . $title_key_3 . $ar_keyw_3 . cr();
	echo '<br>';
}
echo '</div>' . cr();

echo '<div class="col-md-2 col-xs-2 text-right">';
if (strlen($link_pdf)) {
	echo '<span onclick="newwin(\'' . ($link_pdf) . '\');" style="cursor: pointer;">';
	echo '<img src="' . base_url('img/icon/icone_pdf.png') . '" class="img-responsive" style="padding: 10px;" align="right">';
	echo '</span>';
}

/* Links externos */
for ($r = 0; $r < count($links); $r++) {
	$type = trim($links[$r]['bs_type']);
	$status = trim($links[$r]['bs_status']);
	if (($type == 'URL') and ($status != 'X')) {
		$link = '<a href="' . $links[$r]['bs_adress'] . '" target="_blank">' . msg('view_source') . '</a><br>';
		echo $link;
	}
}

/********* GOOGLE *********/
$link_google = '<BR><a href="https://www.google.com.br/search?q='.$ar_titulo_1.'" target="_new_'.date("YmdHis").'">Google</a>';
echo $link_google;

/********* OAI ID *********/
if (strlen($ar_oai_id) > 0)
    {
        $link_oai_id = '<BR><a href="'.base_url('index.php/oai/reharvesting/'.$id_ar).'">OAI-ID</a>';
        echo $link_oai_id;
    }

if (perfil("#BIB") == 1) {
	echo '<div>';
	echo '<br>';
	echo 'Tools<br>';
	echo '<a href="' . base_url("index.php/admin/article_change/" . round($id_ar) . '/' . checkpost_link(round($id_ar))) . '" title="' . msg("change_language") . '">';
	echo '<span class="glyphicon glyphicon-refresh superbig" aria-hidden="true"></span>';
	echo '</a>';
	echo '</div>';
}
echo '</div>' . cr();
echo '</div>' . cr();

function show_abastract_name($idioma = '') {
	switch($idioma) {
		case 'es' :
			$sx = '<b>Resumen</b><br>';
			break;
		case 'en' :
			$sx = '<b>Abstract</b><br>';
			break;
		case 'pt_BR' :
			$sx = '<b>Resumo</b><br>';
			break;
		default :
			$sx = '';
			break;
	}
	return ($sx);
}

function show_key_name($idioma = '') {
	switch($idioma) {
		case 'es' :
			$sx = '<b>Palabras clave:</b> ';
			break;
		case 'en' :
			$sx = '<b>Keywords:</b> ';
			break;
		case 'pt_BR' :
			$sx = '<b>Palavras-chave:</b> ';
			break;
		default :
			$sx = '';
			break;
	}
	return ($sx);
}
?>