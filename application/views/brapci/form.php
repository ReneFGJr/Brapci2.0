<?php
$q = htmlspecialchars(get("q"));
$q = troca($q, '¢', '"');

$pos = get("type");
$op = '';
if (strlen($pos) == 0) {
    $pos = 1;
}
for ($r = 1; $r <= 6; $r++) {
    $check = '';
    if ($r == $pos) {
        $check = 'checked';
    }
    $op .= '<input type="radio" name="type" value="' . $r . '" ' . $check . '>' . cr();
    $op .= '<span style="margin-right: 10px; font-size: 75%;">' . msg('search_' . $r) . '</span>';

}
$opx = '<span style="margin-right: 10px; font-size: 75%;">Para refinar a busca veja <a href="' . base_url(PATH . 'help') . '">' . 'Busca Avançada</a>';
$opx .= '</span>';
?>
<style>
input[type=text] {
    width: 100%;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    background-image: url('<?php echo base_url('img/icone/searchicon.png');?>');
    background-position: 10px 10px;
    background-repeat: no-repeat;
    padding: 12px 20px 12px 40px;
    -webkit-transition: width 0.4s ease-in-out;
    transition: width 0.4s ease-in-out;
}

input[type=text]:focus {
    width: 100%;
    background-image: url('<?php echo base_url('img/icone/searchicon_on.png');?>');
    background-color: white;
    background-position: 10px 10px;
    background-repeat: no-repeat;
    background-size: 24px 24px;
    }
</style>
</head>
<body>
	<div class="container" style="border: 0px solid #ff0000;">
		<div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <form class="card card-sm search-wrapper " style="border: 0px solid #ffffff;">
                            	<?php echo msg('search_term'); ?>
                                <div class="card-body row ucase no-gutters align-items-center" style="padding-bottom: 0px;">
                                    <div class="col-auto">
                                        <i class="fas fa-search h4 text-body"></i>
                                    </div>
                                    <!--end of col-->
                                    <div class="col">
                                    	<input type="text" class="form_input" name="q" id="q" placeholder="<?php msg('search_here'); ?>" 
											value="<?php echo $q; ?>">
                                       
                                    </div>
                                    <!--end of col-->
                                    <div class="col-auto">
                                        <button class="btn btn-lg btn-success" type="submit"><?php echo msg('Search'); ?></button>
                                    </div>
                                    <!--end of col-->
                                </div>
                                <div class="card-body row no-gutters align-items-center"  style="padding-top: 0px;">
                                	<?php echo $op; ?>
                               	</div>
                                <div class="card-body  no-gutters align-items-center"  style="padding-top: 0px;">
                                	<?php echo $opx; ?>
                               	</div>
                                <br>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                                
                                <!---------- LIMITS --------------->
                                <h4><?php echo msg('limits'); ?></h4>
                                <nobr>
                                <?php echo msg('search_delimitation'); ?>: 
                                <?php echo $this -> searchs -> rage("year_s", "1972", (date("Y") + 1)); ?>
                                <?php echo $this -> searchs -> rage("year_e", (date("Y") + 1), "1972"); ?>
                                </nobr>
                                
                                <nobr>
                                    <?php
                                    if (get("order") != '')
                                        {
                                            $_SESSION['order'] = get("order");
                                        }
                                        $chks = array('','','','','');
                                        if (!isset($_SESSION['order']))
                                            {
                                                $_SESSION['order'] = 0;
                                            }
                                        $chk = $_SESSION['order'];
                                        if (strlen($chk) > 0)
                                            {
                                                $chks[$chk] = 'checked';
                                            }
                                    ?>
                                    Ordernar: 
                                    <input name="order" value="0" type="radio" <?php echo $chks[0];?>> Relevância
                                    <input name="order" value="2" type="radio" <?php echo $chks[2];?>> Mais novos
                                    <input name="order" value="1" type="radio" <?php echo $chks[1];?>> Mais antigos
                                    
                                </nobr>
                                
                                
						<?php ?>
                            <?php
                            if (perfil("#ADM")) {
                                echo '<h4>' . msg('Collection') . '</h4>';
                                echo msg('collection_all');
                                echo '| <a href="' . base_url(PATH . 'collection') . '">' . msg('select_collection') . '</a>';
                            }
								?>						
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <?php echo $events; ?>
                        </div>                                
                            </form>
                        </div>			
			<hr>
			
			</div>
		</div>
	</div>

	<script>
				$(function() {
			$("#q").autocomplete({
				source : function(request, response) {
					$.ajax({
						/* url : "http://gd.geobytes.com/AutoCompleteCity", */
						url : <?php echo '"' . base_url(PATH . 'ajax') . '"'; ?>
							, dataType : "json",
							data : { q :
								request.term
							}, success : function(data) {
								response(data);
							}
							});
							}, minLength : 2,
							select : function(event, ui) {
								$("#q").value = this.value;
								/* log(ui.item ? "Selected: " + ui.item.label : "Nothing selected, input was " + this.value); */
							}, open : function() {
								$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
							}, close : function() {
								$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
							}
							});
							});
	</script>
