<!-- Modal -->
<div class="modal fade" id="modalPrefTerm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Termo preferencial</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="dd52b">
				<h3>Alteração do termo preferencial?</h3>
				<input type="hidden" name="id" id="id" value="" readonly="">
                <input type="hidden" name="idt" id="idt" value="" readonly="">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					NÃO
				</button>
				<button type="button" id="confirm_term" class="btn btn-danger">
					SIM
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	function setPrefTerm($id, $idt) {
		jQuery("#id").val($id);
		jQuery("#idt").val($idt);
		jQuery("#modalPrefTerm").modal("toggle");
	}

	jQuery("#confirm_term").click(function() {
	    $id = jQuery("#id").val();
	    $idt = jQuery("#idt").val();
		$.ajax({
			type : "POST",
			url : "<?php echo base_url('index.php/main/ajax_action/setPrefTerm/');?>" + $id,
			data : "q=" + $id+"&t=" + $idt,
			success : function(data) {
				$("#dd52b").html(data);
			}
		});
	}); 
</script>