<?php
$tit = array();
$key['pt'] = '';
$key['en'] = '';
$key['es'] = '';
$key['fr'] = '';
$section = '';
$source = '';
$author = '';
$journal = '';
$pdf = '<img src="'.base_url('img/icone/icone_pdf.png').'" class="img-fluid">';
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
        case 'isPubishIn':
            $journal = $value;
            break;
        case 'hasAuthor' :
            $link = '<a href="'.base_url(PATH.'v/'.$d_r2).'">';
            $linka = '</a>';
            if (strlen($author) > 0) { $author .= '; '; }
            $author .= $link . $value . $linka;
            break;
        case '':
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
            $link = '<a href="'.base_url(PATH.'v/'.$d_r2).'">';
            $linka = '</a>';
            
            if (isset($key[$langM])) {
                $key[$langM] .= $link.$value.$linka . '. ';
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
        <p><?php echo $author;?></p>
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
		?>
	</div>
	<div class="col-2 text-justify">
	      <?php
	      echo $pdf;
	      ?>
	</div>

</div>
