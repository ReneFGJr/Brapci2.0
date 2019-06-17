<!doctype html>
<?php
if (!isset($title)) { $title = 'no title';
}
$HTTP = 'http://www.brapci.inf.br/';

$title = 'Brapci - Base de Dados em Ciência da Informação';
$descr = 'Base de dados de Periódicos em Ciência da Informação publicadas no Brasil desde 1972.';
$url = 'http://www.brapci.inf.br/';
$sufix = ' #Brapci2.0';
$author = '';
$keys = 'artigos científicos, revistas científicas, ciência da informação, biblioteconomia, arquivologia';
if (isset($meta))
	{
		$url = HTTP.PATH.'v/'.$id;
		$keys = '';
		for ($r=0;$r < count($meta);$r++)
			{
				$line = $meta[$r];
				$class = trim($line['c_class']);
				$value = trim($line['n_name']);
				//echo '<br>'.$class.'==>'.$value;
				switch($class)
					{
					case 'prefLabel':
						$title = $value.' '.$sufix;
						break;						
					case 'hasTitle':
						$title = $value.' '.$sufix;
						break;
					case 'hasAuthor':
						$author = '    <META NAME="author" CONTENT="'.$value.'">'.cr();
						break;
					case 'hasAbstract':
						$descr = $value;
						break;
					case 'hasSubject':
						if (strlen($keys) > 0)
							{
								$keys .= ', ';
							}
						$keys .= $value;
					}
			}
	}

?>
<head>
    <head lang="pt-br">
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>    
    <META NAME="title" CONTENT="<?php echo $title;?>">
    <META NAME="url" CONTENT="<?php echo $url;?>">
    <META NAME="description" CONTENT="<?php echo $descr;?>">
<?php echo $author;?>
    
    <META NAME="keywords" CONTENT="<?php echo $keys;?>">
    <META NAME="copyright" CONTENT="Brapci">
    <LINK REV=made href="brapcici@gmail.com">
    <META NAME="language" CONTENT="Portugues">
    <META NAME="Robots" content="All">
    <META NAME="City" content="Curitiba/Porto Alegre">
    <META NAME="State" content="PR - Paraná / RS - Rio Grande do Sul">
    <META NAME="revisit-after" CONTENT="365 days">
    <META HTTP-EQUIV="Content-Language" CONTENT="pt_BR">
    <meta name="google-site-verification" content="VZpzNVBfl5kOEtr9Upjmed96smfsO9p4N79DZT38toA" />
    
    <link rel="icon" href="<?php echo $HTTP.'img/favicon.png';?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $HTTP.'img/favicon.png';?>" type="image/x-icon" />    
    
    <!--- CSS --->
    <link href="<?php echo base_url('css/bootstrap.min.css?v4.0'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('css/style.css?v0.3'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('css/jquery-ui.css?v1.12.1'); ?>" rel="stylesheet">
    
    <!--- JS ---->
    <script src="<?php echo base_url('js/jquery-3.3.1.min.js?v3.3.1'); ?>"></script>
    <script src="<?php echo base_url('js/bootstrap.min.js?v4.0'); ?>"></script>
    <script src="<?php echo base_url('js/jquery-ui.js?v1.12.1'); ?>"></script>
    <script src="<?php echo base_url('js/sisdoc_form.js?v1.1.1'); ?>"></script>
    
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
      (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-4492401449039504",
        enable_page_level_ads: true
      });
    </script> 
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
      ga('create', 'UA-12713129-1', 'auto');
      ga('send', 'pageview');
    </script>
       
<!---    
<script>
	(function(i, s, o, g, r, a, m) {
		i['GoogleAnalyticsObject'] = r;
		i[r] = i[r] ||
		function() {
			(i[r].q = i[r].q || []).push(arguments)
		}, i[r].l = 1 * new Date();
		a = s.createElement(o),
		m = s.getElementsByTagName(o)[0];
		a.async = 1;
		a.src = g;
		m.parentNode.insertBefore(a, m)
	})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

	ga('create', 'UA-12713129-1', 'auto');
	ga('send', 'pageview'); 
</script>
--->   
</head>
<body>