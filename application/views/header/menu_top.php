<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<header>
	<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
		MOOC
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item active">
					<a class="nav-link fsz " href="<?php echo base_url(PATH); ?>">&nbsp;<?php echo msg('home'); ?>&nbsp;<span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link fsz " href="<?php echo base_url(PATH . 'about'); ?>">&nbsp;<?php echo msg('about'); ?>&nbsp;</a>
				</li>
				<li class="nav-item">
					<a class="nav-link fsz " href="<?php echo base_url(PATH . 'courses'); ?>">&nbsp;<?php echo msg('courses'); ?>&nbsp;</a>
				</li>				
				<li class="nav-item dropdown">
					<a class="nav-link fsz dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">&nbsp;<?php echo msg('tools'); ?>&nbsp;</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="#">Action</a>
						<a class="dropdown-item" href="#">Another action</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">Something else here</a>
					</div>
				</li>
                <li class="nav-item active">
                    <a class="nav-link fsz " href="<?php echo base_url(PATH); ?>social/signin">&nbsp;<b><?php echo msg('signin'); ?></b>&nbsp;</span></a>
                </li>				
			</ul>
			<!---
			<form class="form-inline my-2 my-lg-0">
			<input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
			<button class="btn btn-outline-success my-2 my-sm-0" type="submit">
			Search
			</button>
			</form>
			--->
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
</header>
<div class="menu_large"></div>