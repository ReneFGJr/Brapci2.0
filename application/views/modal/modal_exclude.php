<!-- Modal -->
<div class="modal fade" id="modalExclude" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Excluir</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="dd51b">
				<h3>Confirma exclusão do registro?</h3>
				<input type="hidden" name="ide" id="ide" value="" readonly="">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					NÃO
				</button>
				<button type="button" id="confirm_exclude" class="btn btn-danger">
					SIM
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	function exclude($id) {
		jQuery("#ide").val($id);
		jQuery("#modalExclude").modal("toggle");
	}


	jQuery("#confirm_exclude").click(function() {
	    $id = jQuery("#ide").val(); 
		$.ajax({
			type : "POST",
			url : "<?php echo base_url('index.php/main/ajax_action/exclude/');?>" + $id,
			data : "q=" + $id,
			success : function(data) {
				$("#dd51b").html(data);
			}
		});
	}); 
</script>