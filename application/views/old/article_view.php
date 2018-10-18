<div class="container">
	<div class="row">
		<div class="col-md-8">
			<h3><?php echo $jnl_nome; ?></h3>
			<a href="<?php echo base_url('index.php//admin/issue_view/' . round($ar_edition) . '/' . checkpost_link(round($ar_edition))); ?>">
			<span class="middle">v. <?php echo $ed_vol . ', n.' . $ed_nr . ', ' . $ed_ano . $pages; ?></span>
			</a>
		</div>
		<div class="col-md-4">
			<br>
			BDOI: <?php echo $ar_bdoi; ?><BR>
			DOI: <?php echo $ar_doi; ?>
		</div>
	</div>
</br>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Descrição</a></li>
    <li role="presentation"><a href="#refer" aria-controls="refer" role="tab" data-toggle="tab">Referências</a></li>
    <li role="presentation"><a href="#cited" aria-controls="cited" role="tab" data-toggle="tab">Citações</a></li>
    <li role="presentation"><a href="#marc" aria-controls="marc" role="tab" data-toggle="tab">MARC21</a></li>
    <li role="presentation"><a href="#marc" aria-controls="marc" role="tab" data-toggle="tab">RDF</a></li>
    <?php
	if (perfil("#BIB#ADM")) {
		echo '<li role="presentation"><a href="#setting" aria-controls="setting" role="tab" data-toggle="tab">Editar</a></li>';
		echo '<li role="presentation"><a href="#support" aria-controls="setting" role="tab" data-toggle="tab">Suporte</a></li>';
	}
    ?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home"><?php echo $tab_descript; ?></div>
    <div role="tabpanel" class="tab-pane" id="refer"><?php echo $tab_refer; ?></div>
    <div role="tabpanel" class="tab-pane" id="cited">.3.</div>
    <div role="tabpanel" class="tab-pane" id="marc"><?php echo $tab_marc21; ?></div>
    <div role="tabpanel" class="tab-pane" id="cited">RDF</div>
    <?php
	if (perfil("#BIB")) {
		echo '<div role="tabpanel" class="tab-pane" id="setting">' . $tab_author .$tab_editar . '</div>';
		if (isset($tab_support)) {
			echo '<div role="tabpanel" class="tab-pane" id="support">' .		 
				$tab_support . 
			'</div>';
		}
	}
    ?>    
  </div>

</div>