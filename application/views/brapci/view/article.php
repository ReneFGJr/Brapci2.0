<?php
$tit = array();
$key['pt'] = '';
$key['en'] = '';
$key['es'] = '';
$key['fr'] = '';
$disponivel_em = '';
$section = '';
$source = '';
$author = '';
$authores = array();
$journal = '';
$idurl = '';
$pdf = '<img src="' . base_url('img/icone/icone_pdf_off.png') . '" class="img-fluid" title="'.msg('PDF not avaliable').'">';
$linkpdf = '<a href="#">';
$linkpdfa = '</a>';
$epdf = 0;
$pg_first = '';
$pg_last = '';
$doi = '';
if (!isset($social))
    {
        $social = '';
    }
if (!isset($cited))
    {
        $cited = '';
    }	

for ($r = 0; $r < count($article); $r++) {
	$l = $article[$r];
	$class = $l['c_class'];
	$value = $l['n_name'];
	$lang = $l['n_lang'];
	$langM = substr($l['n_lang'], 0, 2);
	$d_r1 = $l['d_r1'];
	$d_r2 = $l['d_r2'];
	//echo '<br>' . $class.' ==> '.$value;

	switch(trim($class)) {
		case 'dateOfAvailability':
			$disponivel_em = $value;
			break;
		case 'hasRegisterId':
			if (substr($value,0,2) == '10') { $doi = $value; }
			break;
		case 'hasPageEnd':
			$pg_first = $value;
			break;
		case 'hasPageStart':
			$pg_last = $value;
			break;
		case 'hasFileStorage':
			$pdf = '<img src="' . base_url('img/icone/icone_pdf.png') . '" class="img-fluid">';
			$linkpdf = '<span onclick="newxy2(\''.base_url(PATH.'download/'.$d_r2).'\',1024,800);" target="_new" style="cursor: pointer;">';
			$linkpdfa = '</span>';
			$epdf = 1;
			break;
		case 'hasPDF' :
			$pdf = '<img src="' . base_url('img/icone/icone_pdf.png') . '" class="img-fluid">';
			$linkpdf = '<a href="#">';
			$linkpdfa = '</a>';
			break;
		case 'prefLabel' :
			$idurl = $value;
			break;
		case 'isPubishIn' :
			$journal = $value;
			break;
		case 'hasAuthor' :
			$link = '<a href="' . base_url(PATH . 'v/' . $d_r2) . '">';
			$linka = '</a>';
			if (strlen($author) > 0) { $author .= '; ';
			}
			$author .= $link . $value . $linka;
			array_push($authores,$value);
			break;
		case '' :
			break;
		case 'hasSource' :
			if (strlen($source) == 0) {
				$source = $value;
			}
			break;
		case 'hasSectionOf' :
			$section = $value;
			break;
		case 'hasTitle' :
			array_push($tit, $value);
			break;
		case 'hasSubject' :
			$link = '<a href="' . base_url(PATH . 'v/' . $d_r2) . '">';
			$linka = '</a>';

			if (isset($key[$langM])) {
				$key[$langM] .= $link . $value . $linka . '. ';
			}
			break;
		case 'hasAbstract' :
			if (substr($lang, 0, 2) == 'pt') {
				$abs['pt'] = $value;
			}
			if (substr($lang, 0, 2) == 'en') {
				$abs['en'] = $value;
			}
			break;
	}
}

?>
</div>
<header>
    <title><?php echo $title;?></title>
    <?php require("article_metadata.php"); ?>
</header>
<div class="row">
	<div class="col-8">
		[<?php echo $source; ?>]
	</div>
	<div class="col-4 btn btn-primary">
		<?php echo $section; ?>
	</div>
</div>

<div class="row" style="margin-top: 40px;">
	<div class="col-10">
	    <?php
	       if (perfil("#ADMIN") > 0)
            {
            echo 'ADMIN Menu | <a href="'.base_url(PATH.'concept_del/'.$id.'/'.checkpost_link($id.'Concept')).'" onClick="return confirm(\''.msg('confirm_exclud_article').'\')" class="btn btn-warning">'.msg('del_article').'</a>';
            }
        ?>
	    
		<center>
			<?php
			for ($r = 0; $r < count($tit); $r++) {
				echo '<span class="article_title_' . $r . ' text-center">' . $tit[$r] . '</span>' . cr();
				echo '<br>' . cr();
				echo '<br>' . cr();
			}
			?>
		</center>
	</div>
	<div class="col-10 text-right article_author">
		<p>
			<?php echo $author; ?>
		</p>
	</div>
	<div class="col-10 text-justify">
		<?php
		if (isset($abs['pt'])) {
			echo '<p><b>Resumo</b>: ' . $abs['pt'] . '</p>' . cr();
			if (isset($key['pt'])) {
				echo '<p><b>Palavras-chave</b>: ' . $key['pt'] . '</p>';
			}
			echo '<br>' . cr();
			echo '<br>' . cr();
		}

		if (isset($abs['en'])) {
			echo '<p><b>Abstract</b>: ' . $abs['en'] . '</p>' . cr();
			if (isset($key['en'])) {
				echo '<p><b>Keywords</b>: ' . $key['en'] . '</p>';
			}

			echo '<br>' . cr();
			echo '<br>' . cr();
		}
		echo '<div>'.msg('how_cite').'<br>'.$cited.'</div>';
		echo '<br>';
		echo '<div>'.msg('how_sharing').'<br>'.$social.'</div>';
		?>
	</div>
	<div class="col-2 text-justify">
		<?php
		echo $linkpdf . $pdf . $linkpdfa;
		
		//if (perfil("#ADM") and ($epdf == 0))
        if (perfil("#ADM"))
			{
				echo '<br><br><div id="download" class="text-center" style="width:100%;">';
				echo '<a href="#" onclick="newxy2(\''.base_url(PATH.'pdf_upload/'.$d_r1).'\',800,400);">';
				echo 'UPLOAD';
				echo '</a>';
				echo ' - ';
				echo '<a href="#" onclick="newxy2(\''.base_url(PATH.'pdf_download/'.$d_r1).'\',800,400);">';
				echo 'LOAD...';
				echo '</a>';
				echo '</div>';
			}
		echo '<br><br>';
		echo $altmetrics;
		echo $plum;
		echo '<hr>'.$ia;
		
		?>
	</div>
</div>
