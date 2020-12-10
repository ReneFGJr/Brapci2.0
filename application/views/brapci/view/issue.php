<?php

$title = '';
$year = '';
$nr = '';
$vol = '';
$article = array();
$art = '';
$nx = '';
for ($r = 0; $r < count($issue); $r++) {
    $l = $issue[$r];
    //print_r($l);
    //echo '<hr>';
    $class = $l['c_class'];
    $value = $l['n_name'];
    $lang = $l['n_lang'];
    $langM = substr($l['n_lang'], 0, 2);
    $d_r1 = $l['d_r1'];
    $d_r2 = $l['d_r2'];

    //echo '<br>' . $class . ' ==> ' . $value;

    switch(trim($class)) {
        case 'altLabel' :
            $title = $value;
            break;
        case 'dateOfPublication' :
            $year = $value;
            break;
        case 'hasPublicationNumber' :
            $nr = $value;
            break;
        case 'hasPublicationVolume' :
            $vol = $value;
            break;
        case 'hasIssueOf' : 
            
            if ($d_r2 > 0) {
                $filex = 'c/' . $d_r2 . '/name.nm';
                $n = $value;
                if (file_exists($filex) and ($d_r2 > 0)) {
                    $n = load_file_local($filex);
                } else {
                    $link = '<a href="'.base_url(PATH.'/v/'.$d_r2).'">';
                    $n = $link.$n.'</a>';
                }
                /* CITED */
                if (perfil("#ADM#CAT"))
                {
                    $file_cited = 'c/'.$d_r2.'/cited.total';
                    if (file_exists(($file_cited)))
                    {
                        $cited = file_get_contents($file_cited);
                        $n .= ' <span class="btn-secondary" style="border-radius: 5px;">&nbsp;'.$cited.'&nbsp;</span>';
                    } else {
                        $n .= ' (sn)';
                    }
                }
                if ($nx != $n)
                {
                    array_push($article, $n);
                }
                $nx = $n;
            }
            break;
    }
}
?>
</div>
<header>
    <title><?php echo $title; ?></title>
</header>
<div class="container">
<div class="row">
	<div class="col-8">
		[<?php echo $title; ?>]
	</div>
	<div class="col-4 btn btn-primary">
		<?php echo $vol . ' ' . $nr . ' ' . $year; ?>
	</div>
</div>

<div class="row" style="margin-top: 40px;">
    <div class="col-3">
        Issues
    </div>
	<div class="col-9">
        <?php
        for ($r = 0; $r < count($article); $r++) {
            $link = '';
            echo $link.$article[$r] .'<hr>';            
        }
        ?>
        
        <?php
        if (perfil("#ADM#CAT")==1)
        {            
            echo '<a href="'.base_url(PATH.'article_new/'.$id).'" class="btn btn-primary">'.msg('new_article').'</a>';
            echo '<br>';
            echo '<br>';
        }
        ?>
	</div>
</div>
</div>