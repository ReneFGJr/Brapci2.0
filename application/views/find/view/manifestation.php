<?php
$date = '';
$editora = '';
$local = '';
$isbn = '';
$edicao = '';
$localizacao = '';

if ($hd == 0) {
    $img = '<img src="' . base_url('img/no_cover.png') . '" height="40">';
    $w = $id;
    $link = '<a href="' . base_url(PATH.'a/' . $id) . '">';
}
{
    for ($r = 0; $r < count($rlt); $r++) {
        $line = $rlt[$r];
        $class = $line['c_class'];
        //echo '<br>'.$class.'='.$line['n_name'];
        switch($class) {
            case 'hasClassificationCDU' :
                $link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                $linka = '</a>';
                $localizacao = $link . 'CDU' . $line['n_name'] . $linka;
                break;
            case 'hasClassificationCDD' :
                $link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                $linka = '</a>';
                $localizacao = $link . 'CDD' . $line['n_name'] . $linka;
                break;
            case 'dateOfPublication' :
                if (strlen($date) > 0) {
                    $date .= '; ';
                }
                $date .= '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                $date .= trim($line['n_name']);
                $date .= '</a>';
                break;
            case 'isPlaceOfPublication' :
                $link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                if (strlen($local) > 0) {
                    $local .= $link . trim($line['n_name']) . '</a>';
                } else {
                    $local .= $link . trim($line['n_name']) . '</a>';
                }
                break;
            case 'isPublisher' :
                if (strlen($editora) > 0) {
                    $editora .= '; ';
                }
                $editora .= '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                $editora .= trim($line['n_name']);
                $editora .= '</a>';
                break;
            case 'hasISBN' :
                $link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                if (strlen($isbn) > 0) {
                    $isbn .= '; ';
                }
                $isbn = $link . trim($line['n_name']) . '</a>';
                break;
            case 'isEdition' :
                $link = '<a href="' . base_url(PATH.'a/' . $line['d_r2']) . '">';
                if (strlen($edicao) > 0) {
                    $edicao .= ', ';
                }
                $edicao .= $link . trim($line['n_name']) . '</a>';
                break;
            case 'hasCover' :
                $img = $this -> frbr -> mostra_imagem($line['d_r2']);
                break;
        }
    }
    /* regras */
    if (strlen($local) == 0) { $local = ': Sem local';
    }

    if (strlen($localizacao) == 0) { $localizacao = '';
    }

    if ($hd == 0) {
        echo '<div class="col-md-2 text-right" style="border-right: 4px solid #8080FF;">' . cr();
        echo '<tt style="font-size: 100%;">' . msg('ManifestationLabel') . '</tt>' . cr();
        echo '</div>' . cr();
    }
}
?>
<!---------------- MANIFESTATION ------------------------------------------------------->

<div class="col-md-2" style="font-size: 85%; border: 0px solid #000000;">
	<?php echo $img; ?>
	<br>
	<?php
    if (strlen($edicao) > 0) {
        echo $edicao . ' - ';
    }
    if (strlen($editora . $local) > 0) {
        echo $local . ': ' . $editora . ', ';
    }
    if (strlen($date) > 0) {
        echo $date;
    }
    if (strlen($edicao . $editora . $date) > 0) {
        echo '. ';
    }
    if (strlen($isbn) > 0) {
        echo '<br>' . $isbn . '.';
    }
	?>
	<br>
	<?php echo $localizacao; ?>
	<br>
	<?php if (perfil("#ADM")) {
	       echo '<a href="'.base_url(PATH.'a/' . $id).'" class="btn btn-secondary">editar</a>';
	}
	?>
</div>
