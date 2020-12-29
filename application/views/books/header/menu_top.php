<style>
body {
	margin-top: 100px;
	background-color: #4C1304;
	font-size: 30px;
	color: white;
}
/***** Wood */
.wood
	{
		/* background-image: url('<?php echo base_url('img/books/bg.jpg');?>'); */
		/*		
		min-height: 800px;
		background-size: 700px;
		*/
	}
/****************** NAV BAR ***************/
.navbar {
	background-color: #4C1304;
	z-index: 10;
	padding: 0px;
    margin: auto;
    display: block;
    position: absolute;
    bottom: 0;	
}
.navbar:hover {
	background: #4C1304;
}
.nav-link
	{
		color:#AC8364;
	}
.nav-link:hover
	{
		color:#FCE3E4;
	}	
/**************** LARGE */
.fsz-lg {
	font-size: 40px;
}
.logo-lg {
	height: 40px;
}

	
/****************** SMALL *********/
.fsz {
	font-size: 30px;
}
#logo {
	height: 80px;
    margin: auto;
    display: block;
    position: absolute;
    bottom: 0;
}
.navbar-wood
{
	background-color: #4C1304;	
}
/*
@media (max-width: 1024px) {
	.col-lg-0 {
		display: none;
	}
	.btn-toggle {
		display: block;
	}
	*/
}
</style>
<img width="200" height="200" src="https://brapci.inf.br/img/brapci_200x200.png" class="custom-logo" alt="Brapci" srcset="https://brapci.inf.br/img/brapci_200x200.png 150w" sizes="(max-width: 200px) 100vw, 200px" style="display: none;">


<div class="container-fluid">
<div class="row">
<div class="col-md-4 bgbooks">
<img src="<?php echo base_url('img/books/header_brapci_livros.png'); ?>" id="logo" class="logo-lg col-lg-0 navbar-brand">
</div>

<div class="col-md-8">
<nav class="navbar navbar-expand-lg ">
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarSupportedContent">
<ul class="navbar-nav mr-auto">
<li class="nav-item">
<a class="nav-link" href="#">HOME</a>
</li>
<li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		SEÇÕES
	</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">
	<a class="dropdown-item" href="#">BIBLIOTECONOMIA</a>
	<a class="dropdown-item" href="#">ARQUIVOLOGIA</a>
	<a class="dropdown-item" href="#">MUSEOLOGIA</a>
</div>
</li>
<li class="nav-item">
	<a class="nav-link" href="<?php echo base_url(PATH.'book_register');?>">DIVULGUE SUA OBRA</a>
</li>	  
</ul>

</div>
</nav>
</div>
<div class="col-md-12">
<img src="<?php echo base_url('img/books/bg_top_carusel.png');?>" style="width: 100%;">
</div>
</div>
</div>
