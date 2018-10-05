<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<title>jQuery SVG 3D Tag Cloud Plugin Example</title>
<script type="text/javascript" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>

<div class="jquery-script-clear"></div>
</div>
</div>
<h1 style="margin:150px auto 30px auto;">jQuery SVG 3D Tag Cloud Plugin Example</h1>
<div id='tag-cloud' style="width: 100%; height: 400px; border:1px solid #000000;"></div>
<script src="<?php echo base_url("js/jquery.svg3dtagcloud.min.js");?>"></script>
<script>

    	$( document ).ready( function() {

            var entries = [ 
				<?php
				$sx = '';
				foreach ($subject as $key => $value) {
					if (strlen($value) > 5)
						{
							if (strlen($sx) > 0)
								{
									$sx .= ', '.cr();
								}
							$sx .= "{ label: '$value', url: '#', target: '_top' }";
						}					
				}
				echo $sx;
				?>
            ];

            var settings = {

                entries: entries,
                width: 1024,
                height: 500,
                radius: '65%',
                radiusMin: 125,
                bgDraw: true,
                bgColor: '#e8e8e8',
                opacityOver: 1.00,
                opacityOut: 0.1,
                opacitySpeed: 25,
                fov: 1800,
                speed: 2,
                fontFamily: 'Oswald, Arial, sans-serif',
                fontSize: '15',
                fontColor: '#333',
                fontWeight: 'normal',//bold
                fontStyle: 'normal',//italic 
                fontStretch: 'normal',//wider, narrower, ultra-condensed, extra-condensed, condensed, semi-condensed, semi-expanded, expanded, extra-expanded, ultra-expanded
                fontToUpperCase: false

            };

            //var svg3DTagCloud = new SVG3DTagCloud( document.getElementById( 'holder'  ), settings );
            $( '#tag-cloud' ).svg3DTagCloud( settings );

		} );
        
    </script>

</body>
</html>
