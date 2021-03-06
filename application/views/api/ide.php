<!doctype html>
<head>
	<head lang="pt-br">
		<meta charset="utf-8">
		<title>API Brapci - Base de Dados em Ciência da Informação</title>    
		<META NAME="title" CONTENT="Brapci - Base de Dados em Ciência da Informação - API">
		<META NAME="url" CONTENT="http://www.brapci.inf.br/">
		<META NAME="description" CONTENT="API para organização da informação">

		<META NAME="keywords" CONTENT="artigos científicos, revistas científicas, ciência da informação, biblioteconomia, arquivologia">
		<META NAME="copyright" CONTENT="Brapci">
		<LINK REV=made href="brapcici@gmail.com">
		<META NAME="language" CONTENT="Portugues">
		<META NAME="Robots" content="All">
		<META NAME="City" content="Curitiba/Porto Alegre">
		<META NAME="State" content="PR - Paraná / RS - Rio Grande do Sul">
		<META NAME="revisit-after" CONTENT="365 days">
		<META HTTP-EQUIV="Content-Language" CONTENT="pt_BR">
		<link href="https://fonts.googleapis.com/css?family=Anonymous+Pro&display=swap" rel="stylesheet">    

		<link rel="icon" href="<?php echo base_url('img/favicon.png');?>" type="image/x-icon" />
		<link rel="shortcut icon" href="<?php echo base_url('img/favicon.png');?>" type="image/x-icon" />    

		<!--- CSS --->
		<link href="<?php echo base_url('css/bootstrap.min.css?v4.0');?>" rel="stylesheet">
		<link href="<?php echo base_url('css/style.css?v0.3');?>" rel="stylesheet">
		<link href="<?php echo base_url('css/jquery-ui.css?v1.12.1');?>" rel="stylesheet">

		<!--- JS ---->
		<script src="<?php echo base_url('js/jquery-3.3.1.min.js?v3.3.1');?>"></script>
		<script src="<?php echo base_url('js/bootstrap.min.js?v4.0');?>"></script>
		<script src="<?php echo base_url('js/jquery-ui.js?v1.12.1');?>"></script>
		<script src="<?php echo base_url('js/sisdoc_form.js?v1.1.1');?>"></script>
	</head>
	<style>
		body {
			margin-top: 200px;
			font-family: "Anonymous Pro";
		}

		.logo {
			height: 40px;
			width: 50px;
		}

	</style>	

	<body>
		<nav class="navbar navbar-expand-lg fixed-top navbar-light bg-light">
			<a href="<?php echo base_url('index.php/api');?>"><img src="<?php echo base_url('img/logo/logo-brapci.png');?>" id="logo" style="height: 30px;" border=0></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse fsz" id="navbarSupportedContent">
				<ul class="navbar-nav ml-auto">
					<!----------------------- ADMIN ---------------------->


					<li class="nav-item active">
						<a class="nav-link fsz " href="<?php echo base_url('index.php/api');?>">home</span></a>
					</li>                        
				</ul>
			</div>
		</nav>

		<div class="container-fluid">
			<div class="row">
				<div class="table-of-contents"><?php include("ide_apis.php");?></div>
				<div class="col-md-8">
					<?php require("api_nlp.php");?>
					<?php require("api_genere.php");?>
				</div>
				<div class="col-md-2">

				</div>
			</div>
		</div>

		<br><br><br><br><br><br><br>



		<style>
			.table-of-contents {
				display: table-cell;
				width: 320px;
				height: 100%;
				background: #eee;
				border-right: 1px solid #ececec;
				padding-left: 20px;
				padding-top: 40px;
				z-index: 2;
			}
		</style>