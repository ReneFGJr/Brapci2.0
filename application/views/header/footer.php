<style>
	.footer {
		/
		color: black;
		border: 0px solid #ffffff;
		box-shadow: 0px -4px 10px 2px #dedede;
		background: rgba(249, 249, 249, .8);
		transition: background .8s ease;
		z-index: 10;
	}

</style>
<div style="height: 150px;"></div>

<?php if (!isset($simple)) {
	?>
	<!------- Facebook -------->
	<div id="fb-root"></div>
	<script>
		( function(d, s, id) {
			var js,
			fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id))
				return;
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.7&appId=547858661992170";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk')); 
	</script>
<?php } ?>
<div class="container-fluid footer" style="min-height: 250px;">
	<div class="row" style="padding: 10px;">
		<div class="col-md-5 col-sm-12" style="line-height: 100%;">
			<b>BRAPCI - Base de Dados em Ciência da Informação</b>
			<br>
			Acervo de Publicações Brasileiras em Ciência da Informação
			<br>
			Universidade Federal do Paraná | Universidade Federal do Rio Grande do Sul
			<br>
			Versão 4.3.20191109 beta | 2010-<?php echo date("Y");?>
			<br>
			<a href="mailto:brapcici@gmail.com"><font color="black">brapcici@gmail.com</font></a> | <a href="mailto:renefgj@gmail.com"><font color="black">renefgj@gmail.com</font></a>
			<br/><br/>
			<br/><br/>
		</div>

		<div class="col-md-3 col-sm-4 hidden-xs text-right">
			<?php if(!isset($_SESSION["user"])) 
			{ ?>
				<div class="fb-like" 
				data-href="https://www.facebook.com/brapci.ci/" 
				data-layout="box_count" 
				data-action="like" 
				data-show-faces="false">
			</div>
		<?php } ?>
	</div>		

		<div class="col-md-1 col-sm-2">
			<a href="http://www.ufrgs.br/" target="_new"><img src="<?php echo base_url("img/instituition/ufrgs-color.png");?>" class="img-fluid"></a>
		</div>
		<div class="col-md-2  col-sm-4" style="margin: 15px 0px 0px 0px;">
			<a href="http://www.ufrgs.br/ppgcin" target="_new"><img src="<?php echo base_url("img/instituition/ufrgs_ppgcin.png");?>" class="img-fluid"></a>
		</div>
		

</div>
</div>