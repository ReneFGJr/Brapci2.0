<script type="text/javascript" src="<?php echo base_url('js/jQWCloudv3.1.js'); ?>"></script>

<style>
    #wordCloud {
        height: 478px;
        width: 100%;
        background-color: #ffffff;
    }
    div
        {
            border: 0px solid #0000FF;
        }
</style>
<div class="col-md-12">
	<div id="wordCloud"></div>
</div>

<script>
	$(document).ready(function() {
        $("#wordCloud").jQWCloud({
        words : [<?php
        $r = 0;
        foreach ($subject as $key => $value) {
            if ($key != '(nc)') {
                if ($r > 0) {
                    echo ', ' . cr();
                }
                $r++;
                echo "{ word : '$key', weight : '$value'}";
            }    
    }
?> ],
	//cloud_color: 'yellow',
	minFont : 10,
	maxFont : 50,
	//fontOffset: 5,
	//cloud_font_family: 'Owned',
	//verticalEnabled: false,
	padding_left : 1,
	//showSpaceDIV: true,
	//spaceDIVColor: 'white',
	word_common_classes : 'WordClass',
	word_mouseEnter : function() {
		$(this).css("text-decoration", "underline");
	}, word_mouseOut : function() {
		$(this).css("text-decoration", "none");
	}, word_click : function() {
		alert("You have selected:" + $(this).text());
	}, beforeCloudRender : function() {
		date1 = new Date();
	}, afterCloudRender : function() {
		var date2 = new Date();
		console.log("Cloud Completed in " + (date2.getTime() - date1.getTime()) + " milliseconds");
	}
	});

	});
</script>


