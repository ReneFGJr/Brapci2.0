<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->
<script src="<?php echo base_url('js/jquery.awesomeCloud-0.2.js');?>"></script>

<style type="text/css">
.wordcloud {
	border: 0px solid #036;
	height: 450px;
	margin: 0px;
	padding: 5px;
	page-break-after: always;
	page-break-inside: avoid;
	width: 100%;
}
</style>

<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">

<div role="main">
<div id="wordcloud1" class="wordcloud">
				<?php
				$sx = '';
				foreach ($subject as $key => $value) {
					if (strlen($value) >= 1)
						{
							$sx .= "<span data-weight='".$value."'>$key</span>".CR();
						}					
				}
				echo $sx;
				?>	
	
</div>
<script>
			$(document).ready(function(){
				$("#wordcloud1").awesomeCloud({
					"size" : {
						"grid" : 16,
						"normalize" : false
					},
					"options" : {
						"color" : "random-dark",
						"rotationRatio" : 0.35,
						"printMultiplier" : 5,
						"sort" : "random"
					},
					"font" : "'Times New Roman', Times, serif",
					"shape" : "square"
				});
				$("#wordcloud2").awesomeCloud({
					"size" : {
						"grid" : 29,
						"factor" : 1
					},
					"options" : {
						"color" : "random-dark",
						"rotationRatio" : 1.35
					},
					"font" : "'Times New Roman', Times, serif",
					"shape" : "circle"
				});
				$("#wordcloud3").awesomeCloud({
					"size" : {
						"grid" : 1,
						"factor" : 1
					},
					"color" : {
						"background" : "#036"
					},
					"options" : {
						"color" : "random-light",
						"rotationRatio" : 0.5,
						"printMultiplier" : 10
					},
					"font" : "'Times New Roman', Times, serif",
					"shape" : "star"
				});
			});
		</script> 
<!--[if lt IE 7 ]>
		<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
		<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
