<style>
	body {
		margin-top: 200px;
	}
	/****************** NAV BAR ***************/
	.navbar {
		box-shadow: 0 4px 20px -4px #ababab;
		background: rgba(249, 249, 249, .8);
		transition: background .8s ease;
		z-index: 10;
	}

	.navbar:hover {
		background: #C0D6FF;
	}
	/**************** LARGE */
	.fsz-lg {
		font-size: 50px;
	}
	.logo-lg {
		height: 100px;
	}
	/****************** SMALL *********/
	.fsz {
		font-size: 40px;
	}
	.logo {
		height: 40px;
		width: 200px;
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
<nav class="navbar navbar-expand-lg fixed-top navbar-light bg-light">
  <a href="#"><img src="<?php echo base_url('img/logo/logo-brapci.png'); ?>" id="logo" class="logo-lg col-lg-0 navbar-brand" border=0></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse fsz" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url(PATH); ?>">&nbsp;<?php echo msg('home'); ?>&nbsp;<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo base_url(PATH . 'about'); ?>">&nbsp;<?php echo msg('about'); ?>&nbsp;</a>
      </li>
				<li class="nav-item">
					<a class="nav-link fsz " href="<?php echo base_url(PATH . 'indice'); ?>">&nbsp;<?php echo msg('indexs'); ?>&nbsp;</a>
				</li>      
			
				<li class="nav-item dropdown">
					<a class="nav-link fsz dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&nbsp;<?php echo msg('tools'); ?>&nbsp;</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="<?php echo base_url(PATH . 'journals'); ?>"><?php echo msg('journals'); ?></a>
						<a class="dropdown-item" href="#">Another action</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="<?php echo base_url(PATH . 'export'); ?>"><?php echo msg('export'); ?></a>
					</div>
				</li>

    </ul>
    <form class="form-inline my-2 my-lg-0">
		<form class="form-inline my-2 my-lg-0">
		<a class="dropdown-item" href="<?php echo base_url(PATH . 'social/login'); ?>"><?php echo msg('signin'); ?></a>
    </form>
  </div>
</nav>

<script type="text/javascript">
	var $menu_size = 0;

	$(document).on("scroll", function() {
		if ($(document).scrollTop() > 100) {//QUANDO O SCROLL PASSAR DOS 100px DO TOPO
			if ($menu_size == 0) {
				$("#logo").switchClass("logo-lg", "logo", 1000, "easeInOutQuad");
				$("header").switchClass("menu_large", "menu_small", 1000, "easeInOutQuad");
				$(".fsz").animate({
					fontSize : "20px"
				}, 1000);
				$menu_size = 1;
			}
			//TROCA P CLASSE MENOR
		} else {
			if ($menu_size == 1) {
				$("header").switchClass("menu_small", "menu_large", 1000, "easeInOutQuad");
				$("#logo").switchClass("logo", "logo-lg", 1000, "easeInOutQuad");
				$(".fsz").animate({
					fontSize : "40px"
				}, 1000);
				$menu_size = 0;
			}
		}
	}); 
</script>