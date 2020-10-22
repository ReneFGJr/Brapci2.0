<?php

function breadcrumb()
{
	$path = $_SERVER['REQUEST_URI'];
	$a = array(0,1,2,3,4,5,6,7,8,9);
	for ($r=0;$r < count($a);$r++)
	{
		$path = troca($path,$a[$r],'');
	}
	
	/****************************************************************/
	$sx = '
	<div class="row">
	<nav aria-label="breadcrumb" style="margin-top: 20px;">
	<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="'.base_url(PATH.'/').'">Home</a></li>'.cr();
	
	/*************************************** Monta estrutura *********/
	$path = substr($path,strpos($path,'main/')+5,strlen($path)).'/';
	$ph = '';
	while (strpos(' '.$path,'/') > 0)
	{
		$link = substr($path,0,strpos($path,'/'));
		$ph .= $link.'/';
		if ((trim($link) != '/') and (strlen($link) > 0))
		{
			$sx .= '<li class="breadcrumb-item"><a href="'.base_url(PATH.$ph).'">'.msg($link).'</a></li>'.cr();
		}
		$path = substr($path,strpos($path,'/')+1,strlen($path));
	}
	/*****************************************************************/
	$sx .= '</ol></nav>
	</div>
	</div>
	';
	return($sx);
}

/**************** IMAGEMS ************************************************/
function image_resize($file, $w, $h, $crop=FALSE) {
	list($width, $height) = getimagesize($file);
	$r = $width / $height;
	if ($crop) {
		if ($width > $height) {
			$width = ceil($width-($width*abs($r-$w/$h)));
		} else {
			$height = ceil($height-($height*abs($r-$w/$h)));
		}
		$newwidth = $w;
		$newheight = $h;
	} else {
		if ($w/$h > $r) {
			$newwidth = $h*$r;
			$newheight = $h;
		} else {
			$newheight = $w/$r;
			$newwidth = $w;
		}
	}
	$src = imagecreatefromjpeg($file);
	$dst = imagecreatetruecolor($newwidth, $newheight);
	imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
	imagejpeg($dst, $file, 100);
	imagedestroy($dst);
	return 1;
} 
function png2jpg($originalFile, $outputFile, $quality) {
	$image = imagecreatefrompng($originalFile);
	imagejpeg($image, $outputFile, $quality);
	imagedestroy($image);
}

function fls($dir,$tp='')
{
	$files = scandir($dir);
	$f = array();
	foreach($files as $id => $file)
	{
		$fls = $dir.'/'.$file;
		if (is_dir($fls))
		{
			if (($tp == '') or ($tp == 'D'))
			{
				array_push($f,array($dir,$file,$fls,'D'));
			}
		} else {
			if (file_exists($fls))
			{
				if (($tp == '') or ($tp == 'F'))
				{
					array_push($f,array($dir,$file,$fls,'F'));
				}
			} else {
				echo "OPS ".$fls;
			}    
		}
	}
	return($f);
}

function menu($m)
{
	if (isset($m['title'])) 
	{ $title = $m['title']; } else { $title = 'MENU'; }
	$sx = '
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<a class="navbar-brand" href="'.base_url(PATH).'">'.$title.'</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#conteudoNavbarSuportado" aria-controls="conteudoNavbarSuportado" aria-expanded="false" aria-label="Alterna navegação">
	<span class="navbar-toggler-icon"></span>
	</button>
	
	<div class="collapse navbar-collapse" id="conteudoNavbarSuportado">
	<ul class="navbar-nav mr-auto">';
	if (isset($m['i']))
	{
		foreach($m['i'] as $txt => $link)
		{
			if (is_array($link))
			{
				$sx .= '<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				'.$txt.'
				</a>';
				$sx .= '<div class="dropdown-menu" aria-labelledby="navbarDropdown">';
				foreach($link as $txt2 => $link2)
				{
					$sx .= '
					<a class="dropdown-item" href="'.$link2.'">'.$txt2.'</a>'.cr();
				}
				$sx .= '</div></li>'.cr();
			} else {
				$sx .= '<li class="nav-item">
				<a class="nav-link" href="'.$link.'">'.msg($txt).'</a>
				</li>'.cr();
			}
		}
	}	
	$sx .= '</ul>';
	
	if (isset($m['s']))
	{
		$sx .= '<form class="form-inline my-2 my-lg-0" action="'.$m['s'].'">
		<input class="form-control mr-sm-2" type="search" placeholder="Pesquisar" aria-label="'.msg("Search").'">
		<button class="btn btn-outline-success my-2 my-sm-0" type="submit">'.msg('Search').'</button>
		</form>';
	}
	$sx .= '</div></nav>';
	return($sx);
}

function read_csv($file,$delim=';')
	{
		$row = 0;
		$ds = array();
		if (($handle = fopen($file, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $delim)) !== FALSE) {
				$num = count($data);
				$row++;
				for ($c=0; $c < $num; $c++) {
					$ds[$row][$c] = utf8_encode($data[$c]);
				}
			}
			fclose($handle);
		}
		return($ds);		
	}
function read_csv_old($file='')
{
    $sht = array();
    $row = 0;
    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);		
            for ($c=0; $c < $num; $c++) {
                $sht[$row][$c] = $data[$c];
            }
            $row++;
        }
        fclose($handle);
    }
    return($sht);
}

function upload_file()
{
    $txt = '
    <!-- O tipo de encoding de dados, enctype, DEVE ser especificado abaixo -->
    <form enctype="multipart/form-data" method="POST">
    <!-- MAX_FILE_SIZE deve preceder o campo input -->
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000000" />
    <!-- O Nome do elemento input determina o nome da array $_FILES -->
    Enviar esse arquivo: <input name="userfile" type="file" />
    <input type="submit" value="Enviar arquivo" />
    </form>
    ';
    return($txt);
}
?>