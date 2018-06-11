<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
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
        background: #003677;
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

    @media (max-width: 1024px) {
        .col-lg-0 {
            display: none;
        }
        .btn-toggle {
            display: block;
        }
    }
</style>
<header>
	<nav class="navbar fixed-top navbar-expand-lg">
		<a href="<?php echo base_url(PATH); ?>"> <img src="<?php echo base_url('img/logo/logo-brapci.png'); ?>" id="logo" class="logo-lg col-lg-0" border=0>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link fsz " href="<?php echo base_url(PATH);?>">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link fsz " href="<?php echo base_url(PATH.'about');?>"><?php echo msg('About');?></a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link fsz dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Dropdown </a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="#">Action</a>
						<a class="dropdown-item" href="#">Another action</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">Something else here</a>
					</div>
				</li>
			</ul>
			<form class="form-inline my-2 my-lg-0">
				<input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
				<button class="btn btn-outline-success my-2 my-sm-0" type="submit">
					Search
				</button>
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
					$(".fsz").animate({fontSize: "20px" }, 1000 );
					$menu_size = 1;
				}
				//TROCA P CLASSE MENOR
			} else {
				if ($menu_size == 1) {
					$("header").switchClass("menu_small", "menu_large", 1000, "easeInOutQuad");
					$("#logo").switchClass("logo", "logo-lg", 1000, "easeInOutQuad");
					$(".fsz").animate({fontSize: "40px" }, 1000 );
					$menu_size = 0;
				}
			}
		});
	</script>
</header>
<div class="menu_large"></div>