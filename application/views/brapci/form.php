<style>
input[type=text] {
    width: 100%;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    background-image: url('<?php echo HTTP; ?>img/icone/searchicon.png');
    background-position: 10px 10px;
    background-repeat: no-repeat;
    padding: 12px 20px 12px 40px;
    -webkit-transition: width 0.4s ease-in-out;
    transition: width 0.4s ease-in-out;
}

input[type=text]:focus {
    width: 100%;
    background-image: url('<?php echo HTTP; ?>img/icone/searchicon.png');
    background-color: white;
    background-position: 10px 10px;
    background-repeat: no-repeat;
    }
</style>
</head>
<body>
	<div class="container" style="border: 0px solid #ff0000;">
		<div class="row">
			<div class="col-12">

				<form class="search-wrapper ucase">
					<div>
						<div>
							<?php echo msg('search_term'); ?>
							<input type="text" class="form_input" name="q" id="q" placeholder="<?php msg('search_here'); ?>" value="<?php echo get("q"); ?>">
							<button type="submit" >
								<?php echo msg('Search'); ?>
							</button>
						</div>
						<?php
                        $pos = get("type");
                        if (strlen($pos) == 0) {
                            $pos = 1;
                        }
                        for ($r = 1; $r <= 6; $r++) {
                            $check = '';
                            if ($r == $pos) {
                                $check = 'checked';
                            }
                            echo '<input type="radio" name="type" value="' . $r . '" ' . $check . '>' . cr();
                            echo '<span style="margin-right: 10px; font-size: 75%;">' . msg('search_' . $r) . '</span>';

                        }
						?>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		$(function() {
			$("#q").autocomplete({
				source : function(request, response) {
					$.ajax({
						/* url : "http://gd.geobytes.com/AutoCompleteCity", */
						url : "<?php echo base_url(PATH.'ajax');?>",
						dataType : "json",
						data : {
							q : request.term
						},
						success : function(data) {
							response(data);
						}
					});
				},
				minLength : 2,
				select : function(event, ui) {
				    $("#q").value = this.value;
					/* log(ui.item ? "Selected: " + ui.item.label : "Nothing selected, input was " + this.value); */
				},
				open : function() {
					$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
				},
				close : function() {
					$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
				}
			});
		});
	</script>
