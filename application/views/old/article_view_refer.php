<div class="row">
	<div class="col-md-12">
		<h3>Referências</h3>
		<?php
		echo $citeds;
		if (perfil("#BIB"))
			{
				echo '<span class="btn btn-default" onclick="newxy(\''.base_url('index.php/admin/refer/'.$ar_codigo).'\',1000,600);">Inserir Bibliografia</span>';
			}
		?>
	</div>
</div>
